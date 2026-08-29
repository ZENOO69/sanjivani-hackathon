<?php
/**
 * ====================================================================
 * FASAL - Automated FTP Deployment Script
 * ====================================================================
 * Uploads project files directly to the live server using env.php credentials
 */

if (php_sapi_name() !== 'cli') {
    die("CLI only.");
}

$envFile = __DIR__ . '/env.php';
if (!file_exists($envFile)) {
    die("❌ env.php not found.\n");
}

$env = include $envFile;
$ftpHost = isset($env['ftp_host']) ? $env['ftp_host'] : '';
$ftpUser = isset($env['ftp_user']) ? $env['ftp_user'] : '';
$ftpPass = isset($env['ftp_pass']) ? $env['ftp_pass'] : '';
$ftpPort = isset($env['ftp_port']) ? (int)$env['ftp_port'] : 21;
$remoteBase = isset($env['ftp_remote_path']) ? rtrim($env['ftp_remote_path'], '/') : '/public_html';
$useSsl = !empty($env['ftp_ssl']);

if (empty($ftpHost) || strpos($ftpHost, 'yourdomain') !== false || empty($ftpUser)) {
    die("⚠️ Please enter your real FTP credentials in env.php first.\n");
}

echo "🚀 Connecting to FTP {$ftpHost}:{$ftpPort} as {$ftpUser}...\n";

$conn = $useSsl ? @ftp_ssl_connect($ftpHost, $ftpPort, 15) : @ftp_connect($ftpHost, $ftpPort, 15);
if (!$conn) {
    die("❌ Could not connect to FTP host {$ftpHost}\n");
}

if (!@ftp_login($conn, $ftpUser, $ftpPass)) {
    ftp_close($conn);
    die("❌ FTP Login failed for user {$ftpUser}\n");
}

ftp_pasv($conn, true);
echo "✅ Connected and authenticated successfully!\n";

// Helper to ensure remote directory exists
function ftpEnsureDir($conn, $dir) {
    $parts = explode('/', trim($dir, '/'));
    $current = '';
    foreach ($parts as $part) {
        $current .= '/' . $part;
        if (!@ftp_chdir($conn, $current)) {
            @ftp_mkdir($conn, $current);
        }
    }
}

// Files/folders to exclude from upload
$ignore = array(
    '.git',
    '.gitignore',
    'deploy.php',
    'README.md',
    'ps.txt',
    'ESP8266-Code',
);

$rootDir = __DIR__;
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$uploadedCount = 0;

foreach ($files as $file) {
    $relPath = str_replace('\\', '/', substr($file->getPathname(), strlen($rootDir) + 1));
    
    // Check exclusions
    $skip = false;
    foreach ($ignore as $ig) {
        if ($relPath === $ig || strpos($relPath, $ig . '/') === 0) {
            $skip = true;
            break;
        }
    }
    if ($skip) continue;

    $remotePath = $remoteBase . '/' . $relPath;

    if ($file->isDir()) {
        ftpEnsureDir($conn, $remotePath);
    } else {
        $remoteDir = dirname($remotePath);
        ftpEnsureDir($conn, $remoteDir);
        
        echo "📤 Uploading: {$relPath} -> {$remotePath}... ";
        if (@ftp_put($conn, $remotePath, $file->getPathname(), FTP_BINARY)) {
            echo "✅\n";
            $uploadedCount++;
        } else {
            echo "❌ FAILED\n";
        }
    }
}

ftp_close($conn);
echo "\n🎉 Deployment Complete! Total files uploaded: {$uploadedCount}\n";
