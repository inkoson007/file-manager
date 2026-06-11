<?php
session_start();

// ╔══════════════════════════════════════════════════════════════╗
// ║               KOFFEE DEVELOPER — НАСТРОЙКИ                  ║
// ╠══════════════════════════════════════════════════════════════╣
// ║  Авторизация                                                 ║
define('AUTH_ENABLED',  false);             // ← false = отключить авторизацию
define('AUTH_LOGIN',    'Логин');           // ← логин
define('AUTH_PASSWORD', 'Пароль');         // ← пароль
// ╠══════════════════════════════════════════════════════════════╣
// ║  Корневая директория (по умолчанию — папка файла)            ║
define('BASE_DIR', __DIR__);
// ╠══════════════════════════════════════════════════════════════╣
// ║  Разрешённые расширения для просмотра/редактирования         ║
define('ALLOWED_EXTENSIONS', array(
    // Web
    'php', 'html', 'htm', 'css', 'js', 'json', 'xml', 'svg',
    // Скрипты / программирование
    'lua', 'py', 'rb', 'sh', 'bash', 'bat', 'cmd', 'ps1',
    'c', 'cpp', 'h', 'hpp', 'cs', 'java', 'kt', 'go', 'rs',
    'ts', 'tsx', 'jsx', 'vue', 'sql', 'r', 'pl', 'swift',
    // Конфиги / данные
    'txt', 'md', 'log', 'ini', 'cfg', 'conf', 'env', 'yaml', 'yml', 'toml',
    // Медиа
    'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'ico', 'svg',
    'mp3', 'wav', 'ogg', 'flac', 'aac',
));
// ╠══════════════════════════════════════════════════════════════╣
// ║  Макс. размер файла для просмотра / загрузки                 ║
define('MAX_FILE_SIZE',    5  * 1024 * 1024); // 5 MB
define('MAX_UPLOAD_SIZE',  10 * 1024 * 1024); // 10 MB
// ╠══════════════════════════════════════════════════════════════╣
// ║  Версия                                                      ║
define('VERSION', '2.1');
// ╠══════════════════════════════════════════════════════════════╣
// ║  Текстовые расширения (редактируются в Monaco Editor)        ║
define('TEXT_EXTENSIONS', array(
    'php','html','htm','css','js','jsx','ts','tsx','vue',
    'json','xml','svg','sql','yaml','yml','toml','ini','cfg','conf','env',
    'lua','py','rb','sh','bash','bat','cmd','ps1',
    'c','h','cpp','hpp','cs','java','kt','go','rs','swift','r','pl',
    'txt','md','log'
));
// ╚══════════════════════════════════════════════════════════════╝

// ── Авторизация ──────────────────────────────────
// Выход
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

$authError = '';

if (AUTH_ENABLED) {
    // Обработка формы входа
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_login'])) {
        if ($_POST['login'] === AUTH_LOGIN && $_POST['password'] === AUTH_PASSWORD) {
            $_SESSION['kd_auth'] = true;
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        } else {
            $authError = 'Неверный логин или пароль.';
        }
    }

    // Проверка сессии
    if (empty($_SESSION['kd_auth'])) {
        // Показываем страницу входа и выходим
        ?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KOFFEE DEVELOPER — Вход</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#1e1e1e;color:#ccc;font-family:'Segoe UI',system-ui,sans-serif;height:100vh;display:flex;align-items:center;justify-content:center;}
.login-wrap{width:360px;}
.login-logo{text-align:center;margin-bottom:32px;}
.login-logo .kd{display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;background:linear-gradient(135deg,#6c3483,#1a5276);border-radius:12px;font-size:20px;font-weight:800;color:#fff;margin-bottom:12px;box-shadow:0 4px 20px rgba(0,0,0,0.4);}
.login-logo h1{font-size:18px;font-weight:600;color:#fff;letter-spacing:0.02em;}
.login-logo p{font-size:12px;color:#555;margin-top:4px;}
.login-box{background:#252526;border:1px solid #3c3c3c;border-radius:8px;padding:28px;box-shadow:0 8px 32px rgba(0,0,0,0.5);}
.form-group{margin-bottom:16px;}
.form-group label{display:block;font-size:12px;color:#858585;margin-bottom:6px;}
.form-group input{width:100%;background:#3c3c3c;border:1px solid #474747;border-radius:4px;padding:9px 12px;color:#fff;font-size:14px;outline:none;transition:border-color .15s;}
.form-group input:focus{border-color:#007acc;}
.form-group .input-wrap{position:relative;}
.form-group .toggle-pw{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#555;cursor:pointer;font-size:13px;}
.form-group .toggle-pw:hover{color:#ccc;}
.btn-login{width:100%;background:#007acc;color:#fff;border:none;border-radius:4px;padding:10px;font-size:14px;cursor:pointer;transition:background .15s;margin-top:4px;}
.btn-login:hover{background:#0098ff;}
.login-error{background:rgba(244,71,71,.12);border:1px solid rgba(244,71,71,.3);border-radius:4px;padding:9px 12px;font-size:13px;color:#f47171;margin-bottom:16px;}
.login-footer{text-align:center;margin-top:20px;font-size:11px;color:#444;}
</style>
</head>
<body>
<div class="login-wrap">
    <div class="login-logo">
        <div class="kd">KD</div>
        <h1>KOFFEE DEVELOPER</h1>
        <p>File Manager v<?= VERSION ?></p>
    </div>
    <div class="login-box">
        <?php if ($authError): ?>
        <div class="login-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($authError) ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="do_login" value="1">
            <div class="form-group">
                <label>Логин</label>
                <input type="text" name="login" autofocus autocomplete="username" placeholder="Введите логин">
            </div>
            <div class="form-group">
                <label>Пароль</label>
                <div class="input-wrap">
                    <input type="password" name="password" id="pwField" autocomplete="current-password" placeholder="Введите пароль">
                    <button type="button" class="toggle-pw" onclick="var f=document.getElementById('pwField');f.type=f.type==='password'?'text':'password'"><i class="fas fa-eye"></i></button>
                </div>
            </div>
            <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Войти</button>
        </form>
    </div>
    <div class="login-footer">Только для авторизованных пользователей</div>
</div>
</body>
</html><?php
        exit;
    }
} // end if AUTH_ENABLED

$isLoggedIn = true;


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
    
    $filePath = $parentDir . '/' . $fileName;
    if (file_exists($filePath)) return false;
    
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($extension, TEXT_EXTENSIONS)) {
        return false;
    }
    
    return file_put_contents($filePath, '') !== false;
}


// Функция для сохранения файла
function saveFileContent($path, $content) {
    if (!is_file($path)) return false;
    
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (!in_array($extension, TEXT_EXTENSIONS)) {
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
        /* ═══════════════════════════════════════════
           VS CODE-STYLE ENHANCED IDE — FULL REDESIGN
           ═══════════════════════════════════════════ */

        :root {
            /* VS Code Dark+ palette */
            --vsc-bg:           #1e1e1e;
            --vsc-sidebar:      #252526;
            --vsc-sidebar-item: #2a2d2e;
            --vsc-panel:        #21252b;
            --vsc-titlebar:     #3c3c3c;
            --vsc-tabbar:       #2d2d2d;
            --vsc-tab-active:   #1e1e1e;
            --vsc-tab-inactive: #2d2d2d;
            --vsc-tab-border:   #007acc;
            --vsc-statusbar:    #007acc;
            --vsc-statusbar-fg: #ffffff;
            --vsc-accent:       #007acc;
            --vsc-accent-hover: #0098ff;
            --vsc-fg:           #cccccc;
            --vsc-fg-dim:       #858585;
            --vsc-fg-bright:    #ffffff;
            --vsc-border:       #3c3c3c;
            --vsc-border-light: #474747;
            --vsc-hover:        rgba(255,255,255,0.06);
            --vsc-selected:     rgba(0,122,204,0.25);
            --vsc-folder:       #dcb67a;
            --vsc-file-js:      #f1e05a;
            --vsc-file-php:     #4f5d95;
            --vsc-file-html:    #e34c26;
            --vsc-file-css:     #563d7c;
            --vsc-file-json:    #40a0ff;
            --vsc-file-md:      #083fa1;
            --vsc-file-img:     #50fa7b;
            --vsc-file-audio:   #ff79c6;
            --vsc-file-txt:     #858585;
            --vsc-danger:       #f44747;
            --vsc-success:      #4ec994;
            --vsc-warn:         #ce9178;
            --sidebar-width:    240px;
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            background: var(--vsc-bg);
            color: var(--vsc-fg);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            font-size: 13px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ── Activity Bar (left icon rail) ── */
        .activity-bar {
            position: fixed;
            left: 0; top: 0;
            width: 48px;
            height: 100vh;
            background: #333333;
            border-right: 1px solid var(--vsc-border);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 0;
            z-index: 200;
            gap: 4px;
        }
        .activity-bar-logo {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #6c3483 0%, #1a5276 100%);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; color: white; font-weight: 800;
            margin-bottom: 12px; letter-spacing: -1px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
            cursor: pointer;
        }
        .activity-btn {
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            color: var(--vsc-fg-dim);
            cursor: pointer;
            border-radius: 6px;
            border: none;
            background: none;
            font-size: 16px;
            transition: color 0.15s, background 0.15s;
            position: relative;
        }
        .activity-btn:hover { color: var(--vsc-fg-bright); background: var(--vsc-hover); }
        .activity-btn.active { color: var(--vsc-fg-bright); }
        .activity-btn.active::before {
            content: '';
            position: absolute;
            left: 0; top: 8px; bottom: 8px;
            width: 2px;
            background: var(--vsc-accent);
            border-radius: 0 2px 2px 0;
        }
        .activity-btn[title]:hover::after {
            content: attr(title);
            position: absolute;
            left: 52px;
            background: #252526;
            color: #ccc;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            border: 1px solid var(--vsc-border);
            pointer-events: none;
            z-index: 999;
        }
        .activity-bar-bottom {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        /* ── Mac window wrapper ── */
        .mac-window {
            display: flex;
            flex-direction: column;
            height: 100vh;
            margin-left: 48px;
            overflow: hidden;
        }

        /* ── Title bar ── */
        .mac-title-bar {
            height: 30px;
            background: var(--vsc-titlebar);
            display: flex;
            align-items: center;
            padding: 0 12px;
            border-bottom: 1px solid var(--vsc-border);
            flex-shrink: 0;
            user-select: none;
            gap: 10px;
        }
        .mac-buttons {
            display: flex; gap: 6px;
        }
        .mac-btn {
            width: 12px; height: 12px; border-radius: 50%;
            transition: filter 0.15s;
        }
        .mac-btn:hover { filter: brightness(1.3); }
        .mac-btn-close { background: #ff5f56; }
        .mac-btn-min   { background: #ffbd2e; }
        .mac-btn-max   { background: #27c93f; }
        .mac-title {
            flex: 1;
            text-align: center;
            font-size: 12px;
            color: var(--vsc-fg-dim);
            font-family: 'Segoe UI', sans-serif;
        }
        .mobile-menu-btn {
            display: none;
            background: none; border: none;
            color: var(--vsc-fg); font-size: 16px; cursor: pointer;
        }
        @media (max-width: 767px) {
            .mobile-menu-btn { display: flex; }
            .activity-bar { display: none; }
            .mac-window { margin-left: 0; }
        }

        /* ── Tab bar ── */
        .tab-bar {
            height: 35px;
            background: var(--vsc-tabbar);
            border-bottom: 1px solid var(--vsc-border);
            display: flex;
            align-items: flex-end;
            overflow-x: auto;
            flex-shrink: 0;
        }
        .tab-bar::-webkit-scrollbar { height: 3px; }
        .tab-bar::-webkit-scrollbar-thumb { background: var(--vsc-border-light); }
        .tab {
            display: flex; align-items: center; gap: 8px;
            padding: 0 16px;
            height: 35px;
            min-width: 100px;
            max-width: 180px;
            background: var(--vsc-tab-inactive);
            border-right: 1px solid var(--vsc-border);
            color: var(--vsc-fg-dim);
            font-size: 12px;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: background 0.1s, color 0.1s;
            text-decoration: none;
            position: relative;
            flex-shrink: 0;
        }
        .tab.active {
            background: var(--vsc-tab-active);
            color: var(--vsc-fg-bright);
            border-top: 1px solid var(--vsc-tab-border);
        }
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 1px;
            background: var(--vsc-tab-active);
        }
        .tab:hover { color: var(--vsc-fg-bright); }
        .tab-icon { font-size: 11px; flex-shrink: 0; }
        .tab-name { overflow: hidden; text-overflow: ellipsis; }
        .tab-close {
            margin-left: auto; flex-shrink: 0;
            width: 16px; height: 16px;
            border-radius: 3px;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; opacity: 0;
            transition: opacity 0.15s, background 0.15s;
        }
        .tab:hover .tab-close { opacity: 0.7; }
        .tab-close:hover { opacity: 1 !important; background: rgba(255,255,255,0.1); }

        /* ── Content area ── */
        .content-area {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            background: var(--vsc-sidebar);
            border-right: 1px solid var(--vsc-border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: width 0.2s, min-width 0.2s;
        }
        /* Десктоп: скрытый sidebar */
        .sidebar.hidden {
            width: 0 !important;
            min-width: 0 !important;
            border-right: none;
            overflow: hidden;
        }
        @media (max-width: 767px) {
            .sidebar {
                position: fixed;
                left: -100%; top: 30px; bottom: 0;
                width: 80% !important; min-width: 0 !important;
                z-index: 1000;
                transition: left 0.25s ease;
            }
            .sidebar.active  { left: 0; box-shadow: 4px 0 20px rgba(0,0,0,0.5); }
            .sidebar.hidden  { left: -100%; width: 80% !important; }
        }
        .sidebar-overlay {
            display: none;
            position: fixed; top:0; left:0; right:0; bottom:0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .sidebar-overlay.active { display: block; }

        .sidebar-section-header {
            padding: 8px 12px 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--vsc-fg-dim);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sidebar-section-actions {
            display: flex; gap: 2px;
        }
        .sidebar-icon-btn {
            width: 22px; height: 22px;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none;
            color: var(--vsc-fg-dim); cursor: pointer;
            border-radius: 4px; font-size: 13px;
            transition: color 0.15s, background 0.15s;
            text-decoration: none;
        }
        .sidebar-icon-btn:hover { color: var(--vsc-fg-bright); background: var(--vsc-hover); }

        /* Breadcrumb in sidebar */
        .sidebar-breadcrumbs {
            padding: 4px 12px 6px;
            border-bottom: 1px solid var(--vsc-border);
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 2px;
        }
        .sidebar-breadcrumb {
            color: var(--vsc-fg-dim);
            text-decoration: none;
            font-size: 11px;
            padding: 1px 3px;
            border-radius: 3px;
            transition: color 0.1s, background 0.1s;
        }
        .sidebar-breadcrumb:hover { color: var(--vsc-fg-bright); background: var(--vsc-hover); }
        .sidebar-breadcrumb-sep { color: #555; font-size: 11px; }

        /* File tree */
        .file-tree {
            flex: 1;
            overflow-y: auto;
        }
        .file-tree::-webkit-scrollbar { width: 6px; }
        .file-tree::-webkit-scrollbar-track { background: transparent; }
        .file-tree::-webkit-scrollbar-thumb { background: #454545; border-radius: 3px; }

        .file-item {
            display: flex;
            align-items: center;
            padding: 3px 8px 3px 12px;
            cursor: pointer;
            color: var(--vsc-fg);
            text-decoration: none;
            position: relative;
            min-height: 22px;
            gap: 6px;
        }
        .file-item:hover { background: var(--vsc-hover); }
        .file-item.active {
            background: var(--vsc-selected);
            color: var(--vsc-fg-bright);
        }
        .file-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 2px;
            background: var(--vsc-accent);
        }
        .file-item-content {
            display: flex; align-items: center; gap: 6px;
            flex: 1; min-width: 0;
        }
        .file-icon { flex-shrink: 0; font-size: 13px; }
        .file-icon.folder  { color: var(--vsc-folder); }
        .file-icon.js      { color: var(--vsc-file-js); }
        .file-icon.php     { color: var(--vsc-file-php); }
        .file-icon.html    { color: var(--vsc-file-html); }
        .file-icon.css     { color: var(--vsc-file-css); }
        .file-icon.json    { color: var(--vsc-file-json); }
        .file-icon.md      { color: var(--vsc-file-md); }
        .file-icon.image   { color: var(--vsc-file-img); }
        .file-icon.audio   { color: var(--vsc-file-audio); }
        .file-icon.other   { color: var(--vsc-file-txt); }
        .file-icon.code    { color: var(--vsc-file-txt); }
        .file-name {
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }
        .file-item-actions {
            display: flex; gap: 2px;
            opacity: 0;
            transition: opacity 0.15s;
            flex-shrink: 0;
        }
        .file-item:hover .file-item-actions { opacity: 1; }
        .file-action-btn {
            width: 18px; height: 18px;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none;
            color: var(--vsc-fg-dim); cursor: pointer;
            border-radius: 3px; font-size: 11px;
            text-decoration: none;
            transition: color 0.15s, background 0.15s;
        }
        .file-action-btn:hover { color: var(--vsc-fg-bright); background: rgba(255,255,255,0.1); }
        .file-action-btn.delete:hover { color: var(--vsc-danger); }

        /* ── Main content ── */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: var(--vsc-bg);
        }

        /* ── Editor path breadcrumbs ── */
        .editor-breadcrumb-bar {
            height: 22px;
            background: #1e1e1e;
            border-bottom: 1px solid var(--vsc-border);
            display: flex;
            align-items: center;
            padding: 0 12px;
            gap: 4px;
            flex-shrink: 0;
            overflow: hidden;
        }
        .editor-breadcrumb {
            font-size: 12px;
            color: var(--vsc-fg-dim);
            text-decoration: none;
            padding: 1px 4px;
            border-radius: 3px;
        }
        .editor-breadcrumb:hover { background: var(--vsc-hover); color: var(--vsc-fg-bright); }
        .editor-breadcrumb.last { color: var(--vsc-fg); }
        .editor-breadcrumb-sep { color: #555; font-size: 12px; }

        /* ── Toolbar ── */
        .file-toolbar {
            height: 35px;
            background: #2d2d2d;
            border-bottom: 1px solid var(--vsc-border);
            display: flex;
            align-items: center;
            padding: 0 10px;
            gap: 6px;
            flex-shrink: 0;
            overflow-x: auto;
        }
        .file-toolbar::-webkit-scrollbar { height: 0; }
        .toolbar-group {
            display: flex; align-items: center; gap: 4px;
        }
        .toolbar-sep {
            width: 1px; height: 18px;
            background: var(--vsc-border-light);
            margin: 0 4px;
            flex-shrink: 0;
        }
        .toolbar-btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 8px;
            height: 26px;
            border-radius: 4px;
            background: transparent;
            color: var(--vsc-fg);
            border: 1px solid transparent;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.15s, border-color 0.15s, color 0.15s;
            flex-shrink: 0;
        }
        .toolbar-btn:hover {
            background: var(--vsc-hover);
            border-color: var(--vsc-border-light);
        }
        .toolbar-btn.primary {
            background: var(--vsc-accent);
            color: white;
            border-color: var(--vsc-accent);
        }
        .toolbar-btn.primary:hover {
            background: var(--vsc-accent-hover);
            border-color: var(--vsc-accent-hover);
        }
        .toolbar-btn.danger {
            color: var(--vsc-danger);
        }
        .toolbar-btn.danger:hover {
            background: rgba(244,71,71,0.15);
            border-color: rgba(244,71,71,0.3);
        }
        .toolbar-btn.success {
            background: var(--vsc-success);
            color: #111;
            border-color: var(--vsc-success);
        }
        .file-path-display {
            margin-left: auto;
            font-size: 11px;
            color: var(--vsc-fg-dim);
            font-family: 'Consolas', 'Cascadia Code', monospace;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
        }

        /* ── File preview area ── */
        .file-preview {
            flex: 1;
            overflow: auto;
            position: relative;
        }
        .file-preview::-webkit-scrollbar { width: 10px; height: 10px; }
        .file-preview::-webkit-scrollbar-track { background: #1e1e1e; }
        .file-preview::-webkit-scrollbar-thumb { background: #454545; border-radius: 3px; }
        .file-preview::-webkit-scrollbar-thumb:hover { background: #5a5a5a; }

        /* ── Empty / welcome state ── */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--vsc-fg-dim);
            text-align: center;
            padding: 40px;
            gap: 16px;
        }
        .empty-icon { font-size: 56px; opacity: 0.3; }
        .empty-text { font-size: 20px; font-weight: 300; color: var(--vsc-fg-dim); }
        .empty-hint { font-size: 12px; color: #555; max-width: 300px; line-height: 1.6; }
        .empty-shortcuts {
            margin-top: 16px;
            display: flex; flex-direction: column; gap: 6px; text-align: left;
        }
        .shortcut-row {
            display: flex; gap: 12px; align-items: center;
            font-size: 12px; color: #666;
        }
        .kbd {
            background: #2d2d2d;
            border: 1px solid #555;
            padding: 1px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 11px;
            color: #ccc;
            box-shadow: 0 1px 0 #000;
        }

        /* ── Editor container ── */
        #file-content { display: none; }
        #editor-container {
            height: 100%;
            width: 100%;
        }

        /* ── Image preview ── */
        .image-preview-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 20px;
            background:
                linear-gradient(45deg, #2a2a2a 25%, transparent 25%),
                linear-gradient(-45deg, #2a2a2a 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #2a2a2a 75%),
                linear-gradient(-45deg, transparent 75%, #2a2a2a 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }
        .image-preview {
            max-width: 100%; max-height: 100%;
            object-fit: contain;
            border-radius: 4px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.6);
        }

        /* ── Status bar ── */
        .status-bar {
            height: 22px;
            background: var(--vsc-statusbar);
            display: flex;
            align-items: center;
            padding: 0 10px;
            gap: 0;
            flex-shrink: 0;
            font-size: 11px;
            color: var(--vsc-statusbar-fg);
            overflow: hidden;
        }
        .status-item {
            display: flex; align-items: center; gap: 4px;
            padding: 0 8px; height: 100%;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.15s;
        }
        .status-item:hover { background: rgba(255,255,255,0.12); }
        .status-item i { font-size: 11px; }
        .status-right { margin-left: auto; display: flex; }

        /* ── Modals ── */
        .modal {
            display: none;
            position: fixed; top:0; left:0; right:0; bottom:0;
            background: rgba(0,0,0,0.6);
            z-index: 2000;
            justify-content: center; align-items: center;
        }
        .modal.show { display: flex; }
        .modal-content {
            background: #252526;
            border: 1px solid var(--vsc-border-light);
            border-radius: 6px;
            width: 420px; max-width: 95vw;
            box-shadow: 0 16px 48px rgba(0,0,0,0.5);
            overflow: hidden;
        }
        .modal-header {
            padding: 14px 16px 12px;
            border-bottom: 1px solid var(--vsc-border);
            display: flex; align-items: center; gap: 10px;
        }
        .modal-title { font-size: 14px; font-weight: 600; color: var(--vsc-fg-bright); flex: 1; }
        .modal-close {
            width: 24px; height: 24px;
            background: none; border: none;
            color: var(--vsc-fg-dim); cursor: pointer;
            border-radius: 4px; font-size: 16px;
            display: flex; align-items: center; justify-content: center;
            transition: color 0.15s, background 0.15s;
        }
        .modal-close:hover { color: var(--vsc-fg-bright); background: var(--vsc-hover); }
        .modal-body { padding: 16px; display: flex; flex-direction: column; gap: 12px; }
        .modal-footer {
            padding: 10px 16px;
            border-top: 1px solid var(--vsc-border);
            display: flex; justify-content: flex-end; gap: 8px;
        }

        /* ── Forms ── */
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-label { font-size: 12px; color: var(--vsc-fg-dim); }
        .form-input {
            background: #3c3c3c;
            border: 1px solid var(--vsc-border-light);
            border-radius: 4px;
            padding: 7px 10px;
            color: var(--vsc-fg-bright);
            font-size: 13px;
            width: 100%;
            outline: none;
            transition: border-color 0.15s;
        }
        .form-input:focus { border-color: var(--vsc-accent); }
        .form-input[type="file"] { padding: 5px 10px; }

        .btn {
            padding: 6px 14px; border-radius: 4px;
            font-size: 13px; cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s;
        }
        .btn-primary {
            background: var(--vsc-accent); color: white;
            border-color: var(--vsc-accent);
        }
        .btn-primary:hover { background: var(--vsc-accent-hover); }
        .btn-secondary {
            background: transparent; color: var(--vsc-fg);
            border-color: var(--vsc-border-light);
        }
        .btn-secondary:hover { background: var(--vsc-hover); }

        /* ── Command Palette ── */
        .cmd-palette {
            display: none;
            position: fixed; top: 60px; left: 50%; transform: translateX(-50%);
            width: 560px; max-width: 95vw;
            background: #252526;
            border: 1px solid var(--vsc-border-light);
            border-radius: 6px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
            z-index: 3000;
            overflow: hidden;
        }
        .cmd-palette.show { display: block; }
        .cmd-input-wrap {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px;
            border-bottom: 1px solid var(--vsc-border);
        }
        .cmd-input-wrap i { color: var(--vsc-fg-dim); font-size: 14px; }
        .cmd-input {
            flex: 1; background: none; border: none;
            color: var(--vsc-fg-bright); font-size: 14px;
            outline: none;
        }
        .cmd-results { max-height: 300px; overflow-y: auto; }
        .cmd-item {
            display: flex; align-items: center; gap: 12px;
            padding: 8px 14px;
            cursor: pointer;
            font-size: 13px;
            color: var(--vsc-fg);
            transition: background 0.1s;
        }
        .cmd-item:hover, .cmd-item.selected { background: var(--vsc-selected); color: var(--vsc-fg-bright); }
        .cmd-item i { width: 16px; text-align: center; color: var(--vsc-fg-dim); font-size: 13px; }
        .cmd-kbd {
            margin-left: auto;
            font-size: 11px; color: #666;
            font-family: monospace;
        }

        /* ── Notifications / alerts ── */
        .notification {
            position: fixed; bottom: 30px; right: 16px;
            background: #333;
            border: 1px solid var(--vsc-border-light);
            border-left: 3px solid var(--vsc-accent);
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 13px;
            color: var(--vsc-fg-bright);
            box-shadow: 0 4px 16px rgba(0,0,0,0.4);
            z-index: 4000;
            display: none;
            gap: 8px;
            align-items: center;
            max-width: 320px;
            animation: slideInRight 0.2s ease;
        }
        .notification.show { display: flex; }
        .notification.error { border-left-color: var(--vsc-danger); }
        .notification.success { border-left-color: var(--vsc-success); }
        @keyframes slideInRight {
            from { transform: translateX(20px); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }

        /* ── Loader — убран ── */

        /* ── Tab extras ── */
        .tab-spacer { flex: 1; min-width: 8px; }
        .tab-actions {
            display: flex; align-items: center; gap: 2px;
            padding: 0 6px; flex-shrink: 0;
            border-left: 1px solid var(--vsc-border);
        }
        .tab-action-btn {
            width: 28px; height: 28px;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none;
            color: var(--vsc-fg-dim); cursor: pointer;
            border-radius: 4px; font-size: 13px;
            transition: color .15s, background .15s;
        }
        .tab-action-btn:hover { color: var(--vsc-fg-bright); background: var(--vsc-hover); }
        .tab-action-btn.copied { color: var(--vsc-success) !important; }

        /* ── Rename form ── */
        .rename-form-container {
            position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%);
            background: #252526;
            border: 1px solid var(--vsc-accent);
            border-radius: 6px;
            padding: 12px 16px;
            width: 400px; max-width: 95vw;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
            z-index: 500;
            display: flex; flex-direction: column; gap: 10px;
        }
        .rename-form-title { font-size: 12px; color: var(--vsc-fg-dim); font-weight: 600; }
        .rename-form { display: flex; gap: 8px; }
        .rename-input {
            flex: 1; background: #3c3c3c;
            border: 1px solid var(--vsc-border-light);
            border-radius: 4px;
            padding: 6px 10px; color: var(--vsc-fg-bright);
            font-size: 13px; outline: none;
        }
        .rename-input:focus { border-color: var(--vsc-accent); }
        .rename-actions { display: flex; gap: 6px; }

        /* ── Version badge ── */
        .version-info {
            display: none; /* shown in status bar now */
        }

        /* ── Scrollbars global ── */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #454545; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #5a5a5a; }

        /* ── File modal version ── */
        .modal-content-sm { max-width: 520px; }

        /* ── Minimap indicator ── */
        .editor-actions-strip {
            position: absolute;
            top: 8px; right: 12px;
            display: flex; gap: 4px;
            z-index: 10;
        }
        .editor-strip-btn {
            width: 24px; height: 24px;
            background: rgba(30,30,30,0.85);
            border: 1px solid var(--vsc-border);
            border-radius: 4px;
            color: var(--vsc-fg-dim);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px;
            transition: color 0.15s, background 0.15s;
        }
        .editor-strip-btn:hover { color: var(--vsc-fg-bright); background: rgba(60,60,60,0.9); }

        /* ── Mobile: sidebar mobile header ── */
        .sidebar-mobile-header {
            display: none;
        }
        @media (max-width: 767px) {
            .sidebar-mobile-header {
                display: flex; align-items: center; gap: 10px;
                padding: 8px 12px;
                border-bottom: 1px solid var(--vsc-border);
                background: var(--vsc-sidebar);
            }
            .sidebar-mobile-header .sidebar-title { font-size: 13px; font-weight: 600; }
            .file-path-display { display: none; }
            .tab-bar { display: none; }
            .editor-breadcrumb-bar { display: none; }
        }

        /* vscode version modal */
        #versionModal .modal-content { background: #252526; }
        #versionModal ul { padding-left: 16px; color: var(--vsc-fg); font-size: 13px; line-height: 2; }
        #versionModal p { color: var(--vsc-fg-dim); font-size: 12px; margin-top: 8px; }

    </style>
</head>
<body>

<!-- ═══════ ACTIVITY BAR ═══════ -->
<div class="activity-bar">
    <div class="activity-bar-logo" title="KOFFEE DEVELOPER">KD</div>
    <button class="activity-btn active" id="explorerBtn" title="Проводник">
        <i class="fas fa-copy"></i>
    </button>
    <button class="activity-btn" title="Command Palette (Ctrl+P)" onclick="openCmdPalette()">
        <i class="fas fa-search"></i>
    </button>
    <button class="activity-btn" title="Загрузить файл" onclick="showModal('uploadModal')">
        <i class="fas fa-upload"></i>
    </button>
    <button class="activity-btn" title="Создать папку" onclick="showModal('newFolderModal')">
        <i class="fas fa-folder-plus"></i>
    </button>
    <button class="activity-btn" title="Создать файл" onclick="showModal('newFileModal')">
        <i class="fas fa-file-medical"></i>
    </button>
    <div class="activity-bar-bottom">
        <button class="activity-btn" title="Обновить" onclick="window.location.reload()">
            <i class="fas fa-sync-alt"></i>
        </button>
        <button class="activity-btn" title="О программе" onclick="showModal('versionModal')">
            <i class="fas fa-info-circle"></i>
        </button>
        <a href="?logout=1" class="activity-btn" title="Выйти из аккаунта" onclick="return confirm('Выйти?')" style="color:#f47171;text-decoration:none;">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</div>

<!-- ═══════ MAIN WINDOW ═══════ -->
<div class="mac-window">
    <!-- Title bar -->
    <div class="mac-title-bar">
        <div class="mac-buttons">
            <div class="mac-btn mac-btn-close"></div>
            <div class="mac-btn mac-btn-min"></div>
            <div class="mac-btn mac-btn-max"></div>
        </div>
        <div class="mac-title">
            <?php if ($isFile): ?>
                <?= htmlspecialchars(basename($absolutePath)) ?> — KOFFEE DEVELOPER
            <?php else: ?>
                KOFFEE DEVELOPER — Файловый менеджер
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab bar — multi-tab -->
    <div class="tab-bar" id="tabBar">
        <?php
        $tabExt = $isFile ? strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) : '';
        if ($tabExt === 'js')                                       { $tabIconClass = 'fab fa-js-square';  $tabIconColor = '#f1e05a'; }
        elseif ($tabExt === 'php')                                  { $tabIconClass = 'fab fa-php';         $tabIconColor = '#8892be'; }
        elseif ($tabExt === 'html')                                 { $tabIconClass = 'fab fa-html5';       $tabIconColor = '#e34c26'; }
        elseif ($tabExt === 'css')                                  { $tabIconClass = 'fab fa-css3-alt';    $tabIconColor = '#563d7c'; }
        elseif ($tabExt === 'json')                                 { $tabIconClass = 'fas fa-code';        $tabIconColor = '#40a0ff'; }
        elseif ($tabExt === 'md')                                   { $tabIconClass = 'fab fa-markdown';    $tabIconColor = '#4078c0'; }
        elseif (in_array($tabExt, array('jpg','jpeg','png','gif'))) { $tabIconClass = 'fas fa-image';       $tabIconColor = '#50fa7b'; }
        elseif (in_array($tabExt, array('mp3','wav','ogg')))        { $tabIconClass = 'fas fa-music';       $tabIconColor = '#ff79c6'; }
        elseif ($isFile)                                            { $tabIconClass = 'fas fa-file-alt';    $tabIconColor = '#858585'; }
        else                                                        { $tabIconClass = 'fas fa-folder-open'; $tabIconColor = '#dcb67a'; }
        $tabLabel = $isFile ? htmlspecialchars(basename($absolutePath)) : ($requestedPath ? htmlspecialchars(basename($absolutePath)) : 'root');
        $tabPath  = htmlspecialchars($requestedPath);
        ?>
        <!-- Saved tabs rendered by JS from localStorage, then current active tab -->
        <div class="tab active" id="currentTab"
             data-path="<?= $tabPath ?>"
             data-label="<?= $tabLabel ?>"
             data-icon="<?= $tabIconClass ?>"
             data-color="<?= $tabIconColor ?>">
            <span class="tab-icon" style="color:<?= $tabIconColor ?>"><i class="<?= $tabIconClass ?>"></i></span>
            <span class="tab-name"><?= $tabLabel ?></span>
            <span class="tab-close" onclick="closeTab(event, '<?= $tabPath ?>')"><i class="fas fa-times"></i></span>
        </div>
        <div class="tab-spacer"></div>
        <!-- Copy link button -->
        <div class="tab-actions">
            <?php if ($isFile): ?>
            <button class="tab-action-btn" id="copyLinkBtn" title="Копировать прямую ссылку на файл">
                <i class="fas fa-link"></i>
            </button>
            <button class="tab-action-btn" id="copyDomainBtn" title="Копировать URL сайта (домен)">
                <i class="fas fa-globe"></i>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="content-area">
        <!-- Mobile overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- ═══════ SIDEBAR ═══════ -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-mobile-header">
                <button class="sidebar-icon-btn" id="closeMobileMenuBtn">
                    <i class="fas fa-times"></i>
                </button>
                <span class="sidebar-title" style="font-size:13px;font-weight:600;">ПРОВОДНИК</span>
            </div>

            <div class="sidebar-section-header">
                <span>ПРОВОДНИК</span>
                <div class="sidebar-section-actions">
                    <button class="sidebar-icon-btn" title="Новый файл" onclick="showModal('newFileModal')"><i class="fas fa-file-medical"></i></button>
                    <button class="sidebar-icon-btn" title="Новая папка" onclick="showModal('newFolderModal')"><i class="fas fa-folder-plus"></i></button>
                    <button class="sidebar-icon-btn" title="Обновить" onclick="window.location.reload()"><i class="fas fa-sync-alt"></i></button>
                    <button class="sidebar-icon-btn" title="Загрузить" onclick="showModal('uploadModal')"><i class="fas fa-upload"></i></button>
                </div>
            </div>

            <!-- Breadcrumbs in sidebar -->
            <div class="sidebar-breadcrumbs">
                <a href="?path=" class="sidebar-breadcrumb" title="Корень"><i class="fas fa-home"></i></a>
                <?php
                $pathParts = explode('/', $requestedPath);
                $crumbPath = '';
                foreach ($pathParts as $part) {
                    if (empty($part)) continue;
                    $crumbPath .= $part . '/';
                    echo '<span class="sidebar-breadcrumb-sep">›</span>';
                    echo '<a href="?path=' . urlencode(rtrim($crumbPath, '/')) . '" class="sidebar-breadcrumb">' . htmlspecialchars($part) . '</a>';
                }
                ?>
            </div>

            <!-- File tree -->
            <div class="file-tree">
                <?php foreach ($filesList as $file):
                    $fext = strtolower($file['extension']);
                    if ($file['is_dir']) {
                        $fIconFa = 'fas fa-folder'; $fIconCls = 'folder';
                    } elseif (in_array($fext, array('jpg','jpeg','png','gif'))) {
                        $fIconFa = 'fas fa-image';  $fIconCls = 'image';
                    } elseif (in_array($fext, array('mp3','wav','ogg'))) {
                        $fIconFa = 'fas fa-music';  $fIconCls = 'audio';
                    } elseif ($fext === 'js') {
                        $fIconFa = 'fab fa-js-square'; $fIconCls = 'js';
                    } elseif ($fext === 'php') {
                        $fIconFa = 'fab fa-php';    $fIconCls = 'php';
                    } elseif ($fext === 'html') {
                        $fIconFa = 'fab fa-html5';  $fIconCls = 'html';
                    } elseif ($fext === 'css') {
                        $fIconFa = 'fab fa-css3-alt'; $fIconCls = 'css';
                    } elseif ($fext === 'json') {
                        $fIconFa = 'fas fa-code';   $fIconCls = 'json';
                    } elseif ($fext === 'md') {
                        $fIconFa = 'fab fa-markdown'; $fIconCls = 'md';
                    } else {
                        $fIconFa = 'fas fa-file-alt'; $fIconCls = 'other';
                    }
                    $isActive = ($absolutePath === $file['path']) ? 'active' : '';
                    $fileLinkPath = urlencode($requestedPath . ($requestedPath ? '/' : '') . $file['name']);
                ?>
                <a href="?path=<?= $fileLinkPath ?>" class="file-item <?= $isActive ?>">
                    <div class="file-item-content">
                        <span class="file-icon <?= $fIconCls ?>"><i class="<?= $fIconFa ?>"></i></span>
                        <span class="file-name" title="<?= htmlspecialchars($file['name']) ?>"><?= htmlspecialchars($file['name']) ?></span>
                    </div>
                    <?php if ($isLoggedIn): ?>
                    <div class="file-item-actions">
                        <button class="file-action-btn" title="Переименовать"
                            onclick="event.preventDefault(); event.stopPropagation(); openRenameModal('<?= htmlspecialchars(addslashes($file['name'])) ?>', '<?= $fileLinkPath ?>')">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <a href="?path=<?= $fileLinkPath ?>&action=delete"
                           class="file-action-btn delete" title="Удалить"
                           onclick="return confirm('Удалить «<?= htmlspecialchars(addslashes($file['name'])) ?>»?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ═══════ MAIN EDITOR AREA ═══════ -->
        <div class="main-content">
            <!-- Editor breadcrumb bar -->
            <div class="editor-breadcrumb-bar">
                <a href="?path=" class="editor-breadcrumb"><i class="fas fa-home"></i></a>
                <?php
                $edCrumbPath = '';
                foreach ($pathParts as $part) {
                    if (empty($part)) continue;
                    $edCrumbPath .= $part . '/';
                    echo '<span class="editor-breadcrumb-sep">›</span>';
                    echo '<a href="?path=' . urlencode(rtrim($edCrumbPath, '/')) . '" class="editor-breadcrumb">' . htmlspecialchars($part) . '</a>';
                }
                if ($isFile) {
                    echo '<span class="editor-breadcrumb-sep">›</span>';
                    echo '<span class="editor-breadcrumb last">' . htmlspecialchars(basename($absolutePath)) . '</span>';
                }
                ?>
            </div>

            <!-- Toolbar -->
            <div class="file-toolbar">
                <div class="toolbar-group">
                    <?php if ($isFile): ?>
                        <a href="?path=<?= urlencode($requestedPath) ?>&action=download" class="toolbar-btn" title="Скачать">
                            <i class="fas fa-download"></i> Скачать
                        </a>
                        <?php if (in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif','html','php'])): ?>
                            <a href="?path=<?= urlencode($requestedPath) ?>&action=view" class="toolbar-btn" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Открыть
                            </a>
                        <?php endif; ?>
                        <?php if (in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), ['mp3','wav','ogg'])): ?>
                            <a href="?path=<?= urlencode($requestedPath) ?>&action=view" class="toolbar-btn">
                                <i class="fas fa-play"></i> Воспроизвести
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($isFile || $isDir): ?>
                        <a href="?path=<?= urlencode($requestedPath) ?>&action=rename" class="toolbar-btn">
                            <i class="fas fa-pencil-alt"></i> Переименовать
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($isFile && in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), TEXT_EXTENSIONS)): ?>
                <div class="toolbar-group">
                    <div class="toolbar-sep"></div>
                    <button class="toolbar-btn primary" id="editBtn">
                        <i class="fas fa-edit"></i> Редактировать
                    </button>
                    <button class="toolbar-btn success" id="saveBtn" style="display:none;">
                        <i class="fas fa-save"></i> Сохранить
                    </button>
                    <button class="toolbar-btn" id="toggleWordWrapBtn" style="display:none;" title="Перенос строк">
                        <i class="fas fa-text-width"></i> Перенос
                    </button>
                    <button class="toolbar-btn" id="toggleMinimapBtn" style="display:none;" title="Миникарта">
                        <i class="fas fa-map"></i> Карта
                    </button>
                    <button class="toolbar-btn" id="formatBtn" style="display:none;" title="Форматировать (Shift+Alt+F)">
                        <i class="fas fa-magic"></i> Форматировать
                    </button>
                </div>
                <?php endif; ?>

                <?php if ($isFile || $isDir): ?>
                <div class="toolbar-group" style="margin-left:auto;">
                    <a href="?path=<?= urlencode($requestedPath) ?>&action=delete"
                       class="toolbar-btn danger"
                       onclick="return confirm('Удалить «<?= htmlspecialchars(addslashes(basename($absolutePath))) ?>»?')">
                        <i class="fas fa-trash"></i> Удалить
                    </a>
                </div>
                <?php endif; ?>

                <div class="file-path-display">
                    /<?= htmlspecialchars($requestedPath ?: '/') ?>
                </div>
            </div>

            <!-- Editor / Preview area -->
            <div class="file-preview" id="filePreviewArea">
                <?php
                $previewExt = $isFile ? strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) : '';
                if ($isDir && empty($filesList)): ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="far fa-folder-open"></i></div>
                        <div class="empty-text">Папка пуста</div>
                        <div class="empty-hint">Создайте файл или загрузите что-нибудь через панель слева</div>
                    </div>
                <?php elseif ($isDir): ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-code"></i></div>
                        <div class="empty-text">Выберите файл</div>
                        <div class="empty-hint">Кликните на файл в проводнике, чтобы открыть его здесь</div>
                        <div class="empty-shortcuts">
                            <div class="shortcut-row">
                                <span class="kbd">Ctrl</span><span class="kbd">P</span>
                                <span>Command Palette</span>
                            </div>
                            <div class="shortcut-row">
                                <span class="kbd">Ctrl</span><span class="kbd">S</span>
                                <span>Сохранить файл</span>
                            </div>
                            <div class="shortcut-row">
                                <span class="kbd">Shift</span><span class="kbd">Alt</span><span class="kbd">F</span>
                                <span>Форматировать документ</span>
                            </div>
                        </div>
                    </div>
                <?php elseif ($isFile && in_array($previewExt, ['jpg','jpeg','png','gif'])): ?>
                    <?php $relPath = str_replace(BASE_DIR, '', $absolutePath); ?>
                    <div class="image-preview-wrap">
                        <img src="<?= htmlspecialchars($relPath) ?>" class="image-preview" alt="Preview">
                    </div>
                <?php elseif ($isFile && in_array($previewExt, ['mp3','wav','ogg'])): ?>
                    <?php $relPath = str_replace(BASE_DIR, '', $absolutePath); ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-music"></i></div>
                        <div class="empty-text"><?= htmlspecialchars(basename($absolutePath)) ?></div>
                        <audio controls style="margin-top:20px; width:80%; max-width:420px; border-radius:8px;">
                            <source src="<?= htmlspecialchars($relPath) ?>" type="audio/<?= $previewExt ?>">
                        </audio>
                    </div>
                <?php elseif ($isFile): ?>
                    <?php
                    $fileContent = '';
                    if (in_array($previewExt, TEXT_EXTENSIONS)) {
                        if (filesize($absolutePath) <= MAX_FILE_SIZE) {
                            $fileContent = file_get_contents($absolutePath);
                        }
                    }
                    ?>
                    <div id="file-content" style="display:none;"><?= htmlspecialchars($fileContent) ?></div>
                    <div id="editor-container" style="height:100%; width:100%;"></div>
                    <form id="saveForm" method="post" style="display:none;">
                        <input type="hidden" name="file_content" id="fileContentInput">
                    </form>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="far fa-file-code"></i></div>
                        <div class="empty-text">Файл не найден</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Status bar -->
            <div class="status-bar">
                <div class="status-item" onclick="toggleSidebar()" title="Переключить проводник" style="cursor:pointer;">
                    <i class="fas fa-code-branch"></i> main
                </div>
                <?php if ($isFile): ?>
                <div class="status-item" id="statusErrors">
                    <i class="fas fa-times-circle"></i> 0 &nbsp;
                    <i class="fas fa-exclamation-triangle"></i> 0
                </div>
                <?php endif; ?>
                <div class="status-right">
                    <?php if ($isFile): ?>
                    <div class="status-item" id="statusCursor">Ln 1, Col 1</div>
                    <div class="status-item">UTF-8</div>
                    <div class="status-item" id="statusLang">
                        <?php
                        $langNames = array(
                            'php'=>'PHP','js'=>'JavaScript','jsx'=>'JavaScript','ts'=>'TypeScript','tsx'=>'TypeScript',
                            'html'=>'HTML','htm'=>'HTML','css'=>'CSS','json'=>'JSON','xml'=>'XML','svg'=>'XML',
                            'vue'=>'Vue','md'=>'Markdown','txt'=>'Plain Text','log'=>'Plain Text',
                            'lua'=>'Lua','py'=>'Python','rb'=>'Ruby',
                            'sh'=>'Shell','bash'=>'Shell','bat'=>'Batch','cmd'=>'Batch','ps1'=>'PowerShell',
                            'c'=>'C','h'=>'C','cpp'=>'C++','hpp'=>'C++','cs'=>'C#',
                            'java'=>'Java','kt'=>'Kotlin','go'=>'Go','rs'=>'Rust','swift'=>'Swift',
                            'r'=>'R','pl'=>'Perl','sql'=>'SQL',
                            'yaml'=>'YAML','yml'=>'YAML','toml'=>'TOML',
                            'ini'=>'INI','cfg'=>'INI','conf'=>'Config','env'=>'ENV',
                        );
                        echo isset($langNames[$previewExt]) ? $langNames[$previewExt] : ($previewExt ? strtoupper($previewExt) : 'Binary');
                        ?>
                    </div>
                    <?php endif; ?>
                    <div class="status-item" onclick="showModal('versionModal')" style="cursor:pointer;">
                        KD v<?= VERSION ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════ COMMAND PALETTE ═══════ -->
<div class="cmd-palette" id="cmdPalette">
    <div class="cmd-input-wrap">
        <i class="fas fa-search"></i>
        <input type="text" class="cmd-input" id="cmdInput" placeholder="Введите команду или имя файла...">
    </div>
    <div class="cmd-results" id="cmdResults">
        <div class="cmd-item" onclick="showModal('newFileModal'); closeCmdPalette()">
            <i class="fas fa-file-medical"></i> Создать новый файл
            <span class="cmd-kbd">Ctrl+N</span>
        </div>
        <div class="cmd-item" onclick="showModal('newFolderModal'); closeCmdPalette()">
            <i class="fas fa-folder-plus"></i> Создать новую папку
        </div>
        <div class="cmd-item" onclick="showModal('uploadModal'); closeCmdPalette()">
            <i class="fas fa-upload"></i> Загрузить файл
        </div>
        <div class="cmd-item" onclick="window.location.reload()">
            <i class="fas fa-sync-alt"></i> Обновить страницу
            <span class="cmd-kbd">F5</span>
        </div>
        <?php if ($isFile && in_array($previewExt, TEXT_EXTENSIONS)): ?>
        <div class="cmd-item" onclick="triggerEdit(); closeCmdPalette()">
            <i class="fas fa-edit"></i> Редактировать файл
            <span class="cmd-kbd">Ctrl+E</span>
        </div>
        <div class="cmd-item" onclick="triggerSave(); closeCmdPalette()">
            <i class="fas fa-save"></i> Сохранить файл
            <span class="cmd-kbd">Ctrl+S</span>
        </div>
        <div class="cmd-item" onclick="triggerFormat(); closeCmdPalette()">
            <i class="fas fa-magic"></i> Форматировать документ
            <span class="cmd-kbd">Shift+Alt+F</span>
        </div>
        <?php endif; ?>
        <?php if ($isFile): ?>
        <div class="cmd-item" onclick="window.location='?path=<?= urlencode($requestedPath) ?>&action=download'; closeCmdPalette()">
            <i class="fas fa-download"></i> Скачать файл
        </div>
        <?php endif; ?>
        <div class="cmd-item" onclick="showModal('versionModal'); closeCmdPalette()">
            <i class="fas fa-info-circle"></i> О программе
        </div>
    </div>
</div>
<div class="cmd-overlay" id="cmdOverlay" onclick="closeCmdPalette()" style="display:none; position:fixed; inset:0; z-index:2999;"></div>

<!-- ═══════ RENAME MODAL ═══════ -->
<div class="modal" id="renameModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-pencil-alt" style="margin-right:8px;color:var(--vsc-accent)"></i>Переименовать</div>
            <button class="modal-close" onclick="hideModal('renameModal')">&times;</button>
        </div>
        <form method="post" id="renameForm">
            <div class="modal-body">
                <input type="hidden" name="rename" value="1">
                <div class="form-group">
                    <label class="form-label">Новое имя</label>
                    <input type="text" name="new_name" id="renameInput" class="form-input" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideModal('renameModal')">Отмена</button>
                <button type="submit" class="btn btn-primary">Переименовать</button>
            </div>
        </form>
    </div>
</div>

<!-- Rename form (server-side, for ?action=rename) -->
<?php if (isset($_GET['action']) && $_GET['action'] === 'rename'): ?>
<div class="rename-form-container">
    <div class="rename-form-title"><i class="fas fa-pencil-alt" style="margin-right:6px;"></i>Переименовать: <?= htmlspecialchars(basename($absolutePath)) ?></div>
    <form method="post" class="rename-form">
        <input type="hidden" name="rename" value="1">
        <input type="text" name="new_name" class="rename-input" value="<?= htmlspecialchars(basename($absolutePath)) ?>" required autofocus>
        <div class="rename-actions">
            <button type="submit" class="toolbar-btn primary"><i class="fas fa-check"></i> OK</button>
            <a href="?path=<?= urlencode($requestedPath) ?>" class="toolbar-btn"><i class="fas fa-times"></i></a>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- ═══════ MODAL: Upload ═══════ -->
<div class="modal" id="uploadModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-upload" style="margin-right:8px;color:var(--vsc-accent)"></i>Загрузить файл</div>
            <button class="modal-close" onclick="hideModal('uploadModal')">&times;</button>
        </div>
        <form method="post" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Выберите файл</label>
                    <input type="file" name="upload_file" class="form-input" required>
                </div>
                <div style="font-size:11px; color:var(--vsc-fg-dim);">Макс. размер: 10MB. Разрешены: php, html, css, js, json, txt, md, jpg, png, gif, mp3, wav, ogg</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideModal('uploadModal')">Отмена</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Загрузить</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════ MODAL: New Folder ═══════ -->
<div class="modal" id="newFolderModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-folder-plus" style="margin-right:8px;color:#dcb67a"></i>Создать папку</div>
            <button class="modal-close" onclick="hideModal('newFolderModal')">&times;</button>
        </div>
        <form method="post">
            <input type="hidden" name="new_folder" value="1">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Имя папки</label>
                    <input type="text" name="new_folder_name" class="form-input" required
                           pattern="[^\/\\:*?&quot;&lt;&gt;|]+" title="Недопустимые символы" autofocus>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideModal('newFolderModal')">Отмена</button>
                <button type="submit" class="btn btn-primary">Создать</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════ MODAL: New File ═══════ -->
<div class="modal" id="newFileModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-file-medical" style="margin-right:8px;color:var(--vsc-accent)"></i>Создать файл</div>
            <button class="modal-close" onclick="hideModal('newFileModal')">&times;</button>
        </div>
        <form method="post">
            <input type="hidden" name="new_file" value="1">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Имя файла (с расширением)</label>
                    <input type="text" name="new_file_name" class="form-input" required
                           pattern="[^\/\\:*?&quot;&lt;&gt;|]+\.[a-zA-Z0-9]+"
                           placeholder="example.php" title="Введите имя с расширением" autofocus>
                </div>
                <div style="font-size:11px;color:var(--vsc-fg-dim);">Поддерживаемые расширения: php, html, css, js, ts, json, xml, lua, py, rb, sh, c, cpp, cs, java, go, rs, sql, md, txt, yaml, ini и др.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideModal('newFileModal')">Отмена</button>
                <button type="submit" class="btn btn-primary">Создать</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════ MODAL: Version ═══════ -->
<div class="modal" id="versionModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title"><span style="color:var(--vsc-accent)">KD</span> KOFFEE DEVELOPER v<?= VERSION ?></div>
            <button class="modal-close" onclick="hideModal('versionModal')">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Changelog -->
            <div style="font-size:12px;color:var(--vsc-fg-dim);margin-bottom:8px;text-transform:uppercase;letter-spacing:0.06em;">Changelog</div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <div style="background:#1e1e1e;border-radius:4px;padding:10px 12px;border-left:2px solid var(--vsc-accent);">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:13px;font-weight:600;color:#fff;">v2.1</span>
                        <span style="font-size:11px;color:var(--vsc-fg-dim);">11.06.2025</span>
                    </div>
                    <ul style="padding-left:14px;color:var(--vsc-fg);font-size:12px;line-height:1.9;margin:0;">
                        <li>Авторизация с логином и паролем</li>
                        <li>Возможность отключить авторизацию</li>
                        <li>Мульти-вкладки файлов (localStorage)</li>
                        <li>Кнопки копирования прямой ссылки</li>
                        <li>Расширенная поддержка языков (Lua, Python, C++, SQL и др.)</li>
                        <li>Исправлена кнопка проводника</li>
                        <li>Убран эффект загрузки</li>
                    </ul>
                </div>
                <div style="background:#1e1e1e;border-radius:4px;padding:10px 12px;border-left:2px solid #555;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:13px;font-weight:600;color:#999;">v2.0</span>
                        <span style="font-size:11px;color:var(--vsc-fg-dim);">ранее</span>
                    </div>
                    <ul style="padding-left:14px;color:#777;font-size:12px;line-height:1.9;margin:0;">
                        <li>Редизайн интерфейса в стиле VS Code</li>
                        <li>Activity Bar, Tab Bar, Status Bar</li>
                        <li>Command Palette (Ctrl+P)</li>
                        <li>Monaco Editor с подсветкой синтаксиса</li>
                        <li>Цветные иконки файлов по типу</li>
                    </ul>
                </div>
            </div>
            <!-- Info -->
            <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--vsc-border);">
                <div style="font-size:13px;color:var(--vsc-fg);display:flex;flex-direction:column;gap:5px;">
                    <div><span style="color:var(--vsc-fg-dim);width:80px;display:inline-block;">Автор:</span> <span style="color:var(--vsc-accent);font-weight:600;">INK</span></div>
                    <div><span style="color:var(--vsc-fg-dim);width:80px;display:inline-block;">Команда:</span> <span style="color:#ccc;">KOFFEE TEAM</span></div>
                    <div><span style="color:var(--vsc-fg-dim);width:80px;display:inline-block;">Версия:</span> <span style="color:#ccc;"><?= VERSION ?></span></div>
                    <div><span style="color:var(--vsc-fg-dim);width:80px;display:inline-block;">Сборка:</span> <span style="color:#ccc;">11.06.2025</span></div>
                </div>
            </div>
            <div style="margin-top:12px;padding:9px 12px;background:rgba(0,122,204,0.08);border:1px solid rgba(0,122,204,0.2);border-radius:4px;font-size:11px;color:#777;line-height:1.7;">
                <i class="fas fa-info-circle" style="color:var(--vsc-accent);margin-right:6px;"></i>
                При модификации данного ПО просьба указать автора оригинального — <strong style="color:#bbb;">INK / KOFFEE TEAM</strong>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('versionModal')">Закрыть</button>
        </div>
    </div>
</div>

<!-- ═══════ NOTIFICATION ═══════ -->
<div class="notification" id="notification">
    <i class="fas fa-check-circle"></i>
    <span id="notificationText"></span>
</div>

<!-- ═══════ SCRIPTS ═══════ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js"></script>
<script>
// ════════════════════════════════════════════
//  SIDEBAR TOGGLE
// ════════════════════════════════════════════
var sidebar        = document.getElementById('sidebar');
var sidebarOverlay = document.getElementById('sidebarOverlay');
var explorerBtn    = document.getElementById('explorerBtn');
var isMobile       = function() { return window.innerWidth <= 767; };

function toggleSidebar() {
    if (isMobile()) {
        // Мобильный: слайд влево/вправо через класс active
        var open = sidebar.classList.contains('active');
        if (open) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            if (explorerBtn) explorerBtn.classList.remove('active');
        } else {
            sidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
            if (explorerBtn) explorerBtn.classList.add('active');
        }
    } else {
        // Десктоп: скрыть/показать через класс hidden (width: 0)
        var hidden = sidebar.classList.contains('hidden');
        if (hidden) {
            sidebar.classList.remove('hidden');
            if (explorerBtn) explorerBtn.classList.add('active');
        } else {
            sidebar.classList.add('hidden');
            if (explorerBtn) explorerBtn.classList.remove('active');
        }
    }
}

// Стартовое состояние — sidebar открыт, кнопка активна
if (explorerBtn) explorerBtn.classList.add('active');

// Wire up explorer button
if (explorerBtn) explorerBtn.addEventListener('click', toggleSidebar);

// Overlay click closes sidebar (mobile)
if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        if (explorerBtn) explorerBtn.classList.remove('active');
    });
}

// Close mobile sidebar button (inside sidebar header)
var closeMobileBtn = document.getElementById('closeMobileMenuBtn');
if (closeMobileBtn) {
    closeMobileBtn.addEventListener('click', function() {
        sidebar.classList.remove('active');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
        if (explorerBtn) explorerBtn.classList.remove('active');
    });
}

// При смене размера окна сбрасываем классы во избежание конфликтов
window.addEventListener('resize', function() {
    if (!isMobile()) {
        sidebar.classList.remove('active');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
    } else {
        sidebar.classList.remove('hidden');
    }
});

// ════════════════════════════════════════════
//  MODAL HELPERS
// ════════════════════════════════════════════
function showModal(id) {
    var el = document.getElementById(id);
    el.classList.add('show');
    el.style.display = 'flex';
}
function hideModal(id) {
    var el = document.getElementById(id);
    el.classList.remove('show');
    el.style.display = 'none';
}
document.querySelectorAll('.modal').forEach(function(modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === this) hideModal(this.id);
    });
});

// ════════════════════════════════════════════
//  NOTIFICATION
// ════════════════════════════════════════════
function showNotification(text, type) {
    type = type || 'success';
    var n = document.getElementById('notification');
    var nt = document.getElementById('notificationText');
    n.className = 'notification show ' + type;
    nt.textContent = text;
    n.style.display = 'flex';
    setTimeout(function() { n.style.display = 'none'; }, 3000);
}

// ════════════════════════════════════════════
//  MULTI-TAB SYSTEM (localStorage)
// ════════════════════════════════════════════
var TABS_KEY = 'kd_tabs_v1';
var currentPath = <?= json_encode($requestedPath) ?>;
var currentLabel = <?= json_encode($isFile ? basename($absolutePath) : ($requestedPath ? basename($absolutePath) : 'root')) ?>;
var currentIcon  = <?= json_encode($tabIconClass) ?>;
var currentColor = <?= json_encode($tabIconColor) ?>;
var isCurrentFile = <?= $isFile ? 'true' : 'false' ?>;

function loadTabs() {
    try { return JSON.parse(localStorage.getItem(TABS_KEY)) || []; }
    catch(e) { return []; }
}
function saveTabs(tabs) {
    try { localStorage.setItem(TABS_KEY, JSON.stringify(tabs)); } catch(e) {}
}

function renderTabs() {
    var tabs = loadTabs();
    var bar = document.getElementById('tabBar');
    // Remove all dynamic tabs (not current, not spacer, not actions)
    bar.querySelectorAll('.tab.dynamic').forEach(function(t) { t.remove(); });

    var currentTab = document.getElementById('currentTab');
    tabs.forEach(function(tab) {
        if (tab.path === currentPath) return; // skip — already shown as active
        var el = document.createElement('div');
        el.className = 'tab dynamic';
        el.setAttribute('data-path', tab.path);
        el.title = tab.label;
        el.innerHTML =
            '<span class="tab-icon" style="color:' + tab.color + '"><i class="' + tab.icon + '"></i></span>' +
            '<span class="tab-name">' + escHtml(tab.label) + '</span>' +
            '<span class="tab-close" data-close="' + escHtml(tab.path) + '"><i class="fas fa-times"></i></span>';
        el.addEventListener('click', function(e) {
            if (e.target.closest('.tab-close')) return;
            window.location.href = '?path=' + encodeURIComponent(tab.path);
        });
        el.querySelector('.tab-close').addEventListener('click', function(e) {
            e.stopPropagation();
            closeTab(e, tab.path);
        });
        // Insert before current tab
        bar.insertBefore(el, currentTab);
    });
}

function addCurrentTab() {
    var tabs = loadTabs();
    var exists = tabs.some(function(t) { return t.path === currentPath; });
    if (!exists && isCurrentFile) {
        tabs.push({ path: currentPath, label: currentLabel, icon: currentIcon, color: currentColor });
        if (tabs.length > 20) tabs.shift(); // max 20 tabs
        saveTabs(tabs);
    }
}

function closeTab(e, path) {
    e.stopPropagation();
    var tabs = loadTabs();
    tabs = tabs.filter(function(t) { return t.path !== path; });
    saveTabs(tabs);
    if (path === currentPath) {
        // Navigate to last remaining tab or root
        if (tabs.length > 0) {
            window.location.href = '?path=' + encodeURIComponent(tabs[tabs.length - 1].path);
        } else {
            window.location.href = '?path=';
        }
    } else {
        renderTabs();
    }
}

function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Init tabs on load
addCurrentTab();
renderTabs();

// ════════════════════════════════════════════
//  COPY LINK BUTTONS
// ════════════════════════════════════════════
var fileRelPath = <?= json_encode($isFile ? str_replace(BASE_DIR, '', $absolutePath) : '') ?>;

function copyToClipboard(text, btn) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            btn.classList.add('copied');
            showNotification('Скопировано: ' + text, 'success');
            setTimeout(function() { btn.classList.remove('copied'); }, 2000);
        });
    } else {
        // Fallback
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        btn.classList.add('copied');
        showNotification('Скопировано: ' + text, 'success');
        setTimeout(function() { btn.classList.remove('copied'); }, 2000);
    }
}

var copyLinkBtn = document.getElementById('copyLinkBtn');
var copyDomainBtn = document.getElementById('copyDomainBtn');

if (copyLinkBtn) {
    copyLinkBtn.addEventListener('click', function() {
        // Прямая ссылка на файл (домен + относительный путь файла)
        var url = window.location.protocol + '//' + window.location.host + fileRelPath;
        copyToClipboard(url, this);
    });
}
if (copyDomainBtn) {
    copyDomainBtn.addEventListener('click', function() {
        // Просто домен (origin)
        var url = window.location.protocol + '//' + window.location.host + '/';
        copyToClipboard(url, this);
    });
}

// ════════════════════════════════════════════
//  RENAME MODAL
// ════════════════════════════════════════════
function openRenameModal(name, pathEncoded) {
    document.getElementById('renameInput').value = name;
    document.getElementById('renameForm').action = '?path=' + pathEncoded + '&action=rename_post';
    showModal('renameModal');
    setTimeout(function() { document.getElementById('renameInput').select(); }, 50);
}

// ════════════════════════════════════════════
//  COMMAND PALETTE
// ════════════════════════════════════════════
function openCmdPalette() {
    document.getElementById('cmdPalette').classList.add('show');
    document.getElementById('cmdOverlay').style.display = 'block';
    setTimeout(function() { document.getElementById('cmdInput').focus(); }, 50);
}
function closeCmdPalette() {
    document.getElementById('cmdPalette').classList.remove('show');
    document.getElementById('cmdOverlay').style.display = 'none';
}

// ════════════════════════════════════════════
//  MONACO EDITOR
// ════════════════════════════════════════════
var monacoEditor = null;
var isEditMode = false;
var wordWrapEnabled = true;
var minimapEnabled = true;

var isTextFile = <?= ($isFile && in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), TEXT_EXTENSIONS)) ? 'true' : 'false' ?>;
var fileExtension = '<?= $isFile ? strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) : "" ?>';

function getLang(ext) {
    var map = {
        // Web
        'js': 'javascript', 'jsx': 'javascript', 'ts': 'typescript', 'tsx': 'typescript',
        'php': 'php', 'html': 'html', 'htm': 'html', 'css': 'css',
        'json': 'json', 'xml': 'xml', 'svg': 'xml',
        'vue': 'html',
        // Скрипты / геймдев
        'lua': 'lua',
        'py': 'python',
        'rb': 'ruby',
        'sh': 'shell', 'bash': 'shell',
        'bat': 'bat', 'cmd': 'bat',
        'ps1': 'powershell',
        // Системные языки
        'c': 'c', 'h': 'c',
        'cpp': 'cpp', 'hpp': 'cpp',
        'cs': 'csharp',
        'java': 'java',
        'kt': 'kotlin',
        'go': 'go',
        'rs': 'rust',
        'swift': 'swift',
        'r': 'r',
        'pl': 'perl',
        // Данные / конфиги
        'sql': 'sql',
        'yaml': 'yaml', 'yml': 'yaml',
        'toml': 'ini',
        'ini': 'ini', 'cfg': 'ini', 'conf': 'ini', 'env': 'ini',
        'md': 'markdown',
        'txt': 'plaintext', 'log': 'plaintext'
    };
    return map[ext] || 'plaintext';
}

function initMonaco(editMode) {
    editMode = editMode || false;
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        var content = (document.getElementById('file-content') || {}).textContent || '';
        monacoEditor = monaco.editor.create(document.getElementById('editor-container'), {
            value: content,
            language: getLang(fileExtension),
            theme: 'vs-dark',
            automaticLayout: true,
            readOnly: !editMode,
            minimap: { enabled: window.innerWidth > 900 },
            fontSize: 14,
            fontFamily: "'Cascadia Code', 'Fira Code', 'JetBrains Mono', Consolas, monospace",
            fontLigatures: true,
            lineHeight: 22,
            scrollBeyondLastLine: false,
            renderWhitespace: 'selection',
            wordWrap: 'on',
            bracketPairColorization: { enabled: true },
            guides: { bracketPairs: true, indentation: true },
            smoothScrolling: true,
            cursorBlinking: 'smooth',
            cursorSmoothCaretAnimation: 'on',
            padding: { top: 12 },
            suggest: { showKeywords: true },
            quickSuggestions: true
        });
        monacoEditor.onDidChangeCursorPosition(function(e) {
            var el = document.getElementById('statusCursor');
            if (el) el.textContent = 'Ln ' + e.position.lineNumber + ', Col ' + e.position.column;
        });
        if (editMode) showEditorButtons();
    });
}

function showEditorButtons() {
    ['saveBtn','toggleWordWrapBtn','toggleMinimapBtn','formatBtn'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.style.display = 'inline-flex';
    });
    var editBtn = document.getElementById('editBtn');
    if (editBtn) editBtn.style.display = 'none';
}

function triggerEdit() {
    if (!isTextFile) return;
    isEditMode = true;
    if (!monacoEditor) {
        initMonaco(true);
    } else {
        monacoEditor.updateOptions({ readOnly: false });
        showEditorButtons();
    }
}

function triggerSave() {
    if (!monacoEditor) return;
    document.getElementById('fileContentInput').value = monacoEditor.getValue();
    document.getElementById('saveForm').submit();
}

function triggerFormat() {
    if (monacoEditor) monacoEditor.getAction('editor.action.formatDocument').run();
}

var editBtnEl = document.getElementById('editBtn');
var saveBtnEl = document.getElementById('saveBtn');
var formatBtnEl = document.getElementById('formatBtn');
var wrapBtnEl = document.getElementById('toggleWordWrapBtn');
var minimapBtnEl = document.getElementById('toggleMinimapBtn');

if (editBtnEl)   editBtnEl.addEventListener('click', triggerEdit);
if (saveBtnEl)   saveBtnEl.addEventListener('click', triggerSave);
if (formatBtnEl) formatBtnEl.addEventListener('click', triggerFormat);
if (wrapBtnEl) {
    wrapBtnEl.addEventListener('click', function() {
        wordWrapEnabled = !wordWrapEnabled;
        if (monacoEditor) monacoEditor.updateOptions({ wordWrap: wordWrapEnabled ? 'on' : 'off' });
        this.style.opacity = wordWrapEnabled ? '1' : '0.5';
    });
}
if (minimapBtnEl) {
    minimapBtnEl.addEventListener('click', function() {
        minimapEnabled = !minimapEnabled;
        if (monacoEditor) monacoEditor.updateOptions({ minimap: { enabled: minimapEnabled } });
        this.style.opacity = minimapEnabled ? '1' : '0.5';
    });
}

// ════════════════════════════════════════════
//  KEYBOARD SHORTCUTS
// ════════════════════════════════════════════
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'p') { e.preventDefault(); openCmdPalette(); }
    if (e.ctrlKey && e.key === 's') { e.preventDefault(); isEditMode ? triggerSave() : triggerEdit(); }
    if (e.ctrlKey && e.key === 'e') { e.preventDefault(); triggerEdit(); }
    if (e.key === 'Escape') { closeCmdPalette(); }
    if (e.shiftKey && e.altKey && e.key === 'F') { e.preventDefault(); triggerFormat(); }
});

// ════════════════════════════════════════════
//  INIT
// ════════════════════════════════════════════
window.addEventListener('DOMContentLoaded', function() {
    if (isTextFile) initMonaco(false);
});
</script>
</body>
</html>