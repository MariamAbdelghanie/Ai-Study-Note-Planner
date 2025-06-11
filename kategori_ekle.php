<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kategori_id = uniqid('cat_');
    $kategori_adi = $_POST['kategori_adi'];
    $aciklama = $_POST['aciklama'];

    try {
        $stmt = $pdo->prepare("CALL sp_KategoriEkle(?, ?, ?)");
        $stmt->execute([$kategori_id, $kategori_adi, $aciklama]);
        echo "<div class='success'>Kategori başarıyla eklendi!</div>";
    } catch (PDOException $e) {
        echo "<div class='error'>Hata: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kategori Ekle</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-folder-plus"></i> Kategori Ekle</h2>
        <form method="POST">
            <input type="text" name="kategori_adi" placeholder="Kategori Adı" required>
            <textarea name="aciklama" placeholder="Açıklama"></textarea>
            <button type="submit"><i class="fas fa-folder-plus"></i> Kategori Ekle</button>
        </form>
        <a href="index.php" class="back-link"><i class="fas fa-home"></i> Ana Sayfaya Dön</a>
    </div>
</body>
</html>


