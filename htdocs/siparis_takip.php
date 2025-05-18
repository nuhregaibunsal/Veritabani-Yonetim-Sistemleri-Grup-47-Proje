<?php
session_start();
include 'baglanti.php';

if (!isset($_SESSION['giren'])) {
    header("Location: giris.php");
    exit();
}

$kullanici_id = $_SESSION['giren'];

// Siparişleri çekiyoruz (bu sefer siparişlerin toplamlarını ve detaylarını alacağız)
$siparis_sorgu = $db->prepare("
    SELECT DISTINCT s.siparis_id, s.siparis_tarihi, s.durum
    FROM vw_siparis_detaylari v
    JOIN siparisler s ON v.siparis_id = s.siparis_id
    WHERE v.kullanici_id = :kullanici_id
    ORDER BY s.siparis_tarihi DESC
");
$siparis_sorgu->execute([':kullanici_id' => $kullanici_id]);
$siparisler = $siparis_sorgu->fetchAll(PDO::FETCH_ASSOC);


$durumlar = [
    'Hazırlanıyor' => 'Hazırlanıyor',
    'Tamamlandı' => 'Tamamlandı',
    'Kargoda' => 'Kargoda',
    'İptal Edildi' => 'İptal Edildi',
];

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Sipariş Takip</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .urun-resmi {
            max-width: 80px;
            max-height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container py-4">

    <h2 class="mb-4">Sipariş Takip</h2>

    <?php if (count($siparisler) === 0): ?>
        <div class="alert alert-info">Henüz siparişiniz bulunmamaktadır.</div>
    <?php else: ?>
        <?php foreach ($siparisler as $siparis): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <strong>Sipariş No:</strong> <?= htmlspecialchars($siparis['siparis_id']) ?>
                    <span class="float-end"><?= date('d.m.Y H:i', strtotime($siparis['siparis_tarihi'])) ?></span>
                </div>
                <div class="card-body">

                    <?php
                    // Bu siparişin ürün detaylarını çekiyoruz
                    $detaylar_sorgu = $db->prepare("
                        SELECT u.ad AS urun_adi, u.resim_url, so.adet, so.birim_fiyat, (so.adet * so.birim_fiyat) AS toplam_tutar
                        FROM siparisogeleri so
                        JOIN urunler u ON so.urun_id = u.urun_id
                        WHERE so.siparis_id = :siparis_id
                    ");
                    $detaylar_sorgu->execute([':siparis_id' => $siparis['siparis_id']]);
                    $urunler = $detaylar_sorgu->fetchAll(PDO::FETCH_ASSOC);

                    $siparis_toplam = 0;
                    ?>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ürün Resmi</th>
                                <th>Ürün Adı</th>
                                <th>Adet</th>
                                <th>Birim Fiyat (₺)</th>
                                <th>Toplam (₺)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($urunler as $urun): 
                                $siparis_toplam += $urun['toplam_tutar'];
                            ?>
                            <tr>
                                <td>
                                    <?php if (!empty($urun['resim_url'])): ?>
                                        <img src="saticipanel/<?= htmlspecialchars($urun['resim_url']) ?>" alt="<?= htmlspecialchars($urun['urun_adi']) ?>" class="urun-resmi">
                                    <?php else: ?>
                                        <span>Resim Yok</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($urun['urun_adi']) ?></td>
                                <td><?= $urun['adet'] ?></td>
                                <td><?= number_format($urun['birim_fiyat'], 2, ',', '.') ?></td>
                                <td><?= number_format($urun['toplam_tutar'], 2, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Sipariş Toplamı:</th>
                                <th><?= number_format($siparis_toplam, 2, ',', '.') ?> ₺</th>
                            </tr>
                        </tfoot>
                    </table>

                    <p><strong>Durum:</strong> <?= htmlspecialchars($durumlar[$siparis['durum']] ?? $siparis['durum']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary mt-3">Ana Sayfa</a>
</div>

</body>
</html>
