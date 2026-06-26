<aside id="sidebar" class="sidebar">
    <div class="sidebar-brand" id="sidebarBrand">
      <img src="assets/img/logo.png" alt="Logo">
      <div class="brand-text">
        GIHEKE
        <small>Admin Panel</small>
      </div>
    </div>

    <ul class="sidebar-nav" id="sidebarNav">
      <li class="nav-item">
        <a class="nav-link" href="index.php">
          <i class="bi bi-grid-fill"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li><span class="sidebar-section-label">School Management</span></li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#attendance" data-bs-toggle="collapse" href="#">
          <i class="bi bi-folder2-open"></i>
          <span>School Category</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="attendance" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="add-category.php"><i class="bi bi-arrow-bar-right"></i><span>Add Category</span></a></li>
          <li><a href="manage-category.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Category</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#trainers" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people"></i>
          <span>School Trainers</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="trainers" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="add-trainers.php"><i class="bi bi-arrow-bar-right"></i><span>Add Trainer</span></a></li>
          <li><a href="manage-trainers.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Trainers</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#gallery" data-bs-toggle="collapse" href="#">
          <i class="bi bi-images"></i>
          <span>School Gallery</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="gallery" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="add-gallerypost.php"><i class="bi bi-arrow-bar-right"></i><span>Add Image</span></a></li>
          <li><a href="manage-gallerypost.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Gallery</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#blog" data-bs-toggle="collapse" href="#">
          <i class="bi bi-newspaper"></i>
          <span>School Blog</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="blog" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="add-blogpost.php"><i class="bi bi-arrow-bar-right"></i><span>Add News</span></a></li>
          <li><a href="manage-blogpost.php"><i class="bi bi-arrow-bar-right"></i><span>Manage News</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#parent" data-bs-toggle="collapse" href="#">
          <i class="bi bi-file-earmark-text"></i>
          <span>Parent Document</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="parent" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="parent-doc.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Document</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#announce" data-bs-toggle="collapse" href="#">
          <i class="bi bi-megaphone"></i>
          <span>Announcements</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="announce" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="announce.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Announcement</span></a></li>
        </ul>
      </li>

      <li><span class="sidebar-section-label">Academics</span></li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#applications" data-bs-toggle="collapse" href="#">
          <i class="bi bi-folder-check"></i>
          <span>Applications</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="applications" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="apply_status.php"><i class="bi bi-arrow-bar-right"></i><span>Set Application Status</span></a></li>
          <li><a href="studentApplication.php"><i class="bi bi-arrow-bar-right"></i><span>Applied Students</span></a></li>
          <li><a href="approved-students.php"><i class="bi bi-arrow-bar-right"></i><span>Approved</span></a></li>
          <li><a href="rejected-students.php"><i class="bi bi-arrow-bar-right"></i><span>Rejected</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#students" data-bs-toggle="collapse" href="#">
          <i class="bi bi-mortarboard"></i>
          <span>Students</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="students" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="add-student.php"><i class="bi bi-arrow-bar-right"></i><span>Add Student</span></a></li>
          <li><a href="manage-students.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Students</span></a></li>
        </ul>
      </li>



      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#elearning" data-bs-toggle="collapse" href="#">
          <i class="bi bi-book-open"></i>
          <span>E-Learning</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="elearning" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="elearning.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Materials</span></a></li>
        </ul>
      </li>

      <li><span class="sidebar-section-label">Site Management</span></li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#siteManage" data-bs-toggle="collapse" href="#">
          <i class="bi bi-gear"></i>
          <span>Site Management</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="siteManage" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="content-manager.php"><i class="bi bi-arrow-bar-right"></i><span>Content Manager</span></a></li>
          <li><a href="site-settings.php"><i class="bi bi-arrow-bar-right"></i><span>Site Settings</span></a></li>
          <li><a href="manage-trades.php"><i class="bi bi-arrow-bar-right"></i><span>Trade Programs</span></a></li>
          <li><a href="manage-staff.php"><i class="bi bi-arrow-bar-right"></i><span>Staff Members</span></a></li>
          <li><a href="manage-facilities.php"><i class="bi bi-arrow-bar-right"></i><span>Facilities</span></a></li>
          <li><a href="manage-features.php"><i class="bi bi-arrow-bar-right"></i><span>Features</span></a></li>
          <li><a href="manage-values.php"><i class="bi bi-arrow-bar-right"></i><span>Core Values</span></a></li>
          <li><a href="media-library.php"><i class="bi bi-arrow-bar-right"></i><span>Media Library</span></a></li>
          <li><a href="navigation-manager.php"><i class="bi bi-arrow-bar-right"></i><span>Navigation</span></a></li>
          <li><a href="seo-manager.php"><i class="bi bi-arrow-bar-right"></i><span>SEO Manager</span></a></li>
          <li><a href="studentApplication.php"><i class="bi bi-arrow-bar-right"></i><span>Student Applications</span></a></li>
          <li><a href="announce.php"><i class="bi bi-arrow-bar-right"></i><span>Announcements</span></a></li>
          <li><a href="activity-logs.php"><i class="bi bi-arrow-bar-right"></i><span>Activity Logs</span></a></li>
        </ul>
      </li>


      <li><span class="sidebar-section-label">System</span></li>

      <li class="nav-item">
        <a class="nav-link" href="schoolPassword.php">
          <i class="bi bi-key"></i>
          <span>Application Password</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="changePassword.php">
          <i class="bi bi-shield-lock"></i>
          <span>Change Password</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link btn-sidebar-action danger" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Logout</span>
        </a>
      </li>
    </ul>
  </aside>