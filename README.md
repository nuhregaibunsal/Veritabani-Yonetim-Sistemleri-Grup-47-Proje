# TechStore E-Ticaret Projesi

Bu proje, bir e-ticaret sitesidir. Ürünleri gösterir, sepete eklenir, kullanıcılar kayıt olabilir, giriş yapabilir ve profillerini yönetebilir.

## Dosyalar

* *baglanti.php:* Veritabanı bağlantısı için.
* *destek.php:* İletişim sayfası.
* *giris.php:* Giriş yapma sayfası.
* *index.php:* Ana sayfa (ürünler burada).
* *kayit.php:* Kayıt olma sayfası.
* *profil.php:* Profil sayfası.
* *sepet.php:* Sepet sayfası.
* *urun\_detay.php:* Ürün detay sayfası.
* *proje.sql:* Veritabanı ayarları.

## Veritabanı

* Tablolar: adresler, kategoriler, kullanicilar, sepetogeleri, siparisler, siparisogeleri, urunler, urunyorumlari, urun\_kategoriler.

## Kurulum

1.  Veritabanı oluştur ve proje.sql dosyasını yükle.
2.  baglanti.php dosyasındaki veritabanı bilgilerini ayarla.
3.  Dosyaları web sunucusuna yükle.

## Gerekli Şeyler

* PHP
* MySQL
* Bootstrap, Tailwind CSS, Font Awesome, Animate.css, Alpine.js, AOS.js

## Önemli

* Şifreler güvenli saklanır.
* Kullanıcı rolleri var (satıcı/müşteri).
* Sepet, sipariş gibi temel e-ticaret özellikleri var.
