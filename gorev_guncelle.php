<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

$kategoriler = $pdo->query("SELECT kategori_id, kategori_adi FROM kategoriler")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['gorev_id'])) {
    $gorev_id = $_GET['gorev_id'];
    $stmt = $pdo->prepare("SELECT * FROM gorevler WHERE gorev_id = ?");
    $stmt->execute([$gorev_id]);
    $gorev = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gorev_id = $_POST['gorev_id'];
    $baslik = $_POST['baslik'];
    $aciklama = $_POST['aciklama'];
    $durum = $_POST['durum'];
    $oncelik = $_POST['oncelik'];
    $bitis_tarihi = $_POST['bitis_tarihi'];
    $kategori_id = $_POST['kategori_id'] ?: '';

    try {
        $stmt = $pdo->prepare("CALL sp_GorevGuncelle(?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$gorev_id, $baslik, $aciklama, $durum, $oncelik, $bitis_tarihi, $kategori_id]);
        echo "<div class='success'>Görev başarıyla güncellendi!</div>";
    } catch (PDOException $e) {
        echo "<div class='error'>Hata: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görev Güncelle</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-edit"></i> Görev Güncelle</h2>
        <?php if (isset($gorev)): ?>
        <form method="POST">
            <input type="hidden" name="gorev_id" value="<?php echo $gorev['gorev_id']; ?>">
            <input type="text" name="baslik" value="<?php echo htmlspecialchars($gorev['baslik']); ?>" required>
            <textarea name="aciklama"><?php echo htmlspecialchars($gorev['aciklama'] ?? ''); ?></textarea>
            <select name="durum">
                <option value="Yapılacak" <?php echo $gorev['durum'] == 'Yapılacak' ? 'selected' : ''; ?>>Yapılacak</option>
                <option value="Devam Ediyor" <?php echo $gorev['durum'] == 'Devam Ediyor' ? 'selected' : ''; ?>>Devam Ediyor</option>
                <option value="Tamamlandı" <?php echo $gorev['durum'] == 'Tamamlandı' ? 'selected' : ''; ?>>Tamamlandı</option>
                <option value="Gecikti" <?php echo $gorev['durum'] == 'Gecikti' ? 'selected' : ''; ?>>Gecikti</option>
            </select>
            <select name="oncelik">
                <option value="Düşük" <?php echo $gorev['oncelik'] == 'Düşük' ? 'selected' : ''; ?>>Düşük</option>
                <option value="Orta" <?php echo $gorev['oncelik'] == 'Orta' ? 'selected' : ''; ?>>Orta</option>
                <option value="Yüksek" <?php echo $gorev['oncelik'] == 'Yüksek' ? 'selected' : ''; ?>>Yüksek</option>
            </select>
            <input type="datetime-local" name="bitis_tarihi" value="<?php echo date('Y-m-d\TH:i', strtotime($gorev['bitis_tarihi'])); ?>" required>
            <select name="kategori_id">
                <option value="">Kategori Seç (Opsiyonel)</option>
                <?php foreach ($kategoriler as $kategori): ?>
                    <option value="<?php echo $kategori['kategori_id']; ?>" <?php echo $gorev['kategori_id'] == $kategori['kategori_id'] ? 'selected' : ''; ?>>
                        <?php echo $kategori['kategori_adi']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit"><i class="fas fa-edit"></i> Görev Güncelle</button>
        </form>
        <?php else: ?>
            <div class='error'>Görev bulunamadı!</div>
        <?php endif; ?>
        <a href="gorev_listesi.php" class="back-link"><i class="fas fa-list"></i> Görev Listesine Dön</a>
    </div>
</body>
</html>


