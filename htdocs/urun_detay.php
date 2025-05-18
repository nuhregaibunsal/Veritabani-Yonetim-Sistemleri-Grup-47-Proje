<?php
session_start();
include 'baglanti.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$urun_id = $_GET['id'];

// Ürün bilgilerini al
$sorgu = $db->prepare("SELECT u.*, uk.kategori_id FROM urunler u 
                      LEFT JOIN urun_kategoriler uk ON u.urun_id = uk.urun_id 
                      WHERE u.urun_id = :id");
$sorgu->execute([':id' => $urun_id]);
$urun = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$urun) {
    header("Location: index.php");
    exit();
}

// Ürün yorumlarını al
$sorgu_yorumlar = $db->prepare("SELECT k.ad_soyad, u.yorum_metni, u.puan, u.yorum_tarihi 
                                FROM urunyorumlari u 
                                LEFT JOIN Kullanicilar k ON u.kullanici_id = k.kullanici_id 
                                WHERE u.urun_id = :urun_id");
$sorgu_yorumlar->execute([':urun_id' => $urun_id]);
$yorumlar = $sorgu_yorumlar->fetchAll(PDO::FETCH_ASSOC);

// Giriş yapan kullanıcı
$kullanici2 = null;
if (isset($_SESSION['giren'])) {
    $sorgu2 = $db->prepare("SELECT * FROM Kullanicilar WHERE kullanici_id = :id");
    $sorgu2->execute([':id' => $_SESSION['giren']]);
    $kullanici2 = $sorgu2->fetch(PDO::FETCH_ASSOC);
}

// Sepete ekleme
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ekle"])) {
    $adet = max(1, intval($_POST["adet"]));

    $stok_sorgu = $db->prepare("SELECT stok FROM urunler WHERE urun_id = :urun_id");
    $stok_sorgu->execute([':urun_id' => $urun_id]);
    $urun_stok = $stok_sorgu->fetch(PDO::FETCH_ASSOC)['stok'];

    if ($urun_stok >= $adet) {
        $sorgu = $db->prepare("SELECT * FROM sepetogeleri WHERE kullanici_id = :kullanici_id AND urun_id = :urun_id");
        $sorgu->execute([':kullanici_id' => $_SESSION['giren'], ':urun_id' => $urun_id]);
        $varsa = $sorgu->fetch(PDO::FETCH_ASSOC);

        if ($varsa) {
            $guncelle = $db->prepare("UPDATE sepetogeleri SET adet = adet + :adet WHERE sepet_oge_id = :id");
            $guncelle->execute([':adet' => $adet, ':id' => $varsa['sepet_oge_id']]);
        } else {
            $ekle = $db->prepare("INSERT INTO sepetogeleri (kullanici_id, urun_id, adet) VALUES (:kullanici_id, :urun_id, :adet)");
            $ekle->execute([':kullanici_id' => $_SESSION['giren'], ':urun_id' => $urun_id, ':adet' => $adet]);
        }

        header("Location: sepet.php");
        exit();
    } else {
        echo "<script>alert('Stok yetersizdir!');</script>";
    }
}

// Yorum ekleme
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["yorum_ekle"])) {
    if (!isset($_SESSION['giren'])) {
        header("Location: giris.php");
        exit();
    }

    $yorum_metni = $_POST["yorum_metni"];
    $puan = $_POST["puan"];

    $ekle_yorum = $db->prepare("INSERT INTO urunyorumlari (kullanici_id, urun_id, yorum_metni, puan, yorum_tarihi) 
                                VALUES (:kullanici_id, :urun_id, :yorum_metni, :puan, NOW())");
    $ekle_yorum->execute([
        ':kullanici_id' => $_SESSION['giren'],
        ':urun_id' => $urun_id,
        ':yorum_metni' => $yorum_metni,
        ':puan' => $puan
    ]);

    header("Location: urun_detay.php?id=$urun_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($urun['ad']); ?> - Ürün Detay</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .product-gallery img {
            transition: transform 0.3s ease;
        }
        .product-gallery img:hover {
            transform: scale(1.05);
        }
        .rating-star {
            color: #FFD700;
        }
        .rating-star-empty {
            color: #E5E7EB;
        }
    </style>
</head>
<body class="bg-gray-50">

<?php include 'navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sol: Ürün görseli ve galeri -->
        <div class="lg:w-1/2">
            <div class="bg-white rounded-2xl shadow-sm p-6 product-gallery">
                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-xl mb-4">
                    <img src="saticipanel/<?php echo $urun['resim_url']; ?>" class="w-full h-full object-contain" alt="Ürün Görseli" />
                </div>
                <div class="flex gap-4 justify-center">
                    <img src="saticipanel/<?php echo $urun['resim_url']; ?>" class="w-20 h-20 object-cover rounded-lg border-2 border-gray-200 cursor-pointer hover:border-blue-500 transition" alt="Küçük Görsel" />
                </div>
            </div>
        </div>

        <!-- Sağ: Ürün detayları -->
        <div class="lg:w-1/2">
            <div class="bg-white rounded-2xl shadow-sm p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($urun['ad']); ?></h1>
                
                <!-- Puan ve yorum -->
                <div class="flex items-center gap-3 mb-6">
                    <?php
                    $toplam_puan = 0;
                    foreach ($yorumlar as $yorum) { $toplam_puan += $yorum['puan']; }
                    $yorum_sayisi = count($yorumlar);
                    $ortalama_puan = $yorum_sayisi > 0 ? round($toplam_puan / $yorum_sayisi, 1) : 0;
                    ?>
                    <div class="flex items-center">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i <= $ortalama_puan ? 'rating-star' : 'rating-star-empty'; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="text-gray-700 font-medium"><?php echo $ortalama_puan; ?>/5</span>
                    <a href="#yorumlar" class="text-blue-600 hover:text-blue-700 transition"><?php echo $yorum_sayisi; ?> Ürün yorumu</a>
                </div>

                <!-- Fiyat ve indirim -->
                <?php
                $eski_fiyat = $urun['fiyat'] * 1.07;
                $indirim_yuzde = 7;
                ?>
                <div class="flex items-center gap-4 mb-6">
                    <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-semibold">%<?php echo $indirim_yuzde; ?> indirim</span>
                    <span class="text-gray-400 line-through">₺<?php echo number_format($eski_fiyat, 2, ',', '.'); ?></span>
                    <span class="text-3xl font-bold text-gray-900">₺<?php echo number_format($urun['fiyat'], 2, ',', '.'); ?></span>
                </div>

                <!-- Açıklama -->
                <div class="mb-6">
                    <div class="text-gray-700 leading-relaxed">
                        <span id="aciklamaKisa"><?php echo htmlspecialchars(mb_substr($urun['aciklama'], 0, 150)) . '...'; ?></span>
                        <span id="aciklamaTam" class="hidden"><?php echo nl2br(htmlspecialchars($urun['aciklama'])); ?></span>
                        <button id="aciklamaDugme" class="text-blue-600 hover:text-blue-700 font-semibold mt-2 block">Devamını gör</button>
                    </div>
                </div>

                <!-- Stok bilgisi -->
                <p class="text-gray-500 mb-4">Stokta: <span class="font-semibold"><?php echo $urun['stok']; ?> adet</span></p>

                <!-- Adet seçimi ve sepete ekleme -->
                <form method="post" class="flex items-center gap-4 mb-6 max-w-xs">
                    <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                        <button type="button" onclick="azalt()" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 transition">-</button>
                        <input type="text" name="adet" id="adet" value="1" readonly class="w-12 text-center border-l border-r border-gray-300 focus:outline-none" />
                        <button type="button" onclick="arttir()" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 transition">+</button>
                    </div>
                    <button type="submit" name="ekle" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition flex items-center gap-2">
                        <i class="fa fa-cart-plus"></i> Sepete Ekle
                    </button>
                </form>
                <p id="stokUyari" class="text-red-600 text-sm hidden">Stok yetersiz!</p>
            </div>
        </div>
    </div>

    <!-- Ürün açıklaması devamı için JS -->
    <script>
        const aciklamaDugme = document.getElementById('aciklamaDugme');
        const aciklamaKisa = document.getElementById('aciklamaKisa');
        const aciklamaTam = document.getElementById('aciklamaTam');

        aciklamaDugme.addEventListener('click', () => {
            if (aciklamaTam.classList.contains('hidden')) {
                aciklamaTam.classList.remove('hidden');
                aciklamaKisa.classList.add('hidden');
                aciklamaDugme.textContent = 'Kısalt';
            } else {
                aciklamaTam.classList.add('hidden');
                aciklamaKisa.classList.remove('hidden');
                aciklamaDugme.textContent = 'Devamını gör';
            }
        });
    </script>

    <!-- Yorumlar bölümü -->
    <div id="yorumlar" class="mt-16">
        <h2 class="text-2xl font-bold mb-6">Ürün Yorumları (<?php echo $yorum_sayisi; ?>)</h2>
        <?php if ($yorum_sayisi > 0): ?>
            <div class="space-y-6">
                <?php foreach ($yorumlar as $yorum): ?>
                    <div class="bg-white p-6 rounded-2xl shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-lg text-gray-900"><?php echo htmlspecialchars($yorum['ad_soyad']); ?></h3>
                            <div class="flex">
                                <?php for ($i=1; $i<=5; $i++): ?>
                                    <i class="fas fa-star <?php echo ($i <= $yorum['puan']) ? 'rating-star' : 'rating-star-empty'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="text-gray-700 whitespace-pre-line"><?php echo htmlspecialchars($yorum['yorum_metni']); ?></p>
                        <small class="text-gray-400"><?php echo date("d.m.Y H:i", strtotime($yorum['yorum_tarihi'])); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600">Henüz yorum yapılmamış.</p>
        <?php endif; ?>

        <!-- Yorum ekleme formu -->
        <div class="mt-12 bg-white p-6 rounded-2xl shadow-sm max-w-lg">
            <?php if ($kullanici2): ?>
                <h3 class="text-xl font-semibold mb-4">Yorum Yap</h3>
                <form method="post" class="space-y-4">
                    <label for="yorum_metni" class="block font-medium text-gray-700">Yorumunuz</label>
                    <textarea id="yorum_metni" name="yorum_metni" rows="4" required maxlength="500" class="w-full border border-gray-300 rounded-lg p-3 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>

                    <label for="puan" class="block font-medium text-gray-700">Puanınız</label>
                    <select id="puan" name="puan" required class="border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="5">5 - Çok İyi</option>
                        <option value="4">4 - İyi</option>
                        <option value="3">3 - Ortalama</option>
                        <option value="2">2 - Kötü</option>
                        <option value="1">1 - Çok Kötü</option>
                    </select>

                    <button type="submit" name="yorum_ekle" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                        Yorum Yap
                    </button>
                </form>
            <?php else: ?>
                <p>Yorum yapabilmek için <a href="giris.php" class="text-blue-600 hover:underline">giriş yapmanız</a> gerekiyor.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Önerilen ürünler -->
    <div class="mt-20">
        <h2 class="text-2xl font-bold mb-8">Önerilen Ürünler</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php
            // Aynı kategorideki diğer ürünler
            $kategoriler = [];
            if ($urun['kategori_id']) {
                $kategoriler[] = $urun['kategori_id'];
            }
            $kategoriler_list = implode(',', $kategoriler);

            if (!empty($kategoriler_list)) {
                $sql = "SELECT * FROM urunler u
                        JOIN urun_kategoriler uk ON u.urun_id = uk.urun_id
                        WHERE uk.kategori_id IN ($kategoriler_list) AND u.urun_id != :urun_id
                        LIMIT 4";
                $stmt = $db->prepare($sql);
                $stmt->execute([':urun_id' => $urun_id]);
                $onerilen_urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $onerilen_urunler = [];
            }
            ?>

            <?php foreach ($onerilen_urunler as $urun_oneri): ?>
                <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition cursor-pointer group">
                    <a href="urun_detay.php?id=<?php echo $urun_oneri['urun_id']; ?>">
                        <img src="saticipanel/<?php echo $urun_oneri['resim_url']; ?>" alt="<?php echo htmlspecialchars($urun_oneri['ad']); ?>" class="w-full h-48 object-cover rounded-md mb-4 group-hover:scale-105 transition-transform" />
                        <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($urun_oneri['ad']); ?></h3>
                        <p class="text-red-600 font-bold mt-2">₺<?php echo number_format($urun_oneri['fiyat'], 2, ',', '.'); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>

            <?php if (empty($onerilen_urunler)): ?>
                <p>Önerilen ürün bulunamadı.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const adetInput = document.getElementById('adet');
    const maxStok = <?php echo (int)$urun['stok']; ?>;
    const stokUyari = document.getElementById('stokUyari');

    function arttir() {
        let adet = parseInt(adetInput.value);
        if (adet < maxStok) {
            adetInput.value = adet + 1;
            stokUyari.classList.add('hidden');
        } else {
            stokUyari.classList.remove('hidden');
        }
    }

    function azalt() {
        let adet = parseInt(adetInput.value);
        if (adet > 1) {
            adetInput.value = adet - 1;
            stokUyari.classList.add('hidden');
        }
    }
</script>

</body>
</html>
