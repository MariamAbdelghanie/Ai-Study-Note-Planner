-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 03:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `personal_task_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GorevEkle` (IN `pid` VARCHAR(64), IN `pbaslik` VARCHAR(128), IN `paciklama` TEXT, IN `pdurum` VARCHAR(32), IN `poncelik` VARCHAR(32), IN `pbitis` DATETIME, IN `pkategori_id` VARCHAR(64))   BEGIN
    IF pbaslik IS NULL OR pbaslik = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Görev başlığı boş olamaz.';
    ELSEIF pbitis IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bitiş tarihi boş olamaz.';
    ELSE
        IF pkategori_id = '' THEN
            SET pkategori_id = NULL;
        END IF;
        INSERT INTO gorevler(gorev_id, baslik, aciklama, durum, oncelik, bitis_tarihi, kategori_id)
        VALUES (pid, pbaslik, paciklama, pdurum, poncelik, pbitis, pkategori_id);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GorevGuncelle` (IN `p_gorev_id` VARCHAR(50), IN `p_baslik` VARCHAR(255), IN `p_aciklama` TEXT, IN `p_durum` VARCHAR(50), IN `p_oncelik` VARCHAR(50), IN `p_bitis_tarihi` DATETIME, IN `p_kategori_id` VARCHAR(50))   BEGIN
    UPDATE gorevler
    SET baslik = p_baslik,
        aciklama = p_aciklama,
        durum = p_durum,
        oncelik = p_oncelik,
        bitis_tarihi = p_bitis_tarihi,
        kategori_id = NULLIF(p_kategori_id, ''),
        guncelleme_tarihi = NOW()
    WHERE gorev_id = p_gorev_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GorevKullaniciEkle` (IN `pid` VARCHAR(64), IN `pgorev_id` VARCHAR(64), IN `pkullanici_id` VARCHAR(64))   BEGIN
    IF EXISTS (
        SELECT 1 FROM gorev_kullanicilar 
        WHERE gorev_id = pgorev_id AND kullanici_id = pkullanici_id
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bu kullanıcı zaten bu göreve atanmış.';
    ELSE
        INSERT INTO gorev_kullanicilar(id, gorev_id, kullanici_id)
        VALUES (pid, pgorev_id, pkullanici_id);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GorevSil` (IN `pid` VARCHAR(64))   BEGIN
    DELETE FROM gorevler WHERE gorev_id = pid;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_HatirlatmaEkle` (IN `p_hatirlatma_id` VARCHAR(50), IN `p_gorev_id` VARCHAR(50), IN `p_tarih` DATETIME, IN `p_aciklama` TEXT)   BEGIN
    INSERT INTO hatirlatmalar (hatirlatma_id, gorev_id, hatirlatma_tarihi, aciklama)
    VALUES (p_hatirlatma_id, p_gorev_id, p_tarih, p_aciklama);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_KategoriEkle` (IN `pid` VARCHAR(64), IN `padi` VARCHAR(64), IN `paciklama` TEXT)   BEGIN
    INSERT INTO kategoriler VALUES (pid, padi, paciklama);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_KullaniciEkle` (IN `pid` VARCHAR(64), IN `pad` VARCHAR(64), IN `psoyad` VARCHAR(64), IN `pemail` VARCHAR(128), IN `psifre` VARCHAR(128))   BEGIN
    IF EXISTS (SELECT 1 FROM kullanicilar WHERE email = pemail) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bu email zaten kayıtlı.';
    ELSE
        INSERT INTO kullanicilar VALUES (pid, pad, psoyad, pemail, psifre, NOW());
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `gorevler`
--

CREATE TABLE `gorevler` (
  `gorev_id` varchar(64) NOT NULL,
  `baslik` varchar(128) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `durum` enum('Yapılacak','Devam Ediyor','Tamamlandı','Gecikti') DEFAULT 'Yapılacak',
  `oncelik` enum('Düşük','Orta','Yüksek') DEFAULT 'Orta',
  `olusturma_tarihi` datetime DEFAULT current_timestamp(),
  `guncelleme_tarihi` datetime DEFAULT NULL,
  `bitis_tarihi` datetime NOT NULL,
  `kategori_id` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gorevler`
--

INSERT INTO `gorevler` (`gorev_id`, `baslik`, `aciklama`, `durum`, `oncelik`, `olusturma_tarihi`, `guncelleme_tarihi`, `bitis_tarihi`, `kategori_id`) VALUES
('gorev_68457975aa003', 'internet tabanli odevi', 'word dosyasi', 'Yapılacak', 'Yüksek', '2025-06-08 14:52:21', NULL, '2025-06-14 20:18:00', NULL),
('tsk_6845857c551f1', 'test cozmek', 'veri sinavi icin', 'Yapılacak', 'Orta', '2025-06-08 15:43:40', NULL, '2025-06-13 21:43:00', NULL);

--
-- Triggers `gorevler`
--
DELIMITER $$
CREATE TRIGGER `trg_gorev_ekle_gecmis` BEFORE INSERT ON `gorevler` FOR EACH ROW BEGIN
    IF NEW.bitis_tarihi < NOW() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Geçmiş tarihte görev oluşturulamaz.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_gorev_gecikti` BEFORE UPDATE ON `gorevler` FOR EACH ROW BEGIN
    IF NEW.bitis_tarihi < NOW() AND NEW.durum != 'Tamamlandı' THEN
        SET NEW.durum = 'Gecikti';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `gorev_kullanicilar`
--

CREATE TABLE `gorev_kullanicilar` (
  `id` varchar(64) NOT NULL,
  `gorev_id` varchar(64) DEFAULT NULL,
  `kullanici_id` varchar(64) DEFAULT NULL,
  `assigned_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hatirlatmalar`
--

CREATE TABLE `hatirlatmalar` (
  `hatirlatma_id` varchar(64) NOT NULL,
  `gorev_id` varchar(64) DEFAULT NULL,
  `hatirlatma_tarihi` datetime NOT NULL,
  `aciklama` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hatirlatmalar`
--

INSERT INTO `hatirlatmalar` (`hatirlatma_id`, `gorev_id`, `hatirlatma_tarihi`, `aciklama`) VALUES
('rem_684585c08bc83', 'tsk_6845857c551f1', '2025-06-05 20:44:00', 'onemli');

-- --------------------------------------------------------

--
-- Table structure for table `kategoriler`
--

CREATE TABLE `kategoriler` (
  `kategori_id` varchar(64) NOT NULL,
  `kategori_adi` varchar(64) NOT NULL,
  `aciklama` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategoriler`
--

INSERT INTO `kategoriler` (`kategori_id`, `kategori_adi`, `aciklama`) VALUES
('k1', 'İş', 'İş ile ilgili görevler');

-- --------------------------------------------------------

--
-- Table structure for table `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `kullanici_id` varchar(64) NOT NULL,
  `ad` varchar(64) NOT NULL,
  `soyad` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `sifre` varchar(128) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kullanicilar`
--

INSERT INTO `kullanicilar` (`kullanici_id`, `ad`, `soyad`, `email`, `sifre`, `created_at`) VALUES
('684579ebe4524', 'maryem', 'yasser', 'maryemy834@gmail.com', '12345', '2025-06-08 14:54:19'),
('u1', 'Ali', 'Yılmaz', 'ali@example.com', 'sifre123', '2025-06-08 14:44:29'),
('usr_68458314ec96c', 'Nihan', 'M', 'nihannour@gmail.com', '$2y$10$0XYrfz5Zo7J6qqlG82dMcu6eFLcqWonE8SR0smKwdbQb4wk0BJpAm', '2025-06-08 15:33:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gorevler`
--
ALTER TABLE `gorevler`
  ADD PRIMARY KEY (`gorev_id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indexes for table `gorev_kullanicilar`
--
ALTER TABLE `gorev_kullanicilar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gorev_id` (`gorev_id`),
  ADD KEY `kullanici_id` (`kullanici_id`);

--
-- Indexes for table `hatirlatmalar`
--
ALTER TABLE `hatirlatmalar`
  ADD PRIMARY KEY (`hatirlatma_id`),
  ADD KEY `gorev_id` (`gorev_id`);

--
-- Indexes for table `kategoriler`
--
ALTER TABLE `kategoriler`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indexes for table `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`kullanici_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gorevler`
--
ALTER TABLE `gorevler`
  ADD CONSTRAINT `gorevler_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategoriler` (`kategori_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `gorev_kullanicilar`
--
ALTER TABLE `gorev_kullanicilar`
  ADD CONSTRAINT `gorev_kullanicilar_ibfk_1` FOREIGN KEY (`gorev_id`) REFERENCES `gorevler` (`gorev_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gorev_kullanicilar_ibfk_2` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hatirlatmalar`
--
ALTER TABLE `hatirlatmalar`
  ADD CONSTRAINT `hatirlatmalar_ibfk_1` FOREIGN KEY (`gorev_id`) REFERENCES `gorevler` (`gorev_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
