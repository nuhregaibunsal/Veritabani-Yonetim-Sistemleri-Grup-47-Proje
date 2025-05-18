<?php
// Aktif sayfayı belirle
$current_page = basename($_SERVER['PHP_SELF']);

// Navbar için sabit stiller
$navbar_styles = [
    'container' => 'container mx-auto px-4',
    'nav' => 'navbar navbar-expand-lg fixed-top bg-white border-b border-gray-200',
    'brand' => 'navbar-brand flex items-center',
    'logo' => 'w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center',
    'logo_icon' => 'fas fa-microchip text-white text-xl',
    'logo_dot' => 'absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-gray-900',
    'brand_text' => 'ml-3 text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent',
    'toggler' => 'navbar-toggler border-0 text-gray-700',
    'nav_links' => 'navbar-nav mx-auto',
    'nav_item' => 'nav-item',
    'nav_link' => 'nav-link px-4 py-2 transition-colors duration-300',
    'nav_link_active' => 'text-blue-500',
    'nav_link_inactive' => 'text-gray-700 hover:text-gray-900',
    'user_menu' => 'flex items-center gap-4',
    'user_link' => 'flex items-center text-gray-700 hover:text-gray-900 transition-colors duration-300',
    'user_icon' => 'fas fa-user-circle text-2xl',
    'seller_icon' => 'fas fa-store mr-2',
    'user_dot' => 'absolute -top-1 -right-1 w-2 h-2 bg-green-500 rounded-full',
    'cart_link' => 'flex items-center text-gray-700 hover:text-gray-900 transition-colors duration-300 relative',
    'cart_icon' => 'fas fa-shopping-cart text-2xl',
    'cart_badge' => 'absolute -top-2 -right-2 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center',
    'shoping_bag' => 'fas fa-shopping-bag'
];

// Sepet sayısını al
$sepet_adet = 0;
if (isset($_SESSION['giren'])) {
    $sepet_sorgu = $db->prepare("SELECT COUNT(*) as adet FROM sepetogeleri WHERE kullanici_id = :kullanici_id");
    $sepet_sorgu->execute([':kullanici_id' => $_SESSION['giren']]);
    $sepet_adet = $sepet_sorgu->fetch(PDO::FETCH_ASSOC)['adet'];
}
?>
<nav class="<?php echo $navbar_styles['nav']; ?>">
    <div class="<?php echo $navbar_styles['container']; ?>">
        <a class="<?php echo $navbar_styles['brand']; ?>" href="index.php">
            <div class="relative">
                <div class="<?php echo $navbar_styles['logo']; ?>">
                    <i class="<?php echo $navbar_styles['logo_icon']; ?>"></i>
                </div>
                <div class="<?php echo $navbar_styles['logo_dot']; ?>"></div>
            </div>
            <span class="<?php echo $navbar_styles['brand_text']; ?>">
                TechStore
            </span>
        </a>
        <button class="<?php echo $navbar_styles['toggler']; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="<?php echo $navbar_styles['nav_links']; ?>">
                <li class="<?php echo $navbar_styles['nav_item']; ?>">
                    <a href="index.php" class="<?php echo $navbar_styles['nav_link'] . ' ' . ($current_page == 'index.php' ? $navbar_styles['nav_link_active'] : $navbar_styles['nav_link_inactive']); ?>">
                        <i class="fas fa-home mr-2"></i>Ana Sayfa
                    </a>
                </li>
                <li class="<?php echo $navbar_styles['nav_item']; ?>">
                    <a href="destek.php" class="<?php echo $navbar_styles['nav_link'] . ' ' . ($current_page == 'destek.php' ? $navbar_styles['nav_link_active'] : $navbar_styles['nav_link_inactive']); ?>">
                        <i class="fas fa-headset mr-2"></i>İletişim
                    </a>
                </li>
            </ul>
            <div class="<?php echo $navbar_styles['user_menu']; ?>">
                <?php if (isset($_SESSION['giren'])): ?>
                    <a href="profil.php" class="<?php echo $navbar_styles['user_link']; ?>">
                        <div class="relative">
                            <i class="<?php echo $navbar_styles['user_icon']; ?>"></i>
                            <span class="<?php echo $navbar_styles['user_dot']; ?>"></span>
                        </div>
                        <span class="ml-2 hidden md:inline">Profilim</span>
                    </a>
                    <?php
                        $kullanici_sorgu = $db->prepare("SELECT satici FROM kullanicilar WHERE kullanici_id = :kullanici_id");
                        $kullanici_sorgu->execute([':kullanici_id' => $_SESSION['giren']]);
                        $kullanici4 = $kullanici_sorgu->fetch(PDO::FETCH_ASSOC);
                        if($kullanici4['satici'] == 1){
                            ?>
                            <a href="saticipanel/saticiindex.php" class="<?php echo $navbar_styles['user_link']; ?>">
                            <div class="relative">
                                <i class="<?php echo $navbar_styles['seller_icon']; ?>"></i>
                                <span class="<?php echo $navbar_styles['user_dot']; ?>"></span>
                            </div>
                            <span class="ml-2 hidden md:inline">Satıcı Paneli</span>
                    </a>
                            <?php
                        }
                    ?>
                    <a href="siparis_takip.php" class="<?php echo $navbar_styles['user_link']; ?>">
                        <div class="relative">
                            <i class="<?php echo $navbar_styles['shoping_bag']; ?>"></i>
                            <span class="<?php echo $navbar_styles['user_dot']; ?>"></span>
                        </div>
                        <span class="ml-2 hidden md:inline">Siparişlerim</span>
                    </a>
                <?php else: ?>
                    <a href="giris.php" class="<?php echo $navbar_styles['user_link']; ?>">
                        <div class="relative">
                            <i class="<?php echo $navbar_styles['user_icon']; ?>"></i>
                            <span class="<?php echo $navbar_styles['user_dot']; ?>"></span>
                        </div>
                        <span class="ml-2 hidden md:inline">Giriş Yap</span>
                    </a>
                <?php endif; ?>
                <a href="sepet.php" class="<?php echo $navbar_styles['cart_link']; ?>">
                    <div class="relative">
                        <i class="<?php echo $navbar_styles['cart_icon']; ?>"></i>
                        <?php if ($sepet_adet > 0): ?>
                        <span class="<?php echo $navbar_styles['cart_badge']; ?>">
                            <?php echo $sepet_adet; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <span class="ml-2 hidden md:inline">Sepetim</span>
                </a>
            </div>
        </div>
    </div>
</nav>
<!-- Add margin-top to account for fixed navbar -->
<div style="margin-top: 80px;"> 