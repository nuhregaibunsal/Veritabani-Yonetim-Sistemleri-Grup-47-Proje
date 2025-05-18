<?php
session_start();
include 'baglanti.php';

if (!isset($_SESSION['giren'])) {
    header("Location: giris.php");
    exit();
}

$kullanici_id = $_SESSION['giren'];

$mesaj = $_SESSION['mesaj'] ?? null;
$mesaj_turu = $_SESSION['mesaj_turu'] ?? null;
unset($_SESSION['mesaj'], $_SESSION['mesaj_turu']);

// Kullanıcı bilgilerini al
$kullaniciSorgu = $db->prepare("SELECT * FROM kullanicilar WHERE kullanici_id = :id");
$kullaniciSorgu->execute([':id' => $kullanici_id]);
$kullanici = $kullaniciSorgu->fetch(PDO::FETCH_ASSOC);

if (isset($_SESSION['giren'])) {
    $sorgu2 = $db->prepare("SELECT * FROM Kullanicilar WHERE kullanici_id = :id");
    $sorgu2->execute([':id' => $_SESSION['giren']]);
    $kullanici2 = $sorgu2->fetch(PDO::FETCH_ASSOC);
}
// Adres bilgisi al
$adresSorgu = $db->prepare("SELECT * FROM adresler WHERE kullanici_id = :id LIMIT 1");
$adresSorgu->execute([':id' => $kullanici_id]);
$adres = $adresSorgu->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim - TechStore</title>
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
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        .form-control {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .btn-primary {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

<?php include 'navbar.php'; ?>

<div class="container mx-auto px-4 pt-24 pb-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center">Profil Bilgileri</h1>

        <?php if ($mesaj): ?>
            <div class="mb-6 p-4 rounded-lg <?= $mesaj_turu === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>" role="alert">
                <?= htmlspecialchars($mesaj) ?>
            </div>
        <?php endif; ?>

        <form action="profil_guncelle.php" method="POST" class="bg-white rounded-3xl shadow-lg p-8 space-y-6" onsubmit="return validateForm();" data-aos="fade-up">
            <h2 class="text-2xl font-semibold mb-6">Kişisel Bilgiler</h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="ad_soyad" class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad</label>
                    <input id="ad_soyad" type="text" name="ad_soyad" class="form-control w-full" value="<?= htmlspecialchars($kullanici['ad_soyad']) ?>" required>
                </div>
                <div>
                    <label for="e_posta" class="block text-sm font-medium text-gray-700 mb-2">E-posta</label>
                    <input id="e_posta" type="email" name="e_posta" class="form-control w-full" value="<?= htmlspecialchars($kullanici['eposta']) ?>" required>
                </div>
                <div>
                    <label for="telefon" class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                    <input id="telefon" type="tel" name="telefon" class="form-control w-full" value="<?= htmlspecialchars($kullanici['telefon']) ?>" required pattern="^\+?[0-9\s\-]{7,15}$" title="Geçerli bir telefon numarası giriniz.">
                </div>
            </div>

            <h2 class="text-2xl font-semibold pt-8 mb-6">Adres Bilgileri</h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="adres_satiri1" class="block text-sm font-medium text-gray-700 mb-2">Adres Satırı 1</label>
                    <input id="adres_satiri1" type="text" name="adres_satiri1" class="form-control w-full" value="<?= htmlspecialchars($adres['adres_satiri1'] ?? '') ?>">
                </div>
                <div>
                    <label for="adres_satiri2" class="block text-sm font-medium text-gray-700 mb-2">Adres Satırı 2</label>
                    <input id="adres_satiri2" type="text" name="adres_satiri2" class="form-control w-full" value="<?= htmlspecialchars($adres['adres_satiri2'] ?? '') ?>">
                </div>
                <div>
                    <label for="sehir" class="block text-sm font-medium text-gray-700 mb-2">Şehir</label>
                    <input id="sehir" type="text" name="sehir" class="form-control w-full" value="<?= htmlspecialchars($adres['sehir'] ?? '') ?>">
                </div>
                <div>
                    <label for="posta_kodu" class="block text-sm font-medium text-gray-700 mb-2">Posta Kodu</label>
                    <input id="posta_kodu" type="text" name="posta_kodu" class="form-control w-full" value="<?= htmlspecialchars($adres['posta_kodu'] ?? '') ?>">
                </div>
                <div>
                    <label for="ulke" class="block text-sm font-medium text-gray-700 mb-2">Ülke</label>
                    <input id="ulke" type="text" name="ulke" class="form-control w-full" value="<?= htmlspecialchars($adres['ulke'] ?? '') ?>">
                </div>
            </div>

            <h2 class="text-2xl font-semibold pt-8 mb-6">Şifre Değiştir</h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="yeni_sifre" class="block text-sm font-medium text-gray-700 mb-2">Yeni Şifre</label>
                    <input id="yeni_sifre" type="password" name="yeni_sifre" class="form-control w-full" minlength="6" placeholder="Yeni şifreniz (en az 6 karakter)">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="yeni_sifre_tekrar" class="block text-sm font-medium text-gray-700 mb-2">Yeni Şifre (Tekrar)</label>
                    <input id="yeni_sifre_tekrar" type="password" name="yeni_sifre_tekrar" class="form-control w-full" minlength="6" placeholder="Yeni şifrenizi tekrar girin">
                </div>
            </div>

            <div class="pt-6 grid md:grid-cols-2 gap-4">
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>Güncelle
                </button>
                <a href="cikis.php" class="block w-full bg-red-500 text-white py-3 rounded-xl hover:bg-red-600 transition-all duration-300 text-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>Çıkış Yap
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Initialize AOS
    AOS.init();

    function validateForm() {
        const yeniSifre = document.getElementById('yeni_sifre').value;
        const yeniSifreTekrar = document.getElementById('yeni_sifre_tekrar').value;

        if (yeniSifre || yeniSifreTekrar) {
            if (yeniSifre.length < 6) {
                alert('Yeni şifre en az 6 karakter olmalı.');
                return false;
            }
            if (yeniSifre !== yeniSifreTekrar) {
                alert('Şifreler eşleşmiyor.');
                return false;
            }
        }
        return true;
    }
</script>
</body>
</html>
