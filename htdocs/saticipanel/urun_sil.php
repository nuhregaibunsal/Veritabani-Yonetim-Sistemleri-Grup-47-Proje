<?php
session_start();
include '../baglanti.php';

if (!isset($_SESSION['giren'])) {
    header("Location: ../giris.php");
    exit;
}

if (!isset($_GET['urun_id']) || !is_numeric($_GET['urun_id'])) {
    header("Location: saticiindex.php");
    exit;
}

$urun_id = intval($_GET['urun_id']);
$kullanici_id = $_SESSION['giren'];

// Önce ürün bu kullanıcıya mı ait kontrol et
$urunKontrol = $db->prepare("SELECT * FROM urunler WHERE urun_id = :urun_id AND satici_id = :satici_id");
$urunKontrol->execute([
    ':urun_id' => $urun_id,
    ':satici_id' => $kullanici_id
]);

if ($urunKontrol->rowCount() === 0) {
    header("Location: saticiindex.php?durum=yetkisiz");
    exit;
}

// Ürünü silinmiş olarak işaretle (yumuşak silme)
$urunsil = $db->prepare("UPDATE urunler SET silindi_mi = 1 WHERE urun_id = :urun_id");
$urunsil->execute([':urun_id' => $urun_id]);

// İlgili kategori ilişkilerini de kaldır (isteğe bağlı)
$db->prepare("DELETE FROM urun_kategoriler WHERE urun_id = :urun_id")->execute([':urun_id' => $urun_id]);

header("Location: saticiindex.php?durum=silindi");
exit;
?>
