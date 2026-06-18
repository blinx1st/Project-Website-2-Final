<?php
declare(strict_types=1);

// File bootstrap của mô hình MVC: khởi tạo session, nạp core class và giao request cho Router.
session_start();
header('Content-Type: text/html; charset=UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');
define('PUBLIC_PATH', __DIR__);

require_once APP_PATH . '/core/helpers.php';
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Repository.php';
require_once APP_PATH . '/core/Validator.php';
require_once APP_PATH . '/core/Mailer.php';
require_once APP_PATH . '/core/Router.php';

(new Router())->dispatch();
