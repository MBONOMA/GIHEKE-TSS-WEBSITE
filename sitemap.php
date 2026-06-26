<?php
header('Content-Type: application/xml; charset=utf-8');
$baseUrl = 'https://localhost/Giheke';
$pages = [
    ['url' => '/', 'priority' => '1.0', 'freq' => 'daily'],
    ['url' => '/index.php', 'priority' => '1.0', 'freq' => 'daily'],
    ['url' => '/SchoolApplication.php', 'priority' => '0.9', 'freq' => 'weekly'],
    ['url' => '/elearning.php', 'priority' => '0.9', 'freq' => 'daily'],
    ['url' => '/gallery.php', 'priority' => '0.8', 'freq' => 'monthly'],
    ['url' => '/blog/blog.php', 'priority' => '0.8', 'freq' => 'weekly'],
    ['url' => '/student-login.php', 'priority' => '0.7', 'freq' => 'monthly'],
    ['url' => '/student-verification.php', 'priority' => '0.6', 'freq' => 'monthly'],
];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $page): ?>
    <url>
        <loc><?php echo $baseUrl . $page['url']; ?></loc>
        <priority><?php echo $page['priority']; ?></priority>
        <changefreq><?php echo $page['freq']; ?></changefreq>
    </url>
<?php endforeach; ?>
</urlset>
