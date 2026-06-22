<?php
// Quản lý một kết nối PDO dùng chung trong suốt vòng đời của request hiện tại.
class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        // Chỉ khởi tạo khi Repository thực sự cần database; các lần sau tái sử dụng cùng kết nối.
        if (self::$connection === null) {
            $config = require APP_PATH . '/config/database.php';
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['host'], $config['database'], $config['charset']);
            // Bật exception và prepared statement thật để lỗi SQL được xử lý rõ ràng, an toàn hơn.
            self::$connection = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$connection;
    }
}
