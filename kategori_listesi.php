<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT kategori_id, kategori_adi, aciklama FROM kategoriler");
$kategoriler = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kategori Listesi</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-list"></i> Kategori Listesi</h2>
        <table>
            <thead>
                <tr>
                    <th>Kategori Adı</th>
                    <th>Açıklama</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kategoriler as $kategori): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($kategori['kategori_adi']); ?></td>
                        <td><?php echo htmlspecialchars($kategori['aciklama'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="back-link"><i class="fas fa-home"></i> Ana Sayfaya Dön</a>
    </div>
</body>
</html>


