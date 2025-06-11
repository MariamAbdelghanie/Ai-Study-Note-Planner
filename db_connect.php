<!-- db_connect.php -->
<?php
$host = 'localhost';
$dbname = 'personal_task_db';
$username = 'root'; // مستخدم XAMPP الافتراضي
$password = ''; // كلمة المرور الافتراضية (فارغة)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div class='error'>Bağlantı hatası: " . $e->getMessage() . "</div>");
}
?>


