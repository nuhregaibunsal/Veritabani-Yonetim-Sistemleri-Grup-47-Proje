-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 18 May 2025, 22:39:17
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `proje`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `adresler`
--
CREATE DATABASE proje;
CREATE TABLE `adresler` (
  `adres_id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `ad_soyad` varchar(100) DEFAULT NULL,
  `adres_satiri1` varchar(255) DEFAULT NULL,
  `adres_satiri2` varchar(255) DEFAULT NULL,
  `sehir` varchar(30) DEFAULT NULL,
  `posta_kodu` varchar(10) DEFAULT NULL,
  `ulke` varchar(30) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `adresler`
--

INSERT INTO `adresler` (`adres_id`, `kullanici_id`, `ad_soyad`, `adres_satiri1`, `adres_satiri2`, `sehir`, `posta_kodu`, `ulke`, `telefon`) VALUES
(3, 12, 'MusteriDeneme1', 'asd', '', '', '', '', '05445686030'),
(4, 10, 'SaticiDeneme1', 'asd', '', '', '', '', '05445686030');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kategoriler`
--

CREATE TABLE `kategoriler` (
  `kategori_id` int(11) NOT NULL,
  `ad` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `kategoriler`
--

INSERT INTO `kategoriler` (`kategori_id`, `ad`) VALUES
(1, 'Elektironik Aletler'),
(2, 'Tablet');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `kullanici_id` int(11) NOT NULL,
  `ad_soyad` varchar(100) DEFAULT NULL,
  `eposta` varchar(100) DEFAULT NULL,
  `sifre_hash` varchar(255) DEFAULT NULL,
  `satici` int(11) NOT NULL DEFAULT 0,
  `kullaniciOnay` int(11) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `kayit_tarihi` datetime DEFAULT current_timestamp(),
  `dogum_tarihi` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`kullanici_id`, `ad_soyad`, `eposta`, `sifre_hash`, `satici`, `kullaniciOnay`, `telefon`, `kayit_tarihi`, `dogum_tarihi`) VALUES
(10, 'SaticiDeneme1', 'deneme1@gmail.com', '$2y$10$QZt9ONVxzA.dTjxdWIERkeRBHhViAx3kbQkF5jrNcUM7PTztgbsdG', 1, 357779, '05445686030', '2025-05-18 22:30:24', '2005-11-11 00:00:00'),
(11, 'SatıcıDeneme2', 'deneme2@gmail.com', '$2y$10$VxPIEIQIWZzoAKe3z/btAOepEXgXg64divRRZqh/95KqDkYCgGiXK', 1, 325813, '05445686030', '2025-05-18 22:30:44', '1111-11-11 00:00:00'),
(12, 'MusteriDeneme1', 'deneme3@gmail.com', '$2y$10$9TZe3Qr1KfiqGif1pTG0EOGhLU37/DZRKZcs2Y2nRONorPuUQLniO', 0, NULL, '05445686030', '2025-05-18 22:31:01', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sepetogeleri`
--

CREATE TABLE `sepetogeleri` (
  `sepet_oge_id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `urun_id` int(11) DEFAULT NULL,
  `adet` int(11) DEFAULT NULL,
  `eklenme_tarihi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siparisler`
--

CREATE TABLE `siparisler` (
  `siparis_id` int(11) NOT NULL,
  `satici_id` int(11) DEFAULT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `adres_id` int(11) DEFAULT NULL,
  `durum` varchar(50) DEFAULT NULL,
  `toplam_tutar` decimal(10,2) DEFAULT NULL,
  `siparis_tarihi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `siparisler`
--

INSERT INTO `siparisler` (`siparis_id`, `satici_id`, `kullanici_id`, `adres_id`, `durum`, `toplam_tutar`, `siparis_tarihi`) VALUES
(7, 10, 12, 3, 'Tamamlandı', 1.00, '2025-05-18 22:36:34'),
(8, 11, 12, 3, 'Tamamlandı', 33.00, '2025-05-18 22:36:34'),
(9, 10, 10, 4, 'Tamamlandı', 1.00, '2025-05-18 22:47:28'),
(10, 10, 10, 4, 'Hazırlanıyor', 1.00, '2025-05-18 23:32:28'),
(11, 10, 10, 4, 'Tamamlandı', 1.00, '2025-05-18 23:32:36');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siparisogeleri`
--

CREATE TABLE `siparisogeleri` (
  `siparis_oge_id` int(11) NOT NULL,
  `siparis_id` int(11) DEFAULT NULL,
  `urun_id` int(11) DEFAULT NULL,
  `adet` int(11) DEFAULT NULL,
  `birim_fiyat` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `siparisogeleri`
--

INSERT INTO `siparisogeleri` (`siparis_oge_id`, `siparis_id`, `urun_id`, `adet`, `birim_fiyat`) VALUES
(6, 7, 8, 1, 1.00),
(7, 8, 9, 3, 11.00),
(8, 9, 8, 1, 1.00),
(9, 10, 8, 1, 1.00),
(10, 11, 8, 1, 1.00);

--
-- Tetikleyiciler `siparisogeleri`
--
DELIMITER $$
CREATE TRIGGER `stok_dusur` AFTER INSERT ON `siparisogeleri` FOR EACH ROW BEGIN
  DECLARE mevcut_stok INT;

  SELECT stok INTO mevcut_stok FROM urunler WHERE urun_id = NEW.urun_id;

  IF mevcut_stok < NEW.adet THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Yetersiz stok!';
  ELSE
    UPDATE urunler
    SET stok = stok - NEW.adet
    WHERE urun_id = NEW.urun_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urunler`
--

CREATE TABLE `urunler` (
  `urun_id` int(11) NOT NULL,
  `ad` varchar(150) DEFAULT NULL,
  `aciklama` text DEFAULT NULL,
  `fiyat` decimal(10,2) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `aktif` int(11) DEFAULT 1,
  `satici_id` int(11) DEFAULT NULL,
  `resim_url` varchar(255) DEFAULT NULL,
  `eklenme_tarihi` datetime DEFAULT current_timestamp(),
  `silindi_mi` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `urunler`
--

INSERT INTO `urunler` (`urun_id`, `ad`, `aciklama`, `fiyat`, `stok`, `aktif`, `satici_id`, `resim_url`, `eklenme_tarihi`, `silindi_mi`) VALUES
(8, 'Deneme Ürünü 1', 'Deneme Ürünü 1', 1.00, 9, 1, 10, 'img/682a35c0ea7d2.jpeg', '2025-05-18 22:32:16', 0),
(9, 'Deneme Ürünü 2', 'Deneme Ürünü 2', 11.00, 0, 1, 11, 'img/682a35ea42b81.jpeg', '2025-05-18 22:32:58', 0),
(10, 'Deneme Ürünü 3', 'Deneme Ürünü 3', 123.00, 13, 0, 11, 'img/682a35fb75a25.png', '2025-05-18 22:33:15', 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urunyorumlari`
--

CREATE TABLE `urunyorumlari` (
  `yorum_id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `urun_id` int(11) DEFAULT NULL,
  `puan` int(11) DEFAULT NULL CHECK (`puan` between 1 and 5),
  `yorum_metni` text DEFAULT NULL,
  `yorum_tarihi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `urunyorumlari`
--

INSERT INTO `urunyorumlari` (`yorum_id`, `kullanici_id`, `urun_id`, `puan`, `yorum_metni`, `yorum_tarihi`) VALUES
(4, 11, 9, NULL, 'asd', '2025-05-18 22:33:51'),
(5, 10, 8, 4, 'asd', '2025-05-18 22:57:51');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_kategoriler`
--

CREATE TABLE `urun_kategoriler` (
  `urun_id` int(11) NOT NULL,
  `kategori_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `urun_kategoriler`
--

INSERT INTO `urun_kategoriler` (`urun_id`, `kategori_id`) VALUES
(8, 2),
(9, 2),
(10, 1),
(10, 2);

-- --------------------------------------------------------

--
-- Görünüm yapısı durumu `vw_siparis_detaylari`
-- (Asıl görünüm için aşağıya bakın)
--
CREATE TABLE `vw_siparis_detaylari` (
`siparis_id` int(11)
,`kullanici_id` int(11)
,`satici_id` int(11)
,`siparis_tarihi` datetime
,`urun_id` int(11)
,`urun_adi` varchar(150)
,`adet` int(11)
,`birim_fiyat` decimal(10,2)
,`toplam_tutar` decimal(20,2)
);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `yoneticikullanicilar`
--

CREATE TABLE `yoneticikullanicilar` (
  `yonetici_id` int(11) NOT NULL,
  `ad_soyad` varchar(100) DEFAULT NULL,
  `eposta` varchar(100) DEFAULT NULL,
  `sifre_hash` varchar(255) DEFAULT NULL,
  `rol` varchar(50) DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `kayit_tarihi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Görünüm yapısı `vw_siparis_detaylari`
--
DROP TABLE IF EXISTS `vw_siparis_detaylari`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_siparis_detaylari`  AS SELECT `s`.`siparis_id` AS `siparis_id`, `s`.`kullanici_id` AS `kullanici_id`, `s`.`satici_id` AS `satici_id`, `s`.`siparis_tarihi` AS `siparis_tarihi`, `u`.`urun_id` AS `urun_id`, `u`.`ad` AS `urun_adi`, `so`.`adet` AS `adet`, `so`.`birim_fiyat` AS `birim_fiyat`, `so`.`adet`* `so`.`birim_fiyat` AS `toplam_tutar` FROM ((`siparisler` `s` join `siparisogeleri` `so` on(`s`.`siparis_id` = `so`.`siparis_id`)) join `urunler` `u` on(`so`.`urun_id` = `u`.`urun_id`)) ;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `adresler`
--
ALTER TABLE `adresler`
  ADD PRIMARY KEY (`adres_id`),
  ADD KEY `kullanici_id` (`kullanici_id`);

--
-- Tablo için indeksler `kategoriler`
--
ALTER TABLE `kategoriler`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`kullanici_id`),
  ADD UNIQUE KEY `eposta` (`eposta`);

--
-- Tablo için indeksler `sepetogeleri`
--
ALTER TABLE `sepetogeleri`
  ADD PRIMARY KEY (`sepet_oge_id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `urun_id` (`urun_id`);

--
-- Tablo için indeksler `siparisler`
--
ALTER TABLE `siparisler`
  ADD PRIMARY KEY (`siparis_id`),
  ADD KEY `adres_id` (`adres_id`),
  ADD KEY `fk_satici_id` (`satici_id`),
  ADD KEY `idx_siparisler_kullanici_id` (`kullanici_id`);

--
-- Tablo için indeksler `siparisogeleri`
--
ALTER TABLE `siparisogeleri`
  ADD PRIMARY KEY (`siparis_oge_id`),
  ADD KEY `siparis_id` (`siparis_id`),
  ADD KEY `urun_id` (`urun_id`);

--
-- Tablo için indeksler `urunler`
--
ALTER TABLE `urunler`
  ADD PRIMARY KEY (`urun_id`),
  ADD KEY `satici_id` (`satici_id`);

--
-- Tablo için indeksler `urunyorumlari`
--
ALTER TABLE `urunyorumlari`
  ADD PRIMARY KEY (`yorum_id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `idx_urunyorumlari_urun_id` (`urun_id`);

--
-- Tablo için indeksler `urun_kategoriler`
--
ALTER TABLE `urun_kategoriler`
  ADD PRIMARY KEY (`urun_id`,`kategori_id`),
  ADD KEY `idx_urun_kategoriler_kategori_id` (`kategori_id`);

--
-- Tablo için indeksler `yoneticikullanicilar`
--
ALTER TABLE `yoneticikullanicilar`
  ADD PRIMARY KEY (`yonetici_id`),
  ADD UNIQUE KEY `eposta` (`eposta`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `adresler`
--
ALTER TABLE `adresler`
  MODIFY `adres_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `kategoriler`
--
ALTER TABLE `kategoriler`
  MODIFY `kategori_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `kullanici_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Tablo için AUTO_INCREMENT değeri `sepetogeleri`
--
ALTER TABLE `sepetogeleri`
  MODIFY `sepet_oge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Tablo için AUTO_INCREMENT değeri `siparisler`
--
ALTER TABLE `siparisler`
  MODIFY `siparis_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Tablo için AUTO_INCREMENT değeri `siparisogeleri`
--
ALTER TABLE `siparisogeleri`
  MODIFY `siparis_oge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Tablo için AUTO_INCREMENT değeri `urunler`
--
ALTER TABLE `urunler`
  MODIFY `urun_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Tablo için AUTO_INCREMENT değeri `urunyorumlari`
--
ALTER TABLE `urunyorumlari`
  MODIFY `yorum_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `yoneticikullanicilar`
--
ALTER TABLE `yoneticikullanicilar`
  MODIFY `yonetici_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `adresler`
--
ALTER TABLE `adresler`
  ADD CONSTRAINT `adresler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`);

--
-- Tablo kısıtlamaları `sepetogeleri`
--
ALTER TABLE `sepetogeleri`
  ADD CONSTRAINT `sepetogeleri_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`),
  ADD CONSTRAINT `sepetogeleri_ibfk_2` FOREIGN KEY (`urun_id`) REFERENCES `urunler` (`urun_id`);

--
-- Tablo kısıtlamaları `siparisler`
--
ALTER TABLE `siparisler`
  ADD CONSTRAINT `fk_satici_id` FOREIGN KEY (`satici_id`) REFERENCES `kullanicilar` (`kullanici_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `siparisler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`),
  ADD CONSTRAINT `siparisler_ibfk_2` FOREIGN KEY (`adres_id`) REFERENCES `adresler` (`adres_id`);

--
-- Tablo kısıtlamaları `siparisogeleri`
--
ALTER TABLE `siparisogeleri`
  ADD CONSTRAINT `siparisogeleri_ibfk_1` FOREIGN KEY (`siparis_id`) REFERENCES `siparisler` (`siparis_id`),
  ADD CONSTRAINT `siparisogeleri_ibfk_2` FOREIGN KEY (`urun_id`) REFERENCES `urunler` (`urun_id`);

--
-- Tablo kısıtlamaları `urunler`
--
ALTER TABLE `urunler`
  ADD CONSTRAINT `satici_id` FOREIGN KEY (`satici_id`) REFERENCES `kullanicilar` (`kullanici_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `urunyorumlari`
--
ALTER TABLE `urunyorumlari`
  ADD CONSTRAINT `urunyorumlari_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`),
  ADD CONSTRAINT `urunyorumlari_ibfk_2` FOREIGN KEY (`urun_id`) REFERENCES `urunler` (`urun_id`);

--
-- Tablo kısıtlamaları `urun_kategoriler`
--
ALTER TABLE `urun_kategoriler`
  ADD CONSTRAINT `urun_kategoriler_ibfk_1` FOREIGN KEY (`urun_id`) REFERENCES `urunler` (`urun_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `urun_kategoriler_ibfk_2` FOREIGN KEY (`kategori_id`) REFERENCES `kategoriler` (`kategori_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
