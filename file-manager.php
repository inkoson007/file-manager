<?php
session_start();

// ╔══════════════════════════════════════════════════════════════╗
// ║               KOFFEE DEVELOPER — НАСТРОЙКИ                  ║
// ╠══════════════════════════════════════════════════════════════╣
// ║  Авторизация                                                 ║
define('AUTH_ENABLED',  false);              // ← false = отключить авторизацию
define('AUTH_LOGIN',    'Логин');           // ← логин
define('AUTH_PASSWORD', 'Пароль');         // ← пароль
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
define('VERSION', '3.0');
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

        case 'ota_proxy':
            $ota_url = 'http://koffepro12.temp.swtest.ru/version.php';
            $json = false;
            if (function_exists('curl_init')) {
                $ch = curl_init($ota_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 8);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $json = curl_exec($ch);
                if (curl_errno($ch)) $json = false;
                curl_close($ch);
            }
            if ($json === false) {
                $ctx = stream_context_create(['http' => ['timeout' => 8, 'ignore_errors' => true]]);
                $json = @file_get_contents($ota_url, false, $ctx);
            }
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store');
            if ($json === false || $json === '') {
                http_response_code(503);
                echo '{}';
            } else {
                echo $json;
            }
            exit;

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
        /* ── Mobile bottom nav (переключение вкладок/меню на мобильных) ── */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            left: 0; right: 0; bottom: 0;
            z-index: 600;
            background: var(--vsc-titlebar);
            border-top: 1px solid var(--vsc-border);
            transition: transform 0.25s ease;
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        .mobile-bottom-nav.collapsed { transform: translateY(calc(100% - 16px)); }
        .mobile-bottom-nav.force-hidden { display: none !important; }
        .mbn-handle {
            position: absolute;
            top: -16px; left: 50%;
            transform: translateX(-50%);
            width: 52px; height: 16px;
            background: var(--vsc-titlebar);
            border: 1px solid var(--vsc-border);
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            display: flex; align-items: center; justify-content: center;
            color: var(--vsc-fg-dim);
            font-size: 10px;
            cursor: pointer;
        }
        .mobile-bottom-nav.collapsed .mbn-handle i { transform: rotate(180deg); }
        .mbn-scroll {
            display: flex;
            align-items: center;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            height: 58px;
            padding: 0 4px;
        }
        .mbn-scroll::-webkit-scrollbar { display: none; }
        .mbn-btn {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            width: 60px;
            height: 100%;
            background: none;
            border: none;
            color: var(--vsc-fg-dim);
            font-size: 16px;
            text-decoration: none;
        }
        .mbn-btn span { font-size: 9px; line-height: 1; white-space: nowrap; }
        .mbn-btn.active { color: var(--vsc-accent); }
        .mbn-btn:active { opacity: 0.6; }
        .mbn-sep { width: 1px; height: 26px; background: var(--vsc-border); flex-shrink: 0; margin: 0 5px; }
        @media (max-width: 767px) {
            .mobile-menu-btn { display: flex !important; }
            .activity-bar { display: none !important; }
            .mobile-bottom-nav { display: block; }
            /* mac-window takes full screen, no left margin */
            .mac-window {
                margin-left: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                position: fixed !important;
                top: 0; left: 0;
                padding-bottom: 58px;
            }
            .mac-window.mbn-collapsed { padding-bottom: 16px; }
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
            /* Sidebar flies in from left, out of normal flow — full-screen file manager on mobile */
            .sidebar {
                position: fixed !important;
                left: -100% !important;
                top: 0 !important; bottom: 0 !important;
                width: 100% !important; min-width: 0 !important;
                max-width: none;
                z-index: 1001;
                transition: left 0.25s ease;
                box-shadow: none;
            }
            .sidebar.active  { left: 0 !important; box-shadow: 6px 0 28px rgba(0,0,0,0.6); }
            /* На мобильных видимость управляется только классом .active — .hidden (десктопный) здесь не используется,
               чтобы не конфликтовать по приоритету CSS с .active */
            /* На сенсорных экранах нет :hover — кнопки переименовать/удалить должны быть видны всегда */
            .file-item-actions { opacity: 1 !important; }
            .file-item { padding-right: 4px; }
            .file-meta { opacity: var(--meta-opacity-hover, 1) !important; }
            .tab-close { opacity: 0.6 !important; }
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
            position: relative;
            overflow: hidden;
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
            user-select: none;
            -webkit-user-drag: none;
            transform-origin: center center;
            cursor: zoom-in;
        }
        .image-viewer-controls {
            position: absolute;
            bottom: 16px; left: 50%;
            transform: translateX(-50%);
            display: flex; align-items: center; gap: 6px;
            background: rgba(20,20,22,0.85);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            padding: 6px 8px;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 5;
            opacity: 0;
            transition: opacity 0.2s;
            pointer-events: none;
        }
        .image-preview-wrap:hover .image-viewer-controls { opacity: 1; pointer-events: all; }
        .image-viewer-btn {
            width: 26px; height: 26px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none; color: #ddd;
            border-radius: 50%; cursor: pointer; font-size: 11px;
            transition: background 0.15s;
        }
        .image-viewer-btn:hover { background: rgba(255,255,255,0.15); }
        .image-viewer-zoom-label {
            font-size: 11px; color: #ccc; min-width: 38px; text-align: center;
            font-variant-numeric: tabular-nums;
        }
        @media (max-width: 767px) {
            .image-viewer-controls { opacity: 1; pointer-events: all; }
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
        /* Окно подтверждения — поверх всего */
        #confirmModal { z-index: 99999 !important; }
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
        .btn-danger {
            background: var(--vsc-danger, #e06c75); color: white;
            border-color: var(--vsc-danger, #e06c75);
        }
        .btn-danger:hover { background: #f47171; }

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
        body.light-theme .notification       { background: #f3f3f3; color: #1e1e1e; }
        body.light-theme .clipboard-banner   { background: #ffffff; color: #1e1e1e; }
        body.light-theme .clipboard-banner button { color: #777; }
        body.light-theme .clipboard-banner button:hover { color: #000; }
        body.light-theme .rename-form-container { background: #ffffff; }
        body.light-theme .rename-input       { background: #fff; color: #1e1e1e; }

        /* ── Black theme (полностью чёрная, OLED-стиль) ── */
        body.black-theme {
            --vsc-bg:           #000000;
            --vsc-sidebar:      #000000;
            --vsc-sidebar-item: #0d0d0d;
            --vsc-panel:        #000000;
            --vsc-titlebar:     #000000;
            --vsc-tabbar:       #000000;
            --vsc-tab-active:   #000000;
            --vsc-tab-inactive: #0a0a0a;
            --vsc-tab-border:   #3b8eff;
            --vsc-statusbar:    #000000;
            --vsc-statusbar-fg: #cfcfcf;
            --vsc-accent:       #3b8eff;
            --vsc-accent-hover: #62a6ff;
            --vsc-fg:           #d4d4d4;
            --vsc-fg-dim:       #6b6b6b;
            --vsc-fg-bright:    #ffffff;
            --vsc-border:       #1a1a1a;
            --vsc-border-light: #242424;
            --vsc-hover:        rgba(255,255,255,0.05);
            --vsc-selected:     rgba(59,142,255,0.18);
            --vsc-folder:       #dcb67a;
            background-color: #000000;
        }
        body.black-theme .mac-window          { background: #000000; }
        body.black-theme .activity-bar        { background: #000000; border-right: 1px solid #1a1a1a; }
        body.black-theme .sidebar             { background: #000000; border-right: 1px solid #1a1a1a; }
        body.black-theme .sidebar-section-header { background: #000000; color: #6b6b6b; }
        body.black-theme .file-item          { color: #d4d4d4; }
        body.black-theme .file-item:hover    { background: rgba(255,255,255,0.05); }
        body.black-theme .file-item.active   { background: rgba(59,142,255,0.18); }
        body.black-theme .tab-bar            { background: #000000; border-color: #1a1a1a; }
        body.black-theme .tab                { background: #0a0a0a; color: #757575; border-color: #1a1a1a; }
        body.black-theme .tab.active         { background: #000000; color: #fff; border-top-color: #3b8eff; }
        body.black-theme .mac-title-bar      { background: #000000; border-color: #1a1a1a; }
        body.black-theme .mac-title          { color: #6b6b6b; }
        body.black-theme .file-toolbar       { background: #000000; border-color: #1a1a1a; }
        body.black-theme .toolbar-btn        { color: #d4d4d4; }
        body.black-theme .toolbar-btn:hover  { background: rgba(255,255,255,0.06); color: #fff; }
        body.black-theme .editor-breadcrumb-bar { background: #000000; border-color: #1a1a1a; }
        body.black-theme .editor-breadcrumb  { color: #6b6b6b; }
        body.black-theme .status-bar         { background: #000000; border-top: 1px solid #1a1a1a; color: #cfcfcf; }
        body.black-theme .modal-content      { background: #000000; border-color: #1f1f1f; }
        body.black-theme .modal-header       { background: #000000; border-color: #1a1a1a; color: #ffffff; }
        body.black-theme .modal-footer       { background: #000000; border-color: #1a1a1a; }
        body.black-theme .modal-close        { color: #6b6b6b; }
        body.black-theme .modal-close:hover  { color: #fff; background: rgba(255,255,255,0.08); }
        body.black-theme input,
        body.black-theme select,
        body.black-theme textarea            { background: #0a0a0a; color: #e6e6e6; border-color: #2a2a2a; }
        body.black-theme .notepad-toolbar,
        body.black-theme .notepad-save-section { background: #000000; border-color: #1a1a1a; }
        body.black-theme .notepad-lang-select,
        body.black-theme .notepad-filename-input { background: #0a0a0a; color: #e6e6e6; }
        body.black-theme .notepad-tabs       { background: #000000; border-color: #1a1a1a; }
        body.black-theme .notepad-tab        { color: #6b6b6b; }
        body.black-theme .notepad-tab.active { color: #fff; border-bottom-color: #3b8eff; }
        body.black-theme .cmd-palette-overlay { background: rgba(0,0,0,0.78); }
        body.black-theme .cmd-palette        { background: #050505; border-color: #1f1f1f; }
        body.black-theme .cmd-input          { background: #0a0a0a; color: #e6e6e6; border-color: #2a2a2a; }
        body.black-theme .cmd-item           { color: #d4d4d4; }
        body.black-theme .cmd-item:hover     { background: rgba(59,142,255,0.16); }
        body.black-theme .file-preview       { background: #000000; }
        body.black-theme .sidebar-breadcrumbs { background: #000000; border-color: #1a1a1a; }
        body.black-theme .sidebar-breadcrumb  { color: #6b6b6b; }
        body.black-theme #ctxMenu            { background: #050505; border-color: #1f1f1f; }
        body.black-theme .ctx-item           { color: #d4d4d4; }
        body.black-theme .ctx-item:hover     { background: #3b8eff; color: #fff; }
        body.black-theme .ctx-sep            { border-color: #1f1f1f; }
        body.black-theme .trash-item:hover,
        body.black-theme .log-item:hover     { background: rgba(255,255,255,0.04); }
        body.black-theme .cl-entry           { background: #050505; }
        body.black-theme .cl-entry-ver,
        body.black-theme .cl-entry-name      { color: #e6e6e6; }
        body.black-theme .search-input-wrap input { background: #0a0a0a; color: #e6e6e6; }
        body.black-theme .search-result-item:hover { background: rgba(255,255,255,0.05); }
        body.black-theme .empty-state        { color: #6b6b6b; }
        body.black-theme .kbd                { background: #0a0a0a; border-color: #2a2a2a; box-shadow: 0 1px 0 #000; }
        body.black-theme .notification       { background: #0a0a0a; color: #e6e6e6; }
        body.black-theme .clipboard-banner   { background: #0a0a0a; color: #e6e6e6; }
        body.black-theme .clipboard-banner button { color: #777; }
        body.black-theme .clipboard-banner button:hover { color: #fff; }
        body.black-theme .rename-form-container { background: #000000; }
        body.black-theme .rename-input       { background: #0a0a0a; color: #e6e6e6; }

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
            overflow: hidden;
            background: #060608;
        }
        /* floating colored aura blobs in the background */
        #kdSsAura {
            position: absolute;
            inset: 0;
            overflow: hidden;
            z-index: 0;
        }
        .ss-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            opacity: 0.5;
            will-change: transform;
        }
        .ss-orb-1 {
            width: 460px; height: 460px;
            background: radial-gradient(circle, #3b8eff 0%, transparent 72%);
            top: -12%; left: -10%;
            animation: ssOrbFloat1 24s ease-in-out infinite alternate;
        }
        .ss-orb-2 {
            width: 380px; height: 380px;
            background: radial-gradient(circle, #8a5cf6 0%, transparent 72%);
            bottom: -14%; right: -8%;
            animation: ssOrbFloat2 28s ease-in-out infinite alternate;
        }
        .ss-orb-3 {
            width: 320px; height: 320px;
            background: radial-gradient(circle, #2bd4c8 0%, transparent 72%);
            top: 38%; right: 18%;
            animation: ssOrbFloat3 32s ease-in-out infinite alternate;
        }
        @keyframes ssOrbFloat1 { from { transform: translate(0,0) scale(1); }    to { transform: translate(70px, 50px) scale(1.18); } }
        @keyframes ssOrbFloat2 { from { transform: translate(0,0) scale(1); }    to { transform: translate(-60px,-70px) scale(1.12); } }
        @keyframes ssOrbFloat3 { from { transform: translate(0,0) scale(1); }    to { transform: translate(-50px, 60px) scale(0.88); } }
        #kdSsBlur {
            position: absolute;
            inset: 0;
            z-index: 1;
            background: rgba(8,8,10,0.66);
            backdrop-filter: blur(24px) saturate(0.7);
            -webkit-backdrop-filter: blur(24px) saturate(0.7);
            animation: ssFadeIn 0.8s ease both;
        }
        #kdSsContent {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: ssFadeIn 1s ease both;
        }
        @keyframes ssFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        #kdSsInner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 26px;
        }
        #kdSsGreeting {
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-size: 22px;
            font-weight: 300;
            letter-spacing: 0.06em;
            color: rgba(255,255,255,0.72);
            text-shadow: 0 0 24px rgba(255,255,255,0.15);
            animation: ssFadeInUp 1.1s ease both;
        }
        @keyframes ssFadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        #kdSsClock {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
            user-select: none;
        }
        #kdSsClock::before {
            content: '';
            position: absolute;
            inset: -70px -120px;
            background: radial-gradient(circle, rgba(90,150,255,0.14) 0%, transparent 70%);
            z-index: -1;
            pointer-events: none;
        }
        #kdSsHint {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            letter-spacing: 0.03em;
            color: rgba(255,255,255,0.32);
            animation: ssHintPulse 2.6s ease-in-out infinite;
        }
        @keyframes ssHintPulse {
            0%, 100% { opacity: 0.28; }
            50%      { opacity: 0.65; }
        }
        @media (max-width: 767px) {
            #kdSsGreeting { font-size: 16px; }
            .ss-orb { filter: blur(50px); }
        }
        /* ── drum row ── */
        .ss-row {
            display: flex;
            align-items: center;
            gap: 0;
        }
        /* glass panel that groups the time drums together (HH:MM:SS) */
        .ss-time-panel {
            position: relative;
            gap: 3px;
            padding: 26px 30px;
            border-radius: 22px;
            background: linear-gradient(165deg, rgba(255,255,255,0.07) 0%, rgba(255,255,255,0.015) 55%, rgba(0,0,0,0.1) 100%);
            border: 1px solid rgba(255,255,255,0.09);
            box-shadow:
                0 1px 0 rgba(255,255,255,0.08) inset,
                0 30px 70px rgba(0,0,0,0.55),
                0 0 0 1px rgba(0,0,0,0.25);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            animation: ssPanelGlow 5s ease-in-out infinite;
        }
        .ss-time-panel::after {
            content: '';
            position: absolute;
            left: 14%; right: 14%; bottom: -18px; height: 36px;
            background: radial-gradient(ellipse at center, rgba(59,142,255,0.4), transparent 72%);
            filter: blur(10px);
            z-index: -1;
            pointer-events: none;
        }
        @keyframes ssPanelGlow {
            0%, 100% { box-shadow: 0 1px 0 rgba(255,255,255,0.08) inset, 0 30px 70px rgba(0,0,0,0.55), 0 0 0 1px rgba(0,0,0,0.25), 0 0 34px rgba(59,142,255,0.10); }
            50%      { box-shadow: 0 1px 0 rgba(255,255,255,0.08) inset, 0 30px 70px rgba(0,0,0,0.55), 0 0 0 1px rgba(0,0,0,0.25), 0 0 50px rgba(59,142,255,0.22); }
        }
        /* lighter pill-style panel for the date row */
        .ss-date-panel {
            gap: 12px;
            padding: 12px 24px;
            border-radius: 999px;
            background: linear-gradient(165deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
            border: 1px solid rgba(255,255,255,0.07);
            box-shadow: 0 12px 30px rgba(0,0,0,0.4), 0 1px 0 rgba(255,255,255,0.06) inset;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }
        /* simple flip-digit cell — same mechanism as the small clock in the title bar, just bigger */
        .ss-digit {
            position: relative;
            overflow: hidden;
            margin: 0 2px;
            border-radius: 10px;
            background: linear-gradient(180deg, rgba(255,255,255,0.10) 0%, rgba(255,255,255,0.03) 12%, rgba(0,0,0,0.22) 50%, rgba(255,255,255,0.03) 88%, rgba(255,255,255,0.10) 100%);
            border: 1px solid rgba(255,255,255,0.10);
            box-shadow:
                0 1px 0 rgba(255,255,255,0.12) inset,
                0 -1px 0 rgba(0,0,0,0.45) inset,
                0 6px 16px rgba(0,0,0,0.45);
        }
        .ss-digit-inner {
            position: absolute;
            top: 0; left: 0; right: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            will-change: transform;
        }
        .ss-digit-inner span {
            display: block;
            width: 100%;
            text-align: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: #ffffff;
            text-shadow: 0 0 16px rgba(120,170,255,0.5), 0 0 3px rgba(255,255,255,0.35);
        }
        /* plain text (месяц / день недели) — мягко затухает при смене значения, без барабана */
        .ss-text {
            position: relative;
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-weight: 600;
            color: rgba(255,255,255,0.78);
            text-align: center;
            white-space: nowrap;
            transition: opacity 0.25s ease;
        }
        /* separator between digit groups (the glowing, blinking colon) */
        .ss-dsep {
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-weight: 700;
            color: #6cb0ff;
            text-shadow: 0 0 12px rgba(59,142,255,0.75), 0 0 26px rgba(59,142,255,0.4);
            align-self: center;
            line-height: 1;
            animation: ssColonBlink 2s steps(1) infinite;
        }
        @keyframes ssColonBlink {
            0%, 45%   { opacity: 1; }
            50%, 100% { opacity: 0.32; }
        }

        /* ── Settings Modal ── */
        #settingsModal .modal-content { width: 400px; max-width: 96vw; max-height: 85vh; display: flex; flex-direction: column; }
        #settingsModal .modal-body { flex: 1; overflow-y: auto; min-height: 0; }
        #settingsModal .modal-body::-webkit-scrollbar { width: 6px; }
        #settingsModal .modal-body::-webkit-scrollbar-thumb { background: var(--vsc-border-light); border-radius: 3px; }
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

        /* ── Constructor (визуальный превью PHP/HTML страницы) ── */
        .constructor-content {
            width: 96vw; max-width: 1500px;
            height: 92vh;
            display: flex; flex-direction: column;
            padding: 0;
        }
        .constructor-toolbar {
            display: flex; align-items: center; gap: 14px;
            padding: 8px 14px;
            background: var(--vsc-titlebar);
            border-bottom: 1px solid var(--vsc-border);
            flex-shrink: 0;
            flex-wrap: wrap;
        }
        .constructor-device-btns { display: flex; align-items: center; gap: 4px; }
        .constructor-device-btn {
            display: flex; align-items: center; gap: 6px;
            background: none; border: 1px solid var(--vsc-border-light);
            color: var(--vsc-fg-dim);
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .constructor-device-btn:hover { color: var(--vsc-fg); background: var(--vsc-hover); }
        .constructor-device-btn.active {
            background: var(--vsc-accent); color: #fff; border-color: var(--vsc-accent);
        }
        .constructor-resize-hint {
            color: var(--vsc-fg-dim); font-size: 12px; margin-left: 4px; opacity: 0.7;
            cursor: help;
        }
        .constructor-view-tabs { display: flex; align-items: center; gap: 2px; }
        .constructor-view-tab {
            background: none; border: none;
            color: var(--vsc-fg-dim);
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .constructor-view-tab:hover { color: var(--vsc-fg); background: var(--vsc-hover); }
        .constructor-view-tab.active { color: #fff; background: var(--vsc-accent); }
        .constructor-inspect-toggle {
            display: flex; align-items: center; gap: 7px;
            font-size: 12px; color: var(--vsc-fg-dim);
            cursor: pointer; user-select: none;
        }
        .constructor-inspect-toggle input { accent-color: var(--vsc-accent); cursor: pointer; }
        .constructor-inspect-toggle:has(input:checked) { color: var(--vsc-accent); }
        .constructor-toolbar-right {
            display: flex; align-items: center; gap: 6px;
            margin-left: auto;
        }
        .constructor-size-label {
            font-size: 11px; color: var(--vsc-fg-dim);
            font-variant-numeric: tabular-nums;
            margin-right: 4px;
            white-space: nowrap;
        }
        .constructor-body {
            flex: 1; display: flex; overflow: hidden;
            position: relative;
        }
        .constructor-frame-wrap {
            flex: 1;
            display: flex; align-items: center; justify-content: center;
            overflow: auto;
            padding: 18px;
            background: repeating-conic-gradient(var(--vsc-bg) 0% 25%, var(--vsc-titlebar) 0% 50%) 50% / 24px 24px;
        }
        /* контейнер, который можно тянуть за уголок мышью — нативный ресайз браузера */
        .constructor-resizable {
            position: relative;
            width: 100%; height: 100%;
            min-width: 240px; min-height: 240px;
            max-width: 100%; max-height: 100%;
            resize: both;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.15), 0 12px 40px rgba(0,0,0,0.35);
            transition: width 0.25s ease, height 0.25s ease, border-radius 0.25s ease;
        }
        .constructor-resizable.no-transition { transition: none; }
        #constructorFrame {
            background: #fff;
            border: none;
            width: 100%; height: 100%;
            display: block;
        }
        .constructor-frame-wrap.device-tablet .constructor-resizable {
            width: 768px; height: 1024px;
            border-radius: 14px;
        }
        .constructor-frame-wrap.device-mobile .constructor-resizable {
            width: 375px; height: 720px;
            border-radius: 24px;
        }
        .constructor-frame-wrap.device-desktop .constructor-resizable {
            width: 100%; height: 100%;
            border-radius: 0;
        }
        /* ── Code view tabs (HTML/CSS/JS) — Monaco editor, тот же дизайн что и в редакторе файлов ── */
        .constructor-code-wrap {
            flex: 1;
            min-width: 0;
            background: var(--vsc-bg);
        }
        .constructor-inspect-panel {
            width: 280px; flex-shrink: 0;
            background: var(--vsc-sidebar);
            border-left: 1px solid var(--vsc-border);
            padding: 14px;
            overflow-y: auto;
        }
        .constructor-inspect-header {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--vsc-fg-dim);
            margin: 16px 0 8px;
        }
        .constructor-inspect-header:first-child { margin-top: 0; }
        .constructor-inspect-empty {
            font-size: 12px; color: var(--vsc-fg-dim); font-style: italic;
        }
        .constructor-inspect-box {
            background: rgba(59,142,255,0.08);
            border: 1px solid rgba(59,142,255,0.25);
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 12px;
            line-height: 1.7;
            color: var(--vsc-fg);
            word-break: break-word;
        }
        .constructor-inspect-box .ci-tag { color: #e06c75; font-weight: 600; }
        .constructor-inspect-box .ci-id { color: #d19a66; }
        .constructor-inspect-box .ci-class { color: #61afef; }
        .constructor-click-hint {
            margin-top: 8px;
            font-size: 11px;
            color: var(--vsc-fg-dim);
            display: flex; align-items: center; gap: 6px;
        }
        .constructor-css-rules {
            display: flex; flex-direction: column; gap: 6px;
            max-height: 220px; overflow-y: auto;
        }
        .constructor-css-rule {
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 11px; line-height: 1.5;
            background: var(--vsc-sidebar-item);
            border: 1px solid var(--vsc-border-light);
            border-radius: 5px;
            padding: 6px 8px;
            color: #98c379;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .constructor-class-list { display: flex; flex-wrap: wrap; gap: 5px; }
        .constructor-class-chip {
            font-size: 11px;
            background: var(--vsc-sidebar-item);
            border: 1px solid var(--vsc-border-light);
            color: var(--vsc-fg);
            border-radius: 10px;
            padding: 2px 9px;
            cursor: pointer;
            transition: all 0.15s;
            font-family: 'Consolas', monospace;
        }
        .constructor-class-chip:hover { background: var(--vsc-accent); color: #fff; border-color: var(--vsc-accent); }
        .constructor-highlight-line { background: rgba(59,142,255,0.18); }
        @media (max-width: 767px) {
            .constructor-content { width: 100vw; height: 100vh; max-width: none; border-radius: 0; }
            .constructor-body { flex-direction: column; }
            .constructor-inspect-panel { width: 100%; max-height: 220px; border-left: none; border-top: 1px solid var(--vsc-border); }
            .constructor-toolbar { gap: 8px; }
            .constructor-device-btn span { display: none; }
            .constructor-resize-hint { display: none; }
            .constructor-frame-wrap.device-tablet .constructor-resizable,
            .constructor-frame-wrap.device-mobile .constructor-resizable { width: 100%; max-width: 100%; resize: none; }
        }
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
        <a href="?logout=1" class="activity-btn" title="Выйти из аккаунта" onclick="event.preventDefault();kdConfirm('Вы уверены, что хотите выйти из аккаунта?', function(){window.location.href='?logout=1';}, {title:'Выход из аккаунта', okText:'Выйти', danger:true});" style="color:#f47171;text-decoration:none;">
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
                           onclick="event.preventDefault();event.stopPropagation();var _h=this.href;kdConfirm('Переместить «<?= htmlspecialchars(addslashes($file['name'])) ?>» в корзину?', function(){window.location.href=_h;}, {title:'Удаление файла', okText:'В корзину', danger:true});">
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
                        <?php if (in_array(strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)), ['html','htm','php'])): ?>
                            <button class="toolbar-btn" onclick="openConstructor('?path=<?= urlencode($requestedPath) ?>&action=view', '<?= htmlspecialchars(addslashes(basename($absolutePath))) ?>')">
                                <i class="fas fa-drafting-compass"></i> Конструктор
                            </button>
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
                       onclick="event.preventDefault();var _h=this.href;kdConfirm('Переместить «<?= htmlspecialchars(addslashes(basename($absolutePath))) ?>» в корзину?', function(){window.location.href=_h;}, {title:'Удаление', okText:'В корзину', danger:true});">
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
                    <div class="image-preview-wrap" id="imageViewerWrap">
                        <img src="<?= htmlspecialchars($relPath) ?>" class="image-preview" id="imageViewerImg" alt="Preview" draggable="false">
                        <div class="image-viewer-controls">
                            <button class="image-viewer-btn" id="imageViewerZoomOutBtn" title="Уменьшить"><i class="fas fa-minus"></i></button>
                            <span class="image-viewer-zoom-label" id="imageViewerZoomLabel">100%</span>
                            <button class="image-viewer-btn" id="imageViewerZoomInBtn" title="Увеличить"><i class="fas fa-plus"></i></button>
                            <button class="image-viewer-btn" id="imageViewerResetBtn" title="Сбросить масштаб" style="display:none;"><i class="fas fa-compress-arrows-alt"></i></button>
                        </div>
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
    overflow-y: auto;
    padding: 14px 16px 0;
    gap: 0;
}
#versionModal .modal-body::-webkit-scrollbar { width: 6px; }
#versionModal .modal-body::-webkit-scrollbar-thumb { background: var(--vsc-border-light); border-radius: 3px; }
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
                        <div><span style="color:var(--vsc-fg-dim);width:72px;display:inline-block;">Сборка:</span> <span style="color:#ccc;">28.06.2026</span></div>
                    </div>
                </div>
                <div style="margin-top:10px;padding:8px 10px;background:rgba(0,122,204,0.07);border:1px solid rgba(0,122,204,0.18);border-radius:4px;font-size:11px;color:#666;line-height:1.7;">
                    <i class="fas fa-info-circle" style="color:var(--vsc-accent);margin-right:5px;"></i>
                    При модификации просьба указать автора — <strong style="color:#999;">INK / KOFFEE TEAM</strong>
                </div>
                <div style="margin-top:10px;">
                    <button class="btn btn-secondary" id="manualCheckUpdateBtn" onclick="manualCheckForUpdates()" style="width:100%;">
                        <i class="fas fa-sync-alt"></i> Проверить обновления
                    </button>
                    <div id="manualCheckUpdateStatus" style="font-size:11px;color:var(--vsc-fg-dim);margin-top:6px;text-align:center;"></div>
                </div>
            </div>

            <!-- Divider + label -->
            <div style="border-top:1px solid var(--vsc-border);padding-top:12px;margin-bottom:8px;" class="cl-label">Changelog</div>

            <!-- Current version — always expanded -->
            <div class="cl-entry current" style="margin-bottom:6px;flex-shrink:0;">
                <div class="cl-entry-head">
                    <span class="cl-entry-ver">v3.0</span>
                    <span style="font-size:11px;background:rgba(0,122,204,0.2);color:var(--vsc-accent);padding:1px 7px;border-radius:10px;font-weight:600;">latest</span>
                    <span class="cl-entry-date">28.06.2026</span>
                </div>
                <div class="cl-entry-body">
                    <ul>
                        <li>Полностью чёрная тема (OLED) — отдельная от светлой/тёмной, со своей темой для редактора кода</li>
                        <li>Снежинки на экране: вкл/выкл, интенсивность слабая/средняя/сильная/метель/авто-погода, разлетаются и закручиваются вокруг курсора</li>
                        <li>Новая заставка: приветствие по времени суток, анимированный фон, простые «флип»-часы в стиле часов из шапки окна, дата на русском</li>
                        <li>Мобильное нижнее меню для быстрого переключения между разделами + кнопка сворачивания</li>
                        <li>Полноценный файловый менеджер на мобильных — рабочий проводник на весь экран, исправлен поиск, кнопки переименовать/удалить всегда видны</li>
                        <li>Окна подтверждения в стиле сайта вместо системных confirm() — для удаления, выхода, очистки лога и т.д.</li>
                        <li>Прокрутка в окнах настроек и «О программе», чтобы кнопки всегда были видны</li>
                        <li>Режим «Конструктор» для PHP/HTML-файлов: живое превью страницы с переключением ПК/Планшет/Моб. и ручным изменением размера</li>
                        <li>В Конструкторе — просмотр исходного HTML, CSS и JS страницы в редакторе с номерами строк</li>
                        <li>Инспектор элементов: подсветка при наведении, список применяемых CSS-правил, окно с HTML/CSS/скриптами элемента по клику</li>
                        <li>Приближение и отдаление изображений колёсиком мыши с перетаскиванием и кнопками управления</li>
                        <li>Автоматическая проверка обновлений — уведомление о новой версии со списком изменений и ссылкой на GitHub</li>
                        <li>Исправления чёрной темы для окна «О программе», формы переименования и уведомлений</li>
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

<!-- ═══════ UPDATE AVAILABLE MODAL (автоматическая проверка версии) ═══════ -->
<div class="modal" id="updateAvailableModal">
    <div class="modal-content" style="width:440px;max-width:96vw;max-height:80vh;display:flex;flex-direction:column;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-cloud-download-alt" style="margin-right:8px;color:var(--vsc-accent)"></i>Доступно обновление</div>
            <button class="modal-close" onclick="dismissUpdateModal()">&times;</button>
        </div>
        <div class="modal-body" style="flex:1;overflow-y:auto;font-size:13px;color:var(--vsc-fg);">
            <div style="margin-bottom:14px;">
                Вышла новая версия <strong id="updateAvailableVersion" style="color:var(--vsc-accent);"></strong>
                <span style="color:var(--vsc-fg-dim);">— у вас установлена v<?= VERSION ?></span>
            </div>
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.06em;color:var(--vsc-fg-dim);margin-bottom:8px;">Что нового</div>
            <ul id="updateAvailableChanges" style="padding-left:18px;line-height:1.9;"></ul>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="dismissUpdateModal()">Напомнить позже</button>
            <a class="btn btn-primary" href="https://github.com/inkoson007/file-manager" target="_blank" rel="noopener" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fab fa-github"></i> Обновить
            </a>
        </div>
    </div>
</div>

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
                        Тема оформления
                        <small>Тёмная, светлая или полностью чёрная</small>
                    </div>
                    <select id="settingThemeMode" onchange="applySettings(true)" style="background:#3c3c3c;border:1px solid var(--vsc-border-light);border-radius:4px;color:#fff;font-size:12px;padding:4px 8px;outline:none;">
                        <option value="dark" selected>Тёмная</option>
                        <option value="light">Светлая</option>
                        <option value="black">Чёрная</option>
                    </select>
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
            <div class="settings-group">
                <div class="settings-group-label">Снежинки</div>
                <div class="settings-row">
                    <div class="settings-row-label">
                        Включить снежинки
                        <small>Падающий снег по всему экрану</small>
                    </div>
                    <label class="kd-switch">
                        <input type="checkbox" id="settingSnowEnabled" onchange="applySettings(true)">
                        <span class="kd-switch-slider"></span>
                    </label>
                </div>
                <div class="settings-row" id="snowIntensityRow" style="display:none;">
                    <div class="settings-row-label">
                        Интенсивность
                        <small>Количество снежинок</small>
                    </div>
                    <select id="settingSnowIntensity" onchange="applySettings(true)" style="background:#3c3c3c;border:1px solid var(--vsc-border-light);border-radius:4px;color:#fff;font-size:12px;padding:4px 8px;outline:none;">
                        <option value="light">Слабая</option>
                        <option value="normal" selected>Средняя</option>
                        <option value="heavy">Сильная</option>
                        <option value="blizzard">Метель</option>
                        <option value="auto">Авто (случайная погода)</option>
                    </select>
                </div>
            </div>
            <div class="settings-group">
                <div class="settings-group-label">Обновления</div>
                <div class="settings-row">
                    <div class="settings-row-label">
                        Проверять обновления автоматически
                        <small>Проверка версии при заходе и перезагрузке страницы</small>
                    </div>
                    <label class="kd-switch">
                        <input type="checkbox" id="settingAutoUpdateCheck" onchange="applySettings(true)" checked>
                        <span class="kd-switch-slider"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideModal('settingsModal')">Закрыть</button>
        </div>
    </div>
</div>

<!-- ═══════ CONFIRM MODAL (замена window.confirm) ═══════ -->
<div class="modal" id="constructorModal">
    <div class="modal-content constructor-content">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-drafting-compass"></i> Конструктор — <span id="constructorFileName"></span></div>
            <button class="modal-close" onclick="closeConstructor()">&times;</button>
        </div>
        <div class="constructor-toolbar">
            <div class="constructor-device-btns">
                <button class="constructor-device-btn active" data-device="desktop" onclick="setConstructorDevice('desktop')" title="ПК"><i class="fas fa-desktop"></i><span>ПК</span></button>
                <button class="constructor-device-btn" data-device="tablet" onclick="setConstructorDevice('tablet')" title="Планшет"><i class="fas fa-tablet-alt"></i><span>Планшет</span></button>
                <button class="constructor-device-btn" data-device="mobile" onclick="setConstructorDevice('mobile')" title="Мобильный"><i class="fas fa-mobile-alt"></i><span>Моб.</span></button>
                <span class="constructor-resize-hint" title="Тянуть за уголок превью, чтобы задать размер вручную"><i class="fas fa-arrows-alt"></i></span>
            </div>
            <div class="constructor-view-tabs" id="constructorViewTabs">
                <button class="constructor-view-tab active" data-view="preview" onclick="setConstructorView('preview')"><i class="fas fa-eye"></i> Превью</button>
                <button class="constructor-view-tab" data-view="html" onclick="setConstructorView('html')">HTML</button>
                <button class="constructor-view-tab" data-view="css" onclick="setConstructorView('css')">CSS</button>
                <button class="constructor-view-tab" data-view="js" onclick="setConstructorView('js')">JS</button>
            </div>
            <label class="constructor-inspect-toggle">
                <input type="checkbox" id="constructorInspectToggle" onchange="toggleConstructorInspect(this.checked)">
                <span><i class="fas fa-crosshairs"></i> Инспектор элементов</span>
            </label>
            <div class="constructor-toolbar-right">
                <span id="constructorViewportSize" class="constructor-size-label"></span>
                <button class="toolbar-btn" onclick="reloadConstructorFrame()" title="Обновить"><i class="fas fa-sync-alt"></i></button>
                <a id="constructorOpenNewTab" target="_blank" class="toolbar-btn" title="Открыть в новой вкладке"><i class="fas fa-external-link-alt"></i></a>
            </div>
        </div>
        <div class="constructor-body">
            <div class="constructor-frame-wrap" id="constructorFrameWrap">
                <div class="constructor-resizable" id="constructorResizable">
                    <iframe id="constructorFrame" src="about:blank" frameborder="0"></iframe>
                </div>
            </div>
            <div class="constructor-code-wrap" id="constructorCodeWrap" style="display:none;">
                <div id="constructorCodeEditor" style="width:100%;height:100%;"></div>
            </div>
            <div class="constructor-inspect-panel" id="constructorInspectPanel" style="display:none;">
                <div class="constructor-inspect-header">Выбранный элемент</div>
                <div id="constructorInspectInfo" class="constructor-inspect-empty">Наведите курсор на элемент на странице</div>
                <div class="constructor-inspect-header">Все классы на странице (<span id="constructorClassCount">0</span>)</div>
                <div id="constructorClassList" class="constructor-class-list"></div>
                <div class="constructor-inspect-header">Все ID на странице (<span id="constructorIdCount">0</span>)</div>
                <div id="constructorIdList" class="constructor-class-list"></div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════ ELEMENT DETAIL MODAL (по клику на элемент в инспекторе) ═══════ -->
<div class="modal" id="elementDetailModal">
    <div class="modal-content" style="width:740px;max-width:96vw;height:78vh;display:flex;flex-direction:column;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-code"></i> <span id="elementDetailTitle">Элемент</span></div>
            <button class="modal-close" onclick="hideModal('elementDetailModal')">&times;</button>
        </div>
        <div class="constructor-view-tabs" style="padding:8px 14px;border-bottom:1px solid var(--vsc-border);flex-shrink:0;">
            <button class="constructor-view-tab active" data-tab="html" onclick="setElementDetailTab('html')"><i class="fas fa-code"></i> HTML</button>
            <button class="constructor-view-tab" data-tab="css" onclick="setElementDetailTab('css')"><i class="fas fa-paint-brush"></i> CSS</button>
            <button class="constructor-view-tab" data-tab="js" onclick="setElementDetailTab('js')"><i class="fas fa-scroll"></i> Скрипты</button>
            <button class="toolbar-btn" style="margin-left:auto;" onclick="revealElementInCode(constructorCurrentElement)" title="Показать в коде страницы"><i class="fas fa-map-marker-alt"></i> Показать в коде</button>
        </div>
        <div id="elementDetailEditor" style="flex:1;min-height:0;"></div>
    </div>
</div>


<!-- ═══════ CONFIRM MODAL (замена window.confirm) ═══════ -->
<div class="modal" id="confirmModal">
    <div class="modal-content" style="width:380px;">
        <div class="modal-header">
            <div class="modal-title" id="confirmModalTitle"><i class="fas fa-exclamation-triangle" style="margin-right:8px;color:#e5c07b"></i>Подтверждение</div>
            <button class="modal-close" onclick="kdConfirmCancel()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="confirmModalMessage" style="font-size:13px;color:var(--vsc-fg);line-height:1.6;"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="kdConfirmCancel()">Отмена</button>
            <button class="btn btn-primary" id="confirmModalOkBtn" onclick="kdConfirmAccept()">Подтвердить</button>
        </div>
    </div>
</div>

<!-- ═══════ MOBILE BOTTOM NAV ═══════ -->
<div class="mobile-bottom-nav" id="mobileBottomNav">
    <button class="mbn-handle" id="mbnHandle" title="Скрыть/показать меню"><i class="fas fa-chevron-down"></i></button>
    <div class="mbn-scroll">
        <button class="mbn-btn active" id="mbnExplorerBtn" title="Проводник"><i class="fas fa-copy"></i><span>Файлы</span></button>
        <button class="mbn-btn" onclick="openSearchPanel()"><i class="fas fa-search"></i><span>Поиск</span></button>
        <button class="mbn-btn" onclick="showModal('uploadModal')"><i class="fas fa-upload"></i><span>Загрузить</span></button>
        <button class="mbn-btn" onclick="showModal('newFolderModal')"><i class="fas fa-folder-plus"></i><span>Папка</span></button>
        <button class="mbn-btn" onclick="showModal('newFileModal')"><i class="fas fa-file-medical"></i><span>Файл</span></button>
        <div class="mbn-sep"></div>
        <button class="mbn-btn" onclick="openNotepad()"><i class="fas fa-sticky-note"></i><span>Блокнот</span></button>
        <button class="mbn-btn" onclick="openTrash()"><i class="fas fa-trash-restore"></i><span>Корзина</span></button>
        <button class="mbn-btn" onclick="openLog()"><i class="fas fa-history"></i><span>Лог</span></button>
        <button class="mbn-btn" onclick="showModal('settingsModal')"><i class="fas fa-cog"></i><span>Настройки</span></button>
        <button class="mbn-btn" onclick="showModal('versionModal')"><i class="fas fa-info-circle"></i><span>О программе</span></button>
        <a class="mbn-btn" href="?logout=1" onclick="event.preventDefault();kdConfirm('Вы уверены, что хотите выйти из аккаунта?', function(){window.location.href='?logout=1';}, {title:'Выход из аккаунта', okText:'Выйти', danger:true});" style="color:#f47171;"><i class="fas fa-sign-out-alt"></i><span>Выход</span></a>
    </div>
</div>

<!-- ═══════ SCREENSAVER OVERLAY ═══════ -->
<div id="kdScreensaver" style="display:none;">
    <div id="kdSsAura">
        <span class="ss-orb ss-orb-1"></span>
        <span class="ss-orb ss-orb-2"></span>
        <span class="ss-orb ss-orb-3"></span>
    </div>
    <div id="kdSsBlur"></div>
    <div id="kdSsContent">
        <div id="kdSsInner">
            <div id="kdSsGreeting"></div>
            <div id="kdSsClock"></div>
            <div id="kdSsHint"><i class="fas fa-arrows-alt"></i> Сдвиньте мышь или нажмите клавишу</div>
        </div>
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
var sidebar         = document.getElementById('sidebar');
var sidebarOverlay  = document.getElementById('sidebarOverlay');
var explorerBtn     = document.getElementById('explorerBtn');
var mbnExplorerBtn  = document.getElementById('mbnExplorerBtn');
var isMobile        = function() { return window.innerWidth <= 767; };

function setExplorerActive(active) {
    if (explorerBtn)    explorerBtn.classList.toggle('active', active);
    if (mbnExplorerBtn) mbnExplorerBtn.classList.toggle('active', active);
}

function toggleSidebar() {
    if (isMobile()) {
        // Мобильный: слайд влево/вправо через класс active
        var open = sidebar.classList.contains('active');
        if (open) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            setExplorerActive(false);
        } else {
            sidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
            setExplorerActive(true);
        }
    } else {
        // Десктоп: скрыть/показать через класс hidden (width: 0)
        var hidden = sidebar.classList.contains('hidden');
        if (hidden) {
            sidebar.classList.remove('hidden');
            setExplorerActive(true);
        } else {
            sidebar.classList.add('hidden');
            setExplorerActive(false);
        }
    }
}

// Стартовое состояние — sidebar открыт, кнопка активна
setExplorerActive(true);

// Wire up explorer button (desktop activity-bar + mobile bottom nav)
if (explorerBtn)    explorerBtn.addEventListener('click', toggleSidebar);
if (mbnExplorerBtn) mbnExplorerBtn.addEventListener('click', toggleSidebar);

// Overlay click closes sidebar (mobile)
if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        setExplorerActive(false);
    });
}

// Close mobile sidebar button (inside sidebar header)
var closeMobileBtn = document.getElementById('closeMobileMenuBtn');
if (closeMobileBtn) {
    closeMobileBtn.addEventListener('click', function() {
        sidebar.classList.remove('active');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
        setExplorerActive(false);
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

// ── Mobile bottom nav: сворачивание/разворачивание панели вручную ──
var mobileBottomNav = document.getElementById('mobileBottomNav');
var mbnHandle        = document.getElementById('mbnHandle');
if (mbnHandle && mobileBottomNav) {
    mbnHandle.addEventListener('click', function() {
        var collapsed = mobileBottomNav.classList.toggle('collapsed');
        var macWin = document.querySelector('.mac-window');
        if (macWin) macWin.classList.toggle('mbn-collapsed', collapsed);
    });
}

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
        if (e.target !== this) return;
        if (this.id === 'confirmModal') kdConfirmCancel();
        else if (this.id === 'constructorModal') closeConstructor();
        else if (this.id === 'updateAvailableModal') dismissUpdateModal();
        else hideModal(this.id);
    });
});

// ════════════════════════════════════════════
//  CONFIRM MODAL (замена window.confirm на модалку в стиле сайта)
// ════════════════════════════════════════════
var _kdConfirmCallback = null;

/* kdConfirm(message, onConfirm, opts)
   opts: { title, okText, danger } — danger = true делает кнопку подтверждения красной
*/
function kdConfirm(message, onConfirm, opts) {
    opts = opts || {};
    var msgEl   = document.getElementById('confirmModalMessage');
    var titleEl = document.getElementById('confirmModalTitle');
    var okBtn   = document.getElementById('confirmModalOkBtn');
    if (msgEl) msgEl.textContent = message;
    if (titleEl) {
        titleEl.innerHTML = '<i class="fas fa-exclamation-triangle" style="margin-right:8px;color:#e5c07b"></i>' +
            (opts.title || 'Подтверждение');
    }
    if (okBtn) {
        okBtn.textContent = opts.okText || 'Подтвердить';
        okBtn.className = 'btn ' + (opts.danger ? 'btn-danger' : 'btn-primary');
    }
    _kdConfirmCallback = (typeof onConfirm === 'function') ? onConfirm : null;
    showModal('confirmModal');
}

function kdConfirmAccept() {
    var cb = _kdConfirmCallback;
    _kdConfirmCallback = null;
    hideModal('confirmModal');
    if (cb) cb();
}

function kdConfirmCancel() {
    _kdConfirmCallback = null;
    hideModal('confirmModal');
}

// ════════════════════════════════════════════
//  OTA UPDATE CHECK (автоматическая проверка новой версии)
// ════════════════════════════════════════════
var OTA_VERSION_URL  = '<?= file_exists(__DIR__."/version.php") ? "version.php" : "?action=ota_proxy" ?>';
var OTA_LOCAL_VERSION = '<?= VERSION ?>';
var _otaPendingVersion = null;

function otaCompareVersions(a, b) {
    var pa = String(a).split('.').map(function(n) { return parseInt(n, 10) || 0; });
    var pb = String(b).split('.').map(function(n) { return parseInt(n, 10) || 0; });
    var len = Math.max(pa.length, pb.length);
    for (var i = 0; i < len; i++) {
        var na = pa[i] || 0, nb = pb[i] || 0;
        if (na > nb) return 1;
        if (na < nb) return -1;
    }
    return 0;
}

function otaParseVersionPage(html) {
    try {
        // version.php отдаёт чистый JSON
        var data = JSON.parse(html.trim());
        if (!data || !data.version) return null;
        return data;
    } catch (e) {
        return null;
    }
}

function checkForUpdates() {
    console.log('[OTA] checkForUpdates() started, URL=', OTA_VERSION_URL, 'local=', OTA_LOCAL_VERSION);
    fetch(OTA_VERSION_URL, { cache: 'no-store' })
        .then(function(r) {
            console.log('[OTA] fetch status:', r.status);
            return r.json();
        })
        .then(function(data) {
            console.log('[OTA] data received:', data);
            if (!data || !data.version) { console.log('[OTA] no data.version, stopping'); return; }
            var cmp = otaCompareVersions(data.version, OTA_LOCAL_VERSION);
            console.log('[OTA] compare remote', data.version, 'vs local', OTA_LOCAL_VERSION, '=', cmp);
            if (cmp <= 0) { console.log('[OTA] remote not newer, stopping'); return; }
            console.log('[OTA] showing update modal');
            showUpdateAvailable(data);
        })
        .catch(function(err) { console.log('[OTA] fetch/parse error:', err); });
}

function showUpdateAvailable(data) {
    _otaPendingVersion = data.version;
    var verEl = document.getElementById('updateAvailableVersion');
    if (verEl) verEl.textContent = 'v' + data.version + (data.date ? ' (' + data.date + ')' : '');
    var list = document.getElementById('updateAvailableChanges');
    if (list) {
        var changes = Array.isArray(data.changes) ? data.changes : [];
        list.innerHTML = changes.length
            ? changes.map(function(c) { return '<li>' + escHtml(c) + '</li>'; }).join('')
            : '<li>Подробности смотрите на странице релиза.</li>';
    }
    showModal('updateAvailableModal');
}

function dismissUpdateModal() {
    // Окно не запоминается — будет показываться заново при каждой загрузке, пока версия не обновится
    hideModal('updateAvailableModal');
}

/* manualCheckForUpdates — кнопка «Проверить обновления» в окне «О программе».
   В отличие от автопроверки, всегда стучится на сервер и показывает результат явно,
   даже если эта версия ранее была отклонена или прошло меньше 6 часов. */
function manualCheckForUpdates() {
    var btn    = document.getElementById('manualCheckUpdateBtn');
    var status = document.getElementById('manualCheckUpdateStatus');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Проверка...'; }
    if (status) status.textContent = '';

    fetch(OTA_VERSION_URL, { cache: 'no-store' })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            try { localStorage.setItem('kd_update_last_check', String(Date.now())); } catch (e) {}
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt"></i> Проверить обновления'; }
            if (!data || !data.version) {
                if (status) status.textContent = 'Не удалось прочитать данные о версии.';
                return;
            }
            if (otaCompareVersions(data.version, OTA_LOCAL_VERSION) > 0) {
                if (status) status.textContent = 'Доступна новая версия v' + data.version + '!';
                showUpdateAvailable(data);
            } else {
                if (status) status.textContent = 'У вас установлена последняя версия (v' + OTA_LOCAL_VERSION + ').';
            }
        })
        .catch(function() {
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt"></i> Проверить обновления'; }
            if (status) status.textContent = 'Сервер проверки обновлений недоступен.';
        });
}

window.addEventListener('DOMContentLoaded', function() {
    // Одноразовая очистка старых записей dismiss — окно больше не запоминается, и старые записи больше не нужны
    try {
        for (var i = localStorage.length - 1; i >= 0; i--) {
            var k = localStorage.key(i);
            if (k && k.indexOf('kd_update_dismissed_') === 0) localStorage.removeItem(k);
        }
    } catch (e) {}
    // Проверка обновлений при каждой загрузке страницы
    if (kdSettings.autoUpdateCheck) {
        setTimeout(checkForUpdates, 1000);
    }
});

// ════════════════════════════════════════════
//  CONSTRUCTOR (визуальный превью PHP/HTML страницы: ПК/планшет/моб., ручной resize,
//  просмотр HTML/CSS/JS страницы, инспектор элементов с подсветкой CSS-правил)
// ════════════════════════════════════════════
var constructorFrame           = null;
var constructorInspectOn       = false;
var constructorLastHighlighted = null;
var constructorCurrentElement  = null;
var constructorCurrentUrl      = '';
var constructorFinalUrl        = '';
var constructorCurrentView     = 'preview';
var constructorPageHTML        = '';
var constructorPageCSS         = '';
var constructorPageJS          = '';
var constructorCodeEditor      = null;
var constructorResizeObserver  = null;

function openConstructor(url, filename) {
    constructorFrame = document.getElementById('constructorFrame');
    document.getElementById('constructorFileName').textContent = filename || '';
    document.getElementById('constructorOpenNewTab').href = url;
    document.getElementById('constructorInspectToggle').checked = false;
    document.getElementById('constructorInspectPanel').style.display = 'none';
    constructorInspectOn = false;
    constructorCurrentUrl = url;
    constructorPageHTML = constructorPageCSS = constructorPageJS = '';
    setConstructorDevice('desktop');
    setConstructorView('preview');
    constructorFrame.onload = onConstructorFrameLoad;
    constructorFrame.src = url;
    showModal('constructorModal');
    setupConstructorResizeObserver();
}

function closeConstructor() {
    detachConstructorInspector();
    hideModal('constructorModal');
    if (constructorFrame) constructorFrame.src = 'about:blank';
}

function reloadConstructorFrame() {
    if (!constructorFrame) return;
    var src = constructorFrame.src;
    constructorFrame.src = 'about:blank';
    setTimeout(function() { constructorFrame.src = src; }, 30);
}

/* ── Device presets + ручной resize ─────────────────────────── */

function setConstructorDevice(device) {
    var wrap = document.getElementById('constructorFrameWrap');
    var resizable = document.getElementById('constructorResizable');
    if (!wrap) return;
    wrap.classList.remove('device-desktop', 'device-tablet', 'device-mobile');
    wrap.classList.add('device-' + device);
    if (resizable) {
        // сбрасываем ручной resize, чтобы применился пресет
        resizable.style.width = '';
        resizable.style.height = '';
    }
    document.querySelectorAll('.constructor-device-btn').forEach(function(btn) {
        btn.classList.toggle('active', btn.getAttribute('data-device') === device);
    });
    var sizes = { desktop: 'Полная ширина', tablet: '768 × 1024', mobile: '375 × 720' };
    var lbl = document.getElementById('constructorViewportSize');
    if (lbl) lbl.textContent = sizes[device] || '';
}

function setupConstructorResizeObserver() {
    if (constructorResizeObserver || !window.ResizeObserver) return;
    var el = document.getElementById('constructorResizable');
    if (!el) return;
    constructorResizeObserver = new ResizeObserver(function(entries) {
        for (var i = 0; i < entries.length; i++) {
            var w = Math.round(entries[i].contentRect.width);
            var h = Math.round(entries[i].contentRect.height);
            var lbl = document.getElementById('constructorViewportSize');
            if (lbl) lbl.textContent = w + ' × ' + h + ' (вручную)';
            // ручной resize больше не соответствует ни одному пресету
            document.querySelectorAll('.constructor-device-btn').forEach(function(btn) { btn.classList.remove('active'); });
        }
    });
    constructorResizeObserver.observe(el);
}

/* ── Переключение Превью / HTML / CSS / JS ──────────────────── */

function setConstructorView(view) {
    constructorCurrentView = view;
    document.querySelectorAll('#constructorViewTabs .constructor-view-tab').forEach(function(t) {
        t.classList.toggle('active', t.getAttribute('data-view') === view);
    });
    var frameWrap = document.getElementById('constructorFrameWrap');
    var codeWrap  = document.getElementById('constructorCodeWrap');
    if (view === 'preview') {
        if (frameWrap) frameWrap.style.display = 'flex';
        if (codeWrap)  codeWrap.style.display  = 'none';
    } else {
        if (frameWrap) frameWrap.style.display = 'none';
        if (codeWrap)  codeWrap.style.display  = 'block';
        renderConstructorCodeView();
    }
}

function renderConstructorCodeView() {
    if (!constructorCodeEditor) {
        require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }});
        require(['vs/editor/editor.main'], function() {
            ensureKdBlackMonacoTheme();
            constructorCodeEditor = monaco.editor.create(document.getElementById('constructorCodeEditor'), {
                value: '', language: 'html', theme: monacoThemeFor(kdSettings.themeMode),
                readOnly: true, automaticLayout: true, minimap: { enabled: false },
                fontSize: 13, scrollBeyondLastLine: false, wordWrap: 'on', padding: { top: 10 }
            });
            updateConstructorCodeEditorContent();
        });
    } else {
        updateConstructorCodeEditorContent();
    }
}

function updateConstructorCodeEditorContent() {
    if (!constructorCodeEditor || !window.monaco) return;
    var lang = 'html', value = '';
    if (constructorCurrentView === 'html') { lang = 'html';       value = constructorPageHTML; }
    else if (constructorCurrentView === 'css') { lang = 'css';        value = constructorPageCSS; }
    else if (constructorCurrentView === 'js')  { lang = 'javascript'; value = constructorPageJS; }
    monaco.editor.setModelLanguage(constructorCodeEditor.getModel(), lang);
    constructorCodeEditor.setValue(value || '/* пусто */');
}

/* ── Загрузка и разбор исходного кода страницы (HTML/CSS/JS) ───────── */

function onConstructorFrameLoad() {
    if (constructorInspectOn) { attachConstructorInspector(); scanConstructorClasses(); }
    fetchConstructorSource();
}

function fetchConstructorSource() {
    if (!constructorCurrentUrl) return;
    fetch(constructorCurrentUrl).then(function(r) {
        constructorFinalUrl = r.url || constructorCurrentUrl;
        return r.text();
    }).then(function(html) {
        constructorPageHTML = html;
        extractConstructorAssets(html);
        if (constructorCurrentView !== 'preview') updateConstructorCodeEditorContent();
    }).catch(function() {});
}

function resolveConstructorURL(raw) {
    try { return new URL(raw, constructorFinalUrl || constructorCurrentUrl).href; } catch (e) { return raw; }
}

function extractConstructorAssets(html) {
    var doc = new DOMParser().parseFromString(html, 'text/html');
    var cssParts = [];
    var jsParts  = [];
    doc.querySelectorAll('style').forEach(function(s) { cssParts.push(s.textContent); });
    doc.querySelectorAll('script').forEach(function(s) {
        if (!s.src) jsParts.push(s.textContent);
    });
    constructorPageCSS = cssParts.join('\n\n/* ───────────────────────── */\n\n');
    constructorPageJS  = jsParts.join('\n\n// ───────────────────────── \n\n');

    var fetches = [];
    doc.querySelectorAll('link[rel="stylesheet"][href]').forEach(function(l) {
        var url = resolveConstructorURL(l.getAttribute('href'));
        fetches.push(
            fetch(url).then(function(r) { return r.text(); }).then(function(t) {
                constructorPageCSS += '\n\n/* ───── ' + l.getAttribute('href') + ' ───── */\n\n' + t;
            }).catch(function() {})
        );
    });
    doc.querySelectorAll('script[src]').forEach(function(s) {
        var url = resolveConstructorURL(s.getAttribute('src'));
        fetches.push(
            fetch(url).then(function(r) { return r.text(); }).then(function(t) {
                constructorPageJS += '\n\n// ───── ' + s.getAttribute('src') + ' ───── \n\n' + t;
            }).catch(function() {})
        );
    });
    Promise.all(fetches).then(function() {
        if (constructorCurrentView !== 'preview') updateConstructorCodeEditorContent();
    });
}

/* ── Инспектор элементов ─────────────────────────────────────── */

function toggleConstructorInspect(enabled) {
    constructorInspectOn = enabled;
    var panel = document.getElementById('constructorInspectPanel');
    if (panel) panel.style.display = enabled ? 'block' : 'none';
    if (enabled) { attachConstructorInspector(); scanConstructorClasses(); }
    else detachConstructorInspector();
}

function getConstructorDoc() {
    try {
        if (!constructorFrame) return null;
        return constructorFrame.contentDocument || constructorFrame.contentWindow.document;
    } catch (e) {
        return null;
    }
}

function attachConstructorInspector() {
    var doc = getConstructorDoc();
    var info = document.getElementById('constructorInspectInfo');
    if (!doc) {
        if (info) info.innerHTML = '<div class="constructor-inspect-empty">Не удалось получить доступ к содержимому страницы.</div>';
        return;
    }
    doc.removeEventListener('mouseover', constructorOnMouseOver, true);
    doc.removeEventListener('click', constructorOnClick, true);
    doc.addEventListener('mouseover', constructorOnMouseOver, true);
    doc.addEventListener('click', constructorOnClick, true);
}

function detachConstructorInspector() {
    if (constructorLastHighlighted) {
        constructorLastHighlighted.style.outline = '';
        constructorLastHighlighted.style.outlineOffset = '';
        constructorLastHighlighted = null;
    }
    var doc = getConstructorDoc();
    if (!doc) return;
    doc.removeEventListener('mouseover', constructorOnMouseOver, true);
    doc.removeEventListener('click', constructorOnClick, true);
}

function constructorOnMouseOver(e) {
    if (!constructorInspectOn) return;
    var el = e.target;
    if (constructorLastHighlighted && constructorLastHighlighted !== el) {
        constructorLastHighlighted.style.outline = '';
        constructorLastHighlighted.style.outlineOffset = '';
    }
    el.style.outline = '2px solid #3b8eff';
    el.style.outlineOffset = '-1px';
    constructorLastHighlighted = el;
    constructorCurrentElement = el;
    showConstructorElementInfo(el);
}

function constructorOnClick(e) {
    if (!constructorInspectOn) return;
    e.preventDefault();
    e.stopPropagation();
    constructorCurrentElement = e.target;
    openConstructorElementDetail(e.target);
}

/* getMatchingCSSRules — какие CSS-правила страницы применяются к элементу */
function getMatchingCSSRules(el, doc) {
    var matched = [];
    var sheets = doc.styleSheets;
    for (var i = 0; i < sheets.length; i++) {
        var rules;
        try { rules = sheets[i].cssRules || sheets[i].rules; } catch (e) { continue; }
        if (!rules) continue;
        for (var j = 0; j < rules.length; j++) {
            var rule = rules[j];
            if (!rule.selectorText) continue;
            try {
                if (el.matches(rule.selectorText)) matched.push(rule.cssText);
            } catch (e) {}
        }
    }
    return matched;
}

function showConstructorElementInfo(el) {
    var info = document.getElementById('constructorInspectInfo');
    if (!info) return;
    var tag = el.tagName ? el.tagName.toLowerCase() : '?';
    var id = el.id ? '#' + el.id : '';
    var classes = (el.classList && el.classList.length)
        ? Array.prototype.slice.call(el.classList).map(function(c) { return '.' + c; }).join(' ')
        : '';
    var doc = getConstructorDoc();
    var rules = doc ? getMatchingCSSRules(el, doc) : [];
    var rulesHtml = rules.length
        ? rules.map(function(r) { return '<div class="constructor-css-rule">' + escHtml(r) + '</div>'; }).join('')
        : '<div class="constructor-inspect-empty">Нет применяемых CSS-правил</div>';
    info.innerHTML =
        '<div class="constructor-inspect-box">' +
            '<span class="ci-tag">&lt;' + escHtml(tag) + '&gt;</span>' +
            (id ? '<br><span class="ci-id">' + escHtml(id) + '</span>' : '') +
            (classes ? '<br><span class="ci-class">' + escHtml(classes) + '</span>' : '') +
        '</div>' +
        '<div class="constructor-inspect-header">Применяемый CSS</div>' +
        '<div class="constructor-css-rules">' + rulesHtml + '</div>' +
        '<div class="constructor-click-hint"><i class="fas fa-mouse-pointer"></i> Кликните — откроется HTML/CSS/скрипты элемента</div>';
}

function scanConstructorClasses() {
    var doc = getConstructorDoc();
    var classListEl = document.getElementById('constructorClassList');
    var idListEl    = document.getElementById('constructorIdList');
    var classCountEl = document.getElementById('constructorClassCount');
    var idCountEl     = document.getElementById('constructorIdCount');
    if (!classListEl || !idListEl) return;
    if (!doc) {
        classListEl.innerHTML = '';
        idListEl.innerHTML = '';
        if (classCountEl) classCountEl.textContent = '0';
        if (idCountEl) idCountEl.textContent = '0';
        return;
    }
    var allEls = doc.querySelectorAll('*');
    var classSet = {};
    var idSet = {};
    allEls.forEach(function(el) {
        if (el.classList && el.classList.length) {
            el.classList.forEach(function(c) { classSet[c] = true; });
        }
        if (el.id) idSet[el.id] = true;
    });
    var classNames = Object.keys(classSet).sort();
    var idNames = Object.keys(idSet).sort();
    if (classCountEl) classCountEl.textContent = classNames.length;
    if (idCountEl) idCountEl.textContent = idNames.length;
    classListEl.innerHTML = classNames.map(function(c) {
        return '<span class="constructor-class-chip" onclick="highlightConstructorSelector(\'.' + c.replace(/'/g, "\\'") + '\')">.' + escHtml(c) + '</span>';
    }).join('');
    idListEl.innerHTML = idNames.map(function(i) {
        return '<span class="constructor-class-chip" onclick="highlightConstructorSelector(\'#' + i.replace(/'/g, "\\'") + '\')">#' + escHtml(i) + '</span>';
    }).join('');
}

function highlightConstructorSelector(selector) {
    var doc = getConstructorDoc();
    if (!doc) return;
    var els;
    try { els = doc.querySelectorAll(selector); } catch (e) { return; }
    els.forEach(function(el) {
        el.style.transition = 'outline-color 0.15s';
        el.style.outline = '2px solid #e5c07b';
        el.style.outlineOffset = '-1px';
    });
    setTimeout(function() {
        els.forEach(function(el) {
            if (el !== constructorLastHighlighted) {
                el.style.outline = '';
                el.style.outlineOffset = '';
            }
        });
    }, 1200);
}

/* ── Окно деталей элемента (клик в инспекторе): HTML / CSS / Скрипты ── */

var elementDetailData        = { html: '', css: '', js: '' };
var elementDetailEditor      = null;
var elementDetailCurrentTab  = 'html';

function prettyPrintHTML(html) {
    var voidTags = ['area','base','br','col','embed','hr','img','input','link','meta','param','source','track','wbr'];
    var withBreaks = html.replace(/></g, '>\n<');
    var lines = withBreaks.split('\n');
    var indent = 0;
    var out = [];
    lines.forEach(function(raw) {
        var line = raw.trim();
        if (!line) return;
        var isClosing = /^<\//.test(line);
        var tagMatch = line.match(/^<\/?([a-zA-Z0-9-]+)/);
        var tagName = tagMatch ? tagMatch[1].toLowerCase() : '';
        var isSelfClosing = /\/>\s*$/.test(line) || voidTags.indexOf(tagName) !== -1;
        var isInlinePair = /^<[^>]+>.*<\/[^>]+>$/.test(line);
        if (isClosing) indent = Math.max(0, indent - 1);
        out.push('  '.repeat(indent) + line);
        if (!isClosing && !isSelfClosing && !isInlinePair && /^<[a-zA-Z]/.test(line)) indent++;
    });
    return out.join('\n');
}

function openConstructorElementDetail(el) {
    var doc = getConstructorDoc();
    var tag = el.tagName ? el.tagName.toLowerCase() : '?';
    var idAttr = el.id || '';
    var classAttr = (el.className && typeof el.className === 'string') ? el.className.trim() : '';

    var titleEl = document.getElementById('elementDetailTitle');
    if (titleEl) {
        titleEl.textContent = '<' + tag + '>' + (idAttr ? '#' + idAttr : '') + (classAttr ? '.' + classAttr.split(/\s+/).join('.') : '');
    }

    var html = '';
    try { html = prettyPrintHTML(el.outerHTML || ''); } catch (e) { html = el.outerHTML || ''; }

    var rules = doc ? getMatchingCSSRules(el, doc) : [];
    var css = rules.length ? rules.join('\n\n') : '/* Нет CSS-правил, применяемых к этому элементу */';

    var handlers = [];
    if (el.attributes) {
        for (var i = 0; i < el.attributes.length; i++) {
            var attr = el.attributes[i];
            if (/^on/i.test(attr.name)) handlers.push(attr.name + '="' + attr.value + '"');
        }
    }
    var related = [];
    if (idAttr) related = related.concat(findRelatedScriptSnippets(idAttr));
    if (classAttr) {
        classAttr.split(/\s+/).filter(Boolean).forEach(function(c) {
            related = related.concat(findRelatedScriptSnippets(c));
        });
    }
    var js = '';
    if (handlers.length) js += '/* Атрибуты-обработчики событий на самом элементе */\n' + handlers.join('\n') + '\n\n';
    if (related.length) js += '/* Фрагменты скриптов страницы, упоминающие этот элемент */\n\n' + related.join('\n\n/* ── */\n\n');
    if (!js) js = '/* Скрипты, явно связанные с этим элементом, не найдены */';

    elementDetailData = { html: html, css: css, js: js };
    setElementDetailTab('html');
    showModal('elementDetailModal');
}

function findRelatedScriptSnippets(needle) {
    var found = [];
    if (!constructorPageJS || !needle) return found;
    var lines = constructorPageJS.split('\n');
    for (var i = 0; i < lines.length; i++) {
        if (lines[i].indexOf(needle) !== -1) {
            var start = Math.max(0, i - 1), end = Math.min(lines.length, i + 2);
            found.push(lines.slice(start, end).join('\n'));
        }
    }
    return found;
}

function setElementDetailTab(tab) {
    elementDetailCurrentTab = tab;
    document.querySelectorAll('#elementDetailModal .constructor-view-tab').forEach(function(t) {
        t.classList.toggle('active', t.getAttribute('data-tab') === tab);
    });
    renderElementDetailEditor();
}

function renderElementDetailEditor() {
    if (!elementDetailEditor) {
        require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }});
        require(['vs/editor/editor.main'], function() {
            ensureKdBlackMonacoTheme();
            elementDetailEditor = monaco.editor.create(document.getElementById('elementDetailEditor'), {
                value: '', language: 'html', theme: monacoThemeFor(kdSettings.themeMode),
                readOnly: true, automaticLayout: true, minimap: { enabled: false },
                fontSize: 13, scrollBeyondLastLine: false, wordWrap: 'on', padding: { top: 10 }
            });
            updateElementDetailEditorContent();
        });
    } else {
        updateElementDetailEditorContent();
    }
}

function updateElementDetailEditorContent() {
    if (!elementDetailEditor || !window.monaco) return;
    var lang = elementDetailCurrentTab === 'css' ? 'css' : (elementDetailCurrentTab === 'js' ? 'javascript' : 'html');
    var value = elementDetailData[elementDetailCurrentTab] || '';
    monaco.editor.setModelLanguage(elementDetailEditor.getModel(), lang);
    elementDetailEditor.setValue(value);
}

/* ── «Показать в коде» — переход к строке HTML-исходника, где находится элемент ── */

function revealElementInCode(el) {
    if (!el || !constructorPageHTML) { showNotification('Код страницы ещё не загружен', 'error'); return; }
    var tag = el.tagName.toLowerCase();
    var needle = null;
    if (el.id) needle = 'id="' + el.id + '"';
    else if (el.className && typeof el.className === 'string' && el.className.trim()) {
        needle = 'class="' + el.className.trim().split(/\s+/)[0];
    }
    if (!needle) needle = '<' + tag;
    var idx = constructorPageHTML.indexOf(needle);
    if (idx === -1) { showNotification('Не удалось найти точное место в коде', 'error'); return; }
    var lineNum = constructorPageHTML.substring(0, idx).split('\n').length;
    hideModal('elementDetailModal');
    setConstructorView('html');
    setTimeout(function() {
        if (constructorCodeEditor && window.monaco) {
            constructorCodeEditor.revealLineInCenter(lineNum);
            constructorCodeEditor.setSelection(new monaco.Range(lineNum, 1, lineNum, 1));
            constructorCodeEditor.deltaDecorations([], [{
                range: new monaco.Range(lineNum, 1, lineNum, 1),
                options: { isWholeLine: true, className: 'constructor-highlight-line' }
            }]);
        }
    }, 150);
}

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
        ensureKdBlackMonacoTheme();
        var content = (document.getElementById('file-content') || {}).textContent || '';
        monacoEditor = monaco.editor.create(document.getElementById('editor-container'), {
            value: content,
            language: getLang(fileExtension),
            theme: monacoThemeFor(kdSettings.themeMode),
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
        ensureKdBlackMonacoTheme();
        notepadEditor = monaco.editor.create(document.getElementById('notepadContainer'), {
            value: '',
            language: 'plaintext',
            theme: monacoThemeFor(kdSettings.themeMode),
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
    if (!notepadEditor) return;
    kdConfirm('Очистить содержимое редактора? Несохранённые изменения будут потеряны.', function() {
        notepadEditor.setValue('');
    }, {title: 'Очистка редактора', okText: 'Очистить', danger: true});
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
    themeMode:   'dark',   // 'dark' | 'light' | 'black'
    showClock:   false,
    clockFormat: 'datetime',
    showMeta:    true,
    screensaverEnabled: false,
    screensaverMode:    'both',
    screensaverTimeout: 30,
    snowEnabled:   false,
    snowIntensity: 'normal',
    autoUpdateCheck: true
};

function loadSettings() {
    try {
        var s = JSON.parse(localStorage.getItem('kd_settings') || '{}');
        if (s.themeMode === 'light' || s.themeMode === 'black' || s.themeMode === 'dark') {
            kdSettings.themeMode = s.themeMode;
        } else {
            kdSettings.themeMode = s.lightTheme ? 'light' : 'dark'; // миграция старой настройки
        }
        kdSettings.showClock   = !!s.showClock;
        kdSettings.clockFormat = s.clockFormat || 'datetime';
        kdSettings.showMeta    = s.showMeta !== false;
        kdSettings.screensaverEnabled = !!s.screensaverEnabled;
        kdSettings.screensaverMode    = s.screensaverMode || 'both';
        kdSettings.screensaverTimeout = parseInt(s.screensaverTimeout) || 30;
        kdSettings.snowEnabled   = !!s.snowEnabled;
        kdSettings.snowIntensity = s.snowIntensity || 'normal';
        kdSettings.autoUpdateCheck = s.autoUpdateCheck !== false;
    } catch(e) {}
}

function saveSettings() {
    localStorage.setItem('kd_settings', JSON.stringify(kdSettings));
}

function monacoThemeFor(mode) {
    if (mode === 'light') return 'vs';
    if (mode === 'black') return 'kd-true-black';
    return 'vs-dark';
}

function applySettings(fromUI) {
    // Only read DOM values when called from UI interaction
    if (fromUI) {
        var tmEl  = document.getElementById('settingThemeMode');
        var clEl  = document.getElementById('settingShowClock');
        var cfEl  = document.getElementById('settingClockFormat');
        var smEl  = document.getElementById('settingShowMeta');
        var ssEl  = document.getElementById('settingScreensaverEnabled');
        var ssMEl = document.getElementById('settingScreensaverMode');
        var ssTEl = document.getElementById('settingScreensaverTimeout');
        var snEl  = document.getElementById('settingSnowEnabled');
        var snIEl = document.getElementById('settingSnowIntensity');
        var auEl  = document.getElementById('settingAutoUpdateCheck');
        if (tmEl) kdSettings.themeMode  = tmEl.value;
        if (clEl) kdSettings.showClock   = clEl.checked;
        if (cfEl) kdSettings.clockFormat = cfEl.value;
        if (smEl) kdSettings.showMeta    = smEl.checked;
        if (ssEl) kdSettings.screensaverEnabled = ssEl.checked;
        if (ssMEl) kdSettings.screensaverMode   = ssMEl.value;
        if (ssTEl) kdSettings.screensaverTimeout = parseInt(ssTEl.value) || 30;
        if (snEl) kdSettings.snowEnabled    = snEl.checked;
        if (snIEl) kdSettings.snowIntensity = snIEl.value;
        if (auEl) kdSettings.autoUpdateCheck = auEl.checked;
    }

    // Theme
    var prevTheme = document.body.classList.contains('light-theme') ? 'light'
                  : document.body.classList.contains('black-theme') ? 'black'
                  : 'dark';
    document.body.classList.remove('light-theme', 'black-theme');
    if (kdSettings.themeMode === 'light') document.body.classList.add('light-theme');
    else if (kdSettings.themeMode === 'black') document.body.classList.add('black-theme');
    if (prevTheme !== kdSettings.themeMode && typeof monaco !== 'undefined') {
        ensureKdBlackMonacoTheme();
        monaco.editor.setTheme(monacoThemeFor(kdSettings.themeMode));
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

    // Snowflakes option row
    var snowRow = document.getElementById('snowIntensityRow');
    if (snowRow) snowRow.style.display = kdSettings.snowEnabled ? '' : 'none';

    // Snowflakes effect
    if (kdSettings.snowEnabled) {
        startSnow();
    } else {
        stopSnow();
    }

    // Meta visibility
    document.documentElement.style.setProperty('--meta-opacity-hover', kdSettings.showMeta ? '1' : '0');

    saveSettings();
}

function syncSettingsUI() {
    var tmEl = document.getElementById('settingThemeMode');
    var clEl = document.getElementById('settingShowClock');
    var cfEl = document.getElementById('settingClockFormat');
    var smEl = document.getElementById('settingShowMeta');
    var ssEl  = document.getElementById('settingScreensaverEnabled');
    var ssMEl = document.getElementById('settingScreensaverMode');
    var ssTEl = document.getElementById('settingScreensaverTimeout');
    var snEl  = document.getElementById('settingSnowEnabled');
    var snIEl = document.getElementById('settingSnowIntensity');
    var auEl  = document.getElementById('settingAutoUpdateCheck');
    if (tmEl) tmEl.value  = kdSettings.themeMode;
    if (clEl) clEl.checked = kdSettings.showClock;
    if (cfEl) cfEl.value   = kdSettings.clockFormat;
    if (smEl) smEl.checked = kdSettings.showMeta;
    if (ssEl)  ssEl.checked  = kdSettings.screensaverEnabled;
    if (ssMEl) ssMEl.value   = kdSettings.screensaverMode;
    if (ssTEl) ssTEl.value   = String(kdSettings.screensaverTimeout);
    if (snEl)  snEl.checked  = kdSettings.snowEnabled;
    if (snIEl) snIEl.value   = kdSettings.snowIntensity;
    if (auEl)  auEl.checked  = kdSettings.autoUpdateCheck;
    var fmtRow = document.getElementById('clockFormatRow');
    if (fmtRow) fmtRow.style.display = kdSettings.showClock ? '' : 'none';
    var ssOptRow  = document.getElementById('screensaverOptionsRow');
    var ssToutRow = document.getElementById('screensaverTimeoutRow');
    if (ssOptRow)  ssOptRow.style.display  = kdSettings.screensaverEnabled ? '' : 'none';
    if (ssToutRow) ssToutRow.style.display = kdSettings.screensaverEnabled ? '' : 'none';
    var snowRow = document.getElementById('snowIntensityRow');
    if (snowRow) snowRow.style.display = kdSettings.snowEnabled ? '' : 'none';
}

// Init settings on load
(function() {
    loadSettings();
    if (kdSettings.themeMode === 'light') document.body.classList.add('light-theme');
    else if (kdSettings.themeMode === 'black') document.body.classList.add('black-theme');
})();

// Defines the custom pure-black Monaco theme (idempotent, safe to call repeatedly)
function ensureKdBlackMonacoTheme() {
    if (typeof monaco === 'undefined') return;
    monaco.editor.defineTheme('kd-true-black', {
        base: 'vs-dark',
        inherit: true,
        rules: [],
        colors: {
            'editor.background': '#000000',
            'editor.foreground': '#d4d4d4',
            'editorGutter.background': '#000000',
            'editorLineNumber.foreground': '#3a3a3a',
            'editorLineNumber.activeForeground': '#9d9d9d',
            'editor.lineHighlightBackground': '#0a0a0a',
            'editor.lineHighlightBorder': '#0a0a0a',
            'editorCursor.foreground': '#3b8eff',
            'editorWhitespace.foreground': '#222222',
            'editor.selectionBackground': '#3b8eff40',
            'editorIndentGuide.background': '#1a1a1a',
            'editorIndentGuide.activeBackground': '#2a2a2a',
            'scrollbarSlider.background': '#ffffff14',
            'scrollbarSlider.hoverBackground': '#ffffff20',
            'scrollbarSlider.activeBackground': '#ffffff28',
            'minimap.background': '#000000',
            'editorWidget.background': '#050505',
            'editorWidget.border': '#1f1f1f',
            'input.background': '#0a0a0a',
            'input.border': '#2a2a2a',
            'dropdown.background': '#050505'
        }
    });
}

// Also apply Monaco theme after it loads
function applyMonacoTheme() {
    if (typeof monaco !== 'undefined') {
        ensureKdBlackMonacoTheme();
        monaco.editor.setTheme(monacoThemeFor(kdSettings.themeMode));
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
// Per-digit / per-text state (простой механизм, без барабана)
var ssDigits = {};
var ssTexts  = {};
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

function ssGreetingText() {
    var h = new Date().getHours();
    if (h >= 5 && h < 12)  return 'Доброе утро';
    if (h >= 12 && h < 17) return 'Добрый день';
    if (h >= 17 && h < 23) return 'Добрый вечер';
    return 'Доброй ночи';
}

function showScreensaver() {
    if (!kdSettings.screensaverEnabled) return;
    ssVisible = true;
    var el = document.getElementById('kdScreensaver');
    if (!el) return;
    el.style.display = 'block';
    var greetEl = document.getElementById('kdSsGreeting');
    if (greetEl) greetEl.textContent = ssGreetingText();
    buildSsClock();
    updateSsClock(true);
    ssClockTimer = setInterval(function() { updateSsClock(false); }, 1000);
    // Если включён снег — поднимаем его поверх заставки, чтобы снежинки были видны над часами
    if (kdSettings.snowEnabled && snowCanvas) snowCanvas.style.zIndex = '10000';
    // Мобильное нижнее меню не должно мешать заставке — скрываем его на время показа
    var mbn = document.getElementById('mobileBottomNav');
    if (mbn) mbn.classList.add('force-hidden');
}

function hideScreensaver() {
    ssVisible = false;
    if (ssClockTimer) { clearInterval(ssClockTimer); ssClockTimer = null; }
    var el = document.getElementById('kdScreensaver');
    if (el) el.style.display = 'none';
    ssDigits = {};
    ssTexts  = {};
    // Возвращаем снег на обычный уровень (под модальные окна)
    if (snowCanvas) snowCanvas.style.zIndex = '500';
    // Возвращаем мобильное нижнее меню
    var mbn = document.getElementById('mobileBottomNav');
    if (mbn) mbn.classList.remove('force-hidden');
}

// ── Simple flip-digit helpers (тот же механизм, что у маленьких часов в шапке окна) ──

/* makeSsDigit — создаёт одну ячейку-цифру с тремя span-ами (пред./текущее/след. значение) */
function makeSsDigit(key, cellW, cellH, fontSize) {
    var wrap = document.createElement('div');
    wrap.className = 'ss-digit';
    wrap.style.width  = cellW + 'px';
    wrap.style.height = cellH + 'px';

    var inner = document.createElement('div');
    inner.className = 'ss-digit-inner';
    for (var i = 0; i < 3; i++) {
        var span = document.createElement('span');
        span.style.height     = cellH + 'px';
        span.style.lineHeight = cellH + 'px';
        span.style.fontSize   = fontSize + 'px';
        span.textContent = '0';
        inner.appendChild(span);
    }
    inner.style.transform = 'translateY(-' + cellH + 'px)';
    wrap.appendChild(inner);

    ssDigits[key] = { inner: inner, cellH: cellH, last: '0' };
    return wrap;
}

/* setSsDigitValue — показывает значение; при изменении — короткий «флип» вниз, без барабана */
function setSsDigitValue(key, value, animate) {
    var d = ssDigits[key];
    if (!d) return;
    value = String(value);
    var spans = d.inner.children;
    if (!animate || value === d.last) {
        spans[0].textContent = value;
        spans[1].textContent = value;
        spans[2].textContent = value;
        d.inner.style.transition = 'none';
        d.inner.style.transform  = 'translateY(-' + d.cellH + 'px)';
        d.last = value;
        return;
    }
    spans[0].textContent = d.last;
    spans[1].textContent = value;
    spans[2].textContent = value;
    d.inner.style.transition = 'none';
    d.inner.style.transform  = 'translateY(0px)';
    d.inner.offsetHeight; // reflow
    d.inner.style.transition = 'transform 0.34s cubic-bezier(0.25,0.46,0.45,0.94)';
    d.inner.style.transform  = 'translateY(-' + d.cellH + 'px)';
    d.last = value;
}

/* makeSsText — простой текстовый блок (месяц / день недели / год), без барабана */
function makeSsText(key, fontSize, extraCss) {
    var el = document.createElement('div');
    el.className = 'ss-text';
    el.style.fontSize = fontSize + 'px';
    if (extraCss) Object.assign(el.style, extraCss);
    ssTexts[key] = { el: el, last: null };
    return el;
}

/* setSsTextValue — меняет текст с мягким затуханием вместо прокрутки барабана */
function setSsTextValue(key, value, animate) {
    var t = ssTexts[key];
    if (!t) return;
    if (value === t.last) return;
    t.last = value;
    if (!animate) {
        t.el.textContent = value;
        t.el.style.opacity = '1';
        return;
    }
    t.el.style.opacity = '0';
    setTimeout(function() {
        t.el.textContent = value;
        t.el.style.opacity = '1';
    }, 200);
}

// ── Build DOM ──────────────────────────────────────────────

function buildSsClock() {
    var root = document.getElementById('kdSsClock');
    if (!root) return;
    root.innerHTML = '';
    ssDigits = {};
    ssTexts  = {};
    var mode = kdSettings.screensaverMode || 'both';

    // ── TIME row ──
    if (mode === 'both' || mode === 'time') {
        var timeRow = document.createElement('div');
        timeRow.className = 'ss-row ss-time-panel';
        timeRow.style.alignItems = 'center';

        timeRow.appendChild(makeSsDigit('h0', 46, 70, 56));
        timeRow.appendChild(makeSsDigit('h1', 46, 70, 56));
        var s1 = document.createElement('div'); s1.className='ss-dsep'; s1.style.fontSize='50px'; s1.style.padding='0 4px'; s1.style.marginBottom='4px'; s1.textContent=':'; timeRow.appendChild(s1);
        timeRow.appendChild(makeSsDigit('m0', 46, 70, 56));
        timeRow.appendChild(makeSsDigit('m1', 46, 70, 56));
        var s2 = document.createElement('div'); s2.className='ss-dsep'; s2.style.fontSize='50px'; s2.style.padding='0 4px'; s2.style.marginBottom='4px'; s2.textContent=':'; timeRow.appendChild(s2);
        timeRow.appendChild(makeSsDigit('s0', 46, 70, 56));
        timeRow.appendChild(makeSsDigit('s1', 46, 70, 56));

        root.appendChild(timeRow);
    }

    // ── DATE row ──
    if (mode === 'both' || mode === 'date') {
        var dateRow = document.createElement('div');
        dateRow.className = 'ss-row ss-date-panel';
        dateRow.style.alignItems = 'center';

        var dayWrap = document.createElement('div');
        dayWrap.className = 'ss-row';
        dayWrap.appendChild(makeSsDigit('d0', 36, 52, 38));
        dayWrap.appendChild(makeSsDigit('d1', 36, 52, 38));
        dateRow.appendChild(dayWrap);

        dateRow.appendChild(makeSsText('mon', 18, { color: 'rgba(255,255,255,0.85)', letterSpacing: '0.02em', minWidth: '92px' }));
        dateRow.appendChild(makeSsText('wday', 13, { color: 'rgba(255,255,255,0.5)', letterSpacing: '0.04em', textTransform: 'uppercase', minWidth: '110px' }));
        dateRow.appendChild(makeSsText('yr', 22, { color: 'rgba(255,255,255,0.55)', fontWeight: '700' }));

        root.appendChild(dateRow);
    }

    updateSsClock(true);
}

function updateSsClock(force) {
    var now  = new Date();
    var mode = kdSettings.screensaverMode || 'both';
    var animate = !force;

    if (mode === 'both' || mode === 'time') {
        var hh = now.getHours(),   mm = now.getMinutes(), ss = now.getSeconds();
        setSsDigitValue('h0', Math.floor(hh / 10), animate);
        setSsDigitValue('h1', hh % 10,             animate);
        setSsDigitValue('m0', Math.floor(mm / 10), animate);
        setSsDigitValue('m1', mm % 10,             animate);
        setSsDigitValue('s0', Math.floor(ss / 10), animate);
        setSsDigitValue('s1', ss % 10,             animate);
    }

    if (mode === 'both' || mode === 'date') {
        var day   = now.getDate();
        var month = now.getMonth();
        var wday  = now.getDay();
        var year  = now.getFullYear();

        setSsDigitValue('d0', Math.floor(day / 10), animate);
        setSsDigitValue('d1', day % 10,              animate);
        setSsTextValue('mon',  SS_MONTHS_GEN[month], animate);
        setSsTextValue('wday', SS_WEEKDAYS[wday],     animate);
        setSsTextValue('yr',   String(year),          animate);
    }
}


// ════════════════════════════════════════════
//  SNOWFLAKES
// ════════════════════════════════════════════
var snowCanvas       = null;
var snowCtx          = null;
var snowParticles     = [];
var snowAnimId        = null;
var snowResizeBound   = false;

// курсор
var kdMouseX = -9999, kdMouseY = -9999;
var SNOW_CURSOR_RADIUS = 95;
document.addEventListener('mousemove', function(e) {
    kdMouseX = e.clientX;
    kdMouseY = e.clientY;
});
document.addEventListener('mouseleave', function() { kdMouseX = -9999; kdMouseY = -9999; });

// случайная погода (слабый / средний / сильный снег / метель)
var snowWeatherTimer    = null;
var snowAutoIntensity   = 'normal';
var snowCurrentWind     = 0;   // фактический ветер (плавно стремится к snowTargetWind)
var snowTargetWind      = 0;

function initSnowCanvas() {
    if (snowCanvas) return;
    snowCanvas = document.createElement('canvas');
    snowCanvas.id = 'kdSnowCanvas';
    snowCanvas.style.position      = 'fixed';
    snowCanvas.style.inset         = '0';
    snowCanvas.style.zIndex        = '500';
    snowCanvas.style.pointerEvents = 'none';
    snowCanvas.style.display       = 'none';
    document.body.appendChild(snowCanvas);
    snowCtx = snowCanvas.getContext('2d');
    resizeSnowCanvas();
    if (!snowResizeBound) {
        window.addEventListener('resize', resizeSnowCanvas);
        snowResizeBound = true;
    }
}

function resizeSnowCanvas() {
    if (!snowCanvas) return;
    snowCanvas.width  = window.innerWidth;
    snowCanvas.height = window.innerHeight;
}

function getEffectiveSnowIntensity() {
    return kdSettings.snowIntensity === 'auto' ? snowAutoIntensity : kdSettings.snowIntensity;
}

function snowCountFor(intensity) {
    if (intensity === 'light')    return 45;
    if (intensity === 'heavy')    return 175;
    if (intensity === 'blizzard') return 260;
    return 90; // normal
}

function snowWindFor(intensity) {
    if (intensity === 'light')    return 0;
    if (intensity === 'heavy')    return 0.55;
    if (intensity === 'blizzard') return 1.7;
    return 0.12; // normal
}

function makeSnowParticle(randomY, intensity) {
    var w = (snowCanvas && snowCanvas.width)  || window.innerWidth;
    var h = (snowCanvas && snowCanvas.height) || window.innerHeight;
    var speedMul = intensity === 'blizzard' ? 1.9 : intensity === 'heavy' ? 1.2 : 1;
    var sizeMul  = intensity === 'blizzard' ? 0.7  : 1;
    return {
        x: Math.random() * w,
        y: randomY ? Math.random() * h : -10,
        r: (Math.random() * 2.4 + 1.1) * sizeMul,
        speed: (Math.random() * 1 + 0.5) * speedMul,
        drift: Math.random() * 0.6 - 0.3,
        sway: Math.random() * Math.PI * 2,
        swaySpeed: Math.random() * 0.02 + 0.006,
        opacity: Math.random() * 0.5 + 0.4,
        shape: Math.random() < 0.2 ? 'star' : 'dot'
    };
}

function spawnSnowParticles() {
    var intensity = getEffectiveSnowIntensity();
    var count = snowCountFor(intensity);
    snowParticles = [];
    for (var i = 0; i < count; i++) snowParticles.push(makeSnowParticle(true, intensity));
    snowTargetWind = snowWindFor(intensity);
}

function clearSnowWeatherSchedule() {
    if (snowWeatherTimer) { clearTimeout(snowWeatherTimer); snowWeatherTimer = null; }
}

// каждые ~18-50 секунд погода может случайно поменяться: слабый/средний/сильный снег или метель
function scheduleSnowWeatherChange() {
    clearSnowWeatherSchedule();
    var delay = 18000 + Math.random() * 32000;
    snowWeatherTimer = setTimeout(function() {
        if (!kdSettings.snowEnabled || kdSettings.snowIntensity !== 'auto') return;
        var pool = ['light', 'light', 'normal', 'normal', 'normal', 'heavy', 'heavy', 'blizzard'];
        var next = pool[Math.floor(Math.random() * pool.length)];
        var tries = 0;
        while (next === snowAutoIntensity && tries < 5) {
            next = pool[Math.floor(Math.random() * pool.length)];
            tries++;
        }
        snowAutoIntensity = next;
        spawnSnowParticles();
        scheduleSnowWeatherChange();
    }, delay);
}

function startSnow() {
    if (!kdSettings.snowEnabled) return;
    initSnowCanvas();
    spawnSnowParticles();
    snowCanvas.style.display = 'block';
    if (!snowAnimId) animateSnow();
    clearSnowWeatherSchedule();
    if (kdSettings.snowIntensity === 'auto') scheduleSnowWeatherChange();
}

function stopSnow() {
    if (snowAnimId) { cancelAnimationFrame(snowAnimId); snowAnimId = null; }
    if (snowCanvas) snowCanvas.style.display = 'none';
    clearSnowWeatherSchedule();
}

// маленькая 6-лучевая снежинка для крупных хлопьев (вместо обычной точки)
function drawSnowflakeShape(ctx, x, y, r) {
    ctx.save();
    ctx.translate(x, y);
    ctx.strokeStyle = '#ffffff';
    ctx.lineWidth = Math.max(0.6, r * 0.22);
    ctx.beginPath();
    for (var a = 0; a < 6; a++) {
        var ang = a * Math.PI / 3;
        ctx.moveTo(0, 0);
        ctx.lineTo(Math.cos(ang) * r, Math.sin(ang) * r);
    }
    ctx.stroke();
    ctx.restore();
}

function animateSnow() {
    if (!kdSettings.snowEnabled || !snowCtx || !snowCanvas) { snowAnimId = null; return; }
    var w = snowCanvas.width, h = snowCanvas.height;
    snowCtx.clearRect(0, 0, w, h);

    // плавный переход ветра при смене погоды/интенсивности
    snowCurrentWind += (snowTargetWind - snowCurrentWind) * 0.01;

    for (var i = 0; i < snowParticles.length; i++) {
        var p = snowParticles[i];

        // взаимодействие с курсором: снежинки разлетаются и закручиваются вокруг него
        var dx = p.x - kdMouseX, dy = p.y - kdMouseY;
        var dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < SNOW_CURSOR_RADIUS && dist > 0.01) {
            var force = (1 - dist / SNOW_CURSOR_RADIUS);
            var nx = dx / dist, ny = dy / dist;
            var tx = -ny, ty = nx; // перпендикуляр — для эффекта закручивания
            p.x += (nx * 2.1 + tx * 1.4) * force;
            p.y += (ny * 2.1 + ty * 1.4) * force;
        }

        p.sway += p.swaySpeed;
        p.x += snowCurrentWind + p.drift + Math.sin(p.sway) * 0.45;
        p.y += p.speed;

        if (p.y > h + 10) { p.y = -10; p.x = Math.random() * w; }
        if (p.x > w + 10) p.x = -10;
        if (p.x < -10) p.x = w + 10;

        snowCtx.globalAlpha = p.opacity;
        snowCtx.fillStyle = '#ffffff';
        if (p.shape === 'star') {
            drawSnowflakeShape(snowCtx, p.x, p.y, p.r * 1.6);
        } else {
            snowCtx.beginPath();
            snowCtx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            snowCtx.fill();
        }
    }
    snowCtx.globalAlpha = 1;

    snowAnimId = requestAnimationFrame(animateSnow);
}

// Останавливаем анимацию, когда вкладка не активна (экономия ресурсов)
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        if (snowAnimId) { cancelAnimationFrame(snowAnimId); snowAnimId = null; }
    } else if (kdSettings.snowEnabled) {
        animateSnow();
    }
});


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
    document.getElementById('ctx-delete').onclick  = function() {
        closeCtxMenu();
        kdConfirm('Переместить «' + name + '» в корзину?', function() {
            window.location.href = '?path=' + encodeURIComponent(path) + '&action=delete';
        }, {title: 'Удаление', okText: 'В корзину', danger: true});
    };
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
//  IMAGE ZOOM (приближение/отдаление колёсиком мыши + панорамирование)
// ════════════════════════════════════════════

/* attachImageZoom — навешивает зум на пару (обёртка, картинка).
   controls (необязательно) — объект с id кнопок { zoomIn, zoomOut, reset, label } */
function attachImageZoom(wrap, img, controls) {
    if (!wrap || !img) return;
    var scale = 1, minScale = 0.2, maxScale = 8;
    var posX = 0, posY = 0;
    var isDragging = false, dragStartX = 0, dragStartY = 0, startPosX = 0, startPosY = 0;

    function applyTransform() {
        img.style.transform = 'translate(' + posX + 'px, ' + posY + 'px) scale(' + scale + ')';
        img.style.cursor = scale > 1 ? 'grab' : 'zoom-in';
        if (controls && controls.label) controls.label.textContent = Math.round(scale * 100) + '%';
        if (controls && controls.reset) controls.reset.style.display = (scale !== 1 || posX !== 0 || posY !== 0) ? 'flex' : 'none';
    }

    function resetZoom() {
        scale = 1; posX = 0; posY = 0;
        applyTransform();
    }

    /* zoomBy — масштабирует относительно точки (clientX, clientY) на экране */
    function zoomBy(factor, clientX, clientY) {
        var rect = img.getBoundingClientRect();
        var prevScale = scale;
        scale = Math.min(maxScale, Math.max(minScale, scale * factor));
        var scaleRatio = scale / prevScale;
        if (scaleRatio === 1) return;
        var cx = (clientX !== undefined) ? clientX : (rect.left + rect.width / 2);
        var cy = (clientY !== undefined) ? clientY : (rect.top + rect.height / 2);
        var offsetX = cx - (rect.left + rect.width / 2);
        var offsetY = cy - (rect.top + rect.height / 2);
        posX -= offsetX * (scaleRatio - 1);
        posY -= offsetY * (scaleRatio - 1);
        applyTransform();
    }

    wrap.addEventListener('wheel', function(e) {
        e.preventDefault();
        zoomBy(e.deltaY < 0 ? 1.15 : 1 / 1.15, e.clientX, e.clientY);
    }, { passive: false });

    img.addEventListener('mousedown', function(e) {
        if (scale <= 1) return;
        isDragging = true;
        dragStartX = e.clientX; dragStartY = e.clientY;
        startPosX = posX; startPosY = posY;
        img.style.cursor = 'grabbing';
        e.preventDefault();
    });
    window.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        posX = startPosX + (e.clientX - dragStartX);
        posY = startPosY + (e.clientY - dragStartY);
        applyTransform();
    });
    window.addEventListener('mouseup', function() {
        if (isDragging) { isDragging = false; img.style.cursor = scale > 1 ? 'grab' : 'zoom-in'; }
    });

    img.addEventListener('dblclick', function() { resetZoom(); });
    img.addEventListener('click', function(e) {
        // одиночный клик увеличивает (если не было перетаскивания), пока не зажат drag
        if (e.detail === 1 && scale === 1) {
            // лёгкий зум по клику для удобства на тач-устройствах без колеса
        }
    });

    if (controls) {
        if (controls.zoomIn)  controls.zoomIn.addEventListener('click', function() { zoomBy(1.3); });
        if (controls.zoomOut) controls.zoomOut.addEventListener('click', function() { zoomBy(1 / 1.3); });
        if (controls.reset)   controls.reset.addEventListener('click', resetZoom);
    }

    applyTransform();
}

(function() {
    var wrap = document.getElementById('imageViewerWrap');
    var img  = document.getElementById('imageViewerImg');
    if (wrap && img) {
        attachImageZoom(wrap, img, {
            zoomIn:  document.getElementById('imageViewerZoomInBtn'),
            zoomOut: document.getElementById('imageViewerZoomOutBtn'),
            reset:   document.getElementById('imageViewerResetBtn'),
            label:   document.getElementById('imageViewerZoomLabel')
        });
    }
})();

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
    // Панель поиска находится внутри сайдбара — на мобильных её не видно,
    // если сам сайдбар (файловый менеджер) ещё не открыт. Открываем его.
    if (typeof isMobile === 'function' && isMobile() && sidebar && !sidebar.classList.contains('active')) {
        sidebar.classList.add('active');
        if (sidebarOverlay) sidebarOverlay.classList.add('active');
        if (typeof setExplorerActive === 'function') setExplorerActive(true);
    }
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
    kdConfirm('Удалить файл навсегда? Это действие необратимо.', function() {
        window.location.href = '?path=&action=trash_delete&tn=' + encodeURIComponent(tn);
    }, {title: 'Удаление навсегда', okText: 'Удалить навсегда', danger: true});
}
function trashEmpty() {
    kdConfirm('Очистить корзину полностью? Это действие необратимо!', function() {
        window.location.href = '?path=&action=trash_empty';
    }, {title: 'Очистка корзины', okText: 'Очистить', danger: true});
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
                container.innerHTML =
                    '<div id="trashImagePreviewWrap" style="position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;width:100%;height:100%;">' +
                        '<img src="' + data.src + '" id="trashImagePreviewImg" draggable="false" style="max-width:100%;max-height:60vh;object-fit:contain;border-radius:4px;user-select:none;cursor:zoom-in;">' +
                        '<div class="image-viewer-controls" style="opacity:1;pointer-events:all;">' +
                            '<button class="image-viewer-btn" id="trashImgZoomOutBtn" title="Уменьшить"><i class="fas fa-minus"></i></button>' +
                            '<span class="image-viewer-zoom-label" id="trashImgZoomLabel">100%</span>' +
                            '<button class="image-viewer-btn" id="trashImgZoomInBtn" title="Увеличить"><i class="fas fa-plus"></i></button>' +
                            '<button class="image-viewer-btn" id="trashImgResetBtn" title="Сбросить масштаб" style="display:none;"><i class="fas fa-compress-arrows-alt"></i></button>' +
                        '</div>' +
                    '</div>';
                attachImageZoom(
                    document.getElementById('trashImagePreviewWrap'),
                    document.getElementById('trashImagePreviewImg'),
                    {
                        zoomIn:  document.getElementById('trashImgZoomInBtn'),
                        zoomOut: document.getElementById('trashImgZoomOutBtn'),
                        reset:   document.getElementById('trashImgResetBtn'),
                        label:   document.getElementById('trashImgZoomLabel')
                    }
                );
            } else if (data.type === 'text') {
                container.style.cssText = 'flex:1;overflow:hidden;';
                container.innerHTML = '<div id="trashPreviewEditor" style="width:100%;height:60vh;"></div>';
                require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }});
                require(['vs/editor/editor.main'], function() {
                    ensureKdBlackMonacoTheme();
                    var langMap = {
                        php:'php',js:'javascript',ts:'typescript',html:'html',css:'css',
                        json:'json',lua:'lua',py:'python',sh:'shell',sql:'sql',
                        md:'markdown',xml:'xml',yaml:'yaml',yml:'yaml',txt:'plaintext',log:'plaintext'
                    };
                    monaco.editor.create(document.getElementById('trashPreviewEditor'), {
                        value: data.content,
                        language: langMap[data.ext] || 'plaintext',
                        theme: monacoThemeFor(kdSettings.themeMode),
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
    kdConfirm('Очистить весь лог действий?', function() {
        fetch('?ajax=log_clear').then(function() { openLog(); });
    }, {title: 'Очистка лога', okText: 'Очистить', danger: true});
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