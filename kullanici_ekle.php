<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici_id = uniqid('usr_');
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $email = $_POST['email'];
    $sifre = password_hash($_POST['sifre'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("CALL sp_KullaniciEkle(?, ?, ?, ?, ?)");
        $stmt->execute([$kullanici_id, $ad, $soyad, $email, $sifre]);
        echo "<div class='success'>Kullanıcı başarıyla eklendi!</div>";
    } catch (PDOException $e) {
        echo "<div class='error'>Hata: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Ekle</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-user-plus"></i> Kullanıcı Ekle</h2>
        <form method="POST">
            <input type="text" name="ad" placeholder="Ad" required>
            <input type="text" name="soyad" placeholder="Soyad" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="sifre" placeholder="Şifre" required>
            <button type="submit"><i class="fas fa-user-plus"></i> Kullanıcı Ekle</button>
        </form>
        <a href="index.php" class="back-link"><i class="fas fa-home"></i> Ana Sayfaya Dön</a>
    </div>
</body>
</html>