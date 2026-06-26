<?php
session_start();
include('../admin/includes/connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Search GIHEKE TSS School Blog">
    <meta name="author" content="GIHEKE TSS">
    <title>Search Results - GIHEKE TSS</title>

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
        .search-results { margin-top: 40px; }
        .search-form-box { padding: 32px; }
    </style>
</head>
<body>

<?php include '../includes/haip-header.php'; ?>

<!-- SEARCH HERO -->
<section class="hero-haip" style="padding: 80px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);">
    <div class="container-haip">
        <div style="text-align: center; max-width: 800px; margin: 0 auto;">
            <span class="section-label" style="background: var(--accent-gold); color: var(--primary-dark);">Search Results</span>
            <h1 style="color: #fff; font-size: 2.5rem; font-weight: 800; margin: 16px 0 12px;">Search</h1>
            <p style="color: rgba(255,255,255,0.9); font-size: 1.15rem;">Find posts, news, and information</p>
        </div>
    </div>
</section>

<!-- SEARCH CONTENT -->
<section class="section-haip">
    <div class="container-haip">
        <div class="row" style="margin-top: 40px;">
            <!-- Search Form -->
            <div class="col-lg-8 mx-auto" style="margin-bottom: 40px;">
                <div class="card-haip" style="padding: 32px;">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 20px; text-align: center;">Search Blog Posts</h3>
                    <form action="search.php" method="post" class="d-flex gap-3" style="max-width: 600px; margin: 0 auto;">
                        <div style="flex: 1;">
                            <input type="text" name="searchtitle" class="form-control-haip" placeholder="Search for posts, news, events..." required style="font-size: 1rem; padding: 14px 20px;">
                        </div>
                        <button type="submit" class="btn-haip btn-haip-primary" style="padding: 14px 32px; font-size: 1rem; white-space: nowrap;">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </form>
                </div>
            </div>

            <!-- Search Results -->
            <div class="col-lg-8 mx-auto">
                <?php
                if($_POST['searchtitle'] != '') {
                    $_SESSION['searchtitle'] = $_POST['searchtitle'];
                }
                $st = $_SESSION['searchtitle'] ?? '';

                if (isset($_GET['pageno'])) {
                    $pageno = $_GET['pageno'];
                } else {
                    $pageno = 1;
                }
                $no_of_records_per_page = 8;
                $offset = ($pageno-1) * $no_of_records_per_page;

                $total_pages_sql = "SELECT COUNT(*) FROM tblposts WHERE PostTitle LIKE '%$st%' AND Is_Active=1";
                $result = mysqli_query($conn, $total_pages_sql);
                $total_rows = mysqli_fetch_array($result)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);

                $query = mysqli_query($conn, "SELECT tblposts.id as pid, tblposts.PostTitle as posttitle, tbl_school_category.CategoryName as category, tblposts.PostDetails as postdetails, tblposts.PostingDate as postingdate, tblposts.PostUrl as url, tblposts.PostImage FROM tblposts LEFT JOIN tbl_school_category ON tbl_school_category.id=tblposts.CategoryId WHERE tblposts.PostTitle LIKE '%$st%' AND tblposts.Is_Active=1 ORDER BY tblposts.id DESC LIMIT $offset, $no_of_records_per_page");

                $rowcount = mysqli_num_rows($query);
                
                if($rowcount == 0) {
                    echo '<div class="card-haip text-center py-5" style="background: var(--bg-light);"><i class="bi bi-search" style="font-size: 3rem; display: block; margin-bottom: 16px; opacity: 0.4; color: var(--primary);"></i><h4 style="color: var(--primary-dark); margin-bottom: 8px;">No results found</h4><p style="color: #666;">No posts found matching "'.htmlspecialchars($st).'". Try different keywords.</p></div>';
                } else {
                    echo '<div class="section-header" style="margin-bottom: 24px;"><h3 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-dark);">Results for "'.htmlspecialchars($st).'"</h3></div>';
                    while ($row = mysqli_fetch_assoc($query)) {
                ?>
                <div class="card-haip" style="overflow: hidden; border-radius: var(--radius-lg); transition: transform 0.3s, box-shadow 0.3s; margin-bottom: 24px;">
                    <img src="../admin/Blog Gallery/<?php echo htmlspecialchars($row['PostImage']); ?>" alt="<?php echo htmlspecialchars($row['posttitle']); ?>" style="width: 100%; height: 220px; object-fit: cover;">
                    <div class="card-haip-body">
                        <div style="display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap;">
                            <span class="badge" style="background: var(--primary); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem;"><?php echo htmlspecialchars($row['category']); ?></span>
                        </div>
                        <small style="color: #999;">Posted on <?php echo date('M d, Y', strtotime($row['PostingDate'])); ?></small>
                        <h4 style="margin: 12px 0; font-weight: 700; color: var(--primary-dark); line-height: 1.4; font-size: 1.1rem;"><?php echo htmlspecialchars($row['posttitle']); ?></h4>
                        <a href="news-details.php?nid=<?php echo htmlspecialchars($row['pid']); ?>" class="btn-haip btn-haip-primary" style="font-size: 0.85rem; padding: 8px 16px;">Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <?php 
                    }
                }

                // Pagination
                if ($total_pages > 1) {
                    echo '<nav aria-label="Search pagination" style="margin-top: 40px;">
                    <ul class="pagination justify-content-center">';
                    echo '<li class="page-item '.($pageno <= 1 ? 'disabled' : '').'"><a class="page-link" href="'.($pageno <= 1 ? '#' : "?pageno=".($pageno - 1)).'">Previous</a></li>';
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo '<li class="page-item '.($pageno == $i ? 'active' : '').'"><a class="page-link" href="?pageno='.$i.'">'.$i.'</a></li>';
                    }
                    echo '<li class="page-item '.($pageno >= $total_pages ? 'disabled' : '').'"><a class="page-link" href="'.($pageno >= $total_pages ? '#' : "?pageno=".($pageno + 1)).'">Next</a></li>';
                    echo '</ul></nav>';
                }
                ?>
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
