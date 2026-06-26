<div class="col-lg-4">
    <!-- Search Widget -->
    <div class="card-haip" style="margin-bottom: 24px; padding: 28px; border: 2px solid var(--primary);">
        <h5 style="font-weight: 700; color: var(--primary-dark); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid var(--primary); font-size: 1.1rem;"><i class="bi bi-search me-2" style="color:var(--primary);"></i>Search News</h5>
        <form action="search.php" method="post">
            <div class="input-group" style="display:flex;gap:8px;">
                <input type="text" name="searchtitle" class="form-control-haip" placeholder="Search for news..." required style="border-radius: var(--radius-sm); padding: 12px 16px; font-size:0.95rem; flex:1;">
                <button class="btn-haip btn-haip-primary" type="submit" style="border-radius: var(--radius-sm); padding: 12px 24px; font-weight:700;">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>

    <!-- Recent News -->
    <div class="card-haip" style="margin-bottom: 24px; padding: 28px;">
        <h5 style="font-weight: 700; color: var(--primary-dark); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid var(--primary); font-size: 1.1rem;"><i class="bi bi-clock-history me-2" style="color:var(--primary);"></i>Recent News</h5>
        <ul class="list-unstyled mb-0">
            <?php
            $query = mysqli_query($conn, "SELECT tblposts.id as pid, tblposts.PostImage, tblposts.PostTitle as posttitle FROM tblposts LEFT JOIN tbl_school_category ON tbl_school_category.id=tblposts.CategoryId WHERE tblposts.Is_Active=1 ORDER BY tblposts.id DESC LIMIT 5");
            while ($row = mysqli_fetch_assoc($query)) {
            ?>
            <li class="d-flex mb-3 align-items-start" style="padding:8px 0; border-bottom:1px solid #f5f5f5;">
                <img src="../admin/Blog Gallery/<?php echo htmlspecialchars($row['PostImage']); ?>" alt="<?php echo htmlspecialchars($row['posttitle']); ?>" width="80px" height="80px" style="object-fit: cover; border-radius: var(--radius-sm); flex-shrink: 0;" loading="lazy" decoding="async">
                <a href="news-details.php?nid=<?php echo htmlspecialchars($row['pid']); ?>" class="text-dark fw-semibold ms-3" style="line-height: 1.4; font-size: 0.95rem; display:block; padding-top:4px;"><?php echo htmlspecialchars($row['posttitle']); ?></a>
            </li>
            <?php } ?>
        </ul>
    </div>

    <!-- Popular News -->
    <div class="card-haip" style="margin-bottom: 24px; padding: 28px;">
        <h5 style="font-weight: 700; color: var(--primary-dark); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid var(--primary); font-size: 1.1rem;"><i class="bi bi-graph-up-arrow me-2" style="color:var(--primary);"></i>Popular News</h5>
        <ul class="list-unstyled mb-0">
            <?php
            $query1 = mysqli_query($conn, "SELECT tblposts.id as pid, tblposts.PostTitle as posttitle, viewCounter FROM tblposts LEFT JOIN tbl_school_category ON tbl_school_category.id=tblposts.CategoryId WHERE tblposts.Is_Active=1 ORDER BY viewCounter DESC LIMIT 5");
            while ($result = mysqli_fetch_assoc($query1)) {
            ?>
            <li class="mb-2" style="padding:10px 0; border-bottom:1px solid #f5f5f5;">
                <a href="news-details.php?nid=<?php echo htmlspecialchars($result['pid']); ?>" class="text-dark d-block" style="font-weight:600; line-height: 1.5; font-size: 0.95rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-dark)'"><?php echo htmlspecialchars($result['posttitle']); ?></a>
                <small style="color:#aaa; font-size:0.75rem;"><i class="bi bi-eye"></i> <?php echo $result['viewCounter']; ?> views</small>
            </li>
            <?php } ?>
        </ul>
    </div>

    <!-- YouTube Videos -->
    <div class="card-haip" style="margin-bottom: 24px; padding: 28px;">
        <h5 style="font-weight: 700; color: var(--primary-dark); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid var(--primary); font-size: 1.1rem;"><i class="bi bi-play-circle-fill me-2" style="color:#ff0000;"></i>Popular Videos</h5>
        <div class="ratio ratio-16x9 mb-3" style="border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.12);">
            <iframe src="https://www.youtube.com/embed/giMQn3KkK_Y" title="Benefits Of TVET School" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen loading="lazy"></iframe>
        </div>
        <div class="ratio ratio-16x9 mb-3" style="border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.12);">
            <iframe src="https://www.youtube.com/embed/KX6_wnjBopQ" title="Rwanda TVET Board Ads" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen loading="lazy"></iframe>
        </div>
        <div class="ratio ratio-16x9" style="border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.12);">
            <iframe src="https://www.youtube.com/embed/cRnq9Q9BLDc" title="Umuyobozi wa Rwanda TVET Board" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen loading="lazy"></iframe>
        </div>
    </div>
</div>
