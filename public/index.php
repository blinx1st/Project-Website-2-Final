<?php
declare(strict_types=1);

// File bootstrap của mô hình MVC: khởi tạo session, nạp core class và giao request cho Router.
// Session phải mở trước controller vì đăng nhập/phân quyền đọc ghi dữ liệu tại đây.
session_start();
header('Content-Type: text/html; charset=UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

// Ba hằng đường dẫn giúp các lớp ghép file ổn định, không phụ thuộc thư mục chạy hiện tại của PHP.
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');
define('PUBLIC_PATH', __DIR__);

// Nạp theo thứ tự phụ thuộc: helper/hạ tầng trước, Router sau cùng.
require_once APP_PATH . '/core/helpers.php';
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Repository.php';
require_once APP_PATH . '/core/Validator.php';
require_once APP_PATH . '/core/Mailer.php';
require_once APP_PATH . '/core/Router.php';

// Mọi request không phải asset đều kết thúc tại đây rồi Router chọn controller/action tương ứng.
(new Router())->dispatch();
