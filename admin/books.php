<?php
session_start();
include('includes/connection.php');
error_reporting(E_ERROR | E_PARSE);
if(!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
}

$msg = '';
$upload_dir = 'uploads/books/';
$image_dir = 'uploads/featured/';

if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
if (!is_dir($image_dir)) mkdir($image_dir, 0755, true);

function handle_file_upload($file, $existing_path = '') {
    global $upload_dir;
    if ($file['error'] !== UPLOAD_ERR_OK) return ['error' => 'Upload error'];
    $max_size = 20 * 1024 * 1024;
    if ($file['size'] > $max_size) return ['error' => 'File exceeds 20MB'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') return ['error' => 'Only PDF files allowed'];
    $unique_name = uniqid('book_', true) . '.' . $ext;
    $dest_path = $upload_dir . $unique_name;
    if (!move_uploaded_file($file['tmp_name'], $dest_path)) return ['error' => 'Upload failed'];
    if (!empty($existing_path) && file_exists($existing_path)) unlink($existing_path);
    return ['path' => $dest_path, 'type' => $ext];
}

function handle_image_upload($file, $existing_path = '') {
    global $image_dir;
    if ($file['error'] !== UPLOAD_ERR_OK) return ['error' => 'Upload error'];
    $max_size = 5 * 1024 * 1024;
    if ($file['size'] > $max_size) return ['error' => 'Image exceeds 5MB'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif'])) return ['error' => 'Invalid image format'];
    $unique_name = uniqid('featured_', true) . '.' . $ext;
    $dest_path = $image_dir . $unique_name;
    if (!move_uploaded_file($file['tmp_name'], $dest_path)) return ['error' => 'Upload failed'];
    if (!empty($existing_path) && file_exists($existing_path)) unlink($existing_path);
    return ['path' => $dest_path, 'type' => $ext];
}

if(isset($_POST['add_book'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $file_result = handle_file_upload($_FILES['book_file']);
    if (isset($file_result['error'])) {
        $msg = $file_result['error'];
    } else {
        $file_path = mysqli_real_escape_string($conn, $file_result['path']);
        $file_type = mysqli_real_escape_string($conn, $file_result['type']);

        $img_result = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $img_result = handle_image_upload($_FILES['featured_image']);
            if (isset($img_result['error'])) {
                if (file_exists($file_result['path'])) unlink($file_result['path']);
                $msg = $img_result['error'];
            }
        }

        if (empty($msg)) {
            $featured = $img_result ? mysqli_real_escape_string($conn, $img_result['path']) : '';
            $featured_type = $img_result ? mysqli_real_escape_string($conn, $img_result['type']) : '';
            $sql = "INSERT INTO tbl_books (title, category, level, department, file_path, file_type, featured_image, featured_image_type, description) VALUES ('$title','$category','$level','$department','$file_path','$file_type','$featured','$featured_type','$description')";
            if(mysqli_query($conn, $sql)) $msg = "Book added successfully.";
            else $msg = "Error: " . mysqli_error($conn);
        }
    }
}

if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = mysqli_query($conn, "SELECT file_path, featured_image FROM tbl_books WHERE id='$id'");
    $row = mysqli_fetch_assoc($res);
    if ($row) {
        if (!empty($row['file_path']) && file_exists($row['file_path'])) unlink($row['file_path']);
        if (!empty($row['featured_image']) && file_exists($row['featured_image'])) unlink($row['featured_image']);
    }
    mysqli_query($conn, "DELETE FROM tbl_books WHERE id='$id'");
    $msg = "Book deleted successfully.";
}

if(isset($_POST['update_book'])) {
    $id = intval($_POST['id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $file_result = null;
    if (isset($_FILES['book_file']) && $_FILES['book_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $res = mysqli_query($conn, "SELECT file_path FROM tbl_books WHERE id='$id'");
        $existing = mysqli_fetch_assoc($res);
        $file_result = handle_file_upload($_FILES['book_file'], $existing['file_path'] ?? '');
    }

    $img_result = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $res = mysqli_query($conn, "SELECT featured_image FROM tbl_books WHERE id='$id'");
        $existing = mysqli_fetch_assoc($res);
        $img_result = handle_image_upload($_FILES['featured_image'], $existing['featured_image'] ?? '');
    }

    $file_path = $file_result ? mysqli_real_escape_string($conn, $file_result['path']) : '';
    $file_type = $file_result ? mysqli_real_escape_string($conn, $file_result['type']) : '';
    $featured = $img_result ? mysqli_real_escape_string($conn, $img_result['path']) : '';
    $featured_type = $img_result ? mysqli_real_escape_string($conn, $img_result['type']) : '';

    $sql = "UPDATE tbl_books SET title='$title', category='$category', level='$level', department='$department', description='$description'";
    if ($file_result) $sql .= ", file_path='$file_path', file_type='$file_type'";
    if ($img_result) $sql .= ", featured_image='$featured', featured_image_type='$featured_type'";
    $sql .= " WHERE id='$id'";

    if(mysqli_query($conn, $sql)) $msg = "Book updated successfully.";
    else $msg = "Error: " . mysqli_error($conn);
}

$books = mysqli_query($conn, "SELECT * FROM tbl_books ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Management - GIHEKE TSS</title>
    <link href="../img/giheke logo.webp" rel="icon">
    <link href="../img/giheke logo.webp" rel="apple-touch-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
    
</head>
<body>
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between" style="width:100%;">
            <a href="index.php" class="logo d-flex align-items-center"><img src="assets/img/logo.png" style="height:38px;border-radius:8px;"><span class="d-none d-lg-block" style="font-weight:800;color:#0F172A;font-size:1.1rem;">GIHEKE <span style="color:#525FE1;">Admin</span></span></a>
            <i class="bi bi-list toggle-sidebar-btn" id="sidebarToggle"></i>
        </div>
    </header>
<?php include('includes/sidebar.php'); ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-book me-2" style="color:#525FE1;"></i>Books & Past Papers Management</h1>
            <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Books</li></ol></nav>
        </div>
        <section class="section"><div class="admin-content">
            <?php if($msg): ?>
                <div class="alert-modern alert-<?php echo strpos($msg,'Error')!==false?'danger':'success'; ?>"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="admin-section-card">
                        <div class="admin-section-header"><h3><i class="bi bi-plus-circle me-2" style="color:#525FE1;"></i>Add New Book</h3></div>
                        <form method="post" enctype="multipart/form-data" class="p-3">
                            <div class="mb-3"><label class="form-label">Title *</label><input type="text" name="title" class="form-modern" required></div>
                            <div class="mb-3"><label class="form-label">Category *</label>
                                <select name="category" class="form-modern" required>
                                    <option value="Book">Book</option><option value="Past Paper">Past Paper</option>
                                </select>
                            </div>
                            <div class="mb-3"><label class="form-label">Level *</label>
                                <select name="level" class="form-modern" required>
                                    <option value="Level 3">Level 3</option><option value="Level 4">Level 4</option><option value="Level 5">Level 5</option>
                                </select>
                            </div>
                            <div class="mb-3"><label class="form-label">Department *</label>
                                <select name="department" class="form-modern" required>
                                    <option value="Software Development">Software Development</option>
                                    <option value="Network and internet technology">Network and internet technology</option>
                                    <option value="Comp Systems &amp; Architecture">Comp Systems &amp; Architecture</option>
                                    <option value="Electrical Technology">Electrical Technology</option>
                                    <option value="Electronics and Telecommunication Services">Electronics and Telecommunication Services</option>
                                    <option value="Building Construction">Building Construction</option>
                                    <option value="Professional Accounting">Professional Accounting</option>
                                </select>
                            </div>
                            <div class="mb-3"><label class="form-label">PDF File * (max 20MB)</label><input type="file" name="book_file" accept=".pdf" required class="form-modern" style="padding:8px;"></div>
                            <div class="mb-3"><label class="form-label">Featured Image (YouTube-style thumbnail)</label><input type="file" name="featured_image" accept=".jpg,.jpeg,.png,.gif" class="form-modern" style="padding:8px;"></div>
                            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-modern" rows="3"></textarea></div>
                            <button type="submit" name="add_book" class="btn-modern btn-modern-primary w-100"><i class="bi bi-plus-lg"></i> Add Book</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="admin-section-card">
                        <div class="admin-section-header"><h3><i class="bi bi-journal-text me-2" style="color:#525FE1;"></i>Books List</h3></div>
                        <div class="table-responsive-custom" style="padding:0 20px 20px;">
                            <table class="table-modern" id="booksTable">
                                <thead><tr>
                                    <th>#</th><th>Title</th><th>Category</th><th>Level</th><th>Department</th><th>Featured</th><th>Actions</th>
                                </tr></thead>
                                <tbody>
                                    <?php $count = 1; while($b = mysqli_fetch_assoc($books)): ?>
                                    <tr>
                                        <td><?php echo $count++; ?></td>
                                        <td><?php echo htmlspecialchars($b['title']); ?></td>
                                        <td><span class="badge-modern" style="background:<?php echo $b['category']=='Past Paper'?'#fff3e0':'#e8f5e9'; ?>;color:<?php echo $b['category']=='Past Paper'?'#e65100':'#2e7d32'; ?>;"><?php echo htmlspecialchars($b['category']); ?></span></td>
                                        <td><?php echo htmlspecialchars($b['level']); ?></td>
                                        <td><?php echo htmlspecialchars($b['department']); ?></td>
                                        <td><?php echo $b['featured_image'] ? '<i class="bi bi-image-fill" style="color:#525FE1;"></i>' : '-'; ?></td>
                                        <td>
                                            <a href="?edit=<?php echo $b['id']; ?>" class="btn-action btn-edit-modern btn-sm"><i class="bi bi-pencil"></i></a>
                                            <a href="?delete=<?php echo $b['id']; ?>" onclick="return confirm('Delete this book?')" class="btn-action btn-delete-modern btn-sm"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if(mysqli_num_rows($books) == 0): ?>
                                    <tr><td colspan="7" style="padding:40px;text-align:center;color:#64748B;">No books added yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <?php if(isset($_GET['edit'])): 
                        $edit_id = intval($_GET['edit']);
                        $edit_res = mysqli_query($conn, "SELECT * FROM tbl_books WHERE id='$edit_id'");
                        $edit_book = mysqli_fetch_assoc($edit_res);
                        if($edit_book): ?>
                    <div class="admin-section-card" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;">
                        <div style="max-width:600px;width:100%;background:#fff;border-radius:14px;padding:32px;max-height:90vh;overflow-y:auto;">
                            <h3 style="font-weight:700;color:#0F172A;margin-bottom:20px;"><i class="bi bi-pencil-square me-2"></i>Edit Book</h3>
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $edit_book['id']; ?>">
                                <div class="row g-3">
                                    <div class="col-md-6"><div class="mb-3"><label>Title</label><input type="text" name="title" class="form-modern" value="<?php echo htmlspecialchars($edit_book['title']); ?>" required></div></div>
                                    <div class="col-md-6"><div class="mb-3"><label>Category</label>
                                        <select name="category" class="form-modern">
                                            <option value="Book" <?php echo $edit_book['category']=='Book'?'selected':''; ?>>Book</option>
                                            <option value="Past Paper" <?php echo $edit_book['category']=='Past Paper'?'selected':''; ?>>Past Paper</option>
                                        </select>
                                    </div></div>
                                    <div class="col-md-6"><div class="mb-3"><label>Level</label>
                                        <select name="level" class="form-modern">
                                            <option value="Level 3" <?php echo $edit_book['level']=='Level 3'?'selected':''; ?>>Level 3</option>
                                            <option value="Level 4" <?php echo $edit_book['level']=='Level 4'?'selected':''; ?>>Level 4</option>
                                            <option value="Level 5" <?php echo $edit_book['level']=='Level 5'?'selected':''; ?>>Level 5</option>
                                        </select>
                                    </div></div>
                                    <div class="col-12"><div class="mb-3"><label>PDF File (leave empty to keep current)</label><input type="file" name="book_file" class="form-modern" style="padding:8px;"></div></div>
                                    <div class="col-12"><div class="mb-3"><label>Featured Image (YouTube-style)</label><input type="file" name="featured_image" class="form-modern" style="padding:8px;"><?php if($edit_book['featured_image']): ?><br><small>Current: <?php echo htmlspecialchars(basename($edit_book['featured_image'])); ?></small><?php endif; ?></div></div>
                                    <div class="col-12"><div class="mb-3"><label>Description</label><textarea name="description" class="form-modern" rows="3"><?php echo htmlspecialchars($edit_book['description']); ?></textarea></div></div>
                                </div>
                                <div class="text-end mt-3"><a href="books.php" class="btn-modern btn-modern-outline me-2">Cancel</a><button type="submit" name="update_book" class="btn-modern btn-modern-primary"><i class="bi bi-check-lg"></i> Update</button></div>
                            </form>
                        </div>
                    </div>
                    <?php endif; endif; ?>
                </div>
            </div>
        </div></section>
    </main>
    <footer class="admin-footer">&copy; <?php echo date('Y'); ?> GIHEKE TSS. All Rights Reserved. Developed by <a href="https://devoma.vercel.app">Omar MBONABUCYA</a></footer>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('main');
            const toggleBtn = document.getElementById('sidebarToggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('toggled');
                    if (sidebar.classList.contains('toggled')) main.classList.add('full-width');
                    else main.classList.remove('full-width');
                });
            }
        })();
    </script>
</body>
</html>