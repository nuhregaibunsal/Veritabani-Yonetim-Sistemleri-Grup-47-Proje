<?php
include 'baglanti.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = $_POST["adsoyad"];
    $email = $_POST["email"];
    $dogum_tarihi = $_POST["dogum"];
    $telefon = $_POST["telefon"];
    $sifre = password_hash($_POST["sifre"], PASSWORD_DEFAULT); // Şifreyi güvenli hale getir
    $rol = $_POST["rol"]; // "satici" veya "musteri"

    if ($rol === "satici") {
        $yetki = 1;
        $onay_kodu = rand(100000, 999999); // Rastgele 6 haneli kod
    } else {
        $yetki = 0;
        $onay_kodu = null;
    }
    
    // Veritabanına ekle
    try {
        $sorgu = $db->prepare("INSERT INTO Kullanicilar (`ad_soyad`, `eposta`, `sifre_hash`, `satici`, `kullaniciOnay`, `telefon`, `dogum_tarihi`) 
                               VALUES (:ad, :email, :sifre, :yetki, :onay_kodu, :telefon, :dogum_tarihi)");

        $sorgu->execute([
            ':ad' => $ad,
            ':email' => $email,
            ':dogum_tarihi' => $dogum_tarihi,
            ':telefon' => $telefon,
            ':sifre' => $sifre,
            ':yetki' => $yetki,
            ':onay_kodu' => $onay_kodu
        ]);

        // Eğer kullanıcı satıcıysa, onay sayfasına yönlendir
        if ($yetki == 1) {
            header("Location: onay.php?email=" . urlencode($email));
            exit();
        } else {
            echo "<p class='text-green-600'>Kayıt başarılı! Giriş yapabilirsiniz.</p>";
        }

    } catch (PDOException $e) {
        echo "<p class='text-red-600'>Hata: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - TechStore</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f6f8fc 0%, #f1f5f9 100%);
            min-height: 100vh;
        }
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        .form-control {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.2s ease;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            font-size: 0.95rem;
        }
        .form-control:focus {
            background: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .btn-primary {
            background: #3b82f6;
            border: none;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 1rem;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .register-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }
        .input-icon {
            color: #94a3b8;
            transition: all 0.2s ease;
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.1rem;
        }
        .form-input:focus + .input-icon {
            color: #3b82f6;
        }
        .form-input {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.875rem 1rem 0.875rem 3rem;
            transition: all 0.2s ease;
            width: 100%;
            font-size: 0.95rem;
        }
        .form-input:focus {
            background: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        .btn-register {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
            width: 100%;
            border: none;
            cursor: pointer;
        }
        .btn-register:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }
        .error-message {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50">

<?php include 'navbar.php'; ?>

<div class="w-full h-screen flex items-center justify-center">
    <div class="container max-w-md mx-auto flex items-center justify-center h-full">
        <div class="register-card w-full p-4 sm:p-6 md:p-6 rounded-2xl shadow-lg animate-fade-in flex flex-col justify-center" style="min-height:unset;">
            <div class="text-center mb-4">
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Hesap Oluştur</h1>
                <p class="text-gray-500 text-sm">TechStore'a hoş geldiniz</p>
            </div>

            <?php if (!empty($hata)): ?>
                <div class="error-message animate-fade-in">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= $hata ?>
                </div>
            <?php endif; ?>

            <form action="kayit.php" method="POST" class="space-y-3">
                <div>
                    <label for="adsoyad" class="block text-xs font-medium text-gray-700 mb-1">Ad Soyad</label>
                    <div class="relative">
                        <input type="text" id="adsoyad" name="adsoyad" class="form-input text-sm py-2" required placeholder="Adınız Soyadınız">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-xs font-medium text-gray-700 mb-1">E-posta</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" class="form-input text-sm py-2" required placeholder="ornek@email.com">
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>
                <div>
                    <label for="dogum" class="block text-xs font-medium text-gray-700 mb-1">Doğum Tarihi</label>
                    <div class="relative">
                        <input type="date" id="dogum" name="dogum" class="form-input text-sm py-2" required>
                        <i class="fas fa-calendar input-icon"></i>
                    </div>
                </div>
                <div>
                    <label for="telefon" class="block text-xs font-medium text-gray-700 mb-1">Telefon Numarası</label>
                    <div class="relative">
                        <input type="tel" id="telefon" name="telefon" class="form-input text-sm py-2" required placeholder="+90 5XX XXX XX XX">
                        <i class="fas fa-phone input-icon"></i>
                    </div>
                </div>
                <div>
                    <label for="sifre" class="block text-xs font-medium text-gray-700 mb-1">Şifre</label>
                    <div class="relative">
                        <input type="password" id="sifre" name="sifre" class="form-input text-sm py-2" required placeholder="••••••••">
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>
                <div>
                    <label for="sifre_tekrar" class="block text-xs font-medium text-gray-700 mb-1">Şifre Tekrar</label>
                    <div class="relative">
                        <input type="password" id="sifre_tekrar" name="sifre_tekrar" class="form-input text-sm py-2" required placeholder="••••••••">
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>
                <div>
                    <label for="rol" class="block text-xs font-medium text-gray-700 mb-1"><i class="fas fa-user-tag mr-1"></i>Hesap Türü</label>
                    <div x-data="{
                        open: false,
                        selected: 'musteri',
                        options: [
                            { value: 'musteri', label: 'Müşteri', icon: 'fa-user' },
                            { value: 'satici', label: 'Satıcı', icon: 'fa-store' }
                        ],
                        select(option) {
                            this.selected = option.value;
                            this.open = false;
                        }
                    }" class="relative">
                        <button type="button" @click="open = !open"
                            class="form-input pl-10 pr-8 flex items-center w-full cursor-pointer text-left relative text-sm py-2">
                            <i :class="'fas ' + options.find(o => o.value === selected).icon + ' absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400'" aria-hidden="true"></i>
                            <span x-text="options.find(o => o.value === selected).label"></span>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </button>
                        <div x-show="open" class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded shadow-lg">
                            <template x-for="option in options" :key="option.value">
                                <button type="button" @click="select(option)" class="w-full px-4 py-2 text-left hover:bg-gray-100 flex items-center text-sm">
                                    <i :class="'fas ' + option.icon + ' mr-2 text-gray-500'"></i>
                                    <span x-text="option.label"></span>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="rol" x-model="selected">
                    </div>

                </div>
                <button type="submit" class="btn-register py-2 text-sm mt-2">
                    <i class="fas fa-user-plus mr-2"></i>Kayıt Ol
                </button>
            </form>
            <div class="mt-3 text-center">
                <p class="text-xs text-gray-500">
                    Zaten hesabınız var mı? 
                    <a href="giris.php" class="text-blue-600 hover:text-blue-700 font-medium">Giriş Yap</a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Add Alpine.js for dropdown functionality -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</body>
</html>
