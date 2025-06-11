<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['gorev_id'])) {
    $gorev_id = $_GET['gorev_id'];

    try {
        $stmt = $pdo->prepare("CALL sp_GorevSil(?)");
        $stmt->execute([$gorev_id]);
        echo "<div class='success'>Görev başarıyla silindi!</div>";
    } catch (PDOException $e) {
        echo "<div class='error'>Hata: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görev Sil</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-trash"></i> Görev Sil</h2>
        <p>Görev silindi veya zaten mevcut değil.</p>
        <a href="gorev_listesi.php" class="back-link"><i class="fas fa-list"></i> Görev Listesine Dön</a>
    </div>
</body>
</html>

