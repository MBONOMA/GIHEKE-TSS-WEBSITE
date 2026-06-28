<?php
/**
 * GIHEKE TSS Website - SEO Meta Tags Template
 * Include this file at the top of each page for consistent SEO
 */

// If $pageTitle is not set, use default
$pageTitle = $pageTitle ?? 'GIHEKE Technical Secondary School';
$pageDescription = $pageDescription ?? 'GIHEKE Technical Secondary School in Rusizi District, Rwanda offers 7 technical trades: Software Development, Network and internet technology, Computer Systems & Architecture, Electrical Technology, Electronics & Telecom, Building Construction, and Professional Accounting.';
$pageImage = $pageImage ?? 'https://giheketss.ac.rw/img/giheke logo.webp';
$pageUrl = $pageUrl ?? 'https://giheketss.ac.rw' . ($_SERVER['REQUEST_URI'] ?? '/');
$pageKeywords = $pageKeywords ?? 'GIHEKE TSS, Technical Secondary School, Rusizi, Rwanda, TVET, Software Development, Network and internet technology, Electrical Technology, Building Construction, Professional Accounting';

// Add site name to title if not already present
if (strpos($pageTitle, 'GIHEKE') === false) {
    $pageTitle .= ' | GIHEKE TSS';
}

// Clean URL for canonical
$canonicalUrl = strtok($pageUrl, '?');
$canonicalUrl = rtrim($canonicalUrl, '/');
?>
<!-- SEO Meta Tags -->
<title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="author" content="GIHEKE Technical Secondary School">
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo htmlspecialchars($pageUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($pageImage, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="GIHEKE TSS">
<meta property="og:locale" content="en_RW">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="<?php echo htmlspecialchars($pageUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($pageImage, ENT_QUOTES, 'UTF-8'); ?>">

<!-- Structured Data (JSON-LD) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "EducationalOrganization",
  "name": "GIHEKE Technical Secondary School",
  "description": "Technical secondary school offering 7 trade programs in Rusizi District, Rwanda",
  "url": "https://giheketss.ac.rw",
  "logo": "https://giheketss.ac.rw/img/giheke logo.webp",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Giheke Sector",
    "addressLocality": "Rusizi District",
    "addressCountry": "RW"
  },
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+250-788-876-460",
    "contactType": "admissions",
    "email": "giheketss@gmail.com"
  },
  "sameAs": [
    "https://twitter.com/giheketss",
    "https://facebook.com/giheketss",
    "https://instagram.com/giheketss",
    "https://linkedin.com/company/giheketss"
  ]
}
</script>
