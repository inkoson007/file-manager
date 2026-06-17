<?php
session_start();

// ╔══════════════════════════════════════════════════════════════╗
// ║               KOFFEE DEVELOPER — НАСТРОЙКИ                  ║
// ╠══════════════════════════════════════════════════════════════╣
// ║  Авторизация                                                 ║
define('AUTH_ENABLED',  false);           // ← false = отключить авторизацию
define('AUTH_LOGIN',    'Логин');           // ← логин
define('AUTH_PASSWORD', 'Пароль');      // ← пароль
// ╠══════════════════════════════════════════════════════════════╣
// ║  Корневая директория (по умолчанию — папка файла)            ║
define('BASE_DIR', __DIR__);
define('TRASH_DIR',  BASE_DIR . '/.kd_trash');
define('LOG_FILE',   BASE_DIR . '/.kd_log.json');
if (!is_dir(TRASH_DIR)) @mkdir(TRASH_DIR, 0755, true);
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
define('VERSION', '2.3');
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
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
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

// AJAX: список дочерних папок для блокнота
if (isset($_GET['ajax']) && $_GET['ajax'] === 'subdirs') {
    header('Content-Type: application/json');
    $relDir = isset($_GET['dir']) ? trim($_GET['dir'], '/') : '';
    $absDir = realpath(BASE_DIR . ($relDir ? '/' . $relDir : ''));
    $result = [];
    if ($absDir && strpos($absDir, realpath(BASE_DIR)) === 0 && is_dir($absDir)) {
        $items = @scandir($absDir);
        if ($items) {
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                if (substr($item, 0, 1) === '.') continue; // skip hidden
                $full = $absDir . '/' . $item;
                if (is_dir($full)) {
                    $childRel = ($relDir ? $relDir . '/' : '') . $item;
                    $hasSubs = false;
                    $sub = @scandir($full);
                    if ($sub) foreach ($sub as $s) {
                        if ($s !== '.' && $s !== '..' && substr($s,0,1) !== '.' && is_dir($full . '/' . $s)) { $hasSubs = true; break; }
                    }
                    $result[] = ['name' => $item, 'path' => $childRel, 'hasSubs' => $hasSubs];
                }
            }
        }
    }
    echo json_encode($result);
    exit;
}

// AJAX: поиск файлов
if (isset($_GET['ajax']) && $_GET['ajax'] === 'search') {
    header('Content-Type: application/json');
    $q = strtolower(trim($_GET['q'] ?? ''));
    $results = [];
    if (strlen($q) >= 1) {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(BASE_DIR, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($it as $file) {
            $rel = str_replace(BASE_DIR . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $rel = str_replace('\\', '/', $rel);
            if (strpos($rel, '.kd_') === 0) continue;
            if (stripos($file->getFilename(), $q) !== false) {
                $results[] = [
                    'name' => $file->getFilename(),
                    'path' => $rel,
                    'isDir' => false,
                    'size'  => $file->getSize(),
                ];
                if (count($results) >= 50) break;
            }
        }
        // also search dirs
        $dit = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(BASE_DIR, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($dit as $file) {
            if (!$file->isDir()) continue;
            $rel = str_replace(BASE_DIR . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $rel = str_replace('\\', '/', $rel);
            if (strpos($rel, '.kd_') === 0) continue;
            if (stripos($file->getFilename(), $q) !== false) {
                $results[] = ['name' => $file->getFilename(), 'path' => $rel, 'isDir' => true, 'size' => 0];
                if (count($results) >= 60) break;
            }
        }
    }
    echo json_encode($results);
    exit;
}

// AJAX: очистить буфер обмена
if (isset($_GET['ajax']) && $_GET['ajax'] === 'clear_clipboard') {
    unset($_SESSION['clipboard']);
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

// AJAX: получить лог
if (isset($_GET['ajax']) && $_GET['ajax'] === 'log') {
    header('Content-Type: application/json');
    $log = [];
    if (file_exists(LOG_FILE)) {
        $log = json_decode(file_get_contents(LOG_FILE), true) ?: [];
    }
    echo json_encode(array_reverse($log));
    exit;
}

// AJAX: очистить лог
if (isset($_GET['ajax']) && $_GET['ajax'] === 'log_clear') {
    header('Content-Type: application/json');
    file_put_contents(LOG_FILE, json_encode([]));
    echo json_encode(['ok' => true]);
    exit;
}

// AJAX: просмотр файла из корзины
if (isset($_GET['ajax']) && $_GET['ajax'] === 'trash_view') {
    header('Content-Type: application/json');
    $trashName = $_GET['tn'] ?? '';
    $trashPath = TRASH_DIR . '/' . basename($trashName);
    if (!$trashName || !file_exists($trashPath)) {
        echo json_encode(['error' => 'Файл не найден']);
        exit;
    }
    $ext  = strtolower(pathinfo($trashPath, PATHINFO_EXTENSION));
    $size = filesize($trashPath);
    $imgExts  = ['jpg','jpeg','png','gif','svg','webp','bmp'];
    $textExts = array_merge(TEXT_EXTENSIONS, ['log','txt','env','htaccess']);
    if (in_array($ext, $imgExts)) {
        $data = base64_encode(file_get_contents($trashPath));
        $mime = $ext === 'svg' ? 'image/svg+xml' : (in_array($ext,['jpg','jpeg']) ? 'image/jpeg' : 'image/'.$ext);
        echo json_encode(['type' => 'image', 'src' => 'data:'.$mime.';base64,'.$data]);
    } elseif (in_array($ext, $textExts) && $size < 512000) {
        echo json_encode(['type' => 'text', 'content' => file_get_contents($trashPath), 'ext' => $ext]);
    } else {
        echo json_encode(['type' => 'binary', 'size' => $size, 'ext' => $ext]);
    }
    exit;
}

// AJAX: список корзины
if (isset($_GET['ajax']) && $_GET['ajax'] === 'trash_list') {
    header('Content-Type: application/json');
    $items = [];
    $meta = [];
    $metaFile = TRASH_DIR . '/.meta.json';
    if (file_exists($metaFile)) $meta = json_decode(file_get_contents($metaFile), true) ?: [];
    $scan = @scandir(TRASH_DIR);
    if ($scan) foreach ($scan as $f) {
        if ($f === '.' || $f === '..' || $f === '.meta.json') continue;
        $full = TRASH_DIR . '/' . $f;
        $items[] = [
            'trashName' => $f,
            'origName'  => $meta[$f]['origName'] ?? $f,
            'origPath'  => $meta[$f]['origPath'] ?? '',
            'deleted'   => $meta[$f]['deleted']  ?? '',
            'isDir'     => is_dir($full),
            'size'      => is_file($full) ? filesize($full) : 0,
        ];
    }
    usort($items, function($a,$b){ return strcmp($b['deleted'], $a['deleted']); });
    echo json_encode($items);
    exit;
}

// ── Вспомогательные функции ──
function kdLog($action, $target, $extra = '') {
    $log = [];
    if (file_exists(LOG_FILE)) $log = json_decode(file_get_contents(LOG_FILE), true) ?: [];
    $log[] = [
        'time'   => date('Y-m-d H:i:s'),
        'action' => $action,
        'target' => $target,
        'extra'  => $extra,
        'user'   => AUTH_LOGIN,
    ];
    if (count($log) > 500) $log = array_slice($log, -500);
    file_put_contents(LOG_FILE, json_encode($log));
}

function kdFormatSize($bytes) {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes/1024, 1) . ' KB';
    return round($bytes/1048576, 1) . ' MB';
}

function kdCopyRecursive($src, $dst) {
    if (is_file($src)) return copy($src, $dst);
    if (!is_dir($dst)) mkdir($dst, 0755, true);
    $items = scandir($src);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        kdCopyRecursive($src.'/'.$item, $dst.'/'.$item);
    }
    return true;
}

function kdDeleteRecursive($path) {
    if (is_file($path)) return unlink($path);
    $items = scandir($path);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        kdDeleteRecursive($path . '/' . $item);
    }
    return rmdir($path);
}

function kdTrashMove($absPath, $relPath) {
    $origName = basename($absPath);
    $trashName = date('YmdHis') . '_' . $origName;
    $trashPath = TRASH_DIR . '/' . $trashName;
    if (!rename($absPath, $trashPath)) return false;
    // Save meta
    $metaFile = TRASH_DIR . '/.meta.json';
    $meta = file_exists($metaFile) ? (json_decode(file_get_contents($metaFile), true) ?: []) : [];
    $meta[$trashName] = ['origName' => $origName, 'origPath' => $relPath, 'deleted' => date('Y-m-d H:i:s')];
    file_put_contents($metaFile, json_encode($meta));
    return true;
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
    $parentPath = is_dir($absolutePath) ? $requestedPath : dirname($requestedPath);

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
                if (in_array($extension, ['jpg','jpeg','png','gif','html','php','mp3','wav','ogg'])) {
                    $relativePath = str_replace(BASE_DIR, '', $absolutePath);
                    header("Location: $relativePath");
                    exit;
                }
            }
            break;

        case 'delete':
            $name = basename($absolutePath);
            if (file_exists($absolutePath)) {
                if (kdTrashMove($absolutePath, $requestedPath)) {
                    kdLog('trash', $requestedPath);
                    $_SESSION['message'] = '«' . $name . '» перемещён в корзину';
                } else {
                    $_SESSION['error'] = 'Ошибка при удалении';
                }
            }
            header("Location: ?path=" . urlencode(dirname($requestedPath)));
            exit;

        case 'zip':
            if (is_dir($absolutePath) && class_exists('ZipArchive')) {
                $zipName = basename($absolutePath) . '_' . date('YmdHis') . '.zip';
                $zipPath = dirname($absolutePath) . '/' . $zipName;
                $zip = new ZipArchive();
                if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
                    $it = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($absolutePath, RecursiveDirectoryIterator::SKIP_DOTS)
                    );
                    foreach ($it as $file) {
                        $rel = substr($file->getPathname(), strlen($absolutePath) + 1);
                        $zip->addFile($file->getPathname(), $rel);
                    }
                    $zip->close();
                    kdLog('zip', $requestedPath, $zipName);
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/zip');
                    header('Content-Disposition: attachment; filename="' . $zipName . '"');
                    header('Content-Length: ' . filesize($zipPath));
                    readfile($zipPath);
                    unlink($zipPath);
                    exit;
                }
            } else {
                $_SESSION['error'] = 'ZipArchive недоступен на сервере';
            }
            header("Location: ?path=" . urlencode($requestedPath));
            exit;

        case 'copy':
            $_SESSION['clipboard'] = ['action' => 'copy', 'path' => $requestedPath, 'abs' => $absolutePath];
            $_SESSION['message'] = '«' . basename($absolutePath) . '» скопирован в буфер';
            header("Location: ?path=" . urlencode($parentPath));
            exit;

        case 'cut':
            $_SESSION['clipboard'] = ['action' => 'cut', 'path' => $requestedPath, 'abs' => $absolutePath];
            $_SESSION['message'] = '«' . basename($absolutePath) . '» вырезан';
            header("Location: ?path=" . urlencode($parentPath));
            exit;

        case 'paste':
            $cb = $_SESSION['clipboard'] ?? null;
            if ($cb && file_exists($cb['abs'])) {
                $destDir = is_dir($absolutePath) ? $absolutePath : dirname($absolutePath);
                $destPath = $destDir . '/' . basename($cb['abs']);
                if (file_exists($destPath)) $destPath = $destDir . '/copy_' . basename($cb['abs']);
                if ($cb['action'] === 'copy') {
                    kdCopyRecursive($cb['abs'], $destPath);
                    kdLog('copy', $cb['path'], $requestedPath);
                    $_SESSION['message'] = '«' . basename($cb['abs']) . '» скопирован';
                } else {
                    rename($cb['abs'], $destPath);
                    kdLog('move', $cb['path'], $requestedPath);
                    $_SESSION['message'] = '«' . basename($cb['abs']) . '» перемещён';
                    unset($_SESSION['clipboard']);
                }
            } else {
                $_SESSION['error'] = 'Буфер пуст или файл не найден';
            }
            header("Location: ?path=" . urlencode($requestedPath));
            exit;

        case 'trash_restore':
            $trashName = $_GET['tn'] ?? '';
            $metaFile  = TRASH_DIR . '/.meta.json';
            $meta = file_exists($metaFile) ? (json_decode(file_get_contents($metaFile), true) ?: []) : [];
            $info = $meta[$trashName] ?? null;
            if ($info && file_exists(TRASH_DIR . '/' . $trashName)) {
                $restoreTo = BASE_DIR . '/' . $info['origPath'];
                if (!file_exists($restoreTo)) {
                    rename(TRASH_DIR . '/' . $trashName, $restoreTo);
                    kdLog('restore', $info['origPath']);
                    unset($meta[$trashName]);
                    file_put_contents($metaFile, json_encode($meta));
                    $_SESSION['message'] = '«' . $info['origName'] . '» восстановлен';
                } else {
                    $_SESSION['error'] = 'Файл с таким именем уже существует';
                }
            }
            header("Location: ?path=");
            exit;

        case 'trash_delete':
            $trashName = $_GET['tn'] ?? '';
            $trashPath = TRASH_DIR . '/' . $trashName;
            if ($trashName && file_exists($trashPath)) {
                kdDeleteRecursive($trashPath);
                $metaFile = TRASH_DIR . '/.meta.json';
                $meta = file_exists($metaFile) ? (json_decode(file_get_contents($metaFile), true) ?: []) : [];
                unset($meta[$trashName]);
                file_put_contents($metaFile, json_encode($meta));
                kdLog('perm_delete', $trashName);
                $_SESSION['message'] = 'Удалено навсегда';
            }
            header("Location: ?path=");
            exit;

        case 'trash_empty':
            $items = @scandir(TRASH_DIR) ?: [];
            foreach ($items as $i) {
                if ($i === '.' || $i === '..') continue;
                kdDeleteRecursive(TRASH_DIR . '/' . $i);
            }
            @mkdir(TRASH_DIR, 0755, true);
            kdLog('trash_empty', '');
            $_SESSION['message'] = 'Корзина очищена';
            header("Location: ?path=");
            exit;

        case 'rename':
            break;

        default:
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
    } elseif (isset($_POST['notepad_save'])) {
        // Notepad: save new file
        $npFilename = basename(trim($_POST['notepad_filename'] ?? ''));
        $npContent  = $_POST['notepad_content'] ?? '';
        if (!empty($npFilename)) {
            $npExt = strtolower(pathinfo($npFilename, PATHINFO_EXTENSION));
            if (in_array($npExt, TEXT_EXTENSIONS)) {
                $npPath = $absolutePath . '/' . $npFilename;
                if (!file_exists($npPath)) {
                    file_put_contents($npPath, $npContent);
                    $_SESSION['message'] = 'Файл «' . htmlspecialchars($npFilename) . '» создан';
                } else {
                    // Overwrite existing
                    file_put_contents($npPath, $npContent);
                    $_SESSION['message'] = 'Файл «' . htmlspecialchars($npFilename) . '» сохранён';
                }
            }
        }
        kdLog('create', $requestedPath . '/' . ($npFilename ?? ''));
        header("Location: ?path=" . urlencode($requestedPath));
        exit;
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
    if (!in_array($extension, TEXT_EXTENSIONS)) return false;
    $result = file_put_contents($path, $content) !== false;
    if ($result) kdLog('save', str_replace(BASE_DIR . '/', '', $path));
    return $result;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
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
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .mobile-menu-btn {
            display: none;
            background: none; border: none;
            color: var(--vsc-fg); font-size: 16px; cursor: pointer;
        }
        @media (max-width: 767px) {
            .mobile-menu-btn { display: flex !important; }
            .activity-bar { display: none !important; }
            /* mac-window takes full screen, no left margin */
            .mac-window {
                margin-left: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                position: fixed !important;
                top: 0; left: 0;
            }
            /* Title bar compact */
            .mac-title { font-size: 10px; }
            .mac-title-bar { padding: 0 8px; gap: 6px; height: 28px; }
            .mac-buttons { gap: 5px; }
            .mac-btn { width: 10px; height: 10px; }
            .mac-title-bar.clock-mode { height: 40px; }
            /* Drum clock smaller on mobile */
            #drumClock { gap: 2px; }
            .drum-digit { width: 11px; height: 20px; }
            .drum-digit-inner span { font-size: 11px; height: 20px; line-height: 20px; }
            .drum-sep { font-size: 11px; line-height: 20px; }
            .drum-block-label { font-size: 7px; }
            .drum-gap { width: 5px; }
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
            /* Sidebar flies in from left, out of normal flow */
            .sidebar {
                position: fixed !important;
                left: -100% !important;
                top: 0 !important; bottom: 0 !important;
                width: 82% !important; min-width: 0 !important;
                max-width: 300px;
                z-index: 1001;
                transition: left 0.25s ease;
                box-shadow: none;
            }
            .sidebar.active  { left: 0 !important; box-shadow: 6px 0 28px rgba(0,0,0,0.6); }
            .sidebar.hidden  { left: -100% !important; }
            /* content-area: sidebar is fixed, so editor gets full width */
            .content-area { flex-direction: row; }
            .content-area > .sidebar { flex: none; }
        }
        .sidebar-overlay {
            display: none;
            position: fixed; top:0; left:0; right:0; bottom:0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
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
            /* Sidebar mobile header */
            .sidebar-mobile-header {
                display: flex; align-items: center; gap: 10px;
                padding: 8px 12px;
                border-bottom: 1px solid var(--vsc-border);
                background: var(--vsc-sidebar);
                flex-shrink: 0;
            }
            .sidebar-mobile-header .sidebar-title { font-size: 13px; font-weight: 600; }

            /* Hide desktop-only elements */
            .file-path-display { display: none; }
            .tab-bar { display: none; }
            .editor-breadcrumb-bar { display: none; }

            /* Editor area takes ALL available width (sidebar is fixed/out-of-flow) */
            .editor-area {
                width: 100% !important;
                min-width: 0 !important;
                flex: 1 !important;
            }

            /* main-content fills remaining height below title bar */
            .main-content {
                flex: 1;
                overflow: hidden;
            }

            /* Toolbar scrollable */
            .file-toolbar { overflow-x: auto; flex-wrap: nowrap; padding: 0 8px; }
            .file-toolbar::-webkit-scrollbar { height: 0; }

            /* Modals full screen */
            .modal {
                align-items: flex-start;
                justify-content: flex-start;
                padding: 0;
            }
            .modal-content {
                width: 100vw !important;
                max-width: 100vw !important;
                height: 100vh !important;
                max-height: 100vh !important;
                border-radius: 0 !important;
                margin: 0 !important;
            }
            .modal-body { max-height: calc(100vh - 100px) !important; }

            /* Status bar */
            .status-bar { font-size: 10px; padding: 0 8px; }

            /* Context menu */
            #ctxMenu { min-width: 200px; font-size: 14px; }
            .ctx-item { padding: 12px 16px; }

            /* Clipboard banner */
            .clipboard-banner { width: 94%; left: 3%; transform: none; bottom: 40px; }

            /* File items bigger touch targets */
            .file-item { min-height: 40px; }
            .file-meta { display: none; }

            /* Settings */
            .settings-row { flex-wrap: wrap; }

            /* Trash items */
            .trash-item { flex-wrap: wrap; }
            .trash-item-btns { width: 100%; justify-content: flex-end; margin-top: 4px; }
        }

        /* vscode version modal */
        #versionModal .modal-content { background: #252526; }
        #versionModal ul { padding-left: 16px; color: var(--vsc-fg); font-size: 13px; line-height: 2; }
        #versionModal p { color: var(--vsc-fg-dim); font-size: 12px; margin-top: 8px; }

        /* ── BSOD ── */
        #bsodScreen {
            display: none;
            position: fixed; inset: 0;
            background: #0078D7;
            color: #fff;
            font-family: 'Segoe UI', system-ui, sans-serif;
            z-index: 99999;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            padding: 10vh 12vw;
            animation: bsodFadeIn 0.3s ease;
            overflow: hidden;
        }
        #bsodScreen.show { display: flex; }
        @keyframes bsodFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .bsod-emoji {
            font-size: clamp(60px, 10vw, 120px);
            margin-bottom: 28px;
            line-height: 1;
        }
        .bsod-title {
            font-size: clamp(18px, 3vw, 36px);
            font-weight: 400;
            margin-bottom: 32px;
            line-height: 1.4;
            max-width: 700px;
        }
        .bsod-desc {
            font-size: clamp(12px, 1.5vw, 16px);
            line-height: 1.8;
            max-width: 640px;
            margin-bottom: 36px;
            opacity: 0.9;
        }
        .bsod-progress-wrap {
            margin-bottom: 32px;
        }
        .bsod-progress-label {
            font-size: clamp(12px, 1.5vw, 16px);
            margin-bottom: 10px;
        }
        .bsod-progress-bar {
            width: 280px;
            height: 6px;
            background: rgba(255,255,255,0.25);
            border-radius: 3px;
            overflow: hidden;
        }
        .bsod-progress-fill {
            height: 100%;
            width: 0%;
            background: #fff;
            border-radius: 3px;
            transition: width 0.08s linear;
        }
        .bsod-qr-row {
            display: flex;
            align-items: flex-start;
            gap: 24px;
            margin-top: 10px;
        }
        .bsod-qr {
            width: 80px; height: 80px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            padding: 6px;
            border-radius: 2px;
            flex-shrink: 0;
        }
        .bsod-qr svg { width: 68px; height: 68px; }
        .bsod-qr-text {
            font-size: clamp(11px, 1.3vw, 14px);
            line-height: 1.8;
            max-width: 440px;
            opacity: 0.9;
        }
        .bsod-stop-code {
            margin-top: 40px;
            font-size: clamp(11px, 1.3vw, 14px);
            opacity: 0.7;
        }
        /* small close hint */
        .bsod-close-hint {
            position: absolute;
            bottom: 20px;
            right: 28px;
            font-size: 11px;
            opacity: 0.4;
            cursor: pointer;
            user-select: none;
        }
        .bsod-close-hint:hover { opacity: 0.7; }

        /* ── Notepad Modal ── */
        #notepadModal .modal-content {
            width: 760px;
            max-width: 98vw;
            height: 80vh;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
        }
        #notepadModal .modal-body {
            flex: 1;
            padding: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .notepad-toolbar {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: #2d2d2d;
            border-bottom: 1px solid var(--vsc-border);
            flex-shrink: 0;
            flex-wrap: wrap;
        }
        .notepad-lang-select {
            background: #3c3c3c;
            border: 1px solid var(--vsc-border-light);
            border-radius: 4px;
            color: #fff;
            font-size: 12px;
            padding: 3px 8px;
            height: 26px;
            outline: none;
            cursor: pointer;
        }
        .notepad-lang-select:focus { border-color: var(--vsc-accent); }
        #notepadContainer {
            flex: 1;
            width: 100%;
            overflow: hidden;
        }
        .notepad-save-section {
            padding: 10px 14px;
            background: #2d2d2d;
            border-top: 1px solid var(--vsc-border);
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            flex-wrap: wrap;
        }
        .notepad-filename-input {
            background: #3c3c3c;
            border: 1px solid var(--vsc-border-light);
            border-radius: 4px;
            color: #fff;
            font-size: 13px;
            padding: 5px 10px;
            height: 30px;
            outline: none;
            flex: 1;
            min-width: 140px;
        }
        .notepad-filename-input:focus { border-color: var(--vsc-accent); }
        .notepad-folder-select {
            background: #3c3c3c;
            border: 1px solid var(--vsc-border-light);
            border-radius: 4px;
            color: #fff;
            font-size: 12px;
            padding: 3px 8px;
            height: 30px;
            outline: none;
            cursor: pointer;
            max-width: 200px;
        }
        .notepad-folder-select:focus { border-color: var(--vsc-accent); }
        .notepad-save-label {
            font-size: 12px;
            color: var(--vsc-fg-dim);
            white-space: nowrap;
        }

        /* ── Folder Tree Picker ── */
        .ftree-item {
            display: flex;
            align-items: center;
            gap: 0;
            cursor: pointer;
            user-select: none;
            border-radius: 3px;
            transition: background 0.1s;
        }
        .ftree-item:hover { background: var(--vsc-hover); }
        .ftree-item.selected { background: var(--vsc-selected); }
        .ftree-item.selected .ftree-name { color: #fff; }
        .ftree-toggle {
            width: 20px; height: 24px;
            display: flex; align-items: center; justify-content: center;
            color: var(--vsc-fg-dim);
            font-size: 10px;
            flex-shrink: 0;
            transition: transform 0.15s;
        }
        .ftree-toggle.open { transform: rotate(90deg); }
        .ftree-toggle.leaf { opacity: 0; pointer-events: none; }
        .ftree-icon {
            font-size: 13px;
            color: var(--vsc-folder);
            margin-right: 6px;
            flex-shrink: 0;
        }
        .ftree-name {
            font-size: 13px;
            color: var(--vsc-fg);
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ftree-children {
            display: none;
        }
        .ftree-children.open { display: block; }
        .ftree-loading {
            padding: 4px 0 4px 20px;
            font-size: 12px;
            color: var(--vsc-fg-dim);
            font-style: italic;
        }


        /* ── Light theme ── */
        body.light-theme {
            --vsc-bg:           #ffffff;
            --vsc-sidebar:      #f3f3f3;
            --vsc-sidebar-item: #e8e8e8;
            --vsc-panel:        #f0f0f0;
            --vsc-titlebar:     #dddddd;
            --vsc-tabbar:       #ececec;
            --vsc-tab-active:   #ffffff;
            --vsc-tab-inactive: #e0e0e0;
            --vsc-tab-border:   #0078d7;
            --vsc-statusbar:    #0078d7;
            --vsc-statusbar-fg: #ffffff;
            --vsc-accent:       #0078d7;
            --vsc-accent-hover: #0058a3;
            --vsc-fg:           #1e1e1e;
            --vsc-fg-dim:       #6e6e6e;
            --vsc-fg-bright:    #000000;
            --vsc-border:       #d4d4d4;
            --vsc-border-light: #c8c8c8;
            --vsc-hover:        rgba(0,0,0,0.06);
            --vsc-selected:     rgba(0,120,212,0.15);
            --vsc-folder:       #c09040;
            background-color: #ffffff;
        }
        body.light-theme .mac-window         { background: #ffffff; }
        body.light-theme .activity-bar       { background: #2c2c2c; }
        body.light-theme .sidebar            { background: #f3f3f3; border-right: 1px solid #d4d4d4; }
        body.light-theme .sidebar-section-header { background: #ececec; color: #444; }
        body.light-theme .file-item          { color: #1e1e1e; }
        body.light-theme .file-item:hover    { background: rgba(0,0,0,0.06); }
        body.light-theme .file-item.active   { background: rgba(0,120,212,0.15); }
        body.light-theme .tab-bar            { background: #ececec; border-color: #d4d4d4; }
        body.light-theme .tab                { background: #e0e0e0; color: #555; border-color: transparent; }
        body.light-theme .tab.active         { background: #ffffff; color: #1e1e1e; border-top-color: #0078d7; }
        body.light-theme .mac-title-bar      { background: #dddddd; border-color: #c8c8c8; }
        body.light-theme .mac-title          { color: #555; }
        body.light-theme .file-toolbar       { background: #f8f8f8; border-color: #d4d4d4; }
        body.light-theme .toolbar-btn        { color: #333; }
        body.light-theme .toolbar-btn:hover  { background: #e0e0e0; color: #000; }
        body.light-theme .editor-breadcrumb-bar { background: #f0f0f0; border-color: #d4d4d4; }
        body.light-theme .editor-breadcrumb  { color: #555; }
        body.light-theme .status-bar         { background: #0078d7; }
        body.light-theme .modal-content      { background: #ffffff; border-color: #d4d4d4; }
        body.light-theme .modal-header       { background: #f3f3f3; border-color: #d4d4d4; color: #1e1e1e; }
        body.light-theme .modal-footer       { background: #f3f3f3; border-color: #d4d4d4; }
        body.light-theme .modal-close        { color: #555; }
        body.light-theme .modal-close:hover  { color: #000; background: rgba(0,0,0,0.08); }
        body.light-theme input,
        body.light-theme select,
        body.light-theme textarea            { background: #fff; color: #1e1e1e; border-color: #bbb; }
        body.light-theme .notepad-toolbar,
        body.light-theme .notepad-save-section { background: #f0f0f0; border-color: #d4d4d4; }
        body.light-theme .notepad-lang-select,
        body.light-theme .notepad-filename-input { background: #fff; color: #1e1e1e; }
        body.light-theme .notepad-tabs       { background: #ececec; border-color: #d4d4d4; }
        body.light-theme .notepad-tab        { color: #555; }
        body.light-theme .notepad-tab.active { color: #000; border-bottom-color: #0078d7; }
        body.light-theme .cmd-palette-overlay { background: rgba(0,0,0,0.3); }
        body.light-theme .cmd-palette        { background: #f3f3f3; border-color: #d4d4d4; }
        body.light-theme .cmd-input          { background: #fff; color: #1e1e1e; border-color: #bbb; }
        body.light-theme .cmd-item           { color: #1e1e1e; }
        body.light-theme .cmd-item:hover     { background: rgba(0,120,212,0.12); }
        body.light-theme .file-preview       { background: #ffffff; }
        body.light-theme .sidebar-breadcrumbs { background: #f0f0f0; border-color: #d4d4d4; }
        body.light-theme .sidebar-breadcrumb  { color: #555; }
        body.light-theme #ctxMenu            { background: #f3f3f3; border-color: #ccc; }
        body.light-theme .ctx-item           { color: #1e1e1e; }
        body.light-theme .ctx-item:hover     { background: #0078d7; color: #fff; }
        body.light-theme .ctx-sep            { border-color: #ddd; }
        body.light-theme .trash-item:hover,
        body.light-theme .log-item:hover     { background: rgba(0,0,0,0.05); }
        body.light-theme .cl-entry           { background: #f3f3f3; }
        body.light-theme .cl-entry-ver,
        body.light-theme .cl-entry-name      { color: #1e1e1e; }
        body.light-theme .search-input-wrap input { background: #fff; color: #1e1e1e; }
        body.light-theme .search-result-item:hover { background: rgba(0,0,0,0.06); }
        body.light-theme .empty-state        { color: #888; }

        /* ── Context menu ── */
        #ctxMenu {
            position: fixed;
            z-index: 9000;
            background: #252526;
            border: 1px solid #454545;
            border-radius: 5px;
            padding: 4px 0;
            min-width: 190px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
            font-size: 13px;
            display: none;
        }
        #ctxMenu.show { display: block; }
        .ctx-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 14px;
            color: var(--vsc-fg);
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        .ctx-item:hover { background: var(--vsc-accent); color: #fff; }
        .ctx-item i { width: 14px; text-align: center; opacity: 0.8; }
        .ctx-item.danger:hover { background: #d44; }
        .ctx-sep { border-top: 1px solid #454545; margin: 3px 0; }
        .ctx-item.disabled { opacity: 0.4; pointer-events: none; }

        /* ── Image hover preview ── */
        #imgPreviewPopup {
            position: fixed;
            z-index: 8000;
            pointer-events: none;
            display: none;
            border: 2px solid var(--vsc-accent);
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 6px 24px rgba(0,0,0,0.6);
            background: #1e1e1e;
            max-width: 260px;
            max-height: 200px;
        }
        #imgPreviewPopup img { display: block; max-width: 260px; max-height: 200px; object-fit: contain; }

        /* ── File size/date in sidebar ── */
        .file-meta {
            font-size: 10px;
            color: var(--vsc-fg-dim);
            opacity: 0;
            margin-left: auto;
            flex-shrink: 0;
            white-space: nowrap;
            padding-right: 4px;
            transition: opacity 0.15s;
        }
        .file-item:hover .file-meta { opacity: var(--meta-opacity-hover, 1); }

        /* ── Unsaved dot on tab ── */
        .tab-unsaved-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #e5c07b;
            display: none;
            flex-shrink: 0;
        }
        .tab.unsaved .tab-unsaved-dot { display: inline-block; }
        .tab.unsaved .tab-close { display: none; }

        /* ── Search panel ── */
        #searchPanel {
            display: none;
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            flex-direction: column;
            z-index: 10;
            background: var(--vsc-sidebar);
        }
        #searchPanel.show { display: flex; }
        .search-input-wrap {
            padding: 10px 10px 6px;
            border-bottom: 1px solid var(--vsc-border);
            display: flex; gap: 6px; align-items: center;
        }
        .search-input-wrap input {
            flex: 1;
            background: #3c3c3c;
            border: 1px solid var(--vsc-border-light);
            border-radius: 4px;
            color: #fff;
            font-size: 13px;
            padding: 5px 10px;
            outline: none;
        }
        .search-input-wrap input:focus { border-color: var(--vsc-accent); }
        .search-close-btn {
            background: none; border: none; color: var(--vsc-fg-dim);
            font-size: 16px; cursor: pointer; padding: 2px 6px;
        }
        .search-close-btn:hover { color: #fff; }
        #searchResults {
            flex: 1; overflow-y: auto; padding: 4px 0;
        }
        #searchResults::-webkit-scrollbar { width: 4px; }
        #searchResults::-webkit-scrollbar-thumb { background: #555; border-radius: 2px; }
        .search-result-item {
            display: flex; align-items: center; gap: 8px;
            padding: 5px 12px;
            cursor: pointer; font-size: 12px; color: var(--vsc-fg);
        }
        .search-result-item:hover { background: var(--vsc-hover); }
        .search-result-item i { width: 13px; text-align: center; opacity: 0.7; }
        .search-result-name { font-weight: 600; }
        .search-result-path { color: var(--vsc-fg-dim); font-size: 11px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .search-no-results { padding: 20px; text-align: center; color: var(--vsc-fg-dim); font-size: 12px; }

        /* ── Trash / Log modals ── */
        #trashModal .modal-content,
        #logModal .modal-content { width: 580px; max-width: 98vw; max-height: 80vh; display: flex; flex-direction: column; }
        #trashModal .modal-body,
        #logModal .modal-body { flex: 1; overflow-y: auto; padding: 0; }
        .trash-item, .log-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 16px;
            border-bottom: 1px solid var(--vsc-border);
            font-size: 12px; color: var(--vsc-fg);
        }
        .trash-item:hover, .log-item:hover { background: var(--vsc-hover); }
        .trash-item-icon { width: 16px; text-align: center; color: var(--vsc-fg-dim); }
        .trash-item-info { flex: 1; overflow: hidden; }
        .trash-item-name { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .trash-item-meta { color: var(--vsc-fg-dim); font-size: 11px; margin-top: 2px; }
        .trash-item-btns { display: flex; gap: 5px; flex-shrink: 0; }
        .log-action { min-width: 80px; font-weight: 600; color: var(--vsc-accent); }
        .log-time { color: var(--vsc-fg-dim); min-width: 130px; }
        .log-target { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .log-action.trash { color: #e5c07b; }
        .log-action.restore { color: #98c379; }
        .log-action.perm_delete { color: #e06c75; }
        .log-action.copy { color: #61afef; }
        .log-action.move { color: #c678dd; }
        .log-action.zip { color: #56b6c2; }
        .clipboard-banner {
            position: fixed; bottom: 36px; left: 50%; transform: translateX(-50%);
            background: #2d2d2d; border: 1px solid var(--vsc-accent);
            border-radius: 5px; padding: 7px 16px; font-size: 12px;
            color: #fff; z-index: 4000; display: none; gap: 10px; align-items: center;
            box-shadow: 0 3px 12px rgba(0,0,0,0.4);
        }
        .clipboard-banner.show { display: flex; }
        .clipboard-banner button { background: none; border: none; color: #aaa; cursor: pointer; font-size: 13px; }
        .clipboard-banner button:hover { color: #fff; }

        /* ── Notepad tabs ── */
        .notepad-tabs {
            display: flex; align-items: center;
            background: #1e1e1e;
            border-bottom: 1px solid var(--vsc-border);
            overflow-x: auto; flex-shrink: 0;
            padding: 0 4px;
        }
        .notepad-tabs::-webkit-scrollbar { height: 3px; }
        .notepad-tabs::-webkit-scrollbar-thumb { background: #555; }
        .notepad-tab {
            display: flex; align-items: center; gap: 6px;
            padding: 6px 12px; cursor: pointer;
            font-size: 12px; color: var(--vsc-fg-dim);
            border-bottom: 2px solid transparent;
            white-space: nowrap; user-select: none; flex-shrink: 0;
        }
        .notepad-tab.active { color: #fff; border-bottom-color: var(--vsc-accent); }
        .notepad-tab:hover { background: var(--vsc-hover); color: #fff; }
        .notepad-tab-close {
            font-size: 10px; opacity: 0.5; padding: 1px 3px;
            border-radius: 2px;
        }
        .notepad-tab-close:hover { background: #e06c7540; opacity: 1; color: #e06c75; }
        .notepad-tab-add {
            padding: 4px 10px; color: var(--vsc-fg-dim); cursor: pointer;
            font-size: 15px; flex-shrink: 0;
        }
        .notepad-tab-add:hover { color: #fff; }


        /* ── Drum Clock ── */
        #drumClock {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 4px;
            flex: 1;
            height: 100%;
            overflow: hidden;
            font-family: 'Segoe UI', monospace;
        }
        #drumClock.show { display: flex; }
        /* Expand title bar when clock is shown */
        .mac-title-bar.clock-mode {
            height: 42px;
        }
        .drum-sep {
            font-size: 16px;
            font-weight: 700;
            color: var(--vsc-fg-dim);
            line-height: 30px;
            padding: 0 1px;
            margin-bottom: 2px;
        }
        .drum-sep.thin { font-size: 12px; padding: 0 2px; color: var(--vsc-fg-dim); opacity: 0.6; }
        .drum-gap {
            width: 10px;
            height: 24px;
            flex-shrink: 0;
            position: relative;
        }
        .drum-gap::after {
            content: '';
            position: absolute;
            left: 50%; top: 4px; bottom: 4px;
            width: 1px;
            background: var(--vsc-border-light);
            opacity: 0.5;
        }
        /* Date block label */
        .drum-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
        }
        .drum-block-label {
            font-size: 7px;
            color: var(--vsc-fg-dim);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: 2px;
            opacity: 0.7;
        }
        .drum-block-digits {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .drum-digit {
            width: 14px;
            height: 24px;
            overflow: hidden;
            position: relative;
        }
        .drum-digit-inner {
            position: absolute;
            top: 0; left: 0; right: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            will-change: transform;
        }
        .drum-digit-inner span {
            display: block;
            height: 24px;
            line-height: 24px;
            font-size: 15px;
            font-weight: 600;
            color: var(--vsc-fg);
            text-align: center;
            width: 100%;
        }
        body.light-theme .drum-digit-inner span { color: #333; }
        body.light-theme .drum-sep { color: #777; }

        /* ── Screensaver ── */
        #kdScreensaver {
            position: fixed;
            inset: 0;
            z-index: 9999;
            pointer-events: all;
            cursor: none;
        }
        #kdSsBlur {
            position: absolute;
            inset: 0;
            background: rgba(10,10,12,0.72);
            backdrop-filter: blur(22px) saturate(0.6);
            -webkit-backdrop-filter: blur(22px) saturate(0.6);
            animation: ssFadeIn 0.8s ease both;
        }
        #kdSsContent {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: ssFadeIn 1s ease both;
        }
        @keyframes ssFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        #kdSsClock {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
            user-select: none;
        }
        /* ── drum row ── */
        .ss-row {
            display: flex;
            align-items: center;
            gap: 0;
        }
        /* each drum slot (one digit or one text column) */
        .ss-drum {
            position: relative;
            overflow: hidden;
            /* fade top/bottom edges */
        }
        .ss-drum::before,
        .ss-drum::after {
            content: '';
            position: absolute;
            left: 0; right: 0;
            z-index: 2;
            pointer-events: none;
        }
        .ss-drum::before {
            top: 0;
            height: 42%;
            background: linear-gradient(to bottom, rgba(10,10,12,0.92) 0%, transparent 100%);
        }
        .ss-drum::after {
            bottom: 0;
            height: 42%;
            background: linear-gradient(to top, rgba(10,10,12,0.92) 0%, transparent 100%);
        }
        /* the scrolling strip inside */
        .ss-strip {
            display: flex;
            flex-direction: column;
            align-items: center;
            will-change: transform;
        }
        /* individual cell in the strip */
        .ss-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-weight: 700;
            white-space: nowrap;
            transition: color 0.15s, opacity 0.15s;
        }
        /* separator between digit groups */
        .ss-dsep {
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-weight: 700;
            color: rgba(255,255,255,0.3);
            align-self: center;
            line-height: 1;
        }

        /* ── Settings Modal ── */
        #settingsModal .modal-content { width: 400px; max-width: 96vw; }
        .settings-group {
            padding: 14px 16px;
            border-bottom: 1px solid var(--vsc-border);
        }
        .settings-group:last-child { border-bottom: none; }
        .settings-group-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--vsc-fg-dim);
            margin-bottom: 10px;
        }
        .settings-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 6px 0;
        }
        .settings-row-label {
            font-size: 13px;
            color: var(--vsc-fg);
        }
        .settings-row-label small {
            display: block;
            font-size: 11px;
            color: var(--vsc-fg-dim);
            margin-top: 2px;
        }
        /* Toggle switch */
        .kd-switch {
            position: relative;
            width: 36px; height: 20px;
            flex-shrink: 0;
        }
        .kd-switch input { opacity: 0; width: 0; height: 0; }
        .kd-switch-slider {
            position: absolute; inset: 0;
            background: #555;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .kd-switch-slider::before {
            content: '';
            position: absolute;
            width: 14px; height: 14px;
            left: 3px; top: 3px;
            background: #fff;
            border-radius: 50%;
            transition: transform 0.2s;
        }
        .kd-switch input:checked + .kd-switch-slider { background: var(--vsc-accent); }
        .kd-switch input:checked + .kd-switch-slider::before { transform: translateX(16px); }
        /* Trash preview */
        #trashPreviewModal .modal-content { width: 700px; max-width: 98vw; max-height: 85vh; display: flex; flex-direction: column; }
        #trashPreviewModal .modal-body { flex: 1; overflow: hidden; display: flex; flex-direction: column; padding: 0; }
        #trashPreviewContainer { flex: 1; width: 100%; overflow: hidden; }


    </style>
</head>
<body>

<!-- ═══════ ACTIVITY BAR ═══════ -->
<div class="activity-bar">
    <div class="activity-bar-logo" title="KOFFEE DEVELOPER">KD</div>
    <button class="activity-btn active" id="explorerBtn" title="Проводник">
        <i class="fas fa-copy"></i>
    </button>
    <button class="activity-btn" title="Поиск (Ctrl+Shift+F)" onclick="openSearchPanel()">
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
        <button class="activity-btn" title="Блокнот" onclick="openNotepad()">
            <i class="fas fa-sticky-note"></i>
        </button>
        <button class="activity-btn" id="trashBtn" title="Корзина" onclick="openTrash()">
            <i class="fas fa-trash-restore"></i>
        </button>
        <button class="activity-btn" title="Лог действий" onclick="openLog()">
            <i class="fas fa-history"></i>
        </button>
        <button class="activity-btn" id="settingsBtn" title="Настройки" onclick="showModal('settingsModal')">
            <i class="fas fa-cog"></i>
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
            <div class="mac-btn mac-btn-close" onclick="tryBsod()" style="cursor:pointer;" title="Закрыть"></div>
            <div class="mac-btn mac-btn-min"></div>
            <div class="mac-btn mac-btn-max"></div>
        </div>
        <!-- Static title (shown when clock is off) -->
        <div class="mac-title" id="macStaticTitle">
            <?php if ($isFile): ?>
                <?= htmlspecialchars(basename($absolutePath)) ?> — KOFFEE DEVELOPER
            <?php else: ?>
                KOFFEE DEVELOPER — Файловый менеджер
            <?php endif; ?>
        </div>
        <!-- Drum clock (shown when clock is on) -->
        <div id="drumClock"></div>
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
            <span class="tab-unsaved-dot" id="unsavedDot"></span>
            <span class="tab-close" id="currentTabClose" onclick="closeTab(event, '<?= $tabPath ?>')"><i class="fas fa-times"></i></span>
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
        <div class="sidebar" id="sidebar" style="position:relative;overflow:hidden;">
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

            <!-- Search panel (overlays sidebar) -->
            <div id="searchPanel">
                <div class="search-input-wrap">
                    <input type="text" id="searchInput" placeholder="Поиск файлов..." autocomplete="off">
                    <button class="search-close-btn" onclick="closeSearchPanel()" title="Закрыть">&times;</button>
                </div>
                <div id="searchResults"></div>
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
                <a href="?path=<?= $fileLinkPath ?>"
                   class="file-item <?= $isActive ?>"
                   data-path="<?= $fileLinkPath ?>"
                   data-name="<?= htmlspecialchars(addslashes($file['name'])) ?>"
                   data-isdir="<?= $file['is_dir'] ? '1' : '0' ?>"
                   data-imgrel="<?= in_array($fIconCls,['image']) ? htmlspecialchars(str_replace(BASE_DIR,'',realpath($file['path']??''))) : '' ?>"
                   oncontextmenu="openCtxMenu(event,this)"
                >
                    <div class="file-item-content">
                        <span class="file-icon <?= $fIconCls ?>"><i class="<?= $fIconFa ?>"></i></span>
                        <span class="file-name" title="<?= htmlspecialchars($file['name']) ?>"><?= htmlspecialchars($file['name']) ?></span>
                    </div>
                    <span class="file-meta"><?= $file['is_dir'] ? '' : kdFormatSize($file['size']) ?> <?= date('d.m H:i', $file['modified']) ?></span>
                    <?php if ($isLoggedIn): ?>
                    <div class="file-item-actions">
                        <button class="file-action-btn" title="Переименовать"
                            onclick="event.preventDefault(); event.stopPropagation(); openRenameModal('<?= htmlspecialchars(addslashes($file['name'])) ?>', '<?= $fileLinkPath ?>')">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <a href="?path=<?= $fileLinkPath ?>&action=delete"
                           class="file-action-btn delete" title="В корзину"
                           onclick="event.preventDefault();event.stopPropagation();if(confirm('В корзину «<?= htmlspecialchars(addslashes($file['name'])) ?>»?'))window.location.href=this.href;">
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
                    <a href="?path=<?= urlencode($requestedPath) ?>&action=copy" class="toolbar-btn" title="Копировать">
                        <i class="fas fa-copy"></i>
                    </a>
                    <a href="?path=<?= urlencode($requestedPath) ?>&action=cut" class="toolbar-btn" title="Вырезать">
                        <i class="fas fa-cut"></i>
                    </a>
                    <?php if (isset($_SESSION['clipboard'])): ?>
                    <a href="?path=<?= urlencode($requestedPath) ?>&action=paste" class="toolbar-btn" title="Вставить">
                        <i class="fas fa-paste"></i> Вставить
                    </a>
                    <?php endif; ?>
                    <?php if ($isDir): ?>
                    <a href="?path=<?= urlencode($requestedPath) ?>&action=zip" class="toolbar-btn" title="Скачать как ZIP">
                        <i class="fas fa-file-archive"></i> ZIP
                    </a>
                    <?php endif; ?>
                    <div class="toolbar-sep"></div>
                    <a href="?path=<?= urlencode($requestedPath) ?>&action=delete"
                       class="toolbar-btn danger"
                       onclick="return confirm('В корзину «<?= htmlspecialchars(addslashes(basename($absolutePath))) ?>»?')">
                        <i class="fas fa-trash"></i>
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
<style>
/* ── Version modal overrides ── */
#versionModal .modal-content {
    width: 460px;
    max-width: 96vw;
    max-height: 88vh;
    display: flex;
    flex-direction: column;
}
#versionModal .modal-body {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 14px 16px 0;
    gap: 0;
}
.cl-info-block {
    flex-shrink: 0;
    padding-bottom: 12px;
}
.cl-label {
    font-size: 12px;
    color: var(--vsc-fg-dim);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 8px;
}
/* Текущая версия — всегда раскрыта */
.cl-entry {
    background: #1e1e1e;
    border-radius: 4px;
    border-left: 2px solid var(--vsc-accent);
    overflow: hidden;
    flex-shrink: 0;
    margin-bottom: 4px;
}
.cl-entry.old {
    border-left-color: #444;
}
.cl-entry-head {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    cursor: default;
    user-select: none;
}
.cl-entry.old .cl-entry-head {
    cursor: pointer;
}
.cl-entry.old .cl-entry-head:hover {
    background: rgba(255,255,255,0.03);
}
.cl-entry-ver {
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    min-width: 36px;
}
.cl-entry.old .cl-entry-ver { color: #888; }
.cl-entry-date {
    font-size: 11px;
    color: var(--vsc-fg-dim);
    margin-left: auto;
}
.cl-entry-chevron {
    font-size: 10px;
    color: #555;
    transition: transform 0.18s;
    flex-shrink: 0;
}
.cl-entry.open .cl-entry-chevron { transform: rotate(90deg); }
.cl-entry-body {
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.22s ease;
}
.cl-entry.current .cl-entry-body {
    max-height: 600px; /* always open */
}
.cl-entry.open .cl-entry-body {
    max-height: 600px;
}
.cl-entry-body ul {
    padding: 0 12px 10px 26px;
    margin: 0;
    color: var(--vsc-fg);
    font-size: 12px;
    line-height: 1.9;
}
.cl-entry.old .cl-entry-body ul { color: #777; }

/* Scrollable history */
.cl-history {
    flex: 1;
    overflow-y: auto;
    min-height: 0;
    padding-bottom: 2px;
    margin-bottom: 10px;
}
.cl-history::-webkit-scrollbar { width: 5px; }
.cl-history::-webkit-scrollbar-thumb { background: #454545; border-radius: 3px; }
</style>
<div class="modal" id="versionModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title"><span style="color:var(--vsc-accent)">KD</span> KOFFEE DEVELOPER v<?= VERSION ?></div>
            <button class="modal-close" onclick="hideModal('versionModal')">&times;</button>
        </div>
        <div class="modal-body">

            <!-- Info block (fixed top) -->
            <div class="cl-info-block">
                <div style="display:flex;gap:20px;flex-wrap:wrap;">
                    <div style="font-size:13px;color:var(--vsc-fg);display:flex;flex-direction:column;gap:4px;">
                        <div><span style="color:var(--vsc-fg-dim);width:72px;display:inline-block;">Автор:</span> <span style="color:var(--vsc-accent);font-weight:600;">INK</span></div>
                        <div><span style="color:var(--vsc-fg-dim);width:72px;display:inline-block;">Команда:</span> <span style="color:#ccc;">KOFFEE TEAM</span></div>
                        <div><span style="color:var(--vsc-fg-dim);width:72px;display:inline-block;">Версия:</span> <span style="color:#ccc;"><?= VERSION ?></span></div>
                        <div><span style="color:var(--vsc-fg-dim);width:72px;display:inline-block;">Сборка:</span> <span style="color:#ccc;">17.06.2025</span></div>
                    </div>
                </div>
                <div style="margin-top:10px;padding:8px 10px;background:rgba(0,122,204,0.07);border:1px solid rgba(0,122,204,0.18);border-radius:4px;font-size:11px;color:#666;line-height:1.7;">
                    <i class="fas fa-info-circle" style="color:var(--vsc-accent);margin-right:5px;"></i>
                    При модификации просьба указать автора — <strong style="color:#999;">INK / KOFFEE TEAM</strong>
                </div>
            </div>

            <!-- Divider + label -->
            <div style="border-top:1px solid var(--vsc-border);padding-top:12px;margin-bottom:8px;" class="cl-label">Changelog</div>

            <!-- Current version — always expanded -->
            <div class="cl-entry current" style="margin-bottom:6px;flex-shrink:0;">
                <div class="cl-entry-head">
                    <span class="cl-entry-ver">v2.3</span>
                    <span style="font-size:11px;background:rgba(0,122,204,0.2);color:var(--vsc-accent);padding:1px 7px;border-radius:10px;font-weight:600;">latest</span>
                    <span class="cl-entry-date">17.06.2025</span>
                </div>
                <div class="cl-entry-body">
                    <ul>
                        <li>Модальное окно настроек (тема, часы, метаданные)</li>
                        <li>Барабанные часы в заголовке окна с анимацией цифр</li>
                        <li>Полная дата ДД.ММ.ГГГГ + время ЧЧ:ММ:СС в часах</li>
                        <li>Исправлена светлая тема — покрывает все блоки</li>
                        <li>Просмотр файлов из корзины (текст + изображения)</li>
                        <li>Drag &amp; drop загрузка файлов</li>
                        <li>Копирование / вырезание / вставка файлов</li>
                        <li>Архивация папки в ZIP одним кликом</li>
                        <li>Поиск файлов по имени (Ctrl+Shift+F)</li>
                        <li>Индикатор несохранённых изменений на вкладке</li>
                        <li>Светлая / тёмная тема с переключением</li>
                        <li>Превью изображений при наведении в проводнике</li>
                        <li>Контекстное меню правой кнопкой мыши</li>
                        <li>Размер файла и дата изменения в проводнике</li>
                        <li>Корзина с восстановлением файлов</li>
                        <li>Лог действий с историей операций</li>
                        <li>Вкладки в блокноте (несколько документов)</li>
                        <li>Автосохранение черновика блокнота в localStorage</li>
                        <li>BSOD при нажатии на красную кнопку title bar в root</li>
                        <li>Реалистичный прогресс-бар и анимация перезагрузки в BSOD</li>
                        <li>Блокнот с Monaco Editor (кнопка в Activity Bar)</li>
                        <li>Выбор языка в блокноте + автоопределение по расширению</li>
                        <li>Сохранение файла из блокнота на сервер</li>
                        <li>Дерево папок для выбора места сохранения (с ленивой подгрузкой)</li>
                        <li>AJAX-эндпоинт для получения вложенных папок</li>
                    </ul>
                </div>
            </div>



        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('versionModal')">Закрыть</button>
        </div>
    </div>
</div>
<script>
function toggleCl(entry) {
    entry.classList.toggle('open');
}
</script>

<!-- ═══════ SETTINGS MODAL ═══════ -->
<div class="modal" id="settingsModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-cog" style="margin-right:8px;color:var(--vsc-accent)"></i>Настройки</div>
            <button class="modal-close" onclick="hideModal('settingsModal')">&times;</button>
        </div>
        <div class="modal-body" style="padding:0;">
            <div class="settings-group">
                <div class="settings-group-label">Внешний вид</div>
                <div class="settings-row">
                    <div class="settings-row-label">
                        Светлая тема
                        <small>Переключить на светлое оформление</small>
                    </div>
                    <label class="kd-switch">
                        <input type="checkbox" id="settingLightTheme" onchange="applySettings(true)">
                        <span class="kd-switch-slider"></span>
                    </label>
                </div>
            </div>
            <div class="settings-group">
                <div class="settings-group-label">Заголовок окна</div>
                <div class="settings-row">
                    <div class="settings-row-label">
                        Показывать часы
                        <small>Заменяет название на часы с датой</small>
                    </div>
                    <label class="kd-switch">
                        <input type="checkbox" id="settingShowClock" onchange="applySettings(true)">
                        <span class="kd-switch-slider"></span>
                    </label>
                </div>
                <div class="settings-row" id="clockFormatRow" style="display:none;">
                    <div class="settings-row-label">
                        Формат
                        <small>Что показывать в часах</small>
                    </div>
                    <select id="settingClockFormat" onchange="applySettings(true)" style="background:#3c3c3c;border:1px solid var(--vsc-border-light);border-radius:4px;color:#fff;font-size:12px;padding:4px 8px;outline:none;">
                        <option value="time">Только время</option>
                        <option value="datetime" selected>Дата + Время</option>
                    </select>
                </div>
            </div>
            <div class="settings-group">
                <div class="settings-group-label">Проводник</div>
                <div class="settings-row">
                    <div class="settings-row-label">
                        Показывать размер и дату
                        <small>При наведении на файл</small>
                    </div>
                    <label class="kd-switch">
                        <input type="checkbox" id="settingShowMeta" checked onchange="applySettings(true)">
                        <span class="kd-switch-slider"></span>
                    </label>
                </div>
            </div>
            <div class="settings-group">
                <div class="settings-group-label">Заставка</div>
                <div class="settings-row">
                    <div class="settings-row-label">
                        Включить заставку
                        <small>Часы/дата при бездействии</small>
                    </div>
                    <label class="kd-switch">
                        <input type="checkbox" id="settingScreensaverEnabled" onchange="applySettings(true)">
                        <span class="kd-switch-slider"></span>
                    </label>
                </div>
                <div class="settings-row" id="screensaverOptionsRow" style="display:none;">
                    <div class="settings-row-label">
                        Показывать
                        <small>Что отображать на заставке</small>
                    </div>
                    <select id="settingScreensaverMode" onchange="applySettings(true)" style="background:#3c3c3c;border:1px solid var(--vsc-border-light);border-radius:4px;color:#fff;font-size:12px;padding:4px 8px;outline:none;">
                        <option value="both">Дата + Время</option>
                        <option value="time">Только время</option>
                        <option value="date">Только дата</option>
                    </select>
                </div>
                <div class="settings-row" id="screensaverTimeoutRow" style="display:none;">
                    <div class="settings-row-label">
                        Секунд до показа
                        <small>Время бездействия (сек)</small>
                    </div>
                    <select id="settingScreensaverTimeout" onchange="applySettings(true)" style="background:#3c3c3c;border:1px solid var(--vsc-border-light);border-radius:4px;color:#fff;font-size:12px;padding:4px 8px;outline:none;">
                        <option value="15">15</option>
                        <option value="30" selected>30</option>
                        <option value="60">60</option>
                        <option value="120">120</option>
                        <option value="300">300</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('settingsModal')">Закрыть</button>
        </div>
    </div>
</div>

<!-- ═══════ SCREENSAVER OVERLAY ═══════ -->
<div id="kdScreensaver" style="display:none;">
    <div id="kdSsBlur"></div>
    <div id="kdSsContent">
        <div id="kdSsClock"></div>
    </div>
</div>

<!-- ═══════ TRASH PREVIEW MODAL ═══════ -->
<div class="modal" id="trashPreviewModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-eye" style="margin-right:8px;color:#98c379"></i><span id="trashPreviewTitle">Просмотр</span></div>
            <button class="modal-close" onclick="hideModal('trashPreviewModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="trashPreviewContainer"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('trashPreviewModal')">Закрыть</button>
        </div>
    </div>
</div>

<!-- ═══════ BSOD SCREEN ═══════ -->
<div id="bsodScreen">
    <div class="bsod-emoji">:(</div>
    <div class="bsod-title">На вашем устройстве возникла проблема, и его<br>нужно перезагрузить.</div>
    <div class="bsod-desc">
        Мы просто собираем сведения об ошибке, а затем выполним<br>
        перезапуск. (<span id="bsodPct">0</span>% готово)
    </div>
    <div class="bsod-progress-wrap">
        <div class="bsod-progress-bar">
            <div class="bsod-progress-fill" id="bsodFill"></div>
        </div>
    </div>
    <div class="bsod-qr-row">
        <div class="bsod-qr">
            <!-- Simple QR-like pattern SVG -->
            <svg viewBox="0 0 68 68" xmlns="http://www.w3.org/2000/svg" fill="#000">
                <rect x="0" y="0" width="28" height="28" rx="2"/>
                <rect x="4" y="4" width="20" height="20" rx="1" fill="#0078D7"/>
                <rect x="8" y="8" width="12" height="12" rx="1"/>
                <rect x="40" y="0" width="28" height="28" rx="2"/>
                <rect x="44" y="4" width="20" height="20" rx="1" fill="#0078D7"/>
                <rect x="48" y="8" width="12" height="12" rx="1"/>
                <rect x="0" y="40" width="28" height="28" rx="2"/>
                <rect x="4" y="44" width="20" height="20" rx="1" fill="#0078D7"/>
                <rect x="8" y="48" width="12" height="12" rx="1"/>
                <rect x="40" y="40" width="4" height="4"/><rect x="48" y="40" width="4" height="4"/>
                <rect x="56" y="40" width="4" height="4"/><rect x="64" y="40" width="4" height="4"/>
                <rect x="40" y="48" width="4" height="4"/><rect x="48" y="52" width="4" height="4"/>
                <rect x="56" y="48" width="4" height="4"/><rect x="60" y="56" width="4" height="4"/>
                <rect x="40" y="56" width="4" height="4"/><rect x="48" y="60" width="4" height="4"/>
                <rect x="56" y="60" width="4" height="4"/><rect x="64" y="56" width="4" height="4"/>
            </svg>
        </div>
        <div class="bsod-qr-text">
            Дополнительные сведения об этой проблеме и возможных исправлениях<br>
            можно найти по ссылке: <u>https://www.windows.com/stopcode</u><br><br>
            Если вы обращаетесь в службу поддержки, сообщите им<br>
            следующую информацию:
        </div>
    </div>
    <div class="bsod-stop-code">
        Код остановки: KOFFEE_FILE_MANAGER_CRITICAL_ERROR
    </div>
    <div class="bsod-close-hint" onclick="hideBsod()">[ Нажмите здесь для возврата ]</div>
</div>

<!-- ═══════ NOTEPAD MODAL ═══════ -->
<div class="modal" id="notepadModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-sticky-note" style="margin-right:8px;color:#f1c40f"></i>Блокнот</div>
            <button class="modal-close" onclick="closeNotepad()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Notepad tabs -->
            <div class="notepad-tabs" id="notepadTabs">
                <div class="notepad-tab active" data-idx="0">
                    <span class="notepad-tab-name">Без имени 1</span>
                    <span class="notepad-tab-close" onclick="closeNotepadTab(event,0)"><i class="fas fa-times"></i></span>
                </div>
                <div class="notepad-tab-add" onclick="addNotepadTab()" title="Новая вкладка">+</div>
            </div>
            <div class="notepad-toolbar">
                <span style="font-size:12px;color:var(--vsc-fg-dim);">Язык:</span>
                <select class="notepad-lang-select" id="notepadLang" onchange="changeNotepadLang()">
                    <option value="plaintext">Plain Text</option>
                    <option value="php">PHP</option>
                    <option value="javascript">JavaScript</option>
                    <option value="typescript">TypeScript</option>
                    <option value="html">HTML</option>
                    <option value="css">CSS</option>
                    <option value="json">JSON</option>
                    <option value="lua">Lua</option>
                    <option value="python">Python</option>
                    <option value="shell">Shell/Bash</option>
                    <option value="sql">SQL</option>
                    <option value="markdown">Markdown</option>
                    <option value="xml">XML</option>
                    <option value="yaml">YAML</option>
                    <option value="ini">INI / Config</option>
                    <option value="csharp">C#</option>
                    <option value="cpp">C++</option>
                    <option value="java">Java</option>
                    <option value="go">Go</option>
                    <option value="rust">Rust</option>
                </select>
                <div class="toolbar-sep"></div>
                <button class="toolbar-btn" onclick="notepadFormat()" title="Форматировать (Shift+Alt+F)">
                    <i class="fas fa-magic"></i> Форматировать
                </button>
                <button class="toolbar-btn" onclick="notepadClear()" title="Очистить редактор">
                    <i class="fas fa-trash"></i> Очистить
                </button>
                <span style="margin-left:auto;font-size:11px;color:var(--vsc-fg-dim);" id="notepadCursor">Ln 1, Col 1</span>
            </div>
            <div id="notepadContainer"></div>
            <div class="notepad-save-section">
                <span class="notepad-save-label"><i class="fas fa-save"></i> Сохранить как:</span>
                <input type="text" class="notepad-filename-input" id="notepadFilename" placeholder="имя_файла.php">
                <span class="notepad-save-label">в папку:</span>
                <button class="toolbar-btn" onclick="openFolderPicker()" id="notepadFolderBtn" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    <i class="fas fa-folder"></i> <span id="notepadFolderLabel">/ (корень)</span>
                </button>
                <input type="hidden" id="notepadFolderValue" value="">
                <button class="btn btn-primary" onclick="notepadSave()" style="height:30px;padding:0 14px;">
                    <i class="fas fa-save"></i> Сохранить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════ FOLDER PICKER MODAL ═══════ -->
<div class="modal" id="folderPickerModal">
    <div class="modal-content" style="width:420px;max-width:96vw;max-height:80vh;display:flex;flex-direction:column;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-folder-open" style="margin-right:8px;color:#dcb67a"></i>Выбрать папку</div>
            <button class="modal-close" onclick="hideModal('folderPickerModal')">&times;</button>
        </div>
        <div class="modal-body" style="padding:0;flex:1;overflow:hidden;display:flex;flex-direction:column;">
            <div style="padding:10px 14px;border-bottom:1px solid var(--vsc-border);font-size:12px;color:var(--vsc-fg-dim);">
                Выбрано: <span id="pickerCurrentPath" style="color:#fff;font-weight:600;">/ (корень)</span>
            </div>
            <div id="folderTree" style="flex:1;overflow-y:auto;padding:6px 0;"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('folderPickerModal')">Отмена</button>
            <button class="btn btn-primary" onclick="confirmFolderPick()"><i class="fas fa-check"></i> Выбрать</button>
        </div>
    </div>
</div>

<!-- ═══════ TRASH MODAL ═══════ -->
<div class="modal" id="trashModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-trash-restore" style="margin-right:8px;color:#e5c07b"></i>Корзина</div>
            <button class="modal-close" onclick="hideModal('trashModal')">&times;</button>
        </div>
        <div class="modal-body" id="trashList">
            <div style="padding:30px;text-align:center;color:var(--vsc-fg-dim);font-size:13px;"><i class="fas fa-spinner fa-spin"></i> Загрузка...</div>
        </div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn btn-danger" onclick="trashEmpty()"><i class="fas fa-fire"></i> Очистить корзину</button>
            <button class="btn btn-secondary" onclick="hideModal('trashModal')">Закрыть</button>
        </div>
    </div>
</div>

<!-- ═══════ LOG MODAL ═══════ -->
<div class="modal" id="logModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-history" style="margin-right:8px;color:#61afef"></i>Лог действий</div>
            <button class="modal-close" onclick="hideModal('logModal')">&times;</button>
        </div>
        <div class="modal-body" id="logList">
            <div style="padding:30px;text-align:center;color:var(--vsc-fg-dim);font-size:13px;"><i class="fas fa-spinner fa-spin"></i> Загрузка...</div>
        </div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn btn-secondary" onclick="logClear()"><i class="fas fa-eraser"></i> Очистить лог</button>
            <button class="btn btn-secondary" onclick="hideModal('logModal')">Закрыть</button>
        </div>
    </div>
</div>

<!-- ═══════ CONTEXT MENU ═══════ -->
<div id="ctxMenu">
    <div class="ctx-item" id="ctx-open"><i class="fas fa-external-link-alt"></i> Открыть</div>
    <div class="ctx-sep"></div>
    <div class="ctx-item" id="ctx-copy"><i class="fas fa-copy"></i> Копировать</div>
    <div class="ctx-item" id="ctx-cut"><i class="fas fa-cut"></i> Вырезать</div>
    <div class="ctx-item" id="ctx-paste"><i class="fas fa-paste"></i> Вставить сюда</div>
    <div class="ctx-sep"></div>
    <div class="ctx-item" id="ctx-rename"><i class="fas fa-pencil-alt"></i> Переименовать</div>
    <div class="ctx-item" id="ctx-zip"><i class="fas fa-file-archive"></i> Скачать как ZIP</div>
    <div class="ctx-item" id="ctx-download"><i class="fas fa-download"></i> Скачать</div>
    <div class="ctx-sep"></div>
    <div class="ctx-item danger" id="ctx-delete"><i class="fas fa-trash"></i> В корзину</div>
</div>

<!-- ═══════ IMAGE HOVER PREVIEW ═══════ -->
<div id="imgPreviewPopup"><img src="" id="imgPreviewImg" alt=""></div>

<!-- ═══════ CLIPBOARD STATE ═══════ -->
<div id="kdClipboardState"
     data-has-cb="<?= isset($_SESSION['clipboard']) ? 'true' : 'false' ?>"
     data-cb="<?= isset($_SESSION['clipboard']) ? htmlspecialchars(json_encode($_SESSION['clipboard'])) : '' ?>"
     style="display:none;"></div>
<!-- ═══════ CLIPBOARD BANNER ═══════ -->
<div class="clipboard-banner" id="clipboardBanner">
    <i class="fas fa-clipboard"></i>
    <span id="clipboardBannerText">Скопировано в буфер</span>
    <button onclick="clearClipboard()" title="Очистить буфер"><i class="fas fa-times"></i></button>
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
    // If closing the root tab (empty path = root) — show BSOD
    if ((path === '' || path === '/') && (currentPath === '' || currentPath === '/')) {
        showBsod();
        return;
    }
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
//  BSOD
// ════════════════════════════════════════════
var isRootPath = (currentPath === '' || currentPath === '/');

function tryBsod() {
    if (isRootPath) {
        showBsod();
    } else {
        // Not root — just navigate up or do nothing (like a close)
        window.location.href = '?path=';
    }
}

function showBsod() {
    var screen = document.getElementById('bsodScreen');
    screen.classList.add('show');
    var fill = document.getElementById('bsodFill');
    var pct = document.getElementById('bsodPct');
    var progress = 0;
    fill.style.width = '0%';
    pct.textContent = '0';
    var interval = setInterval(function() {
        // Simulate realistic Windows progress: fast then slow
        var step = progress < 60 ? 1.2 : (progress < 85 ? 0.5 : 0.15);
        progress = Math.min(progress + step, 100);
        fill.style.width = progress.toFixed(1) + '%';
        pct.textContent = Math.floor(progress);
        if (progress >= 100) {
            clearInterval(interval);
            // After 2s "restart" — reload page
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        }
    }, 80);
}

function hideBsod() {
    var screen = document.getElementById('bsodScreen');
    screen.classList.remove('show');
}

// ════════════════════════════════════════════
//  NOTEPAD (Monaco-based)
// ════════════════════════════════════════════
var notepadEditor = null;
var notepadInited = false;

function openNotepad() {
    showModal('notepadModal');
    if (!notepadInited) {
        renderNotepadTabs();
        initNotepadEditor();
        notepadInited = true;
    } else if (notepadEditor) {
        renderNotepadTabs();
        setTimeout(function() { notepadEditor.layout(); }, 50);
    }
}

function closeNotepad() {
    hideModal('notepadModal');
}

function initNotepadEditor() {
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        notepadEditor = monaco.editor.create(document.getElementById('notepadContainer'), {
            value: '',
            language: 'plaintext',
            theme: 'vs-dark',
            automaticLayout: true,
            readOnly: false,
            minimap: { enabled: false },
            fontSize: 14,
            fontFamily: "'Cascadia Code', 'Fira Code', 'JetBrains Mono', Consolas, monospace",
            fontLigatures: true,
            lineHeight: 22,
            scrollBeyondLastLine: false,
            wordWrap: 'on',
            bracketPairColorization: { enabled: true },
            smoothScrolling: true,
            cursorBlinking: 'smooth',
            cursorSmoothCaretAnimation: 'on',
            padding: { top: 10 },
            quickSuggestions: true
        });
        notepadEditor.onDidChangeModelContent(function() {
            notepadTabs[notepadActive].content = notepadEditor.getValue();
        });
        notepadEditor.onDidChangeCursorPosition(function(e) {
            var el = document.getElementById('notepadCursor');
            if (el) el.textContent = 'Ln ' + e.position.lineNumber + ', Col ' + e.position.column;
        });
        // Auto-detect extension from filename input
        document.getElementById('notepadFilename').addEventListener('input', function() {
            var fn = this.value;
            var ext = fn.split('.').pop().toLowerCase();
            var extLangMap = {
                'php':'php','js':'javascript','jsx':'javascript','ts':'typescript','tsx':'typescript',
                'html':'html','htm':'html','css':'css','json':'json','xml':'xml','svg':'xml',
                'lua':'lua','py':'python','rb':'ruby','sh':'shell','bash':'shell',
                'bat':'bat','ps1':'powershell','c':'c','h':'c','cpp':'cpp','hpp':'cpp',
                'cs':'csharp','java':'java','kt':'kotlin','go':'go','rs':'rust',
                'sql':'sql','yaml':'yaml','yml':'yaml','toml':'ini','ini':'ini',
                'cfg':'ini','conf':'ini','env':'ini','md':'markdown','txt':'plaintext','log':'plaintext'
            };
            if (extLangMap[ext]) {
                var sel = document.getElementById('notepadLang');
                sel.value = extLangMap[ext] || 'plaintext';
                monaco.editor.setModelLanguage(notepadEditor.getModel(), extLangMap[ext] || 'plaintext');
            }
        });
    });
}

function changeNotepadLang() {
    if (!notepadEditor) return;
    var lang = document.getElementById('notepadLang').value;
    monaco.editor.setModelLanguage(notepadEditor.getModel(), lang);
}

function notepadFormat() {
    if (notepadEditor) notepadEditor.getAction('editor.action.formatDocument').run();
}

function notepadClear() {
    if (notepadEditor && confirm('Очистить редактор?')) {
        notepadEditor.setValue('');
    }
}

function notepadSave() {
    if (!notepadEditor) { showNotification('Редактор ещё не загружен', 'error'); return; }
    var filename = document.getElementById('notepadFilename').value.trim();
    if (!filename) { showNotification('Введите имя файла', 'error'); return; }
    var folder = document.getElementById('notepadFolderValue').value;
    var content = notepadEditor.getValue();

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '?path=' + encodeURIComponent(folder);
    form.style.display = 'none';

    var inp1 = document.createElement('input');
    inp1.type = 'hidden'; inp1.name = 'notepad_save'; inp1.value = '1';
    var inp2 = document.createElement('input');
    inp2.type = 'hidden'; inp2.name = 'notepad_filename'; inp2.value = filename;
    var inp3 = document.createElement('input');
    inp3.type = 'hidden'; inp3.name = 'notepad_content'; inp3.value = content;

    form.appendChild(inp1); form.appendChild(inp2); form.appendChild(inp3);
    document.body.appendChild(form);
    form.submit();
}

// ════════════════════════════════════════════
//  FOLDER TREE PICKER
// ════════════════════════════════════════════
var pickerSelectedPath = '';

function openFolderPicker() {
    pickerSelectedPath = document.getElementById('notepadFolderValue').value || '';
    buildFolderTree('', document.getElementById('folderTree'), 0);
    updatePickerDisplay();
    showModal('folderPickerModal');
}

function confirmFolderPick() {
    document.getElementById('notepadFolderValue').value = pickerSelectedPath;
    var label = pickerSelectedPath ? '/' + pickerSelectedPath : '/ (корень)';
    document.getElementById('notepadFolderLabel').textContent = label;
    hideModal('folderPickerModal');
}

function updatePickerDisplay() {
    var label = pickerSelectedPath ? '/' + pickerSelectedPath : '/ (корень)';
    document.getElementById('pickerCurrentPath').textContent = label;
}

function buildFolderTree(dirPath, container, depth) {
    // Add root item if top level
    if (depth === 0) {
        container.innerHTML = '';
        var rootItem = createFtreeItem('', '/ (корень)', true, false, 0);
        container.appendChild(rootItem.row);
        var rootChildren = document.createElement('div');
        rootChildren.className = 'ftree-children open';
        rootChildren.style.paddingLeft = '16px';
        container.appendChild(rootChildren);
        // fetch root subdirs
        fetchSubdirs('', rootChildren, 1);
    }
}

function fetchSubdirs(dirPath, container, depth) {
    container.innerHTML = '<div class="ftree-loading"><i class="fas fa-spinner fa-spin"></i> Загрузка...</div>';
    fetch('?' + new URLSearchParams({ ajax: 'subdirs', dir: dirPath }))
        .then(function(r) { return r.json(); })
        .then(function(items) {
            container.innerHTML = '';
            if (items.length === 0) {
                container.innerHTML = '<div class="ftree-loading">Нет подпапок</div>';
                return;
            }
            items.forEach(function(item) {
                var row = createFtreeItem(item.path, item.name, item.hasSubs, item.hasSubs, depth);
                container.appendChild(row.row);
                if (item.hasSubs) {
                    var childrenDiv = document.createElement('div');
                    childrenDiv.className = 'ftree-children';
                    childrenDiv.style.paddingLeft = '16px';
                    container.appendChild(childrenDiv);
                    row.toggle.addEventListener('click', function(e) {
                        e.stopPropagation();
                        var isOpen = childrenDiv.classList.contains('open');
                        if (!isOpen) {
                            childrenDiv.classList.add('open');
                            row.toggle.classList.add('open');
                            row.icon.className = 'ftree-icon fas fa-folder-open';
                            if (!childrenDiv._loaded) {
                                childrenDiv._loaded = true;
                                fetchSubdirs(item.path, childrenDiv, depth + 1);
                            }
                        } else {
                            childrenDiv.classList.remove('open');
                            row.toggle.classList.remove('open');
                            row.icon.className = 'ftree-icon fas fa-folder';
                        }
                    });
                }
            });
        })
        .catch(function() {
            container.innerHTML = '<div class="ftree-loading">Ошибка загрузки</div>';
        });
}

function createFtreeItem(path, label, hasSubs, hasToggle, depth) {
    var row = document.createElement('div');
    row.className = 'ftree-item' + (path === pickerSelectedPath ? ' selected' : '');
    row.style.paddingLeft = (8 + depth * 4) + 'px';
    row.style.paddingRight = '8px';
    row.style.minHeight = '26px';

    var toggle = document.createElement('span');
    toggle.className = 'ftree-toggle' + (hasToggle ? '' : ' leaf');
    toggle.innerHTML = '<i class="fas fa-chevron-right"></i>';

    var icon = document.createElement('span');
    icon.className = 'ftree-icon fas fa-folder';

    var name = document.createElement('span');
    name.className = 'ftree-name';
    name.textContent = label;

    row.appendChild(toggle);
    row.appendChild(icon);
    row.appendChild(name);

    // Select on click (not toggle)
    row.addEventListener('click', function(e) {
        if (e.target.closest('.ftree-toggle') && hasToggle) return;
        // Deselect previous
        var prev = document.querySelector('.ftree-item.selected');
        if (prev) prev.classList.remove('selected');
        row.classList.add('selected');
        pickerSelectedPath = path;
        updatePickerDisplay();
    });

    return { row: row, toggle: toggle, icon: icon };
}




// ════════════════════════════════════════════
//  SETTINGS
// ════════════════════════════════════════════
var kdSettings = {
    lightTheme:  false,
    showClock:   false,
    clockFormat: 'datetime',
    showMeta:    true,
    screensaverEnabled: false,
    screensaverMode:    'both',
    screensaverTimeout: 30
};

function loadSettings() {
    try {
        var s = JSON.parse(localStorage.getItem('kd_settings') || '{}');
        kdSettings.lightTheme  = !!s.lightTheme;
        kdSettings.showClock   = !!s.showClock;
        kdSettings.clockFormat = s.clockFormat || 'datetime';
        kdSettings.showMeta    = s.showMeta !== false;
        kdSettings.screensaverEnabled = !!s.screensaverEnabled;
        kdSettings.screensaverMode    = s.screensaverMode || 'both';
        kdSettings.screensaverTimeout = parseInt(s.screensaverTimeout) || 30;
    } catch(e) {}
}

function saveSettings() {
    localStorage.setItem('kd_settings', JSON.stringify(kdSettings));
}

function applySettings(fromUI) {
    // Only read DOM values when called from UI interaction
    if (fromUI) {
        var ltEl  = document.getElementById('settingLightTheme');
        var clEl  = document.getElementById('settingShowClock');
        var cfEl  = document.getElementById('settingClockFormat');
        var smEl  = document.getElementById('settingShowMeta');
        var ssEl  = document.getElementById('settingScreensaverEnabled');
        var ssMEl = document.getElementById('settingScreensaverMode');
        var ssTEl = document.getElementById('settingScreensaverTimeout');
        if (ltEl) kdSettings.lightTheme  = ltEl.checked;
        if (clEl) kdSettings.showClock   = clEl.checked;
        if (cfEl) kdSettings.clockFormat = cfEl.value;
        if (smEl) kdSettings.showMeta    = smEl.checked;
        if (ssEl) kdSettings.screensaverEnabled = ssEl.checked;
        if (ssMEl) kdSettings.screensaverMode   = ssMEl.value;
        if (ssTEl) kdSettings.screensaverTimeout = parseInt(ssTEl.value) || 30;
    }

    // Theme
    var wasLight = document.body.classList.contains('light-theme');
    if (kdSettings.lightTheme) document.body.classList.add('light-theme');
    else                        document.body.classList.remove('light-theme');
    if (wasLight !== kdSettings.lightTheme && typeof monaco !== 'undefined') {
        monaco.editor.setTheme(kdSettings.lightTheme ? 'vs' : 'vs-dark');
    }

    // Clock
    var clockEl  = document.getElementById('drumClock');
    var titleEl  = document.getElementById('macStaticTitle');
    var fmtRow   = document.getElementById('clockFormatRow');
    if (clockEl && titleEl) {
        var titleBar = document.querySelector('.mac-title-bar');
        if (kdSettings.showClock) {
            clockEl.classList.add('show');
            titleEl.style.display = 'none';
            if (titleBar) titleBar.classList.add('clock-mode');
            startDrumClock();
        } else {
            clockEl.classList.remove('show');
            titleEl.style.display = '';
            if (titleBar) titleBar.classList.remove('clock-mode');
            stopDrumClock();
        }
    }
    if (fmtRow) fmtRow.style.display = kdSettings.showClock ? '' : 'none';

    // Screensaver option rows
    var ssOptRow = document.getElementById('screensaverOptionsRow');
    var ssToutRow = document.getElementById('screensaverTimeoutRow');
    if (ssOptRow)  ssOptRow.style.display  = kdSettings.screensaverEnabled ? '' : 'none';
    if (ssToutRow) ssToutRow.style.display = kdSettings.screensaverEnabled ? '' : 'none';

    // Screensaver idle timer
    if (kdSettings.screensaverEnabled) {
        startIdleTimer();
    } else {
        stopIdleTimer();
        hideScreensaver();
    }

    // Meta visibility
    document.documentElement.style.setProperty('--meta-opacity-hover', kdSettings.showMeta ? '1' : '0');

    saveSettings();
}

function syncSettingsUI() {
    var ltEl = document.getElementById('settingLightTheme');
    var clEl = document.getElementById('settingShowClock');
    var cfEl = document.getElementById('settingClockFormat');
    var smEl = document.getElementById('settingShowMeta');
    var ssEl  = document.getElementById('settingScreensaverEnabled');
    var ssMEl = document.getElementById('settingScreensaverMode');
    var ssTEl = document.getElementById('settingScreensaverTimeout');
    if (ltEl) ltEl.checked = kdSettings.lightTheme;
    if (clEl) clEl.checked = kdSettings.showClock;
    if (cfEl) cfEl.value   = kdSettings.clockFormat;
    if (smEl) smEl.checked = kdSettings.showMeta;
    if (ssEl)  ssEl.checked  = kdSettings.screensaverEnabled;
    if (ssMEl) ssMEl.value   = kdSettings.screensaverMode;
    if (ssTEl) ssTEl.value   = String(kdSettings.screensaverTimeout);
    var fmtRow = document.getElementById('clockFormatRow');
    if (fmtRow) fmtRow.style.display = kdSettings.showClock ? '' : 'none';
    var ssOptRow  = document.getElementById('screensaverOptionsRow');
    var ssToutRow = document.getElementById('screensaverTimeoutRow');
    if (ssOptRow)  ssOptRow.style.display  = kdSettings.screensaverEnabled ? '' : 'none';
    if (ssToutRow) ssToutRow.style.display = kdSettings.screensaverEnabled ? '' : 'none';
}

// Init settings on load
(function() {
    loadSettings();
    if (kdSettings.lightTheme) document.body.classList.add('light-theme');
})();

// Also apply Monaco theme after it loads
function applyMonacoTheme() {
    if (typeof monaco !== 'undefined') {
        monaco.editor.setTheme(kdSettings.lightTheme ? 'vs' : 'vs-dark');
    }
}

// Sync UI when settings modal opens
var _origShowModal = null;
window.addEventListener('DOMContentLoaded', function() {
    // Wrap showModal to sync settings UI
    var _origShowModal = window.showModal;
    window.showModal = function(id) {
        _origShowModal(id);
        if (id === 'settingsModal') syncSettingsUI();
    };
    // Apply all saved settings from localStorage (no DOM read)
    applySettings(false);
    // Apply Monaco theme once available
    setTimeout(applyMonacoTheme, 600);
    // Screensaver click-to-dismiss
    var ssEl = document.getElementById('kdScreensaver');
    if (ssEl) {
        ssEl.addEventListener('click', function() {
            hideScreensaver();
            resetIdleTimer();
        });
        ssEl.addEventListener('keydown', function() {
            hideScreensaver();
            resetIdleTimer();
        });
    }
});

// ════════════════════════════════════════════
//  DRUM CLOCK
// ════════════════════════════════════════════
var drumClockInterval = null;
var drumDigitEls = {};   // key -> { inner, current }
var drumLastValues = {}; // key -> digit char

function stopDrumClock() {
    if (drumClockInterval) { clearInterval(drumClockInterval); drumClockInterval = null; }
}

function startDrumClock() {
    stopDrumClock();
    buildDrumClockDOM();
    renderDrumClock(true);
    drumClockInterval = setInterval(function() { renderDrumClock(false); }, 1000);
}

function makeDrumDigit(key) {
    var wrap = document.createElement('div');
    wrap.className = 'drum-digit';
    var inner = document.createElement('div');
    inner.className = 'drum-digit-inner';
    for (var i = 0; i < 3; i++) {
        var span = document.createElement('span');
        span.textContent = '0';
        inner.appendChild(span);
    }
    inner.style.transform = 'translateY(-24px)';
    wrap.appendChild(inner);
    drumDigitEls[key] = { wrap: wrap, inner: inner };
    return wrap;
}

function makeSep(char, thin) {
    var sep = document.createElement('div');
    sep.className = 'drum-sep' + (thin ? ' thin' : '');
    sep.textContent = char;
    return sep;
}

function buildDrumClockDOM() {
    var el = document.getElementById('drumClock');
    if (!el) return;
    el.innerHTML = '';
    drumDigitEls = {};
    drumLastValues = {};

    if (kdSettings.clockFormat === 'datetime') {
        // ── DATE block ──────────────────────
        var dateBlock = document.createElement('div');
        dateBlock.className = 'drum-block';
        var dateLbl = document.createElement('div');
        dateLbl.className = 'drum-block-label';
        dateLbl.textContent = 'дата';
        var dateDigits = document.createElement('div');
        dateDigits.className = 'drum-block-digits';
        dateDigits.appendChild(makeDrumDigit('d0'));
        dateDigits.appendChild(makeDrumDigit('d1'));
        dateDigits.appendChild(makeSep('.', true));
        dateDigits.appendChild(makeDrumDigit('d2'));
        dateDigits.appendChild(makeDrumDigit('d3'));
        dateDigits.appendChild(makeSep('.', true));
        dateDigits.appendChild(makeDrumDigit('d4'));
        dateDigits.appendChild(makeDrumDigit('d5'));
        dateDigits.appendChild(makeDrumDigit('d6'));
        dateDigits.appendChild(makeDrumDigit('d7'));
        dateBlock.appendChild(dateLbl);
        dateBlock.appendChild(dateDigits);
        el.appendChild(dateBlock);

        // ── GAP ─────────────────────────────
        var gap = document.createElement('div');
        gap.className = 'drum-gap';
        el.appendChild(gap);
    }

    // ── TIME block ───────────────────────
    var timeBlock = document.createElement('div');
    timeBlock.className = 'drum-block';
    var timeLbl = document.createElement('div');
    timeLbl.className = 'drum-block-label';
    timeLbl.textContent = 'время';
    var timeDigits = document.createElement('div');
    timeDigits.className = 'drum-block-digits';
    timeDigits.appendChild(makeDrumDigit('h0'));
    timeDigits.appendChild(makeDrumDigit('h1'));
    timeDigits.appendChild(makeSep(':', false));
    timeDigits.appendChild(makeDrumDigit('m0'));
    timeDigits.appendChild(makeDrumDigit('m1'));
    timeDigits.appendChild(makeSep(':', false));
    timeDigits.appendChild(makeDrumDigit('s0'));
    timeDigits.appendChild(makeDrumDigit('s1'));
    timeBlock.appendChild(timeLbl);
    timeBlock.appendChild(timeDigits);
    el.appendChild(timeBlock);
}

function renderDrumClock(force) {
    var now = new Date();
    var dd   = String(now.getDate()).padStart(2, '0');
    var mo   = String(now.getMonth() + 1).padStart(2, '0');
    var yyyy = String(now.getFullYear());
    var hh   = String(now.getHours()).padStart(2, '0');
    var mm   = String(now.getMinutes()).padStart(2, '0');
    var ss   = String(now.getSeconds()).padStart(2, '0');

    var vals = {};
    if (kdSettings.clockFormat === 'datetime') {
        vals.d0 = dd[0];   vals.d1 = dd[1];
        vals.d2 = mo[0];   vals.d3 = mo[1];
        vals.d4 = yyyy[0]; vals.d5 = yyyy[1]; vals.d6 = yyyy[2]; vals.d7 = yyyy[3];
    }
    vals.h0 = hh[0]; vals.h1 = hh[1];
    vals.m0 = mm[0]; vals.m1 = mm[1];
    vals.s0 = ss[0]; vals.s1 = ss[1];

    Object.keys(vals).forEach(function(key) {
        var el = drumDigitEls[key];
        if (!el) return;
        var newVal = vals[key];
        var oldVal = drumLastValues[key];
        if (newVal === oldVal && !force) return;
        drumLastValues[key] = newVal;
        spinDigit(el.inner, oldVal || '0', newVal);
    });
}

function spinDigit(inner, from, to) {
    var spans = inner.querySelectorAll('span');
    spans[0].textContent = from;
    spans[1].textContent = to;
    spans[2].textContent = to;
    var h = inner.parentElement ? inner.parentElement.offsetHeight || 24 : 24;
    // Start at top (showing "from")
    inner.style.transition = 'none';
    inner.style.transform  = 'translateY(0px)';
    inner.offsetHeight; // reflow
    // Animate to middle (showing "to")
    inner.style.transition = 'transform 0.32s cubic-bezier(0.25,0.46,0.45,0.94)';
    inner.style.transform  = 'translateY(-' + h + 'px)';
}


// ════════════════════════════════════════════
//  SCREENSAVER
// ════════════════════════════════════════════
var ssIdleTimer  = null;
var ssClockTimer = null;
var ssVisible    = false;
// Per-drum state
var ssDrums = {};
var SS_MONTHS_GEN = ['января','февраля','марта','апреля','мая','июня',
                     'июля','августа','сентября','октября','ноября','декабря'];
var SS_WEEKDAYS   = ['воскресенье','понедельник','вторник','среда',
                     'четверг','пятница','суббота'];

function startIdleTimer() {
    stopIdleTimer();
    resetIdleTimer();
    document.addEventListener('mousemove', onUserActivity, true);
    document.addEventListener('keydown',   onUserActivity, true);
    document.addEventListener('mousedown', onUserActivity, true);
    document.addEventListener('touchstart',onUserActivity, true);
}

function stopIdleTimer() {
    if (ssIdleTimer) { clearTimeout(ssIdleTimer); ssIdleTimer = null; }
    document.removeEventListener('mousemove', onUserActivity, true);
    document.removeEventListener('keydown',   onUserActivity, true);
    document.removeEventListener('mousedown', onUserActivity, true);
    document.removeEventListener('touchstart',onUserActivity, true);
}

function resetIdleTimer() {
    if (ssIdleTimer) clearTimeout(ssIdleTimer);
    var timeout = (kdSettings.screensaverTimeout || 30) * 1000;
    ssIdleTimer = setTimeout(showScreensaver, timeout);
}

function onUserActivity() {
    if (ssVisible) {
        hideScreensaver();
    }
    resetIdleTimer();
}

function showScreensaver() {
    if (!kdSettings.screensaverEnabled) return;
    ssVisible = true;
    var el = document.getElementById('kdScreensaver');
    if (!el) return;
    el.style.display = 'block';
    buildSsClock();
    updateSsClock(true);
    ssClockTimer = setInterval(function() { updateSsClock(false); }, 1000);
}

function hideScreensaver() {
    ssVisible = false;
    if (ssClockTimer) { clearInterval(ssClockTimer); ssClockTimer = null; }
    var el = document.getElementById('kdScreensaver');
    if (el) el.style.display = 'none';
    ssDrums = {};
}

// ── Drum helpers ──────────────────────────────────────────

var SS_VISIBLE = 7;   // rows visible in the window
var SS_GHOST   = 3;   // ghost rows on each side

/*  makeDrum(key, values, cellW, cellH, fontSize, fontW)
    values = ordered array of all items the drum can show (digits 0-9, or month names etc.)
    returns the .ss-drum wrapper element
*/
function makeDrum(key, values, cellW, cellH, fontSize, fontW, extraCellCss) {
    var drum = document.createElement('div');
    drum.className = 'ss-drum';
    drum.style.width  = cellW + 'px';
    drum.style.height = (cellH * SS_VISIBLE) + 'px';

    var strip = document.createElement('div');
    strip.className = 'ss-strip';
    strip.style.width = cellW + 'px';

    // Fill strip with ALL values repeated enough times for smooth looping
    // We put: ...tail, full cycle, full cycle, full cycle, ...head
    // so we always have items above and below
    var totalCopies = 3;
    var allCells = [];
    for (var c = 0; c < totalCopies; c++) {
        for (var v = 0; v < values.length; v++) {
            var cell = document.createElement('div');
            cell.className = 'ss-cell';
            cell.style.height      = cellH + 'px';
            cell.style.lineHeight  = cellH + 'px';
            cell.style.fontSize    = fontSize + 'px';
            cell.style.fontWeight  = fontW || '700';
            cell.style.width       = cellW + 'px';
            if (extraCellCss) Object.assign(cell.style, extraCellCss);
            cell.textContent = values[v];
            strip.appendChild(cell);
            allCells.push(cell);
        }
    }

    drum.appendChild(strip);

    ssDrums[key] = {
        values:    values,
        strip:     strip,
        cells:     allCells,
        cellH:     cellH,
        cellW:     cellW,
        copies:    totalCopies,
        curIdx:    0   // index within values[]
    };
    return drum;
}

/* setDrumIndex — instantly position drum to show values[idx] in center */
function setDrumIndex(key, idx, animate) {
    var d = ssDrums[key];
    if (!d) return;
    d.curIdx = ((idx % d.values.length) + d.values.length) % d.values.length;

    // Center copy is copy index 1 (0-based)
    var centerCopyOffset = d.values.length;   // start of copy 1
    var targetRow = centerCopyOffset + d.curIdx;

    // We want that row to be in the center of the visible window
    // top of strip should be: -(targetRow - SS_GHOST) * cellH
    var translateY = -((targetRow - SS_GHOST) * d.cellH);

    updateDrumColors(key);

    d.strip.style.transition = animate
        ? 'transform 0.45s cubic-bezier(0.25,0.46,0.45,0.94)'
        : 'none';
    d.strip.style.transform = 'translateY(' + translateY + 'px)';
}

/* spinDrumTo — animate drum from its current position to newIdx */
function spinDrumTo(key, newIdx) {
    var d = ssDrums[key];
    if (!d) return;
    var len = d.values.length;
    newIdx = ((newIdx % len) + len) % len;
    if (newIdx === d.curIdx) return;

    // determine direction: always go forward (top→bottom = index increases)
    var steps = (newIdx - d.curIdx + len) % len;
    if (steps === 0) return;

    d.curIdx = newIdx;
    var centerCopyOffset = len;
    var targetRow = centerCopyOffset + d.curIdx;
    var translateY = -((targetRow - SS_GHOST) * d.cellH);

    d.strip.style.transition = 'transform 0.45s cubic-bezier(0.25,0.46,0.45,0.94)';
    d.strip.style.transform  = 'translateY(' + translateY + 'px)';

    // After animation, silently jump to center copy so we have room to spin again
    setTimeout(function() {
        updateDrumColors(key);
        // re-anchor without animation
        d.strip.style.transition = 'none';
        d.strip.style.transform  = 'translateY(' + translateY + 'px)';
    }, 460);

    updateDrumColors(key);
}

function updateDrumColors(key) {
    var d = ssDrums[key];
    if (!d) return;
    var len = d.values.length;
    var centerCopyOffset = len;
    var centerRow = centerCopyOffset + d.curIdx;

    d.cells.forEach(function(cell, i) {
        var dist = Math.abs(i - centerRow);
        if (dist === 0) {
            cell.style.color   = '#ffffff';
            cell.style.opacity = '1';
        } else if (dist === 1) {
            cell.style.color   = 'rgba(255,255,255,0.45)';
            cell.style.opacity = '1';
        } else if (dist === 2) {
            cell.style.color   = 'rgba(255,255,255,0.22)';
            cell.style.opacity = '1';
        } else if (dist === 3) {
            cell.style.color   = 'rgba(255,255,255,0.10)';
            cell.style.opacity = '1';
        } else {
            cell.style.color   = 'rgba(255,255,255,0.04)';
            cell.style.opacity = '0.5';
        }
    });
}

// ── Build DOM ──────────────────────────────────────────────

function buildSsClock() {
    var root = document.getElementById('kdSsClock');
    if (!root) return;
    root.innerHTML = '';
    ssDrums = {};
    var mode = kdSettings.screensaverMode || 'both';
    var DIGITS = ['0','1','2','3','4','5','6','7','8','9'];

    // ── TIME row ──
    if (mode === 'both' || mode === 'time') {
        var timeRow = document.createElement('div');
        timeRow.className = 'ss-row';
        timeRow.style.alignItems = 'center';

        // H0, H1 : 0-2 / 0-9
        timeRow.appendChild(makeDrum('h0', ['0','1','2'],        42, 70, 62));
        timeRow.appendChild(makeDrum('h1', DIGITS,               42, 70, 62));
        var s1 = document.createElement('div'); s1.className='ss-dsep'; s1.style.fontSize='50px'; s1.style.padding='0 4px'; s1.style.marginBottom='4px'; s1.textContent=':'; timeRow.appendChild(s1);
        timeRow.appendChild(makeDrum('m0', ['0','1','2','3','4','5'], 42, 70, 62));
        timeRow.appendChild(makeDrum('m1', DIGITS,               42, 70, 62));
        var s2 = document.createElement('div'); s2.className='ss-dsep'; s2.style.fontSize='50px'; s2.style.padding='0 4px'; s2.style.marginBottom='4px'; s2.textContent=':'; timeRow.appendChild(s2);
        timeRow.appendChild(makeDrum('s0', ['0','1','2','3','4','5'], 42, 70, 62));
        timeRow.appendChild(makeDrum('s1', DIGITS,               42, 70, 62));

        root.appendChild(timeRow);
    }

    // ── DATE row ──
    if (mode === 'both' || mode === 'date') {
        var dateRow = document.createElement('div');
        dateRow.className = 'ss-row';
        dateRow.style.alignItems = 'center';
        dateRow.style.gap = '10px';

        // Day number drums: D0 0-3, D1 0-9
        var dayWrap = document.createElement('div');
        dayWrap.className = 'ss-row';
        dayWrap.appendChild(makeDrum('d0', ['0','1','2','3'],    34, 52, 44));
        dayWrap.appendChild(makeDrum('d1', DIGITS,               34, 52, 44));
        dateRow.appendChild(dayWrap);

        // month name drum
        var months = ['январь','февраль','март','апрель','май','июнь',
                      'июль','август','сентябрь','октябрь','ноябрь','декабрь'];
        var mDrum = makeDrum('mon', months, 130, 52, 20, '600', {letterSpacing: '0.02em'});
        dateRow.appendChild(mDrum);

        // weekday drum
        var wdays = ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'];
        var wDrum = makeDrum('wday', wdays, 148, 52, 16, '500', {color: 'rgba(255,255,255,0.55)', letterSpacing:'0.01em'});
        dateRow.appendChild(wDrum);

        // year drum: show last 1-2 digits changing (we'll do full year as single value but spin it)
        var years = [];
        var nowY = new Date().getFullYear();
        for (var y = nowY - 3; y <= nowY + 3; y++) years.push(String(y));
        var yDrum = makeDrum('yr', years, 80, 52, 28, '600', {color:'rgba(255,255,255,0.45)'});
        dateRow.appendChild(yDrum);

        root.appendChild(dateRow);
    }

    // initial position without animation
    updateSsClock(true);
}

var ssPrevVals = {};

function updateSsClock(force) {
    var now  = new Date();
    var mode = kdSettings.screensaverMode || 'both';

    if (mode === 'both' || mode === 'time') {
        var hh = now.getHours(),   mm = now.getMinutes(), ss = now.getSeconds();
        var h0 = Math.floor(hh/10), h1 = hh%10;
        var m0 = Math.floor(mm/10), m1 = mm%10;
        var s0 = Math.floor(ss/10), s1 = ss%10;
        spinOrSet('h0', h0, force); spinOrSet('h1', h1, force);
        spinOrSet('m0', m0, force); spinOrSet('m1', m1, force);
        spinOrSet('s0', s0, force); spinOrSet('s1', s1, force);
    }

    if (mode === 'both' || mode === 'date') {
        var day   = now.getDate();
        var month = now.getMonth();   // 0-based
        var wday  = now.getDay();     // 0=Sun
        var year  = now.getFullYear();

        var d0 = Math.floor(day/10), d1 = day%10;
        spinOrSet('d0', d0, force);
        spinOrSet('d1', d1, force);
        spinOrSet('mon',  month, force);
        spinOrSet('wday', wday,  force);

        // find year index in drum values
        var yDrum = ssDrums['yr'];
        if (yDrum) {
            var yIdx = yDrum.values.indexOf(String(year));
            if (yIdx < 0) yIdx = Math.floor(yDrum.values.length / 2);
            spinOrSet('yr', yIdx, force);
        }
    }
}

function spinOrSet(key, idx, force) {
    if (!ssDrums[key]) return;
    if (force) {
        setDrumIndex(key, idx, false);
        ssPrevVals[key] = idx;
    } else if (idx !== ssPrevVals[key]) {
        spinDrumTo(key, idx);
        ssPrevVals[key] = idx;
    }
}


// ════════════════════════════════════════════
//  CONTEXT MENU
// ════════════════════════════════════════════
var ctxTarget = null;
var ctxMenu   = document.getElementById('ctxMenu');

function openCtxMenu(e, el) {
    e.preventDefault();
    e.stopPropagation();
    ctxTarget = el;
    var path   = el.getAttribute('data-path');
    var name   = el.getAttribute('data-name');
    var isDir  = el.getAttribute('data-isdir') === '1';
    var hasCb  = document.getElementById('kdClipboardState').getAttribute('data-has-cb') === 'true';

    document.getElementById('ctx-open').onclick    = function() { window.location.href = '?path=' + encodeURIComponent(path); closeCtxMenu(); };
    document.getElementById('ctx-copy').onclick    = function() { window.location.href = '?path=' + encodeURIComponent(path) + '&action=copy'; closeCtxMenu(); };
    document.getElementById('ctx-cut').onclick     = function() { window.location.href = '?path=' + encodeURIComponent(path) + '&action=cut'; closeCtxMenu(); };
    document.getElementById('ctx-paste').onclick   = function() { window.location.href = '?path=' + encodeURIComponent(path) + '&action=paste'; closeCtxMenu(); };
    document.getElementById('ctx-rename').onclick  = function() { openRenameModal(name, path); closeCtxMenu(); };
    document.getElementById('ctx-delete').onclick  = function() { if(confirm('В корзину «'+name+'»?')) window.location.href='?path='+encodeURIComponent(path)+'&action=delete'; closeCtxMenu(); };
    document.getElementById('ctx-download').onclick= function() { window.location.href='?path='+encodeURIComponent(path)+'&action=download'; closeCtxMenu(); };
    document.getElementById('ctx-zip').onclick     = function() { window.location.href='?path='+encodeURIComponent(path)+'&action=zip'; closeCtxMenu(); };

    // Show/hide items based on type
    document.getElementById('ctx-zip').style.display      = isDir ? 'flex' : 'none';
    document.getElementById('ctx-download').style.display = isDir ? 'none' : 'flex';
    document.getElementById('ctx-paste').classList.toggle('disabled', !hasCb);

    // Position
    var x = e.clientX, y = e.clientY;
    ctxMenu.style.left = (x + ctxMenu.offsetWidth + 10 > window.innerWidth ? x - ctxMenu.offsetWidth : x) + 'px';
    ctxMenu.style.top  = (y + 260 > window.innerHeight ? y - 200 : y) + 'px';
    ctxMenu.classList.add('show');
}

function closeCtxMenu() { ctxMenu && ctxMenu.classList.remove('show'); }
document.addEventListener('click',     function() { closeCtxMenu(); });
document.addEventListener('keydown',   function(e) { if (e.key==='Escape') closeCtxMenu(); });
document.addEventListener('contextmenu', function(e) {
    if (!e.target.closest('[data-path]') && !e.target.closest('#ctxMenu')) closeCtxMenu();
});

// ════════════════════════════════════════════
//  IMAGE HOVER PREVIEW
// ════════════════════════════════════════════
var imgPopup    = document.getElementById('imgPreviewPopup');
var imgPopupImg = document.getElementById('imgPreviewImg');
var imgTimer    = null;

document.addEventListener('mouseover', function(e) {
    var item = e.target.closest('[data-imgrel]');
    if (!item) return;
    var rel = item.getAttribute('data-imgrel');
    if (!rel) return;
    imgTimer = setTimeout(function() {
        imgPopupImg.src = rel;
        imgPopup.style.display = 'block';
    }, 350);
});
document.addEventListener('mouseout', function(e) {
    var item = e.target.closest('[data-imgrel]');
    if (!item) return;
    clearTimeout(imgTimer);
    imgPopup.style.display = 'none';
    imgPopupImg.src = '';
});
document.addEventListener('mousemove', function(e) {
    if (imgPopup.style.display === 'block') {
        var x = e.clientX + 16, y = e.clientY - 20;
        if (x + 280 > window.innerWidth)  x = e.clientX - 290;
        if (y + 220 > window.innerHeight) y = e.clientY - 220;
        imgPopup.style.left = x + 'px';
        imgPopup.style.top  = y + 'px';
    }
});

// ════════════════════════════════════════════
//  UNSAVED INDICATOR
// ════════════════════════════════════════════
function markUnsaved() {
    var tab = document.getElementById('currentTab');
    var dot = document.getElementById('unsavedDot');
    if (tab) tab.classList.add('unsaved');
    if (dot) dot.style.display = 'inline-block';
    var cl = document.getElementById('currentTabClose');
    if (cl) cl.style.display = 'none';
}
function markSaved() {
    var tab = document.getElementById('currentTab');
    var dot = document.getElementById('unsavedDot');
    if (tab) tab.classList.remove('unsaved');
    if (dot) dot.style.display = 'none';
    var cl = document.getElementById('currentTabClose');
    if (cl) cl.style.display = 'inline-flex';
}

// ════════════════════════════════════════════
//  SEARCH PANEL
// ════════════════════════════════════════════
var searchDebounce = null;

function openSearchPanel() {
    document.getElementById('searchPanel').classList.add('show');
    document.getElementById('searchInput').focus();
}
function closeSearchPanel() {
    document.getElementById('searchPanel').classList.remove('show');
}
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchDebounce);
    var q = this.value.trim();
    if (q.length < 1) { document.getElementById('searchResults').innerHTML = ''; return; }
    document.getElementById('searchResults').innerHTML = '<div class="search-no-results"><i class="fas fa-spinner fa-spin"></i></div>';
    searchDebounce = setTimeout(function() { doSearch(q); }, 280);
});
function doSearch(q) {
    fetch('?' + new URLSearchParams({ ajax: 'search', q: q }))
        .then(function(r) { return r.json(); })
        .then(function(items) {
            var res = document.getElementById('searchResults');
            if (!items.length) {
                res.innerHTML = '<div class="search-no-results">Ничего не найдено</div>';
                return;
            }
            res.innerHTML = '';
            items.forEach(function(item) {
                var div = document.createElement('div');
                div.className = 'search-result-item';
                div.innerHTML = '<i class="fas ' + (item.isDir ? 'fa-folder' : 'fa-file-code') + '"></i>' +
                    '<div><div class="search-result-name">' + escHtml(item.name) + '</div>' +
                    '<div class="search-result-path">/' + escHtml(item.path) + '</div></div>';
                div.addEventListener('click', function() {
                    window.location.href = '?path=' + encodeURIComponent(item.path);
                });
                res.appendChild(div);
            });
        });
}
function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// ════════════════════════════════════════════
//  TRASH
// ════════════════════════════════════════════
function openTrash() {
    showModal('trashModal');
    fetch('?ajax=trash_list')
        .then(function(r) { return r.json(); })
        .then(function(items) {
            var el = document.getElementById('trashList');
            if (!items.length) {
                el.innerHTML = '<div style="padding:30px;text-align:center;color:var(--vsc-fg-dim);font-size:13px;"><i class="fas fa-check-circle" style="color:#98c379;margin-right:8px;"></i>Корзина пуста</div>';
                return;
            }
            el.innerHTML = '';
            items.forEach(function(item) {
                var div = document.createElement('div');
                div.className = 'trash-item';
                div.innerHTML =
                    '<span class="trash-item-icon"><i class="fas ' + (item.isDir ? 'fa-folder' : 'fa-file') + '"></i></span>' +
                    '<div class="trash-item-info">' +
                        '<div class="trash-item-name">' + escHtml(item.origName) + '</div>' +
                        '<div class="trash-item-meta">/' + escHtml(item.origPath) + ' &nbsp;·&nbsp; ' + escHtml(item.deleted) + (item.isDir ? '' : ' &nbsp;·&nbsp; ' + formatSize(item.size)) + '</div>' +
                    '</div>' +
                    '<div class="trash-item-btns">' +
                        '<button class="btn btn-secondary" style="padding:3px 10px;font-size:11px;" onclick="trashPreview(&quot;' + escHtml(item.trashName) + '&quot;,&quot;' + escHtml(item.origName) + '&quot;)"><i class="fas fa-eye"></i></button>' +
                        '<button class="btn btn-secondary" style="padding:3px 10px;font-size:11px;" onclick="trashRestore(&quot;' + escHtml(item.trashName) + '&quot;)"><i class="fas fa-undo"></i> Восстановить</button>' +
                        '<button class="btn btn-danger" style="padding:3px 10px;font-size:11px;" onclick="trashDeletePerm(&quot;' + escHtml(item.trashName) + '&quot;)"><i class="fas fa-times"></i></button>' +
                    '</div>';
                el.appendChild(div);
            });
        });
}
function trashRestore(tn) {
    window.location.href = '?path=&action=trash_restore&tn=' + encodeURIComponent(tn);
}
function trashDeletePerm(tn) {
    if (confirm('Удалить навсегда?')) window.location.href = '?path=&action=trash_delete&tn=' + encodeURIComponent(tn);
}
function trashEmpty() {
    if (confirm('Очистить корзину? Это действие необратимо!')) window.location.href = '?path=&action=trash_empty';
}
function formatSize(b) {
    if (b < 1024) return b + ' B';
    if (b < 1048576) return (b/1024).toFixed(1) + ' KB';
    return (b/1048576).toFixed(1) + ' MB';
}

function trashPreview(trashName, origName) {
    document.getElementById('trashPreviewTitle').textContent = origName;
    var container = document.getElementById('trashPreviewContainer');
    container.innerHTML = '<div style="padding:30px;text-align:center;color:var(--vsc-fg-dim);"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
    showModal('trashPreviewModal');
    fetch('?' + new URLSearchParams({ ajax: 'trash_view', tn: trashName }))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.error) {
                container.innerHTML = '<div style="padding:30px;text-align:center;color:#e06c75;">' + escHtml(data.error) + '</div>';
                return;
            }
            if (data.type === 'image') {
                container.style.cssText = 'display:flex;align-items:center;justify-content:center;padding:16px;min-height:200px;';
                container.innerHTML = '<img src="' + data.src + '" style="max-width:100%;max-height:60vh;object-fit:contain;border-radius:4px;">';
            } else if (data.type === 'text') {
                container.style.cssText = 'flex:1;overflow:hidden;';
                container.innerHTML = '<div id="trashPreviewEditor" style="width:100%;height:60vh;"></div>';
                require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }});
                require(['vs/editor/editor.main'], function() {
                    var langMap = {
                        php:'php',js:'javascript',ts:'typescript',html:'html',css:'css',
                        json:'json',lua:'lua',py:'python',sh:'shell',sql:'sql',
                        md:'markdown',xml:'xml',yaml:'yaml',yml:'yaml',txt:'plaintext',log:'plaintext'
                    };
                    monaco.editor.create(document.getElementById('trashPreviewEditor'), {
                        value: data.content,
                        language: langMap[data.ext] || 'plaintext',
                        theme: kdSettings.lightTheme ? 'vs' : 'vs-dark',
                        readOnly: true,
                        automaticLayout: true,
                        minimap: { enabled: false },
                        fontSize: 13,
                        scrollBeyondLastLine: false,
                        wordWrap: 'on',
                        padding: { top: 10 }
                    });
                });
            } else {
                container.style.cssText = 'padding:30px;text-align:center;';
                container.innerHTML =
                    '<i class="fas fa-file-archive fa-4x" style="color:var(--vsc-fg-dim);margin-bottom:16px;display:block;"></i>' +
                    '<div style="color:var(--vsc-fg);font-size:14px;">Бинарный файл — просмотр недоступен</div>' +
                    '<div style="color:var(--vsc-fg-dim);font-size:12px;margin-top:8px;">.' + escHtml(data.ext) + ' · ' + formatSize(data.size) + '</div>';
            }
        })
        .catch(function() {
            container.innerHTML = '<div style="padding:30px;text-align:center;color:#e06c75;">Ошибка загрузки</div>';
        });
}

// ════════════════════════════════════════════
//  LOG
// ════════════════════════════════════════════
var logActionLabels = {
    trash:'В корзину', restore:'Восстановлен', perm_delete:'Удалён навсегда',
    copy:'Копирован', move:'Перемещён', zip:'ZIP', trash_empty:'Корзина очищена',
    create:'Создан', rename:'Переименован', save:'Сохранён', upload:'Загружен'
};
function openLog() {
    showModal('logModal');
    fetch('?ajax=log')
        .then(function(r) { return r.json(); })
        .then(function(items) {
            var el = document.getElementById('logList');
            if (!items.length) {
                el.innerHTML = '<div style="padding:30px;text-align:center;color:var(--vsc-fg-dim);font-size:13px;">Лог пуст</div>';
                return;
            }
            el.innerHTML = '';
            items.forEach(function(item) {
                var div = document.createElement('div');
                div.className = 'log-item';
                div.innerHTML =
                    '<span class="log-time">' + escHtml(item.time) + '</span>' +
                    '<span class="log-action ' + escHtml(item.action) + '">' + (logActionLabels[item.action] || item.action) + '</span>' +
                    '<span class="log-target">/' + escHtml(item.target) + (item.extra ? ' → ' + escHtml(item.extra) : '') + '</span>';
                el.appendChild(div);
            });
        });
}
function logClear() {
    if (!confirm('Очистить лог?')) return;
    fetch('?ajax=log_clear').then(function() { openLog(); });
}

// ════════════════════════════════════════════
//  CLIPBOARD BANNER
// ════════════════════════════════════════════
(function() {
    var stateEl = document.getElementById('kdClipboardState');
    if (!stateEl || stateEl.getAttribute('data-has-cb') !== 'true') return;
    var cbRaw = stateEl.getAttribute('data-cb');
    if (!cbRaw) return;
    try {
        var cb = JSON.parse(cbRaw);
        var banner = document.getElementById('clipboardBanner');
        var text   = document.getElementById('clipboardBannerText');
        if (banner && cb) {
            text.textContent = (cb.action === 'copy' ? 'Скопировано' : 'Вырезано') + ': ' + (cb.path || '').split('/').pop();
            banner.classList.add('show');
        }
    } catch(e) {}
})();
function clearClipboard() {
    document.getElementById('clipboardBanner').classList.remove('show');
    fetch('?ajax=clear_clipboard');
}

// ════════════════════════════════════════════
//  NOTEPAD TABS
// ════════════════════════════════════════════
var notepadTabs   = [{ name: 'Без имени 1', content: '', lang: 'plaintext' }];
var notepadActive = 0;

function renderNotepadTabs() {
    var tabsEl = document.getElementById('notepadTabs');
    tabsEl.innerHTML = '';
    notepadTabs.forEach(function(tab, i) {
        var div = document.createElement('div');
        div.className = 'notepad-tab' + (i === notepadActive ? ' active' : '');
        div.setAttribute('data-idx', i);
        div.innerHTML = '<span class="notepad-tab-name">' + escHtml(tab.name) + '</span>' +
            '<span class="notepad-tab-close"><i class="fas fa-times"></i></span>';
        div.querySelector('.notepad-tab-name').addEventListener('click', function() { switchNotepadTab(i); });
        div.querySelector('.notepad-tab-close').addEventListener('click', function(e) { e.stopPropagation(); closeNotepadTab(i); });
        tabsEl.appendChild(div);
    });
    var add = document.createElement('div');
    add.className = 'notepad-tab-add';
    add.title = 'Новая вкладка';
    add.textContent = '+';
    add.onclick = addNotepadTab;
    tabsEl.appendChild(add);
}

function switchNotepadTab(i) {
    if (!notepadEditor) return;
    // Save current content
    notepadTabs[notepadActive].content = notepadEditor.getValue();
    notepadTabs[notepadActive].lang    = document.getElementById('notepadLang').value;
    notepadActive = i;
    notepadEditor.setValue(notepadTabs[i].content);
    monaco.editor.setModelLanguage(notepadEditor.getModel(), notepadTabs[i].lang);
    document.getElementById('notepadLang').value = notepadTabs[i].lang;
    document.getElementById('notepadFilename').value = notepadTabs[i].name.match(/\./) ? notepadTabs[i].name : '';
    // Restore draft
    var draft = localStorage.getItem('kd_notepad_draft_' + i);
    if (draft && !notepadTabs[i].content) notepadEditor.setValue(draft);
    renderNotepadTabs();
}

function addNotepadTab() {
    if (notepadEditor) notepadTabs[notepadActive].content = notepadEditor.getValue();
    notepadTabs.push({ name: 'Без имени ' + (notepadTabs.length + 1), content: '', lang: 'plaintext' });
    notepadActive = notepadTabs.length - 1;
    renderNotepadTabs();
    if (notepadEditor) {
        notepadEditor.setValue('');
        monaco.editor.setModelLanguage(notepadEditor.getModel(), 'plaintext');
        document.getElementById('notepadLang').value = 'plaintext';
        document.getElementById('notepadFilename').value = '';
    }
}

function closeNotepadTab(i) {
    if (notepadTabs.length === 1) { if (notepadEditor) notepadEditor.setValue(''); return; }
    notepadTabs.splice(i, 1);
    localStorage.removeItem('kd_notepad_draft_' + i);
    notepadActive = Math.min(notepadActive, notepadTabs.length - 1);
    renderNotepadTabs();
    if (notepadEditor) notepadEditor.setValue(notepadTabs[notepadActive].content || '');
}

// Autosave draft every 3s
setInterval(function() {
    if (notepadEditor && document.getElementById('notepadModal').classList.contains('show')) {
        localStorage.setItem('kd_notepad_draft_' + notepadActive, notepadEditor.getValue());
        notepadTabs[notepadActive].content = notepadEditor.getValue();
    }
}, 3000);

// ── Update tab name from filename input ──
document.getElementById('notepadFilename').addEventListener('input', function() {
    var fn = this.value.trim();
    if (fn) notepadTabs[notepadActive].name = fn;
    renderNotepadTabs();
});


// ════════════════════════════════════════════
//  DRAG & DROP UPLOAD
// ════════════════════════════════════════════
(function() {
    var body = document.body;
    var overlay = document.createElement('div');
    overlay.id = 'dropOverlay';
    overlay.style.cssText = 'display:none;position:fixed;inset:0;z-index:7000;background:rgba(0,122,204,0.18);border:3px dashed var(--vsc-accent);pointer-events:none;align-items:center;justify-content:center;';
    overlay.innerHTML = '<div style="text-align:center;color:#fff;font-size:22px;pointer-events:none;text-shadow:0 2px 8px #000;"><i class="fas fa-cloud-upload-alt" style="font-size:48px;display:block;margin-bottom:14px;"></i>Отпустите для загрузки</div>';
    document.body.appendChild(overlay);

    var dragCount = 0;
    body.addEventListener('dragenter', function(e) {
        if (!e.dataTransfer.types.includes('Files')) return;
        dragCount++;
        overlay.style.display = 'flex';
    });
    body.addEventListener('dragleave', function(e) {
        dragCount--;
        if (dragCount <= 0) { dragCount = 0; overlay.style.display = 'none'; }
    });
    body.addEventListener('dragover', function(e) { e.preventDefault(); });
    body.addEventListener('drop', function(e) {
        e.preventDefault();
        dragCount = 0;
        overlay.style.display = 'none';
        var files = e.dataTransfer.files;
        if (!files.length) return;
        uploadFiles(files);
    });
})();

function uploadFiles(files) {
    var currentPath = new URLSearchParams(window.location.search).get('path') || '';
    var total = files.length;
    var done  = 0;
    showNotification('Загрузка ' + total + ' файл(ов)...', 'info');
    Array.from(files).forEach(function(file) {
        var fd = new FormData();
        fd.append('upload_file', file);
        fd.append('upload_path', currentPath);
        fetch(window.location.href, { method: 'POST', body: fd })
            .then(function() {
                done++;
                if (done === total) {
                    showNotification(total + ' файл(ов) загружено', 'success');
                    setTimeout(function() { window.location.reload(); }, 800);
                }
            })
            .catch(function() {
                showNotification('Ошибка загрузки: ' + file.name, 'error');
            });
    });
}

document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.shiftKey && e.key === 'F') { e.preventDefault(); openSearchPanel(); return; }
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