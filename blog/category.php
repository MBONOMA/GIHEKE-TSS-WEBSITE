<?php
session_start();
include('../admin/includes/connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="GIHEKE TSS School Blog - Category Posts">
    <meta name="author" content="GIHEKE TSS">
    <title>Category Posts - GIHEKE TSS</title>

    <link href="../img/giheke logo.webp" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="../assets/haip-theme.css" rel="stylesheet">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <style>
        .category-posts { margin-top: 40px; }
    </style>
</head>
<body>

<?php include '../includes/haip-header.php'; ?>

<!-- CATEGORY HERO -->
<?php
$cat_name = '';
if(isset($_SESSION['catid'])) {
    $cat_query = mysqli_query($conn, "SELECT CategoryName FROM tbl_school_category WHERE id='".$_SESSION['catid']."'");
    $cat_row = mysqli_fetch_assoc($cat_query);
    $cat_name = $cat_row['CategoryName'] ?? 'Category';
}
?>
<section class="hero-haip" style="padding: 80px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);">
    <div class="container-haip">
        <div style="text-align: center; max-width: 800px; margin: 0 auto;">
            <span class="section-label" style="background: var(--accent-gold); color: var(--primary-dark);"><?php echo htmlspecialchars($cat_name); ?></span>
            <h1 style="color: #fff; font-size: 2.5rem; font-weight: 800; margin: 16px 0 12px;">Category Posts</h1>
            <p style="color: rgba(255,255,255,0.9); font-size: 1.15rem;">Browse all posts in this category</p>
        </div>
    </div>
</section>

<!-- CATEGORY POSTS -->
<section class="section-haip">
    <div class="container-haip">
        <div class="row" style="margin-top: 40px;">
            <!-- Categories Sidebar -->
            <div class="col-lg-3">
                <div class="card-haip" style="padding: 24px;">
                    <h5 style="font-weight: 700; color: var(--primary-dark); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">Categories</h5>
                    <ul class="list-unstyled mb-0">
                        <?php 
                        $query = mysqli_query($conn, "SELECT id, CategoryName FROM tbl_school_category");
                        while($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <li class="mb-2">
                            <a href="category.php?catid=<?php echo htmlspecialchars($row['id']); ?>" class="text-decoration-none d-block p-2 rounded" style="color: var(--text-dark); transition: all 0.2s; border-radius: var(--radius-sm);" onmouseover="this.style.background='var(--bg-light)'" onmouseout="this.style.background='transparent'">
                                <i class="bi bi-chevron-right me-2" style="color: var(--primary);"></i>
                                <?php echo htmlspecialchars($row['CategoryName']); ?>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <!-- Blog Posts -->
            <div class="col-lg-9">
                <div class="section-header" style="margin-bottom: 24px;">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-dark);"><?php echo htmlspecialchars($cat_name); ?> Posts</h3>
                </div>

                <?php
                if(isset($_GET['catid']) && $_GET['catid'] != '') {
                    $_SESSION['catid'] = intval($_GET['catid']);
                }

                if (isset($_GET['pageno'])) {
                    $pageno = $_GET['pageno'];
                } else {
                    $pageno = 1;
                }
                $no_of_records_per_page = 8;
                $offset = ($pageno-1) * $no_of_records_per_page;

                $total_pages_sql = "SELECT COUNT(*) FROM tblposts WHERE CategoryId='".$_SESSION['catid']."' AND Is_Active=1";
                $result = mysqli_query($conn, $total_pages_sql);
                $total_rows = mysqli_fetch_array($result)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);

                $query = mysqli_query($conn, "SELECT tblposts.id as pid, tblposts.PostTitle as posttitle, tblposts.PostImage, tblposts.PostDetails, tblposts.PostingDate, tblposts.PostUrl, tbl_school_category.CategoryName as category, tbl_school_category.id as cid FROM tblposts LEFT JOIN tbl_school_category ON tbl_school_category.id=tblposts.CategoryId WHERE tblposts.CategoryId='".$_SESSION['catid']."' AND tblposts.Is_Active=1 ORDER BY tblposts.id DESC LIMIT $offset, $no_of_records_per_page");

                $rowcount = mysqli_num_rows($query);
                if($rowcount == 0) {
                    echo '<div class="text-center py-5" style="color: #666;"><i class="bi bi-folder2-open" style="font-size: 3rem; display: block; margin-bottom: 16px; opacity: 0.4;"></i><p>No posts found in this category yet.</p></div>';
                } else {
                    while ($row = mysqli_fetch_assoc($query)) {
                ?>
                <div class="card-haip" style="overflow: hidden; border-radius: var(--radius-lg); transition: transform 0.3s, box-shadow 0.3s; margin-bottom: 24px;">
                    <img src="../admin/Blog Gallery/<?php echo htmlspecialchars($row['PostImage']); ?>" alt="<?php echo htmlspecialchars($row['posttitle']); ?>" style="width: 100%; height: 220px; object-fit: cover;">
                    <div class="card-haip-body">
                        <div style="display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap;">
                            <a href="category.php?catid=<?php echo htmlspecialchars($row['cid']); ?>" class="badge" style="background: var(--primary); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; text-decoration: none;"><?php echo htmlspecialchars($row['category']); ?></a>
                        </div>
                        <small style="color: #999;">Posted on <?php echo date('M d, Y', strtotime($row['PostingDate'])); ?></small>
                        <h4 style="margin: 12px 0; font-weight: 700; color: var(--primary-dark); line-height: 1.4; font-size: 1.1rem;"><?php echo htmlspecialchars($row['posttitle']); ?></h4>
                        <a href="news-details.php?nid=<?php echo htmlspecialchars($row['pid']); ?>" class="btn-haip btn-haip-primary" style="font-size: 0.85rem; padding: 8px 16px;">Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <?php 
                    }
                }
                ?>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Category pagination" style="margin-top: 40px;">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php if($pageno <= 1){ echo 'disabled'; } ?>">
                            <a class="page-link" href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1)."&catid=".$_SESSION['catid']; } ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if($pageno == $i){ echo 'active'; } ?>">
                            <a class="page-link" href="?pageno=<?php echo $i; ?>&catid=<?php echo $_SESSION['catid']; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        <li class="page-item <?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                            <a class="page-link" href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1)."&catid=".$_SESSION['catid']; } ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/haip-footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/owl.carousel.min.js"></script>

</body>
</html>
