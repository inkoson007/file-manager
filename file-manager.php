<?php
// config.php - Настройки безопасности
define('BASE_DIR', __DIR__); // Корневая директория для просмотра
define('ALLOWED_EXTENSIONS', ['php', 'html', 'css', 'js', 'json', 'txt', 'md', 'jpg', 'jpeg', 'png', 'gif', 'mp3', 'wav', 'ogg']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB максимальный размер файла для просмотра
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB максимальный размер загружаемого файла
define('VERSION', '1.5.0'); // Версия файлового менеджера

// Проверяем авторизацию
$isLoggedIn = true; // Замените на реальную проверку авторизации

if (!$isLoggedIn) {
    header("HTTP/1.1 403 Forbidden");
    exit("Доступ запрещен. Требуется авторизация.");
}


// Получаем путь к запрашиваемому файлу
$requestedPath = isset($_GET['path']) ? trim($_GET['path'], '/') : '';
$absolutePath = realpath(BASE_DIR . '/' . $requestedPath);

// Защита от directory traversal
if ($absolutePath === false || strpos($absolutePath, realpath(BASE_DIR)) !== 0) {
    $absolutePath = realpath(BASE_DIR);
    $requestedPath = '';
}

// Определяем, это файл или директория
$isFile = is_file($absolutePath);
$isDir = is_dir($absolutePath);

// Обработка действий
if (isset($_GET['action'])) {
    handleAction($absolutePath, $requestedPath);
}

// Обработка переименования
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rename'])) {
    $newName = trim($_POST['new_name']);
    if (!empty($newName)) {
        renameItem($absolutePath, $newName, dirname($requestedPath));
    }
}

// Функция для переименования файла/папки
function renameItem($oldPath, $newName, $parentPath) {
    $newPath = dirname($oldPath) . '/' . $newName;
    
    // Проверка на допустимые символы
    if (preg_match('/[\/\\\\:*?"<>|]/', $newName)) {
        $_SESSION['error'] = 'Недопустимые символы в имени';
        return false;
    }
    
    // Проверка на существование
    if (file_exists($newPath)) {
        $_SESSION['error'] = 'Файл или папка с таким именем уже существует';
        return false;
    }
    
    if (rename($oldPath, $newPath)) {
        header("Location: ?path=" . urlencode($parentPath));
        exit;
    } else {
        $_SESSION['error'] = 'Ошибка при переименовании';
        return false;
    }
}

// Полная функция для обработки действий
function handleAction($absolutePath, $requestedPath) {
    $action = $_GET['action'];
    
    switch ($action) {
        case 'download':
            if (is_file($absolutePath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($absolutePath).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($absolutePath));
                readfile($absolutePath);
                exit;
            }
            break;
            
     case 'view':
    if (is_file($absolutePath)) {
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'html', 'php', 'mp3', 'wav', 'ogg'])) {
            $relativePath = str_replace(BASE_DIR, '', $absolutePath);
            header("Location: $relativePath");
            exit;
        }
    }
    break;
            
        case 'delete':
            if (is_file($absolutePath)) {
                if (unlink($absolutePath)) {
                    $_SESSION['message'] = 'Файл успешно удален';
                } else {
                    $_SESSION['error'] = 'Ошибка при удалении файла';
                }
            } elseif (is_dir($absolutePath)) {
                // Удаляем только пустые папки
                $files = scandir($absolutePath);
                if (count($files) === 2) { // Только . и ..
                    if (rmdir($absolutePath)) {
                        $_SESSION['message'] = 'Папка успешно удалена';
                    } else {
                        $_SESSION['error'] = 'Ошибка при удалении папки';
                    }
                } else {
                    $_SESSION['error'] = 'Папка не пуста';
                }
            }
            header("Location: ?path=" . urlencode(dirname($requestedPath)));
            exit;
            break;
            
        case 'rename':
            // Просто показываем форму переименования
            break;
            
        default:
            // Неизвестное действие
            break;
    }
}

// Отображение сообщений об ошибках/успехе
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']);
}

// Обработка сохранения файла
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['file_content'])) {
        saveFileContent($absolutePath, $_POST['file_content']);
    } elseif (isset($_FILES['upload_file'])) {
        handleFileUpload($absolutePath);
    } elseif (isset($_POST['new_folder'])) {
        createNewFolder($absolutePath);
    } elseif (isset($_POST['new_file'])) {
        createNewFile($absolutePath);
    }
}

// Функция для обработки загрузки файла
function handleFileUpload($targetDir) {
    if (!is_dir($targetDir)) return false;
    
    $uploadedFile = $_FILES['upload_file'];
    $fileName = basename($uploadedFile['name']);
    $targetPath = $targetDir . '/' . $fileName;
    
    // Проверка размера файла
    if ($uploadedFile['size'] > MAX_UPLOAD_SIZE) {
        return false;
    }
    
    // Проверка расширения файла
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return false;
    }
    
    // Перемещаем файл
    return move_uploaded_file($uploadedFile['tmp_name'], $targetPath);
}

// Функция для создания новой папки
function createNewFolder($parentDir) {
    if (!is_dir($parentDir)) return false;
    
    $folderName = trim($_POST['new_folder_name']);
    if (empty($folderName)) return false;
    
    $folderPath = $parentDir . '/' . $folderName;
    if (file_exists($folderPath)) return false;
    
    return mkdir($folderPath);
}

// Функция для создания нового файла
function createNewFile($parentDir) {
    if (!is_dir($parentDir)) return false;
    
    $fileName = trim($_POST['new_file_name']);
    if (empty($fileName)) return false;
    
    $filePath = $parentDir . '/' . $fileName;
    if (file_exists($filePath)) return false;
    
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($extension, ['php', 'html', 'css', 'js', 'json', 'txt', 'md'])) {
        return false;
    }
    
    return file_put_contents($filePath, '') !== false;
}


// Функция для сохранения файла
function saveFileContent($path, $content) {
    if (!is_file($path)) return false;
    
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (!in_array($extension, ['php', 'html', 'css', 'js', 'json', 'txt', 'md'])) {
        return false;
    }
    
    return file_put_contents($path, $content) !== false;
}

// Функция для получения списка файлов
function getFilesList($dir) {
    $files = [];
    $items = scandir($dir);
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $path = $dir . '/' . $item;
        $files[] = [
            'name' => $item,
            'path' => $path,
            'is_dir' => is_dir($path),
            'size' => is_file($path) ? filesize($path) : 0,
            'modified' => filemtime($path),
            'extension' => pathinfo($item, PATHINFO_EXTENSION)
        ];
    }
    
    // Сортируем: сначала папки, потом файлы
    usort($files, function($a, $b) {
        if ($a['is_dir'] && !$b['is_dir']) return -1;
        if (!$a['is_dir'] && $b['is_dir']) return 1;
        return strcmp($a['name'], $b['name']);
    });
    
    return $files;
}

// Получаем список файлов для текущей директории
$filesList = $isDir ? getFilesList($absolutePath) : [];

// Функция для определения типа файла
function getFileType($file) {
    if ($file['is_dir']) return 'folder';
    
    $imageExts = ['jpg', 'jpeg', 'png', 'gif'];
    $audioExts = ['mp3', 'wav', 'ogg']; // Добавьте это
    $codeExts = ['php', 'html', 'css', 'js', 'json', 'txt', 'md'];
    
    $ext = strtolower($file['extension']);
    
    if (in_array($ext, $imageExts)) return 'image';
    if (in_array($ext, $audioExts)) return 'audio'; // Добавьте это
    if (in_array($ext, $codeExts)) return 'code';
    return 'other';
}

// Функция для отображения содержимого файла
function displayFileContent($path) {
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return '<div class="alert">Тип файла не поддерживается для просмотра</div>';
    }
    
    if (filesize($path) > MAX_FILE_SIZE) {
        return '<div class="alert">Файл слишком большой для просмотра</div>';
    }
    
    $content = file_get_contents($path);
    
    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
        $relativePath = str_replace(BASE_DIR, '', $path);
        return '<img src="'.$relativePath.'" class="image-preview" alt="Preview">';
    }
    
    // Добавьте этот блок для аудиофайлов
    if (in_array($extension, ['mp3', 'wav', 'ogg'])) {
        $relativePath = str_replace(BASE_DIR, '', $path);
        return '<audio controls style="width:100%; margin-top:20px;">
                    <source src="'.$relativePath.'" type="audio/'.$extension.'">
                    Ваш браузер не поддерживает аудио элемент.
                </audio>';
    }
    
    return '<div id="file-content" style="display:none;">'.htmlspecialchars($content).'</div>
            <div id="editor-container" style="height:calc(100vh - 150px); width:100%;"></div>';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOFFEE DEVELOPER - Файловый менеджер</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .file-icon.audio { color: #ff79c6; }

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
            <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
            <div class="mac-buttons">
                <div class="mac-btn mac-btn-close"></div>
                <div class="mac-btn mac-btn-min"></div>
                <div class="mac-btn mac-btn-max"></div>
            </div>
            <div class="mac-title">KOFFEE DEVELOPER - Файловый менеджер</div>
        </div>

        <div class="content-area">
            <!-- Добавляем оверлей для мобильного меню -->
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            
            <div class="sidebar" id="sidebar">
                <div class="sidebar-mobile-header">
                    <button class="mobile-menu-btn" id="closeMobileMenuBtn"><i class="fas fa-times"></i></button>
                    <div class="sidebar-title">Файлы проекта</div>
                </div>
                <div class="sidebar-header">
                    <div class="sidebar-title">Файлы проекта</div>
                    <div class="sidebar-actions">
                        <button class="sidebar-btn" title="Обновить" onclick="window.location.reload()"><i class="fas fa-sync-alt"></i></button>
                    </div>
                </div>

                <div class="file-tree">
                    <!-- Хлебные крошки -->
                    <div class="breadcrumbs">
                        <a href="?path=" class="breadcrumb"><i class="fas fa-home"></i></a>
                        <?php
                        $pathParts = explode('/', $requestedPath);
                        $currentPath = '';
                        foreach ($pathParts as $i => $part) {
                            if (empty($part)) continue;
                            $currentPath .= $part . '/';
                            echo '<span class="breadcrumb-separator">/</span>';
                            echo '<a href="?path=' . urlencode(rtrim($currentPath, '/')) . '" class="breadcrumb">' . htmlspecialchars($part) . '</a>';
                        }
                        ?>
                    </div>

                    <!-- Кнопки действий -->
                    <div class="file-actions" style="padding: 10px 15px; border-bottom: 1px solid var(--border-color);">
                        <button class="action-btn" onclick="showModal('uploadModal')"><i class="fas fa-upload"></i> Загрузить</button>
                        <button class="action-btn" onclick="showModal('newFolderModal')"><i class="fas fa-folder-plus"></i> Папка</button>
                        <button class="action-btn" onclick="showModal('newFileModal')"><i class="fas fa-file-plus"></i> Файл</button>
                    </div>

                    <!-- Список файлов -->
              <?php foreach ($filesList as $file): ?>
                <a href="?path=<?= urlencode($requestedPath . ($requestedPath ? '/' : '') . $file['name']) ?>" 
                   class="file-item <?= $absolutePath === $file['path'] ? 'active' : '' ?>">
                    <div class="file-item-content">
                       <span class="file-icon <?= getFileType($file) ?>">
                       <i class="fas <?= $file['is_dir'] ? 'fa-folder' : ($file['extension'] === 'mp3' || $file['extension'] === 'wav' || $file['extension'] === 'ogg' ? 'fa-music' : 'fa-file') ?>"></i>
                        </span>
                        <span class="file-name"><?= htmlspecialchars($file['name']) ?></span>
                    </div>
                    <?php if ($isLoggedIn): ?>
                        <div class="file-item-actions">
                            <a href="?path=<?= urlencode($requestedPath . ($requestedPath ? '/' : '') . $file['name']) ?>&action=delete" 
                               class="file-action-btn delete" 
                               onclick="return confirm('Вы уверены, что хотите удалить <?= htmlspecialchars($file['name']) ?>?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
            </div>

                <div class="file-preview">
                    <?php if ($isDir && empty($filesList)): ?>
                        <div class="empty-state">
                            <div class="empty-icon"><i class="far fa-folder-open"></i></div>
                            <div class="empty-text">Папка пуста</div>
                        </div>
                    <?php elseif ($isDir): ?>
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-folder"></i></div>
                            <div class="empty-text">Выберите файл для просмотра</div>
                        </div>
                    <?php elseif ($isFile): ?>
                        <?= displayFileContent($absolutePath) ?>
                        <form id="saveForm" method="post">
                            <input type="hidden" name="file_content" id="fileContentInput">
                        </form>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon"><i class="far fa-file-alt"></i></div>
                            <div class="empty-text">Выберите файл для просмотра</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="file-toolbar">
        <div class="file-path">
            <?= $isDir ? 'Директория' : 'Файл' ?>: /<?= htmlspecialchars($requestedPath) ?>
        </div>
        
        <div class="file-actions">
            <?php if ($isFile): ?>
                <!-- Кнопки для файлов -->
                <a href="?path=<?= urlencode($requestedPath) ?>&action=download" class="action-btn">
                    <i class="fas fa-download"></i> <span class="action-text">Скачать</span>
                </a>
                
                <?php if (in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'html', 'php'])): ?>
                    <a href="?path=<?= urlencode($requestedPath) ?>&action=view" class="action-btn">
                        <i class="fas fa-external-link-alt"></i> <span class="action-text">Открыть</span>
                    </a>
                <?php endif; ?>
                
                <a href="?path=<?= urlencode($requestedPath) ?>&action=rename" class="action-btn">
                    <i class="fas fa-pencil-alt"></i> <span class="action-text">Переименовать</span>
                </a>
                
                <?php if (in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), ['php', 'html', 'css', 'js', 'json', 'txt', 'md'])): ?>
                    <button class="action-btn primary" id="editBtn">
                        <i class="fas fa-edit"></i> <span class="action-text">Редактировать</span>
                    </button>
                    <button class="action-btn primary" id="saveBtn" style="display: none;">
                        <i class="fas fa-save"></i> <span class="action-text">Сохранить</span>
                    </button>
                <?php endif; ?>
                
                <a href="?path=<?= urlencode($requestedPath) ?>&action=delete" class="action-btn btn-danger"
                   onclick="return confirm('Вы уверены, что хотите удалить этот файл?')">
                    <i class="fas fa-trash"></i> <span class="action-text">Удалить</span>
                </a>
                
            <?php elseif ($isDir): ?>
                <!-- Кнопки для папок -->
                <a href="?path=<?= urlencode($requestedPath) ?>&action=rename" class="action-btn">
                    <i class="fas fa-pencil-alt"></i> <span class="action-text">Переименовать</span>
                </a>
                
                <a href="?path=<?= urlencode($requestedPath) ?>&action=delete" class="action-btn btn-danger"
                   onclick="return confirm('Вы уверены, что хотите удалить эту папку?')">
                    <i class="fas fa-trash"></i> <span class="action-text">Удалить</span>
                </a>
            <?php endif; ?>
            <?php if (in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), ['mp3', 'wav', 'ogg'])): ?>
            <a href="?path=<?= urlencode($requestedPath) ?>&action=view" class="action-btn">
            <i class="fas fa-play"></i> <span class="action-text">Прослушать</span>
            </a>
           <?php endif; ?>
        </div>
    </div>

        <!-- Модальное окно для загрузки файла -->
    <div class="modal" id="uploadModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Загрузить файл</div>
                <button class="modal-close" onclick="hideModal('uploadModal')">&times;</button>
            </div>
            <form class="upload-form" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="upload_file">Выберите файл для загрузки</label>
                    <input type="file" name="upload_file" id="upload_file" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="form-btn secondary" onclick="hideModal('uploadModal')">Отмена</button>
                    <button type="submit" class="form-btn primary">Загрузить</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальное окно для создания папки -->
    <div class="modal" id="newFolderModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Создать папку</div>
                <button class="modal-close" onclick="hideModal('newFolderModal')">&times;</button>
            </div>
            <form class="new-folder-form" method="post">
                <input type="hidden" name="new_folder" value="1">
                <div class="form-group">
                    <label for="new_folder_name">Имя папки</label>
                    <input type="text" name="new_folder_name" id="new_folder_name" required pattern="[^\/\\:*?\"<>|]+" title="Недопустимые символы в имени папки">
                </div>
                <div class="form-actions">
                    <button type="button" class="form-btn secondary" onclick="hideModal('newFolderModal')">Отмена</button>
                    <button type="submit" class="form-btn primary">Создать</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальное окно для создания файла -->
    <div class="modal" id="newFileModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Создать файл</div>
                <button class="modal-close" onclick="hideModal('newFileModal')">&times;</button>
            </div>
            <form class="new-file-form" method="post">
                <input type="hidden" name="new_file" value="1">
                <div class="form-group">
                    <label for="new_file_name">Имя файла (с расширением)</label>
                    <input type="text" name="new_file_name" id="new_file_name" required pattern="[^\/\\:*?\"<>|]+\.[a-zA-Z0-9]+" title="Введите имя файла с расширением (например, file.txt)">
                </div>
                <div class="form-actions">
                    <button type="button" class="form-btn secondary" onclick="hideModal('newFileModal')">Отмена</button>
                    <button type="submit" class="form-btn primary">Создать</button>
                </div>
            </form>
        </div>
    </div>

   <!-- Форма переименования -->
    <?php if (isset($_GET['action']) && $_GET['action'] === 'rename'): ?>
    <div class="rename-form-container">
        <form method="post" class="rename-form">
            <input type="hidden" name="rename" value="1">
            <div class="form-group">
                <input type="text" name="new_name" id="new_name" 
                       value="<?= htmlspecialchars(basename($absolutePath)) ?>" 
                       class="rename-input" required>
            </div>
            <div class="rename-actions">
                <button type="submit" class="action-btn primary">
                    <i class="fas fa-check"></i> <span>ОК</span>
                </button>
                <a href="?path=<?= urlencode($requestedPath) ?>" class="action-btn secondary">
                    <i class="fas fa-times"></i> <span>Отмена</span>
                </a>
            </div>
        </form>
    </div>
    <?php endif; ?>



    <!-- Версия в углу -->
    <div class="version-info" id="versionInfo">
        Версия: <?= VERSION ?>
    </div>

    <!-- Модальное окно с информацией о версии -->
    <div class="modal" id="versionModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">KOFFEE DEVELOPER File Manager v<?= VERSION ?></div>
                <button class="modal-close" id="modalClose">&times;</button>
            </div>
            <div>
                <h3>Что нового в этой версии:</h3>
                <ul>
                    <li>Добавлена поддержка музыкальных файлов</li>
                </ul>
                <p>Дата выпуска: <?= date('d.m.Y') ?></p>
            </div>
        </div>
    </div>

    <!-- Прелоадер -->
  <div class="loader-overlay" id="loaderOverlay">
     <div class="loader-title">KOFFEE DEVELOPER - Файловый менеджер <?= VERSION ?></div>
     <div class="loader-spinner"></div>
  </div>

    <!-- Подключаем Monaco Editor -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs/loader.min.js"></script>
    <script>
          // Функции для работы с модальными окнами
        function showModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        
        function hideModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        
        // Улучшенное управление мобильным меню
        document.getElementById('mobileMenuBtn').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('sidebar').classList.add('active');
            document.getElementById('sidebarOverlay').classList.add('active');
        });
        
        document.getElementById('closeMobileMenuBtn').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('sidebar').classList.remove('active');
            document.getElementById('sidebarOverlay').classList.remove('active');
        });
        
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('active');
            document.getElementById('sidebarOverlay').classList.remove('active');
        });
        
        // Закрытие меню при клике вне его
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            
            if (window.innerWidth <= 767 && sidebar.classList.contains('active') && 
                !sidebar.contains(event.target) && event.target !== mobileMenuBtn) {
                sidebar.classList.remove('active');
                document.getElementById('sidebarOverlay').classList.remove('active');
            }
        });

        // Monaco Editor configuration
        let monacoEditor;
        let isEditMode = false;

        // Инициализация Monaco Editor
        function initMonacoEditor() {
            require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs' }});
            
            require(['vs/editor/editor.main'], function() {
                const fileContent = document.getElementById('file-content')?.textContent || '';
                const fileExtension = '<?= $isFile ? strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) : "" ?>';
                
                // Определяем язык для подсветки синтаксиса
                let language = 'text';
                switch(fileExtension) {
                    case 'js': language = 'javascript'; break;
                    case 'php': language = 'php'; break;
                    case 'html': language = 'html'; break;
                    case 'css': language = 'css'; break;
                    case 'json': language = 'json'; break;
                    case 'md': language = 'markdown'; break;
                }
                
                // Создаем редактор
                monacoEditor = monaco.editor.create(document.getElementById('editor-container'), {
                    value: fileContent,
                    language: language,
                    theme: 'vs-dark',
                    automaticLayout: true,
                    minimap: { enabled: window.innerWidth > 768 },
                    fontSize: 14,
                    scrollBeyondLastLine: false,
                    renderWhitespace: 'selection',
                    wordWrap: 'on'
                });
                
                // Скрываем редактор по умолчанию (показываем только в режиме редактирования)
                if (!isEditMode) {
                    document.getElementById('editor-container').style.display = 'none';
                    document.getElementById('file-content').style.display = 'block';
                }

                // Обновляем мини-карту при изменении размера окна
                window.addEventListener('resize', function() {
                    if (monacoEditor) {
                        monacoEditor.updateOptions({
                            minimap: { enabled: window.innerWidth > 768 }
                        });
                    }
                });
            });
        }

        // Переключение режима редактирования
        document.getElementById('editBtn')?.addEventListener('click', function() {
            if (!monacoEditor) {
                initMonacoEditor();
            } else {
                document.getElementById('editor-container').style.display = 'block';
                document.getElementById('file-content').style.display = 'none';
            }
            
            isEditMode = true;
            document.getElementById('editBtn').style.display = 'none';
            document.getElementById('saveBtn').style.display = 'block';
        });

        // Сохранение файла
        document.getElementById('saveBtn')?.addEventListener('click', function() {
            if (!monacoEditor || !isEditMode) return;
            
            document.getElementById('fileContentInput').value = monacoEditor.getValue();
            document.getElementById('saveForm').submit();
        });

        // Модальное окно версии
        document.getElementById('versionInfo').addEventListener('click', function() {
            document.getElementById('versionModal').style.display = 'flex';
        });

        document.getElementById('modalClose').addEventListener('click', function() {
            document.getElementById('versionModal').style.display = 'none';
        });

        // Закрытие модального окна при клике вне его
        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('versionModal')) {
                document.getElementById('versionModal').style.display = 'none';
            }
        });

        // Управление мобильным меню
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('active');
        });

        document.getElementById('closeMobileMenuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('active');
        });

        // Закрытие меню при клике вне его
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            
            if (!sidebar.contains(event.target) && event.target !== mobileMenuBtn) {
                sidebar.classList.remove('active');
            }
        });

        // Инициализация при загрузке
        window.addEventListener('DOMContentLoaded', function() {
            // Для текстовых файлов сразу инициализируем редактор (в режиме просмотра)
            if (<?= $isFile && in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), ['php', 'html', 'css', 'js', 'json', 'txt', 'md']) ? 'true' : 'false' ?>) {
                initMonacoEditor();
            }
        });

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