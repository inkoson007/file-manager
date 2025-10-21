<?php
// Устанавливаем заголовок UTF-8
header('Content-Type: text/html; charset=utf-8');

// Получаем информацию о сервере
$serverInfo = [
    'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
    'Server IP' => $_SERVER['SERVER_ADDR'] ?? 'N/A',
    'Server Port' => $_SERVER['SERVER_PORT'] ?? 'N/A',
    'Server Protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'N/A',
    'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    'PHP Version' => phpversion(),
    'Zend Version' => zend_version(),
    'OS' => php_uname(),
    'Loaded PHP Extensions' => implode(', ', get_loaded_extensions()),
    'Max Execution Time' => ini_get('max_execution_time') . ' sec',
    'Memory Limit' => ini_get('memory_limit'),
    'Upload Max Filesize' => ini_get('upload_max_filesize'),
    'Post Max Size' => ini_get('post_max_size'),
    'MySQL Client Version' => function_exists('mysql_get_client_info') ? mysql_get_client_info() : 'N/A',
    'MySQL Server Version' => function_exists('mysql_get_server_info') ? mysql_get_server_info() : 'N/A',
    'PDO Drivers' => class_exists('PDO') ? implode(', ', PDO::getAvailableDrivers()) : 'N/A',
    'Current User' => get_current_user(),
    'Free Disk Space' => round(disk_free_space("/") / (1024 * 1024 * 1024), 2) . ' GB',
    'Total Disk Space' => round(disk_total_space("/") / (1024 * 1024 * 1024), 2) . ' GB',
    'CPU Cores' => function_exists('sys_getloadavg') ? sys_getloadavg()[0] : 'N/A',
    'System Load' => function_exists('sys_getloadavg') ? implode(', ', sys_getloadavg()) : 'N/A',
    'Included Files Count' => count(get_included_files()),
    'PHP SAPI' => php_sapi_name(),
    'HTTP Host' => $_SERVER['HTTP_HOST'] ?? 'N/A',
    'Client IP' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
    'User Agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
    'Request Method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
    'Request URI' => $_SERVER['REQUEST_URI'] ?? 'N/A',
    'Script Name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
    'Script Filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'N/A',
    'Request Time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? time()),
    'Timezone' => date_default_timezone_get(),
    'Session Status' => session_status() === PHP_SESSION_ACTIVE ? 'Active' : (session_status() === PHP_SESSION_NONE ? 'None' : 'Disabled'),
    'Cookie Parameters' => print_r(session_get_cookie_params(), true),
    'PHP INI File' => php_ini_loaded_file() ?: 'N/A',
    'Disabled Functions' => ini_get('disable_functions') ?: 'None',
    'OpenSSL Version' => OPENSSL_VERSION_TEXT ?? 'N/A',
    'cURL Version' => function_exists('curl_version') ? curl_version()['version'] : 'N/A',
    'GD Version' => function_exists('gd_info') ? gd_info()['GD Version'] : 'N/A',
    'Imagick Version' => class_exists('Imagick') ? Imagick::getVersion()['versionString'] : 'N/A',
    'Max Input Vars' => ini_get('max_input_vars'),
    'Allow URL Fopen' => ini_get('allow_url_fopen') ? 'Yes' : 'No',
    'Display Errors' => ini_get('display_errors') ? 'On' : 'Off',
    'Error Reporting' => error_reporting(),
    'Error Log' => ini_get('error_log') ?: 'N/A',
    'Last Error' => error_get_last() ? print_r(error_get_last(), true) : 'None',
    'Headers List' => headers_list() ? implode('<br>', headers_list()) : 'None',
    'Environment Variables' => print_r($_ENV, true),
    'Server Variables' => print_r($_SERVER, true),
    'PHP Configuration' => print_r(ini_get_all(), true),
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOFFEE DEVELOPER - Server Debug</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --header-height: 40px;
            --primary-color: #646cff;
            --header-bg: rgba(30, 30, 46, 0.7);
            --main-bg: rgba(40, 42, 54, 0.9);
            --text-color: #f8f8f2;
            --border-color: #44475a;
            --mac-btn-close: #ff5f56;
            --mac-btn-min: #ffbd2e;
            --mac-btn-max: #27c93f;
            --error-color: #ff6b6b;
            --success-color: #50fa7b;
            --warning-color: #ffb86c;
            --info-color: #8be9fd;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #282a36, #1e1e2e);
            color: var(--text-color);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* MacOS Window */
        .mac-window {
            width: 95%;
            max-width: 1200px;
            height: 90vh;
            background: var(--main-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Title Bar */
        .mac-title-bar {
            height: var(--header-height);
            background: var(--header-bg);
            display: flex;
            align-items: center;
            padding: 0 15px;
            -webkit-user-select: none;
            user-select: none;
            border-bottom: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
        }

        .mac-buttons {
            display: flex;
            gap: 8px;
            margin-right: 15px;
        }

        .mac-btn {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            cursor: pointer;
        }

        .mac-btn-close { background-color: var(--mac-btn-close); }
        .mac-btn-min { background-color: var(--mac-btn-min); }
        .mac-btn-max { background-color: var(--mac-btn-max); }

        .mac-title {
            flex: 1;
            text-align: center;
            font-size: 13px;
            color: #a9a9a9;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 10px;
        }

        /* Content Area */
        .mac-content {
            flex: 1;
            overflow: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        /* Debug Info */
        .debug-section {
            margin-bottom: 30px;
            background: rgba(30, 30, 46, 0.5);
            border-radius: 10px;
            padding: 20px;
            border: 1px solid var(--border-color);
        }

        .debug-section h2 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
        }

        .debug-section h2 i {
            margin-right: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            color: var(--info-color);
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .info-value {
            background: rgba(0, 0, 0, 0.2);
            padding: 10px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            word-break: break-all;
            overflow-x: auto;
            max-height: 200px;
            overflow-y: auto;
        }

        .info-value pre {
            margin: 0;
            white-space: pre-wrap;
        }

        /* Version Info */
        .version-info {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            z-index: 100;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mac-window {
                width: 98%;
                height: 95vh;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--main-bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
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
            <div class="mac-title">KOFFEE DEVELOPER — Server Debug Information</div>
        </div>
        
        <div class="mac-content">
            <!-- Basic Server Info -->
            <div class="debug-section">
                <h2><i class="fas fa-server"></i> Server Information</h2>
                <div class="info-grid">
                    <?php foreach($serverInfo as $label => $value): ?>
                        <?php if(!is_array($value) && !is_object($value)): ?>
                            <div class="info-item">
                                <div class="info-label"><?= htmlspecialchars($label) ?></div>
                                <div class="info-value"><?= htmlspecialchars($value) ?></div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- PHP Configuration -->
            <div class="debug-section">
                <h2><i class="fas fa-cog"></i> PHP Configuration</h2>
                <div class="info-item">
                    <div class="info-value"><pre><?= htmlspecialchars(print_r(ini_get_all(), true)) ?></pre></div>
                </div>
            </div>

            <!-- Server Variables -->
            <div class="debug-section">
                <h2><i class="fas fa-code-branch"></i> Server Variables</h2>
                <div class="info-item">
                    <div class="info-value"><pre><?= htmlspecialchars(print_r($_SERVER, true)) ?></pre></div>
                </div>
            </div>

            <!-- Environment Variables -->
            <div class="debug-section">
                <h2><i class="fas fa-globe"></i> Environment Variables</h2>
                <div class="info-item">
                    <div class="info-value"><pre><?= htmlspecialchars(print_r($_ENV, true)) ?></pre></div>
                </div>
            </div>

            <!-- Included Files -->
            <div class="debug-section">
                <h2><i class="fas fa-file-code"></i> Included Files (<?= count(get_included_files()) ?>)</h2>
                <div class="info-item">
                    <div class="info-value"><pre><?= htmlspecialchars(print_r(get_included_files(), true)) ?></pre></div>
                </div>
            </div>
        </div>
        
        <div class="version-info">KOFFEE DEVELOPER Debug v1.5.0</div>
    </div>

    <script>
        // Эффекты кнопок окна macOS
        const macBtnClose = document.querySelector('.mac-btn-close');
        const macBtnMin = document.querySelector('.mac-btn-min');
        const macBtnMax = document.querySelector('.mac-btn-max');
        
        macBtnClose.addEventListener('click', () => {
            document.querySelector('.mac-window').style.transform = 'scale(0.9)';
            setTimeout(() => {
                document.querySelector('.mac-window').style.display = 'none';
            }, 300);
        });
        
        macBtnMin.addEventListener('click', () => {
            document.querySelector('.mac-window').style.transform = 'translateY(100vh)';
            setTimeout(() => {
                document.querySelector('.mac-window').style.display = 'none';
            }, 300);
        });
        
        macBtnMax.addEventListener('click', () => {
            const window = document.querySelector('.mac-window');
            if (window.style.width === '100%') {
                window.style.width = '95%';
                window.style.height = '90vh';
                window.style.maxWidth = '1200px';
            } else {
                window.style.width = '100%';
                window.style.height = '100vh';
                window.style.maxWidth = 'none';
            }
        });
    </script>
</body>
</html>