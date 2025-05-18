<?php
session_start();
include 'baglanti.php';

if (!isset($_SESSION['giren'])) {
    header("Location: giris.php");
    exit();
}

$kullanici_id = $_SESSION['giren'];

if (isset($_GET['sil'])) {
    $sil_id = $_GET['sil'];
    $sil = $db->prepare("DELETE FROM sepetogeleri WHERE sepet_oge_id = :id AND kullanici_id = :kullanici_id");
    $sil->execute([':id' => $sil_id, ':kullanici_id' => $kullanici_id]);
    header("Location: sepet.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ekle"])) {
    $urun_id = $_POST["urun_id"];
    $adet = max(1, intval($_POST["adet"]));

    $sorgu = $db->prepare("SELECT * FROM sepetogeleri WHERE kullanici_id = :kullanici_id AND urun_id = :urun_id");
    $sorgu->execute([':kullanici_id' => $kullanici_id, ':urun_id' => $urun_id]);
    $urun = $sorgu->fetch(PDO::FETCH_ASSOC);

    if ($urun) {
        $guncelle = $db->prepare("UPDATE sepetogeleri SET adet = adet + :adet WHERE sepet_oge_id = :sepet_oge_id");
        $guncelle->execute([':adet' => $adet, ':sepet_oge_id' => $urun['sepet_oge_id']]);
    } else {
        $ekle = $db->prepare("INSERT INTO sepetogeleri (kullanici_id, urun_id, adet) VALUES (:kullanici_id, :urun_id, :adet)");
        $ekle->execute([':kullanici_id' => $kullanici_id, ':urun_id' => $urun_id, ':adet' => $adet]);
    }

    header("Location: sepet.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["urun_id"]) && isset($_POST["adet"])) {
        $urun_id = $_POST["urun_id"];
        $adet = max(1, intval($_POST["adet"]));

        $sorgu = $db->prepare("SELECT * FROM sepetogeleri JOIN urunler ON sepetogeleri.urun_id = urunler.urun_id WHERE sepetogeleri.kullanici_id = :kullanici_id AND sepetogeleri.urun_id = :urun_id");
        $sorgu->execute([':kullanici_id' => $kullanici_id, ':urun_id' => $urun_id]);
        $urun = $sorgu->fetch(PDO::FETCH_ASSOC);

        if ($urun) {
            if ($adet > $urun['stok']) {
                $_SESSION['hata'] = "Stok yetersizdir. Maksimum {$urun['stok']} adet eklenebilir.";
                header("Location: sepet.php");
                exit();
            }

            $guncelle = $db->prepare("UPDATE sepetogeleri SET adet = :adet WHERE sepet_oge_id = :sepet_oge_id");
            $guncelle->execute([':adet' => $adet, ':sepet_oge_id' => $urun['sepet_oge_id']]);
        }

        header("Location: sepet.php");
        exit();
    }
}

$sorgu = $db->prepare("SELECT sepetogeleri.sepet_oge_id, sepetogeleri.adet, urunler.urun_id, urunler.ad, urunler.stok, urunler.fiyat, urunler.resim_url FROM sepetogeleri JOIN urunler ON sepetogeleri.urun_id = urunler.urun_id WHERE sepetogeleri.kullanici_id = :kullanici_id");
$sorgu->execute([':kullanici_id' => $kullanici_id]);
$urunler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

$toplam = 0;
foreach ($urunler as $urun) {
    $toplam += $urun['fiyat'] * $urun['adet'];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepetim - TechStore</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #1a1a1a;
        }
        .cart-item {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .cart-item:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }
        .quantity-btn {
            transition: all 0.2s ease;
            border: 1px solid #e5e7eb;
        }
        .quantity-btn:hover {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        .btn-primary {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            border: none;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }
        .btn-secondary {
            background: #f3f4f6;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-secondary:hover {
            background: #e5e7eb;
        }
        .price {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-gray-50">

<?php include 'navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-medium mb-8 text-gray-900 flex items-center">
            <i class="fas fa-shopping-cart text-blue-600 mr-3"></i>
            Sepetim
        </h1>

        <?php if (isset($_SESSION['hata'])): ?>
            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 border border-red-100">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo $_SESSION['hata']; unset($_SESSION['hata']); ?>
            </div>
        <?php endif; ?>

        <?php if (count($urunler) === 0): ?>
            <div class="text-center p-8 bg-white rounded-xl shadow-sm border border-gray-100">
                <i class="fas fa-shopping-basket text-4xl text-blue-600 mb-4"></i>
                <p class="text-gray-600 mb-6">Sepetinizde ürün bulunmuyor.</p>
                <a href="index.php" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:shadow-lg transition-all duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Alışverişe Devam Et
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($urunler as $urun): ?>
                    <div class="cart-item p-4">
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <!-- Ürün Resmi -->
                            <div class="w-24 h-24 flex-shrink-0">
                                <img src="saticipanel/<?php echo htmlspecialchars($urun['resim_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($urun['ad']); ?>" 
                                     class="w-full h-full object-cover rounded-lg">
                            </div>

                            <!-- Ürün Bilgileri -->
                            <div class="flex-1 text-center md:text-left">
                                <h2 class="text-base font-medium text-gray-900 mb-1">
                                    <?php echo htmlspecialchars($urun['ad']); ?>
                                </h2>
                                <p class="text-sm price font-medium">
                                    ₺<?php echo number_format($urun['fiyat'], 2, ',', '.'); ?>
                                </p>
                            </div>

                            <!-- Adet Kontrolü -->
                            <div class="flex items-center gap-2">
                                <form action="sepet.php" method="POST" id="form_<?php echo $urun['urun_id']; ?>" class="flex items-center gap-2">
                                    <input type="hidden" name="urun_id" value="<?php echo $urun['urun_id']; ?>">
                                    <button type="button" 
                                            onclick="updateAdet(<?php echo $urun['urun_id']; ?>, <?php echo $urun['adet'] - 1; ?>)" 
                                            class="quantity-btn w-8 h-8 rounded-lg flex items-center justify-center"
                                            <?php echo ($urun['adet'] <= 1) ? 'disabled' : ''; ?>>
                                        <i class="fas fa-minus text-sm"></i>
                                    </button>
                                    <input type="number" 
                                           id="adet_<?php echo $urun['urun_id']; ?>" 
                                           name="adet" 
                                           value="<?php echo $urun['adet']; ?>" 
                                           min="1" 
                                           class="w-12 text-center p-1 border rounded-lg bg-white" 
                                           readonly>
                                    <button type="button" 
                                            onclick="updateAdet(<?php echo $urun['urun_id']; ?>, <?php echo $urun['adet'] + 1; ?>)" 
                                            class="quantity-btn w-8 h-8 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Toplam ve Sil -->
                            <div class="flex flex-col items-center gap-2">
                                <div class="text-sm font-medium price">
                                    ₺<?php echo number_format($urun['fiyat'] * $urun['adet'], 2, ',', '.'); ?>
                                </div>
                                <a href="sepet.php?sil=<?php echo $urun['sepet_oge_id']; ?>" 
                                   class="text-sm text-gray-500 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash-alt mr-1"></i>
                                    Kaldır
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Genel Toplam ve Butonlar -->
            <div class="mt-8 p-6 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-lg font-medium price">
                        Toplam: ₺<?php echo number_format($toplam, 2, ',', '.'); ?>
                    </div>
                    <div class="flex gap-3">
                        <a href="index.php" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Alışverişe Devam Et
                        </a>
                        <a href="siparis_tamamla.php" 
                           class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all duration-300">
                            <i class="fas fa-check mr-2"></i>
                            Siparişi Tamamla
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function updateAdet(urunId, yeniAdet) {
        if (yeniAdet < 1) return;
        document.getElementById('adet_' + urunId).value = yeniAdet;
        document.getElementById('form_' + urunId).submit();
    }
</script>

</body>
</html>
