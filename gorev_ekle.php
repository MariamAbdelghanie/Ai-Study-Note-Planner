<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

$kategoriler = $pdo->query("SELECT kategori_id, kategori_adi FROM kategoriler")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gorev_id = uniqid('tsk_');
    $baslik = $_POST['baslik'];
    $aciklama = $_POST['aciklama'];
    $durum = $_POST['durum'];
    $oncelik = $_POST['oncelik'];
    $bitis_tarihi = $_POST['bitis_tarihi'];
    $kategori_id = $_POST['kategori_id'] ?: '';

    try {
        $stmt = $pdo->prepare("CALL sp_GorevEkle(?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$gorev_id, $baslik, $aciklama, $durum, $oncelik, $bitis_tarihi, $kategori_id]);
        echo "<div class='success'>Görev başarıyla eklendi!</div>";
    } catch (PDOException $e) {
        echo "<div class='error'>Hata: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görev Ekle</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-plus"></i> Görev Ekle</h2>
        <form method="POST">
            <input type="text" name="baslik" placeholder="Görev Başlığı" required>
            <textarea name="aciklama" placeholder="Açıklama"></textarea>
            <select name="durum">
                <option value="Yapılacak">Yapılacak</option>
                <option value="Devam Ediyor">Devam Ediyor</option>
                <option value="Tamamlandı">Tamamlandı</option>
                <option value="Gecikti">Gecikti</option>
            </select>
            <select name="oncelik">
                <option value="Düşük">Düşük</option>
                <option value="Orta">Orta</option>
                <option value="Yüksek">Yüksek</option>
            </select>
            <input type="datetime-local" name="bitis_tarihi" required>
            <select name="kategori_id">
                <option value="">Kategori Seç (Opsiyonel)</option>
                <?php foreach ($kategoriler as $kategori): ?>
                    <option value="<?php echo $kategori['kategori_id']; ?>">
                        <?php echo $kategori['kategori_adi']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit"><i class="fas fa-plus"></i> Görev Ekle</button>
        </form>
        <a href="index.php" class="back-link"><i class="fas fa-home"></i> Ana Sayfaya Dön</a>
    </div>
</body>
</html>
