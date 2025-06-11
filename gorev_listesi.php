<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("
    SELECT g.*, k.kategori_adi 
    FROM gorevler g 
    LEFT JOIN kategoriler k ON g.kategori_id = k.kategori_id
");
$gorevler = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görev Listesi</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-tasks"></i> Görev Listesi</h2>
        <table>
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Açıklama</th>
                    <th>Durum</th>
                    <th>Öncelik</th>
                    <th>Bitiş Tarihi</th>
                    <th>Kategori</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gorevler as $gorev): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($gorev['baslik']); ?></td>
                        <td><?php echo htmlspecialchars($gorev['aciklama'] ?? ''); ?></td>
                        <td><?php echo $gorev['durum']; ?></td>
                        <td><?php echo $gorev['oncelik']; ?></td>
                        <td><?php echo $gorev['bitis_tarihi']; ?></td>
                        <td><?php echo htmlspecialchars($gorev['kategori_adi'] ?? 'Yok'); ?></td>
                        <td>
                            <a href="gorev_guncelle.php?gorev_id=<?php echo $gorev['gorev_id']; ?>"><i class="fas fa-edit"></i> Güncelle</a>
                            <a href="gorev_sil.php?gorev_id=<?php echo $gorev['gorev_id']; ?>" onclick="return confirm('Bu görevi silmek istediğinize emin misiniz?');"><i class="fas fa-trash"></i> Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="back-link"><i class="fas fa-home"></i> Ana Sayfaya Dön</a>
    </div>
</body>
</html>



