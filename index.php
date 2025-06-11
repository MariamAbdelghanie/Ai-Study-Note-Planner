<?php
session_start();
include 'db_connect.php';

// إعادة توجيه إلى تسجيل الدخول إذا لم يكن المستخدم مسجلاً دخوله
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

// الحصول على التاريخ والوقت الحاليين
date_default_timezone_set('Europe/Berlin'); // تعيين التوقيت إلى CEST
$tarih = date('d.m.Y H:i', strtotime('03:05 PM CEST'));
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Personal Task Management System</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- إضافة Font Awesome للأيقونات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background: url('images/background.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #1a202c;
            overflow-x: hidden;
        }
        .container {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            height: calc(100vh - 40px);
        }
        .sidebar {
            width: 200px; /* تقليص الحجم قليلاً */
            background: rgba(74, 20, 140, 0.9); /* أرجواني داكن شبه شفاف */
            padding: 15px;
            border-radius: 10px 0 0 10px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
            height: 100%;
            position: sticky;
            top: 20px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sidebar .logo {
            margin-bottom: 15px;
        }
        .sidebar .logo img {
            max-width: 80px;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .sidebar h3 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.3em;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }
        .sidebar ul li {
            margin: 8px 0;
        }
        .sidebar ul li a {
            display: flex;
            align-items: center;
            color: #ffffff;
            text-decoration: none;
            padding: 8px 10px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
            font-size: 0.9em; /* تقليل حجم الخط قليلاً */
        }
        .sidebar ul li a i {
            margin-right: 8px;
            color: #ffffff;
            transition: color 0.3s;
        }
        .sidebar ul li a:hover {
            background-color: rgba(255, 182, 193, 0.3); /* وردي شاحب شفاف */
            transform: translateX(5px);
        }
        .sidebar ul li a:hover i {
            color: #FFB6C1; /* وردي شاحب */
        }
        .content {
            flex-grow: 1;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8); /* خلفية بيضاء شبه شفافة */
            border-radius: 0 10px 10px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            color: #1a202c;
        }
        h1 {
            font-size: 2.5em; /* تقليل حجم العنوان قليلاً */
            margin-bottom: 15px;
            text-align: center;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }
        .welcome-section {
            text-align: center;
            padding: 15px;
            background: rgba(255, 182, 193, 0.1); /* وردي شاحب شفاف */
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .welcome-section::before, .welcome-section::after {
            content: "✨"; /* زخرفة بنجوم */
            color: #4B0082;
            margin: 0 10px;
        }
        .welcome-text {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .site-info {
            font-size: 1em;
            margin-bottom: 15px;
        }
        .datetime {
            color: #4B0082;
            font-style: italic;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        @media (max-width: 800px) {
            .container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                border-radius: 10px 10px 0 0;
            }
            .sidebar .logo img {
                max-width: 60px;
            }
            .content {
                border-radius: 0 0 10px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <img src="images/logo.png" alt="Personal Task Management Logo">
            </div>
            <h3>Menü</h3>
            <ul>
                <li><a href="kullanici_ekle.php"><i class="fas fa-user-plus"></i> Kullanıcı Ekle</a></li>
                <li><a href="kullanici_listesi.php"><i class="fas fa-users"></i> Kullanıcıları Listele</a></li>
                <li><a href="kategori_ekle.php"><i class="fas fa-folder-plus"></i> Kategori Ekle</a></li>
                <li><a href="kategori_listesi.php"><i class="fas fa-list"></i> Kategorileri Listele</a></li>
                <li><a href="gorev_ekle.php"><i class="fas fa-plus"></i> Görev Ekle</a></li>
                <li><a href="gorev_listesi.php"><i class="fas fa-tasks"></i> Görevleri Listele</a></li>
                <li><a href="gorev_kullanici_ata.php"><i class="fas fa-user-tag"></i> Göreve Kullanıcı Ata</a></li>
                <li><a href="hatirlatma_ekle.php"><i class="fas fa-bell"></i> Hatırlatma Ekle</a></li>
                <li><a href="hatirlatma_listesi.php"><i class="fas fa-bell"></i> Hatırlatmaları Listele</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
            </ul>
        </div>
        <div class="content">
            <h1>Personal Task Management System</h1>
            <div class="welcome-section">
                <p class="welcome-text">Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['ad'] . ' ' . $_SESSION['soyad']); ?>!</p>
                <p class="site-info">Bu sistem, kişisel görevlerinizi yönetmek için tasarlanmıştır. Görev ekleyebilir, kategorilere ayırabilir, kullanıcılara atayabilir ve hatırlatmalar ayarlayabilirsiniz.</p>
                <p class="datetime">Tarih ve Saat: <?php echo $tarih; ?> (CEST)</p>
            </div>
        </div>
    </div>
</body>
</html>
