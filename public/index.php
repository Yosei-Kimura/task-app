<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 本番環境用の強制的な環境変数設定
if (!defined('LARAVEL_START')) {
    // すでに定義されているので、この条件は実行されない
}

// データベース設定を強制的に設定（本番環境用）
$_ENV['DB_CONNECTION'] = 'mysql';
$_ENV['DB_HOST'] = 'mysql322.phy.lolipop.lan';
$_ENV['DB_PORT'] = '3306';
$_ENV['DB_DATABASE'] = 'LAA0956269-taskapp';
$_ENV['DB_USERNAME'] = 'LAA0956269';
$_ENV['DB_PASSWORD'] = 'marie2011';

putenv('DB_CONNECTION=mysql');
putenv('DB_HOST=mysql322.phy.lolipop.lan');
putenv('DB_PORT=3306');
putenv('DB_DATABASE=LAA0956269-taskapp');
putenv('DB_USERNAME=LAA0956269');
putenv('DB_PASSWORD=marie2011');

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
