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
            transition: width 0.2s;
        }
        @media (max-width: 767px) {
            .sidebar {
                position: fixed;
                left: -100%; top: 30px; bottom: 0;
                width: 80%; min-width: 0;
                z-index: 1000;
                transition: left 0.25s ease;
            }
            .sidebar.active { left: 0; box-shadow: 4px 0 20px rgba(0,0,0,0.5); }
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

        /* ── Loader ── */
        .loader-overlay {
            position: fixed; inset: 0;
            background: #1e1e1e;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            z-index: 9999;
            transition: opacity 0.4s ease;
        }
        .loader-overlay.fade-out { opacity: 0; pointer-events: none; }
        .loader-logo {
            font-size: 13px;
            color: var(--vsc-fg-dim);
            font-family: 'Consolas', monospace;
            margin-bottom: 24px;
            letter-spacing: 0.05em;
        }
        .loader-logo span { color: var(--vsc-accent); }
        .loader-bar {
            width: 200px; height: 2px;
            background: #333;
            border-radius: 2px;
            overflow: hidden;
        }
        .loader-bar-fill {
            height: 100%; width: 0;
            background: var(--vsc-accent);
            animation: loadBar 1s ease-out forwards;
        }
        @keyframes loadBar {
            0%   { width: 0; }
            60%  { width: 70%; }
            100% { width: 100%; }
        }

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

<!-- ═══════ LOADER ═══════ -->
<div class="loader-overlay" id="loaderOverlay">
    <div class="loader-logo">KOFFEE <span>DEVELOPER</span> — File Manager v<?= VERSION ?></div>
    <div class="loader-bar"><div class="loader-bar-fill"></div></div>
</div>

<!-- ═══════ ACTIVITY BAR ═══════ -->
<div class="activity-bar">
    <div class="activity-bar-logo" title="KOFFEE DEVELOPER">KD</div>
    <button class="activity-btn active" title="Проводник" onclick="document.getElementById('sidebar').classList.toggle('active'); document.getElementById('sidebarOverlay').classList.toggle('active')">
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

    <!-- Tab bar -->
    <div class="tab-bar">
        <?php
        $tabExt = $isFile ? strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) : '';
        $tabIconClass = match($tabExt) {
            'js'   => 'fab fa-js-square',
            'php'  => 'fab fa-php',
            'html' => 'fab fa-html5',
            'css'  => 'fab fa-css3-alt',
            'json' => 'fas fa-code',
            'md'   => 'fab fa-markdown',
            'jpg','jpeg','png','gif' => 'fas fa-image',
            'mp3','wav','ogg'        => 'fas fa-music',
            default => $isFile ? 'fas fa-file-alt' : 'fas fa-folder-open',
        };
        $tabIconColor = match($tabExt) {
            'js'   => '#f1e05a',
            'php'  => '#8892be',
            'html' => '#e34c26',
            'css'  => '#563d7c',
            'json' => '#40a0ff',
            'md'   => '#4078c0',
            'jpg','jpeg','png','gif' => '#50fa7b',
            'mp3','wav','ogg'        => '#ff79c6',
            default => $isFile ? '#858585' : '#dcb67a',
        };
        $tabLabel = $isFile ? htmlspecialchars(basename($absolutePath)) : ($requestedPath ? htmlspecialchars(basename($absolutePath)) : 'root');
        ?>
        <div class="tab active">
            <span class="tab-icon" style="color:<?= $tabIconColor ?>"><i class="<?= $tabIconClass ?>"></i></span>
            <span class="tab-name"><?= $tabLabel ?></span>
            <span class="tab-close"><i class="fas fa-times"></i></span>
        </div>
    </div>

    <div class="content-area">
        <!-- Mobile overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"
             onclick="this.classList.remove('active'); document.getElementById('sidebar').classList.remove('active')">
        </div>

        <!-- ═══════ SIDEBAR ═══════ -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-mobile-header">
                <button class="sidebar-icon-btn" onclick="document.getElementById('sidebar').classList.remove('active'); document.getElementById('sidebarOverlay').classList.remove('active')" id="closeMobileMenuBtn">
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
                    $fIconFa = match(true) {
                        $file['is_dir']                           => 'fas fa-folder',
                        in_array($fext,['jpg','jpeg','png','gif'])=> 'fas fa-image',
                        in_array($fext,['mp3','wav','ogg'])       => 'fas fa-music',
                        $fext === 'js'                            => 'fab fa-js-square',
                        $fext === 'php'                           => 'fab fa-php',
                        $fext === 'html'                          => 'fab fa-html5',
                        $fext === 'css'                           => 'fab fa-css3-alt',
                        $fext === 'json'                          => 'fas fa-code',
                        $fext === 'md'                            => 'fab fa-markdown',
                        default                                   => 'fas fa-file-alt',
                    };
                    $fIconCls = match(true) {
                        $file['is_dir']                           => 'folder',
                        in_array($fext,['jpg','jpeg','png','gif'])=> 'image',
                        in_array($fext,['mp3','wav','ogg'])       => 'audio',
                        $fext === 'js'                            => 'js',
                        $fext === 'php'                           => 'php',
                        $fext === 'html'                          => 'html',
                        $fext === 'css'                           => 'css',
                        $fext === 'json'                          => 'json',
                        $fext === 'md'                            => 'md',
                        default                                   => 'other',
                    };
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

                <?php if ($isFile && in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), ['php','html','css','js','json','txt','md'])): ?>
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
                    if (in_array($previewExt, ['php','html','css','js','json','txt','md'])) {
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
                <div class="status-item" onclick="document.getElementById('sidebar').classList.toggle('active'); document.getElementById('sidebarOverlay').classList.toggle('active')" title="Переключить проводник">
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
                        <?= match($previewExt) {
                            'php'  => 'PHP',
                            'js'   => 'JavaScript',
                            'html' => 'HTML',
                            'css'  => 'CSS',
                            'json' => 'JSON',
                            'md'   => 'Markdown',
                            'txt'  => 'Plain Text',
                            default => strtoupper($previewExt) ?: 'Binary',
                        } ?>
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
        <?php if ($isFile && in_array($previewExt, ['php','html','css','js','json','txt','md'])): ?>
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
                <div style="font-size:11px;color:var(--vsc-fg-dim);">Поддерживаемые расширения: php, html, css, js, json, txt, md</div>
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
            <div style="font-size:12px;color:var(--vsc-fg-dim);margin-bottom:8px;">Что нового:</div>
            <ul style="padding-left:16px;color:var(--vsc-fg);font-size:13px;line-height:2;">
                <li>Редизайн интерфейса в стиле VS Code</li>
                <li>Activity Bar с быстрыми действиями</li>
                <li>Command Palette (Ctrl+P)</li>
                <li>Цветные иконки файлов по типу</li>
                <li>Горячие клавиши редактора</li>
                <li>Статус-бар с позицией курсора</li>
                <li>Поддержка музыкальных файлов</li>
            </ul>
            <p style="font-size:11px;color:var(--vsc-fg-dim);margin-top:12px;">Дата сборки: <?= date('d.m.Y') ?></p>
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
// ── Modal helpers ──
function showModal(id) {
    document.getElementById(id).classList.add('show');
    document.getElementById(id).style.display = 'flex';
}
function hideModal(id) {
    document.getElementById(id).classList.remove('show');
    document.getElementById(id).style.display = 'none';
}

// Close modal on backdrop click
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) hideModal(this.id);
    });
});

// ── Notification ──
function showNotification(text, type = 'success') {
    const n = document.getElementById('notification');
    const nt = document.getElementById('notificationText');
    n.className = 'notification show ' + type;
    nt.textContent = text;
    n.style.display = 'flex';
    setTimeout(() => { n.style.display = 'none'; }, 3000);
}

// ── Rename modal ──
function openRenameModal(name, pathEncoded) {
    document.getElementById('renameInput').value = name;
    document.getElementById('renameForm').action = '?path=' + pathEncoded + '&action=rename_post';
    showModal('renameModal');
    setTimeout(() => document.getElementById('renameInput').select(), 50);
}

// ── Command Palette ──
function openCmdPalette() {
    document.getElementById('cmdPalette').classList.add('show');
    document.getElementById('cmdOverlay').style.display = 'block';
    setTimeout(() => document.getElementById('cmdInput').focus(), 50);
}
function closeCmdPalette() {
    document.getElementById('cmdPalette').classList.remove('show');
    document.getElementById('cmdOverlay').style.display = 'none';
}

// ── Monaco Editor ──
let monacoEditor = null;
let isEditMode = false;
let wordWrapEnabled = true;
let minimapEnabled = true;

const isTextFile = <?= ($isFile && in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), ['php','html','css','js','json','txt','md'])) ? 'true' : 'false' ?>;
const fileExtension = '<?= $isFile ? strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) : "" ?>';

function getLang(ext) {
    const map = { js:'javascript', php:'php', html:'html', css:'css', json:'json', md:'markdown', txt:'plaintext' };
    return map[ext] || 'plaintext';
}

function initMonaco(editMode = false) {
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        const content = document.getElementById('file-content')?.textContent || '';
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
            quickSuggestions: true,
        });

        // Track cursor position
        monacoEditor.onDidChangeCursorPosition(e => {
            const el = document.getElementById('statusCursor');
            if (el) el.textContent = `Ln ${e.position.lineNumber}, Col ${e.position.column}`;
        });

        if (editMode) {
            showEditorButtons();
        }
    });
}

function showEditorButtons() {
    ['saveBtn','toggleWordWrapBtn','toggleMinimapBtn','formatBtn'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'inline-flex';
    });
    const editBtn = document.getElementById('editBtn');
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

// Кнопки тулбара
document.getElementById('editBtn')?.addEventListener('click', triggerEdit);
document.getElementById('saveBtn')?.addEventListener('click', triggerSave);
document.getElementById('formatBtn')?.addEventListener('click', triggerFormat);

document.getElementById('toggleWordWrapBtn')?.addEventListener('click', function() {
    wordWrapEnabled = !wordWrapEnabled;
    monacoEditor?.updateOptions({ wordWrap: wordWrapEnabled ? 'on' : 'off' });
    this.style.opacity = wordWrapEnabled ? '1' : '0.5';
});
document.getElementById('toggleMinimapBtn')?.addEventListener('click', function() {
    minimapEnabled = !minimapEnabled;
    monacoEditor?.updateOptions({ minimap: { enabled: minimapEnabled } });
    this.style.opacity = minimapEnabled ? '1' : '0.5';
});

// ── Keyboard shortcuts ──
document.addEventListener('keydown', function(e) {
    // Ctrl+P — Command Palette
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        openCmdPalette();
    }
    // Ctrl+S — Save
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        if (isEditMode) triggerSave();
        else triggerEdit();
    }
    // Ctrl+E — Edit mode
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        triggerEdit();
    }
    // Escape — close palette / modal
    if (e.key === 'Escape') {
        closeCmdPalette();
    }
    // Shift+Alt+F — Format
    if (e.shiftKey && e.altKey && e.key === 'F') {
        e.preventDefault();
        triggerFormat();
    }
});

// ── Init ──
window.addEventListener('DOMContentLoaded', function() {
    if (isTextFile) {
        initMonaco(false); // read-only view by default
    }
});

// ── Loader ──
window.addEventListener('load', function() {
    setTimeout(function() {
        const overlay = document.getElementById('loaderOverlay');
        overlay.classList.add('fade-out');
        setTimeout(() => overlay.remove(), 400);
    }, 700);
});
</script>
</body>
</html>