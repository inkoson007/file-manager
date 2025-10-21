<?php
// Определяем код ошибки
$error_code = $_SERVER['REDIRECT_STATUS'] ?? http_response_code();
define('VERSION', '1.5.0'); // Версия файлового менеджера
// Описание ошибок
$error_messages = [
    '400' => 'Неверный запрос. Проверьте корректность URL или параметров.',
    '401' => 'Требуется авторизация. Доступ ограничен.',
    '403' => 'Доступ запрещен. У вас нет прав на просмотр этой страницы.',
    '404' => 'Страница не найдена. Проверьте адрес или вернитесь на главную.',
    '500' => 'Внутренняя ошибка сервера. Попробуйте позже.',
    '502' => 'Плохой шлюз. Сервер получил неверный ответ.',
    '503' => 'Сервис временно недоступен. Пожалуйста, повторите позже.',
];

$code = isset($error_messages[$error_code]) ? $error_code : '500';
$message = $error_messages[$code];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Ошибка <?= htmlspecialchars($code) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
          :root {
            --sidebar-width: 280px;
            --header-height: 50px;
            --primary-color: #646cff;
            --sidebar-bg: #2a2d3e;
            --header-bg: #1e1e2e;
            --main-bg: #282a36;
            --editor-bg: #1e1e1e;
            --text-color: #f8f8f2;
            --border-color: #44475a;
            --mobile-breakpoint: 768px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body {
            background-color: var(--main-bg);
            color: var(--text-color);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Макет в стиле macOS */
        .mac-window {
            display: flex;
            flex-direction: column;
            height: 100vh;
            border-radius: 0;
            overflow: hidden;
            background-color: var(--main-bg);
            position: relative;
        }

        @media (min-width: 768px) {
            .mac-window {
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            }
        }

        .mac-title-bar {
            height: var(--header-height);
            background-color: var(--header-bg);
            display: flex;
            align-items: center;
            padding: 0 15px;
            border-bottom: 1px solid var(--border-color);
            -webkit-user-select: none;
            user-select: none;
        }

        .mac-buttons {
            display: none; /* Скрываем кнопки на мобильных */
        }

        @media (min-width: 768px) {
            .mac-buttons {
                display: flex;
                gap: 8px;
                margin-right: 15px;
            }
        }

        .mac-btn {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .mac-btn-close { background-color: #ff5f56; }
        .mac-btn-min { background-color: #ffbd2e; }
        .mac-btn-max { background-color: #27c93f; }

        .mac-title {
            flex: 1;
            text-align: center;
            font-size: 14px;
            color: #a9a9a9;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 10px;
        }

        .content-area {
            display: flex;
            flex: 1;
            overflow: hidden;
            flex-direction: column;
        }

        @media (min-width: 768px) {
            .content-area {
                flex-direction: row;
            }
        }

        /* Боковая панель */
        .sidebar {
            width: 100%;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
            z-index: 10;
        }

        @media (min-width: 768px) {
            .sidebar {
                width: var(--sidebar-width);
            }
        }

        .sidebar-header {
            padding: 10px 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-title {
            font-size: 16px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-actions {
            display: flex;
            gap: 10px;
        }

        .sidebar-btn {
            background: none;
            border: none;
            color: var(--text-color);
            cursor: pointer;
            font-size: 14px;
            opacity: 0.7;
            transition: opacity 0.2s;
            padding: 5px;
        }

        .mobile-menu-btn {
            display: block;
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 18px;
            cursor: pointer;
            margin-right: 10px;
        }

        @media (min-width: 768px) {
            .mobile-menu-btn {
                display: none;
            }
        }

        .sidebar-btn:hover {
            opacity: 1;
        }

        .file-tree {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
            max-height: 300px;
        }

        @media (min-width: 768px) {
            .file-tree {
                max-height: none;
            }
        }

        .file-item {
            padding: 8px 15px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s;
            position: relative;
            color: var(--text-color);
            text-decoration: none;
        }

        .file-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .file-item.active {
            background-color: rgba(100, 108, 255, 0.2);
        }

        .file-icon {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .file-icon.folder { color: #ffb86c; }
        .file-icon.code { color: #8be9fd; }
        .file-icon.image { color: #50fa7b; }
        .file-icon.other { color: #bd93f9; }

        .file-name {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Основная область */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

         /* Обновляем контейнер для тулбара */
    .file-toolbar {
        padding: 8px 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
        background-color: var(--header-bg);
        flex-direction: column; /* Вертикальное расположение на мобильных */
    }

    @media (min-width: 768px) {
        .file-toolbar {
            padding: 10px 15px;
            flex-wrap: nowrap;
            gap: 10px;
            flex-direction: row; /* Горизонтальное расположение на десктопе */
        }
    }

    /* Обновляем контейнер для кнопок действий */
    .file-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
    padding: 5px 0;
    }  

    @media (min-width: 768px) {
        .file-actions {
            width: auto; /* Автоматическая ширина на десктопе */
            justify-content: flex-start; /* Выравнивание по левому краю */
        }
    }

      .action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 4px;
    background-color: rgba(100, 108, 255, 0.2);
    color: var(--text-color);
    border: none;
    cursor: pointer;
    font-size: 13px;
    text-decoration: none;
    transition: background-color 0.2s;
}

        @media (min-width: 768px) {
            .action-btn {
                padding: 6px 12px;
            }
        }

        .action-btn:hover {
    background-color: rgba(100, 108, 255, 0.3);
}

.action-btn.primary {
    background-color: rgba(100, 108, 255, 0.5);
}

.action-btn.primary:hover {
    background-color: rgba(100, 108, 255, 0.7);
}

.action-btn.btn-danger {
    background-color: rgba(255, 71, 71, 0.2);
}

       .file-path {
        font-size: 12px;
        color: #a9a9a9;
        font-family: monospace;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: center; /* Выравнивание по центру */
        flex-grow: 1; /* Занимает все доступное пространство */
        padding: 0 10px; /* Отступы по бокам */
    }

    .action-btn.btn-danger:hover {
    background-color: rgba(255, 71, 71, 0.3);
}

/* Форма переименования */
.rename-form-container {
    padding: 10px;
    background-color: rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    margin: 10px 0;
}

.rename-form {
    display: flex;
    gap: 10px;
    align-items: center;
}

.rename-input {
    flex: 1;
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid var(--border-color);
    background-color: rgba(255, 255, 255, 0.1);
    color: var(--text-color);
}

.rename-actions {
    display: flex;
    gap: 8px;
}

@media (max-width: 768px) {
    .file-actions {
        justify-content: center;
    }
    
    .rename-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .rename-actions {
        justify-content: flex-end;
    }
}

    @media (min-width: 768px) {
        .file-path {
            font-size: 13px;
        }
    }

        .file-preview {
            flex: 1;
            overflow: auto;
            padding: 15px;
        }

        @media (min-width: 768px) {
            .file-preview {
                padding: 20px;
            }
        }

        /* Предпросмотр изображения */
        .image-preview {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 6px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Построчное отображение файла */
        #file-content {
            display: flex;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            line-height: 1.5;
            background-color: var(--editor-bg);
            border-radius: 6px;
            overflow: auto;
        }

        @media (min-width: 768px) {
            #file-content {
                font-size: 14px;
            }
        }

        .line-numbers {
            background-color: #1e1e1e;
            color: #858585;
            padding: 10px;
            text-align: right;
            user-select: none;
            border-right: 1px solid #444;
            flex-shrink: 0;
        }

        .line-number {
            padding: 0 5px;
        }

        .file-lines {
            flex: 1;
            padding: 10px;
            overflow-x: auto;
            white-space: pre;
        }

        .line-content {
            white-space: pre;
        }

        /* Пустое состояние */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #a9a9a9;
            text-align: center;
            padding: 20px;
        }

        .empty-icon {
            font-size: 36px;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        @media (min-width: 768px) {
            .empty-icon {
                font-size: 48px;
                margin-bottom: 15px;
            }
        }

        .empty-text {
            font-size: 14px;
        }

        @media (min-width: 768px) {
            .empty-text {
                font-size: 16px;
            }
        }

        /* Хлебные крошки */
        .breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 5px;
            padding: 10px;
            overflow-x: auto;
            white-space: nowrap;
        }

        @media (min-width: 768px) {
            .breadcrumbs {
                padding: 10px 15px;
            }
        }

        .breadcrumb {
            color: var(--text-color);
            text-decoration: none;
            font-size: 12px;
        }

        @media (min-width: 768px) {
            .breadcrumb {
                font-size: 13px;
            }
        }

        .breadcrumb:hover {
            text-decoration: underline;
        }

        .breadcrumb-separator {
            color: #666;
        }

        /* Алерты */
        .alert {
            padding: 12px;
            background-color: rgba(255, 71, 71, 0.1);
            border-radius: 6px;
            border-left: 3px solid #ff4747;
            font-size: 13px;
        }

        /* Модальное окно */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--sidebar-bg);
            border-radius: 8px;
            padding: 15px;
            max-width: 90%;
            width: 100%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            margin: 20px;
        }

        @media (min-width: 768px) {
            .modal-content {
                padding: 20px;
                max-width: 600px;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-title {
            font-size: 16px;
            font-weight: bold;
        }

        @media (min-width: 768px) {
            .modal-title {
                font-size: 18px;
            }
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 20px;
            cursor: pointer;
        }

        .version-info {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            z-index: 100;
        }

        /* Форма сохранения */
        #saveForm {
            display: none;
        }

        /* Мобильное меню */
        .sidebar-mobile-header {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid var(--border-color);
        }

        @media (min-width: 768px) {
            .sidebar-mobile-header {
                display: none;
            }
        }

        /* Скрытие/показ сайдбара на мобильных */
        @media (max-width: 767px) {
            .sidebar {
                position: absolute;
                left: -100%;
                width: 80%;
                height: 100%;
                transition: left 0.3s ease;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                width: 100%;
            }
        }

        /* Редактор на мобильных */
        #editor-container {
            height: calc(100vh - 150px);
        }

        @media (max-width: 767px) {
            #editor-container {
                height: calc(100vh - 200px);
            }
        }

        /* Эффект загрузки */
.loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: var(--sidebar-bg);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    transition: opacity 0.5s ease-out;
}

.loader-overlay.fade-out {
    opacity: 0;
    pointer-events: none;
}

.loader-title {
    color: var(--text-color);
    font-size: 24px;
    margin-bottom: 30px;
    text-align: center;
}

.loader-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid rgba(100, 108, 255, 0.3);
    border-radius: 50%;
    border-top-color: var(--primary-color);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Анимация текста (опционально) */
.loader-title {
    animation: fadeIn 1.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
    /* Добавляем новые стили для модальных окон и кнопок */
        .upload-form, .new-folder-form, .new-file-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .form-group label {
            font-size: 14px;
            color: #a9a9a9;
        }
        
        .form-group input[type="text"],
        .form-group input[type="file"] {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 8px 12px;
            color: var(--text-color);
            width: 100%;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .form-btn {
            padding: 8px 15px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .form-btn.primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .form-btn.secondary {
            background-color: transparent;
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }
        
        /* Улучшаем мобильное меню */
        @media (max-width: 767px) {
            .sidebar {
                position: fixed;
                left: -100%;
                top: 0;
                width: 85%;
                height: 100%;
                z-index: 1000;
                transition: left 0.3s ease;
            }
            
            .sidebar.active {
                left: 0;
                box-shadow: 2px 0 15px rgba(0, 0, 0, 0.5);
            }
            
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }
            
            .sidebar-overlay.active {
                display: block;
            }
        }
        /* Добавляем новые стили для скроллбаров */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--sidebar-bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Обновляем стили для кнопок действий в файлах */
        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .file-item-content {
            display: flex;
            align-items: center;
            flex-grow: 1;
            min-width: 0; /* Для правильного обрезания текста */
        }

        .file-item-actions {
            display: flex;
            gap: 5px;
            margin-left: 10px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .file-item:hover .file-item-actions {
            opacity: 1;
        }

        .file-action-btn {
            background: none;
            border: none;
            color: #a9a9a9;
            cursor: pointer;
            font-size: 14px;
            padding: 5px;
            transition: color 0.2s;
        }

        .file-action-btn:hover {
            color: var(--text-color);
        }

        .file-action-btn.delete:hover {
            color: #ff6b6b;
        }

        /* Обновляем стили для кнопок в тулбаре */
        .file-actions {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 5px;
        }

        .file-actions::-webkit-scrollbar {
            height: 4px;
        }

        .file-actions::-webkit-scrollbar-thumb {
            background: rgba(100, 108, 255, 0.5);
        }

        .action-btn {
            flex-shrink: 0; /* Чтобы кнопки не сжимались */
        }
         /* Добавляем стили для формы переименования */
        .rename-form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
        }
        
        .rename-input {
            flex-grow: 1;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 6px 10px;
            color: var(--text-color);
        }
        
        .rename-actions {
            display: flex;
            gap: 5px;
        }
        
        /* Обновляем стили для кнопок действий */
        .file-item-actions {
            display: flex;
            gap: 5px;
            margin-left: 10px;
        }
        
        .file-action-btn {
            background: none;
            border: none;
            color: #a9a9a9;
            cursor: pointer;
            font-size: 14px;
            padding: 5px;
            transition: color 0.2s;
        }
        
        .file-action-btn:hover {
            color: var(--text-color);
        }
        
        .file-action-btn.rename:hover {
            color: #8be9fd;
        }
        
        .file-action-btn.delete:hover {
            color: #ff6b6b;
        }
  </style>
</head>
<body>
  <div class="mac-window">
    <div class="mac-title-bar">
      <div class="mac-buttons">
        <div class="mac-btn mac-btn-close"></div>
        <div class="mac-btn mac-btn-min"></div>
        <div class="mac-btn mac-btn-max"></div>
      </div>
      <div class="mac-title">KOFFEE DEVELOPER - Файловый менеджер</div>
    </div>
    <div class="main-content">
      <div class="file-preview empty-state">
        <div class="empty-icon">(<?= htmlspecialchars($code) ?>)</div>
        <div class="empty-text"><?= htmlspecialchars($message) ?></div>
      </div>
       <!-- Прелоадер -->
    <div class="loader-overlay" id="loaderOverlay">
     <div class="loader-title">KOFFEE DEVELOPER - Файловый менеджер <?= VERSION ?></div>
     <div class="loader-spinner"></div>
    </div>
    </div>
  </div>
  <script>
    // Эффект загрузки
     window.addEventListener('load', function() {
    // Имитация загрузки (можно удалить setTimeout в реальном проекте)
    setTimeout(function() {
        document.getElementById('loaderOverlay').classList.add('fade-out');
        
        // Удаляем прелоадер после анимации
        setTimeout(function() {
            document.getElementById('loaderOverlay').remove();
        }, 500);
    }, 1000); // Уменьшите или удалите задержку для реального проекта
});

// Показываем прелоадер сразу при начале загрузки
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('loaderOverlay').style.display = 'flex';
});
  </script>
</body>
</html>
