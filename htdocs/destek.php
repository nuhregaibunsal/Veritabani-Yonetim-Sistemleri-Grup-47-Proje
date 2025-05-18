<?php
session_start();
include 'baglanti.php';

// Kullanıcı bilgisi
$kullanici = null;
if (isset($_SESSION['giren'])) {
    $sorgu = $db->prepare("SELECT * FROM Kullanicilar WHERE kullanici_id = :id");
    $sorgu->execute([':id' => $_SESSION['giren']]);
    $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim - TechStore</title>
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
        .support-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .support-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        .faq-item {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .faq-item:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .contact-input {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .contact-input:focus {
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

<!-- Main Content -->
<div class="container mx-auto px-4 pt-24 pb-12">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-blue-50 to-purple-50 py-16 rounded-3xl mb-12 overflow-hidden" data-aos="fade-up">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>

        <div class="relative text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 text-gray-800">
                Size Nasıl Yardımcı Olabiliriz?
            </h1>
            <p class="text-gray-600 text-lg">
                Sorularınız için 7/24 destek ekibimiz yanınızda
            </p>
        </div>
    </div>

    <!-- Support Options -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="support-card p-6 text-center" data-aos="fade-up" data-aos-delay="100">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-headset text-3xl text-blue-600"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Canlı Destek</h3>
            <p class="text-gray-600 mb-4">7/24 canlı destek ekibimizle anında iletişime geçin</p>
            <button class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                Başlat
            </button>
        </div>

        <div class="support-card p-6 text-center" data-aos="fade-up" data-aos-delay="200">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-envelope text-3xl text-purple-600"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">E-posta Desteği</h3>
            <p class="text-gray-600 mb-4">Detaylı sorularınız için e-posta gönderin</p>
            <button class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-2 rounded-lg hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                Gönder
            </button>
        </div>

        <div class="support-card p-6 text-center" data-aos="fade-up" data-aos-delay="300">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-phone text-3xl text-green-600"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Telefon Desteği</h3>
            <p class="text-gray-600 mb-4">0850 123 45 67 numaralı hattımızdan bize ulaşın</p>
            <button class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-2 rounded-lg hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                Ara
            </button>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="mb-12" data-aos="fade-up">
        <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">Sıkça Sorulan Sorular</h2>
        <div class="space-y-4">
            <div class="faq-item p-6">
                <button class="w-full text-left flex justify-between items-center" onclick="toggleFAQ(this)">
                    <span class="font-medium text-gray-800">Siparişim ne zaman elime ulaşır?</span>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-gray-600"></i>
                </button>
                <div class="mt-2 text-gray-600 hidden">
                    Siparişleriniz genellikle 1-3 iş günü içerisinde kargoya verilir ve 1-2 iş günü içerisinde teslim edilir.
                </div>
            </div>

            <div class="faq-item p-6">
                <button class="w-full text-left flex justify-between items-center" onclick="toggleFAQ(this)">
                    <span class="font-medium text-gray-800">İade ve değişim politikası nedir?</span>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-gray-600"></i>
                </button>
                <div class="mt-2 text-gray-600 hidden">
                    14 gün içerisinde ücretsiz iade ve değişim hakkınız bulunmaktadır. Ürün orijinal ambalajında ve kullanılmamış olmalıdır.
                </div>
            </div>

            <div class="faq-item p-6">
                <button class="w-full text-left flex justify-between items-center" onclick="toggleFAQ(this)">
                    <span class="font-medium text-gray-800">Ödeme seçenekleri nelerdir?</span>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-gray-600"></i>
                </button>
                <div class="mt-2 text-gray-600 hidden">
                    Kredi kartı, banka kartı, havale/EFT ve kapıda ödeme seçeneklerimiz mevcuttur.
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Form -->
    <div class="max-w-2xl mx-auto bg-white rounded-3xl p-8 shadow-lg" data-aos="fade-up">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold mb-2 text-gray-800">Bize Ulaşın</h2>
            <p class="text-gray-600">Sorularınız için bize ulaşın</p>
        </div>
        <form class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <input type="text" class="w-full contact-input py-3 px-4 text-gray-800 placeholder-gray-500" placeholder="Ad Soyad">
                </div>
                <div>
                    <input type="email" class="w-full contact-input py-3 px-4 text-gray-800 placeholder-gray-500" placeholder="E-posta">
                </div>
            </div>
            <div>
                <select class="w-full contact-input py-3 px-4 text-gray-800">
                    <option value="" class="bg-white">Konu Seçiniz</option>
                    <option value="siparis" class="bg-white">Sipariş Hakkında</option>
                    <option value="urun" class="bg-white">Ürün Bilgisi</option>
                    <option value="iade" class="bg-white">İade/Değişim</option>
                    <option value="diger" class="bg-white">Diğer</option>
                </select>
            </div>
            <div>
                <textarea class="w-full contact-input py-3 px-4 text-gray-800 placeholder-gray-500 resize-none" rows="4" placeholder="Mesajınız"></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                Gönder
            </button>
        </form>
    </div>
</div>

<script>
    // Initialize AOS
    AOS.init();

    // FAQ Toggle
    function toggleFAQ(button) {
        const content = button.nextElementSibling;
        const icon = button.querySelector('i');
        
        content.classList.toggle('hidden');
        icon.style.transform = content.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }
</script>
</body>
</html> 