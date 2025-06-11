<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

$kullanicilar = $pdo->query("SELECT kullanici_id, CONCAT(ad, ' ', soyad) AS ad_soyad FROM kullanicilar")->fetchAll(PDO::FETCH_ASSOC);
$gorevler = $pdo->query("SELECT gorev_id, baslik FROM gorevler")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = uniqid('gu_');
    $gorev_id = $_POST['gorev_id'];
    $kullanici_id = $_POST['kullanici_id'];

    try {
        $stmt = $pdo->prepare("CALL sp_GorevKullaniciEkle(?, ?, ?)");
        $stmt->execute([$id, $gorev_id, $kullanici_id]);
        echo "<div class='success'>Kullanıcı göreve başarıyla atandı!</div>";
    } catch (PDOException $e) {
        echo "<div class='error'>Hata: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Göreve Kullanıcı Ata</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-user-tag"></i> Göreve Kullanıcı Ata</h2>
        <form method="POST">
            <select name="gorev_id" required>
                <option value="">Görev Seç</option>
                <?php foreach ($gorevler as $gorev): ?>
                    <option value="<?php echo $gorev['gorev_id']; ?>">
                        <?php echo $gorev['baslik']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="kullanici_id" required>
                <option value="">Kullanıcı Seç</option>
                <?php foreach ($kullanicilar as $kullanici): ?>
                    <option value="<?php echo $kullanici['kullanici_id']; ?>">
                        <?php echo $kullanici['ad_soyad']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit"><i class="fas fa-user-tag"></i> Kullanıcıyı Ata</button>
        </form>
        <a href="index.php" class="back-link"><i class="fas fa-home"></i> Ana Sayfaya Dön</a>
    </div>
</body>
</html>


