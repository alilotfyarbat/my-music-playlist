<?php
// تنظیمات اتصال به دیتابیس
$host = 'localhost';
$dbname = 'music';
$username = 'music';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // تنظیم حالت خطا به استثنا
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // تنظیم حالت بازگشت به آرایه انجمنی
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die(json_encode(['error' => 'خطا در اتصال به دیتابیس: ' . $e->getMessage()]));
}