<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

$gorevler = $pdo->query("SELECT gorev_id, baslik FROM gorevler")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hatirlatma_id = uniqid('rem_');
    $gorev_id = $_POST['gorev_id'];
    $hatirlatma_tarihi = $_POST['hatirlatma_tarihi'];
    $aciklama = $_POST['aciklama'];

    try {
        $stmt = $pdo->prepare("CALL sp_HatirlatmaEkle(?, ?, ?, ?)");
        $stmt->execute([$hatirlatma_id, $gorev_id, $hatirlatma_tarihi, $aciklama]);
        echo "<div class='success'>Hatırlatma başarıyla eklendi!</div>";
    } catch (PDOException $e) {
        echo "<div class='error'>Hata: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hatırlatma Ekle</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-bell"></i> Hatırlatma Ekle</h2>
        <form method="POST">
            <select name="gorev_id" required>
                <option value="">Görev Seç</option>
                <?php foreach ($gorevler as $gorev): ?>
                    <option value="<?php echo $gorev['gorev_id']; ?>">
                        <?php echo $gorev['baslik']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="datetime-local" name="hatirlatma_tarihi" required>
            <textarea name="aciklama" placeholder="Hatırlatma Açıklaması"></textarea>
            <button type="submit"><i class="fas fa-bell"></i> Hatırlatma Ekle</button>
        </form>
        <a href="index.php" class="back-link"><i class="fas fa-home"></i> Ana Sayfaya Dön</a>
    </div>
</body>
</html>



