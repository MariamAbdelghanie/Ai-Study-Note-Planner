<?php
session_start();
include 'db_connect.php';

if (isset($_SESSION['kullanici_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE email = ?");
        $stmt->execute([$email]);
        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($kullanici && password_verify($sifre, $kullanici['sifre'])) {
            $_SESSION['kullanici_id'] = $kullanici['kullanici_id'];
            $_SESSION['ad'] = $kullanici['ad'];
            $_SESSION['soyad'] = $kullanici['soyad'];
            echo "<div class='success'>Giriş başarılı! Yönlendiriliyorsunuz...</div>";
            header("Refresh:2; url=index.php");
        } else {
            echo "<div class='error'>Geçersiz email veya şifre!</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='error'>Hata: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-sign-in-alt"></i> Giriş Yap</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="sifre" placeholder="Şifre" required>
            <button type="submit"><i class="fas fa-sign-in-alt"></i> Giriş Yap</button>
        </form>
        <p>Hesabınız yok mu? <a href="register.php" class="back-link"><i class="fas fa-user-plus"></i> Kayıt Ol</a></p>
    </div>
</body>
</html>






