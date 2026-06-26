<?php
if (!isset($siteSettings)) {
    $siteSettings = [];
    if (!isset($conn) || !$conn) {
        include_once dirname(__FILE__) . '/connection.php';
    }
    if (isset($conn)) {
        $ssq = mysqli_query($conn, "SELECT setting_key, setting_value FROM tbl_site_settings");
        if ($ssq) {
            while ($ssr = mysqli_fetch_assoc($ssq)) {
                $siteSettings[$ssr['setting_key']] = $ssr['setting_value'];
            }
        }
    }
}
$site_name = $siteSettings['site_name'] ?? 'GIHEKE';
$site_tagline = $siteSettings['site_tagline'] ?? 'Technical Secondary School';
$site_email = $siteSettings['site_email'] ?? 'giheketss@gmail.com';
$site_phone = $siteSettings['site_phone'] ?? '+250 788 885 418';
$site_address = $siteSettings['site_address'] ?? 'Rusizi District, Giheke Sector';
$social_fb = $siteSettings['social_facebook'] ?? '#';
$social_tw = $siteSettings['social_twitter'] ?? '#';
$social_ig = $siteSettings['social_instagram'] ?? '#';
$social_li = $siteSettings['social_linkedin'] ?? '#';
$footer_copy = $siteSettings['footer_copyright'] ?? '&copy; ' . date('Y') . ' Copyright <strong><span>' . $site_name . ' TSS</span></strong>. All Rights Reserved.';
?>
<!-- FOOTER -->
<footer class="footer-haip" id="footer">
    <div class="container-haip-wide">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="logo-text">
                    <img src="img/giheke logo.webp" alt="Logo">
                    <?php echo htmlspecialchars($site_name); ?><span>TSS</span>
                </div>
                <p><?php echo htmlspecialchars($site_tagline ?: $site_name . ' Technical Secondary School — Empowering the next generation of skilled professionals in Rusizi District, Rwanda.'); ?></p>
                <div class="footer-social">
                    <a href="<?php echo htmlspecialchars($social_tw); ?>"><i class="bi bi-twitter"></i></a>
                    <a href="<?php echo htmlspecialchars($social_fb); ?>"><i class="bi bi-facebook"></i></a>
                    <a href="<?php echo htmlspecialchars($social_ig); ?>"><i class="bi bi-instagram"></i></a>
                    <a href="<?php echo htmlspecialchars($social_li); ?>"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="bi bi-chevron-right"></i> Home</a></li>
                    <li><a href="index.php#about"><i class="bi bi-chevron-right"></i> About Us</a></li>
                    <li><a href="index.php#team"><i class="bi bi-chevron-right"></i> Trades</a></li>
                    <li><a href="gallery.php"><i class="bi bi-chevron-right"></i> Gallery</a></li>
                    <li><a href="blog/blog.php"><i class="bi bi-chevron-right"></i> School Blog</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Our Trades</h4>
                <ul class="footer-links">
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Software Development</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Network and internet technology</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Computer Systems and Architecture</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Electrical Technology</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Electronics and Telecommunication Services</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Building Construction</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Professional Accounting</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Contact Info</h4>
                <div class="footer-contact-item">
                    <i class="bi bi-geo-alt"></i>
                    <div class="text"><strong>Address:</strong> <?php echo htmlspecialchars($site_address); ?></div>
                </div>
                <div class="footer-contact-item">
                    <i class="bi bi-envelope"></i>
                    <div class="text"><strong>Email:</strong> <?php echo htmlspecialchars($site_email); ?></div>
                </div>
                <div class="footer-contact-item">
                    <i class="bi bi-telephone"></i>
                    <div class="text"><strong>Phone:</strong> <?php echo htmlspecialchars($site_phone); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container-haip">
            <p><?php echo $footer_copy; ?></p>
            <div class="footer-credit">
                Developed by <a href="https://devoma.vercel.app" target="_blank">Omar MBONABUCYA</a>
            </div>
        </div>
    </div>
</footer>

<!-- BACK TO TOP -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="bi bi-arrow-up-short"></i>
</button>
