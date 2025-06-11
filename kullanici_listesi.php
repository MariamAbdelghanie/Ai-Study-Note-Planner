<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT kullanici_id, ad, soyad, email, created_at FROM kullanicilar");
$kullanicilar = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Listesi</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-users"></i> Kullanıcı Listesi</h2>
        <table>
            <thead>
                <tr>
                    <th>Ad</th>
                    <th>Soyad</th>
                    <th>Email</th>
                    <th>Kayıt Tarihi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kullanicilar as $kullanici): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($kullanici['ad']); ?></td>
                        <td><?php echo htmlspecialchars($kullanici['soyad']); ?></td>
                        <td><?php echo htmlspecialchars($kullanici['email']); ?></td>
                        <td><?php echo $kullanici['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="back-link"><i class="fas fa-home"></i> Ana Sayfaya Dön</a>
    </div>
</body>
</html>
