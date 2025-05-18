<?php
session_start(); // Oturum başlatıyoruz

include 'baglanti.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    // Veritabanından kullanıcıyı al
    $sorgu = $db->prepare("SELECT * FROM Kullanicilar WHERE eposta = :email");
    $sorgu->execute([':email' => $email]);
    $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

    if ($kullanici && password_verify($_POST['sifre'], $kullanici['sifre_hash'])) {
        $_SESSION['giren'] = $kullanici['kullanici_id'];
        header("Location: index.php");
        exit();
    } else {
        $error = 'E-posta veya şifre hatalı';
    }
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - TechStore</title>
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
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 1rem;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .login-card:hover {
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
        .btn-login {
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
        .btn-login:hover {
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
        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.875rem;
        }
        .remember-me input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            border-radius: 4px;
            border: 2px solid #e2e8f0;
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
<body>

<?php include 'navbar.php'; ?>

<div class="login-container">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <div class="login-card p-8 animate-fade-in">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Hoş Geldiniz</h1>
                    <p class="text-gray-500">Hesabınıza giriş yapın</p>
                </div>

                <?php if ($error): ?>
                    <div class="error-message animate-fade-in">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form action="giris.php" method="POST" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-posta</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" class="form-input" required 
                                   placeholder="ornek@email.com">
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                    </div>

                    <div>
                        <label for="sifre" class="block text-sm font-medium text-gray-700 mb-2">Şifre</label>
                        <div class="relative">
                            <input type="password" id="sifre" name="sifre" class="form-input" required
                                   placeholder="••••••••">
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            Beni hatırla
                        </label>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-700">Şifremi unuttum</a>
                    </div>

                    <button type="submit" class="btn-login">
                        Giriş Yap
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">
                        Hesabınız yok mu? 
                        <a href="kayit.php" class="text-blue-600 hover:text-blue-700 font-medium">
                            Hemen kayıt olun
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
