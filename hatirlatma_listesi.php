<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("
    SELECT h.hatirlatma_id, h.gorev_id, h.hatirlatma_tarihi, h.aciklama, g.baslik 
    FROM hatirlatmalar h
    LEFT JOIN gorevler g ON h.gorev_id = g.gorev_id
");
$hatirlatmalar = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hatırlatma Listesi</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-bell"></i> Hatırlatma Listesi</h2>
        <?php if (count($hatirlatmalar) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Hatırlatma ID</th>
                    <th>Görev Başlığı</th>
                    <th>Hatırlatma Tarihi</th>
                    <th>Açıklama</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hatirlatmalar as $hatirlatma): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($hatirlatma['hatirlatma_id']); ?></td>
                        <td><?php echo htmlspecialchars($hatirlatma['baslik'] ?? 'Görev Silinmiş'); ?></td>
                        <td><?php echo $hatirlatma['hatirlatma_tarihi']; ?></td>
                        <td><?php echo htmlspecialchars($hatirlatma['aciklama'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="error">Henüz hatırlatma bulunmamaktadır!</div>
        <?php endif; ?>
        <a href="index.php" class="back-link"><i class="fas fa-home"></i> Ana Sayfaya Dön</a>
    </div>
</body>
</html>


