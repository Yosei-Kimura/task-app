<?php
// 設定キャッシュをクリアする一時的なスクリプト
require_once 'vendor/autoload.php';

// キャッシュディレクトリのパス
$cacheDir = __DIR__ . '/bootstrap/cache/';

// 設定キャッシュファイルを削除
if (file_exists($cacheDir . 'config.php')) {
    unlink($cacheDir . 'config.php');
    echo "config.php deleted\n";
}

// ルートキャッシュファイルを削除
if (file_exists($cacheDir . 'routes-v7.php')) {
    unlink($cacheDir . 'routes-v7.php');
    echo "routes-v7.php deleted\n";
}

// その他のキャッシュファイルも削除
$cacheFiles = glob($cacheDir . '*.php');
foreach ($cacheFiles as $file) {
    if (basename($file) !== 'packages.php' && basename($file) !== 'services.php') {
        unlink($file);
        echo "Deleted: " . basename($file) . "\n";
    }
}

echo "Cache cleared successfully!\n";
