<?php
session_start();
include 'baglanti.php';

$arama = isset($_GET['arama']) ? trim($_GET['arama']) : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$sirala = isset($_GET['sirala']) ? $_GET['sirala'] : '';

// Ürünleri çekmek
$sql = "SELECT * FROM urunler WHERE 1=1";
$params = [];

if ($arama != "") {
    $sql .= " AND ad LIKE :arama";
    $params[':arama'] = "%$arama%";
}

if ($kategori != "") {
    $sql .= " AND urun_id IN (SELECT urun_id FROM urun_kategoriler WHERE kategori_id = :kategori)";
    $params[':kategori'] = $kategori;
}

if ($sirala == "artan") {
    $sql .= " ORDER BY fiyat ASC";
} elseif ($sirala == "azalan") {
    $sql .= " ORDER BY fiyat DESC";
} elseif ($sirala == "yeni") {
    $sql .= " ORDER BY urun_id DESC";
}

$sorgu = $db->prepare($sql);
$sorgu->execute($params);
$urunler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

// Kategorileri çekmek
$sorgu_kategoriler = $db->prepare("SELECT * FROM kategoriler");
$sorgu_kategoriler->execute();
$kategoriler = $sorgu_kategoriler->fetchAll(PDO::FETCH_ASSOC);

// Kullanıcı bilgisi
$kullanici2 = null;
if (isset($_SESSION['giren'])) {
    $sorgu2 = $db->prepare("SELECT * FROM Kullanicilar WHERE kullanici_id = :id");
    $sorgu2->execute([':id' => $_SESSION['giren']]);
    $kullanici2 = $sorgu2->fetch(PDO::FETCH_ASSOC);
}

// Sepete ekleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ekle"])) {
    $urun_id = $_POST["urun_id"];
    $adet = max(1, intval($_POST["adet"]));

    $stok_sorgu = $db->prepare("SELECT stok FROM urunler WHERE urun_id = :urun_id");
    $stok_sorgu->execute([':urun_id' => $urun_id]);
    $urun_stok = $stok_sorgu->fetch(PDO::FETCH_ASSOC)['stok'];

    if ($urun_stok >= $adet) {
        $sorgu = $db->prepare("SELECT * FROM sepetogeleri WHERE kullanici_id = :kullanici_id AND urun_id = :urun_id");
        $sorgu->execute([':kullanici_id' => $_SESSION['giren'], ':urun_id' => $urun_id]);
        $urun = $sorgu->fetch(PDO::FETCH_ASSOC);

        if ($urun) {
            $guncelle = $db->prepare("UPDATE sepetogeleri SET adet = adet + :adet WHERE sepet_oge_id = :sepet_oge_id");
            $guncelle->execute([':adet' => $adet, ':sepet_oge_id' => $urun['sepet_oge_id']]);
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
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa - E-Ticaret</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .carousel-inner { 
            height: 500px; 
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .carousel-inner img { 
            height: 100%; 
            width: 100%; 
            object-fit: cover; 
        }
        .product-card { 
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .product-card:hover { 
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .filter-section { 
            background: white; 
            border-radius: 20px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .filter-section:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .nav-link { 
            position: relative; 
            transition: all 0.3s ease;
        }
        .nav-link::after { 
            content: ''; 
            position: absolute; 
            width: 0; 
            height: 2px; 
            bottom: -2px; 
            left: 0; 
            background-color: #d46bb3; 
            transition: width 0.3s ease; 
        }
        .nav-link:hover::after { 
            width: 100%; 
        }
        .btn-primary {
            background: linear-gradient(45deg, #d46bb3, #e83e8c);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(232, 62, 140, 0.3);
        }
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        .product-image {
            transition: all 0.5s ease;
        }
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        .quantity-btn {
            transition: all 0.2s ease;
        }
        .quantity-btn:hover {
            background-color: #d46bb3;
            color: white;
        }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        @keyframes spin-slow {
            from {
                transform: translate(-50%, -50%) rotate(0deg);
            }
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
        .animate-spin-slow {
            animation: spin-slow 20s linear infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

<?php include 'navbar.php'; ?>

<!-- Hero Section -->
<div class="relative bg-gradient-to-br from-blue-50 to-purple-50 py-16 overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <div class="container mx-auto px-4 relative">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="space-y-8" data-aos="fade-right">
                <div class="inline-block px-4 py-2 bg-blue-100 rounded-full border border-blue-200">
                    <span class="text-blue-600 text-sm font-medium">
                        <i class="fas fa-bolt mr-2"></i>Yeni Teknolojiler
                    </span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold text-gray-800 leading-tight">
                    Geleceğin
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Teknolojisi
                    </span>
                    <br>Bugün Sizinle
                </h1>
                <p class="text-lg text-gray-600">
                    En yeni teknoloji ürünleri, akıllı cihazlar ve elektronik aksesuarlar. 
                    Uygun fiyatlar ve güvenli alışveriş garantisiyle teknoloji dünyasına adım atın.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#urunler" class="group px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <span class="flex items-center">
                            <i class="fas fa-shopping-basket mr-2 group-hover:rotate-12 transition-transform"></i>
                            Hemen Keşfet
                        </span>
                    </a>
                </div>
                <!-- Features -->
                <div class="grid grid-cols-3 gap-4 pt-6">
                    <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                        <i class="fas fa-truck-fast text-2xl text-blue-600 mb-2"></i>
                        <p class="text-sm font-medium text-gray-600">Hızlı Teslimat</p>
                    </div>
                    <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                        <i class="fas fa-lock text-2xl text-blue-600 mb-2"></i>
                        <p class="text-sm font-medium text-gray-600">Güvenli Ödeme</p>
                    </div>
                    <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                        <i class="fas fa-headset text-2xl text-blue-600 mb-2"></i>
                        <p class="text-sm font-medium text-gray-600">7/24 Destek</p>
                    </div>
                </div>
            </div>
            <!-- Right Content - Modern Product Showcase -->
            <div class="relative h-[600px]" data-aos="fade-left">
                <!-- Main Product -->
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 z-30" style="background:#fff; border-radius:30px; box-shadow:0 10px 30px rgba(0,0,0,0.1); display:flex; align-items:center; justify-content:center;">
                    <img src="saticipanel/img/ürün-1.png" alt="Ürün 1" style="max-width:90%; max-height:90%; object-fit:contain;">
                </div>
                <!-- Floating Elements -->
                <div class="absolute top-8 right-8 w-40 h-40 z-50 transform hover:scale-110 transition-all duration-700">
                    <div class="relative w-full h-full">
                        <div class="absolute inset-0 bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl border-2 border-blue-200 flex items-center justify-center transform rotate-6 hover:rotate-0 transition-all duration-700">
                            <img src="saticipanel/img/ürün-2.png" alt="Ürün 2" class="w-5/6 h-5/6 object-contain rounded-2xl" onerror="this.onerror=null; this.src='saticipanel/img/68244946081db.png';">
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-blue-500 rounded-full animate-pulse"></div>
                    </div>
                </div>
                <div class="absolute bottom-8 left-8 w-40 h-40 z-50 transform hover:scale-110 transition-all duration-700">
                    <div class="relative w-full h-full">
                        <div class="absolute inset-0 bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl border-2 border-purple-200 flex items-center justify-center transform -rotate-6 hover:rotate-0 transition-all duration-700">
                            <img src="saticipanel/img/ürün-3.png" alt="Ürün 3" class="w-5/6 h-5/6 object-contain rounded-2xl" onerror="this.onerror=null; this.src='saticipanel/img/682444159ef59.png';">
                        </div>
                        <div class="absolute -top-2 -left-2 w-8 h-8 bg-purple-500 rounded-full animate-pulse"></div>
                    </div>
                </div>
                <!-- Decorative Elements -->
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full border-2 border-dashed border-gray-200/50 animate-spin-slow">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-circle-notch text-4xl text-blue-500/30 animate-spin"></i>
                    </div>
                </div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full border-2 border-dashed border-gray-200/30 animate-spin-slow animation-delay-2000">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-cog text-4xl text-purple-500/30 animate-spin"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container mx-auto px-4 py-12" id="urunler">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters -->
        <div class="lg:w-1/4" data-aos="fade-right" data-aos-duration="1000">
            <div class="bg-white/80 backdrop-blur-sm rounded-3xl p-6 shadow-lg border border-gray-100">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                    <i class="fas fa-sliders-h me-2 text-blue-600"></i>
                    Filtreler
                </h2>
                <form method="GET" class="space-y-6">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" name="arama" placeholder="Ürün adı ara..." 
                            class="w-full p-4 bg-gray-50/50 border border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300" 
                            value="<?php echo htmlspecialchars($arama); ?>">
                        <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>

                    <!-- Categories -->
                    <div class="space-y-3">
                        <label class="block text-gray-600 font-medium">
                            <i class="fas fa-tags me-2 text-blue-600"></i>Kategoriler
                        </label>
                        <div class="space-y-2">
                            <input type="radio" 
                                id="kategori-tumu" 
                                name="kategori" 
                                value="" 
                                class="hidden peer"
                                <?php echo empty($kategori) ? 'checked' : ''; ?>>

                            <label for="kategori-tumu" 
                                class="cursor-pointer select-none flex items-center gap-2 rounded-xl px-4 py-2 
                                        text-gray-700 bg-white border border-gray-300 hover:bg-blue-50 
                                        peer-checked:bg-blue-600 peer-checked:text-white transition-all duration-300">
                                Tümünü Görüntüle

                                <span class="w-3 h-3 rounded-full border-2 border-gray-400 
                                            peer-checked:border-white peer-checked:bg-white ml-auto"></span>
                            </label>

                            <?php foreach ($kategoriler as $kat): 
                                $kategoriAdi = htmlspecialchars($kat['ad']);
                                $checked = ($kategori == $kategoriAdi) ? 'checked' : '';
                            ?>
                                <input type="radio" 
                                    id="kategori-<?php echo $kategoriAdi; ?>" 
                                    name="kategori" 
                                    value="<?php echo $kategoriAdi; ?>" 
                                    class="hidden peer" 
                                    <?php echo $checked; ?>>

                                <label for="kategori-<?php echo $kategoriAdi; ?>" 
                                    class="cursor-pointer select-none flex items-center gap-2 rounded-xl px-4 py-2 
                                            text-gray-700 bg-white border border-gray-300 hover:bg-blue-50 
                                            peer-checked:bg-blue-600 peer-checked:text-white transition-all duration-300">
                                    
                                    <?php echo $kategoriAdi; ?>

                                    <span class="w-3 h-3 rounded-full border-2 border-gray-400 
                                                peer-checked:border-white peer-checked:bg-white ml-auto"></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="space-y-3">
                        <label class="block text-gray-600 font-medium">
                            <i class="fas fa-sort me-2 text-blue-600"></i>Sıralama
                        </label>
                        <select class="w-full p-4 bg-gray-50/50 border border-gray-200 rounded-xl text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300" name="sirala">
                            <option value="">Varsayılan</option>
                            <option value="artan" <?php echo ($sirala == 'artan' ? 'selected' : ''); ?>>Fiyat Artan</option>
                            <option value="azalan" <?php echo ($sirala == 'azalan' ? 'selected' : ''); ?>>Fiyat Azalan</option>
                            <option value="yeni" <?php echo ($sirala == 'yeni' ? 'selected' : ''); ?>>Yeniden Eskiye</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3 pt-4">
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                            <i class="fas fa-filter mr-2"></i>Filtrele
                        </button>
                        <a href="index.php" class="block w-full bg-gray-50/50 text-gray-600 py-4 rounded-xl text-center hover:bg-gray-50 transition-all duration-300 border border-gray-200">
                            <i class="fas fa-times mr-2"></i>Filtreyi Temizle
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="lg:w-3/4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($urunler as $index => $urun): 
                    if($urun['aktif']==1 && $urun['stok'] >=1){
                    ?>
                    <div class="group bg-white/80 backdrop-blur-sm rounded-3xl overflow-hidden shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl hover:-translate-y-1" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <!-- Product Image -->
                        <div class="relative overflow-hidden">
                            <a href="urun_detay.php?id=<?php echo $urun['urun_id']; ?>" class="block aspect-square">
                                <img style = "height: 300px;" src="saticipanel/<?php echo $urun['resim_url']; ?>" 
                                    class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500" 
                                    alt="<?php echo htmlspecialchars($urun['ad']); ?>">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="absolute bottom-4 left-4 right-4 flex items-center gap-2">
                                        <span class="px-3 py-1 bg-blue-500/90 backdrop-blur-sm text-white text-sm rounded-full">
                                            <i class="fas fa-bolt mr-1"></i>Yeni
                                        </span>
                                        <span class="px-3 py-1 bg-purple-500/90 backdrop-blur-sm text-white text-sm rounded-full">
                                            <i class="fas fa-star mr-1"></i>Popüler
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Product Info -->
                        <div class="p-6">
                            <a href="urun_detay.php?id=<?php echo $urun['urun_id']; ?>" class="block">
                                <h3 class="font-bold text-lg mb-2 text-gray-800 group-hover:text-blue-600 transition-colors duration-300">
                                    <?php echo htmlspecialchars($urun['ad']); ?>
                                </h3>
                            </a>
                            <div class="flex justify-between items-center mb-4">
                                <p class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                    ₺<?php echo number_format($urun['fiyat'], 2, ',', '.'); ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-box me-1"></i>
                                    Stok: <?php echo htmlspecialchars($urun['stok']); ?>
                                </p>
                            </div>

                            <?php if (isset($_SESSION['giren'])): ?>
                                <form action="index.php" method="POST" class="space-y-3">
                                    <input type="hidden" name="urun_id" value="<?php echo $urun['urun_id']; ?>">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="adetDegistir(this, -1, <?php echo $urun['stok']; ?>)" 
                                            class="quantity-btn bg-gray-50 hover:bg-blue-600 w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 text-gray-600 hover:text-white">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" name="adet" min="1" max="<?php echo $urun['stok']; ?>" value="1" 
                                            class="w-16 text-center p-2 bg-gray-50/50 border border-gray-200 rounded-xl text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                        <button type="button" onclick="adetDegistir(this, 1, <?php echo $urun['stok']; ?>)" 
                                            class="quantity-btn bg-gray-50 hover:bg-blue-600 w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 text-gray-600 hover:text-white">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <button type="submit" name="ekle" 
                                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center">
                                        <i class="fas fa-cart-plus mr-2"></i>Sepete Ekle
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="giris.php" 
                                    class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-xl text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Giriş Yapın
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php } endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize AOS
AOS.init();

function adetDegistir(button, degisim, maxStok) {
    const input = button.parentElement.querySelector('input[name="adet"]');
    let adet = parseInt(input.value);
    if (isNaN(adet)) adet = 1;
    adet += degisim;
    if (adet < 1) adet = 1;
    if (adet > maxStok) adet = maxStok;
    input.value = adet;
}

// Smooth scroll to products section
document.querySelector('a[href="#urunler"]').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('urunler').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
});
</script>

</div>
</body>
</html>

