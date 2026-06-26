# GIHEKE TSS Website — Administrator & User Training Guide

**Version:** 1.0  
**Last Updated:** June 2026  
**Website:** http://localhost/Giheke (Local)  
**Target Presentation Date:** June 7, 2026

---

## Table of Contents
1. [System Overview](#system-overview)
2. [User Roles & Access](#user-roles--access)
3. [Public Website Features](#public-website-features)
4. [Admin Panel Guide](#admin-panel-guide)
5. [Trainer Panel Guide](#trainer-panel-guide)
6. [Student Portal](#student-portal)
7. [E-Learning System](#e-learning-system)
8. [Troubleshooting](#troubleshooting)
9. [Security Best Practices](#security-best-practices)

---

## 1. System Overview

GIHEKE Technical Secondary School's website is a comprehensive platform serving four user types:

- **Public Visitors** — Browse programs, apply for admission, view news & gallery
- **Students** — Access books, past papers, quizzes, and track marks
- **Trainers (Teachers)** — Manage modules, quizzes, books, and student marks
- **Administrators** — Full control: manage students, trainers, content, applications

**Technology Stack:**
- Backend: PHP 7.4+ with MySQL (giheke_tss_db)
- Frontend: Bootstrap 5.3, Custom CSS (haip-theme.css)
- Admin Theme: Modern 2027 design (admin/assets/css/admin-2027-theme.css)
- Browser: Playwright automated testing enabled
- Email: PHPMailer (SMTP via Gmail)

---

## 2. User Roles & Access

| Role | Access URL | Credentials |
|------|-----------|-------------|
| Public | http://localhost/Giheke | No login required |
| Student | /Student Task/student.php | School-issued credentials |
| Trainer | /trainer/login.php | School-issued credentials |
| Admin | /admin/login.php | Admin-issued credentials |

---

## 3. Public Website Features

### 3.1 Homepage (`index.php`)
- **Announcement Bar** — Editable by admin, displays scrolling announcements
- **Hero Section** — School introduction with stats (700+ students, 35 teachers, 7 trades)
- **About Us** — School history and values
- **Programs** — 7 trade cards (clickable, navigates to #team)
- **Features** — Why Choose GIHEKE
- **Stats Counter** — Animated counters (requires JavaScript)
- **News Section** — Latest 6 blog posts
- **Contact Section** — Embedded Google Maps + contact form

### 3.2 Gallery (`gallery.php`)
- Grid display of school images
- Lightbox functionality (click to enlarge)
- Navigation through image carousel

### 3.3 Blog (`blog/blog.php`)
- Category-based blog posts
- Search functionality
- View counter per article
- Comment system (moderated)
- Featured posts slider

### 3.4 Student Application (`SchoolApplication.php`)
- Multi-step form (3 steps)
- File upload for school report (PDF only)
- Fields: Personal info, academic history, message
- Status tracking via admin approval

### 3.5 E-Learning (`elearning.php`)
- Books & Past Papers library
- Filter by type (Book / Past Paper)
- Filter by trade (7 trades available)
- Combined filtering (e.g., "Book" + "Software Development")
- Download/Open links

---

## 4. Admin Panel Guide

**Access:** http://localhost/Giheke/admin/login.php  
**Theme:** Modern 2027 Clean Professional (slate/indigo/white palette)

### 4.1 Dashboard (`admin/index.php`)
Features:
- **8 Quick-Action Cards**: Gallery, Blog, Books, Quiz, Messages, Applications, Students, Trainers
- **Recent Activity Table** — Shows latest admin actions
- **Sidebar Navigation** — Collapsible (click hamburger icon)
- **Back to Top** button

### 4.2 Key Admin Functions

#### Manage Students (`admin/manage-students.php`)
- View all registered students
- Approve/reject student applications
- Add new students
- Update student records
- Delete students

**Security Note:** Approved student actions trigger email via PHPMailer. SMTP credentials must be set in environment variable `GIHEKE_SMTP_PASS`.

#### Manage Books (`admin/books.php`, `admin/add-book.php`, `admin/update-books.php`, `admin/delete-books.php`)
- Upload textbooks and past papers
- Assign to trade/department
- Set level (L3, L4, L5)
- Categorize as "Book" or "Past Paper"
- Attach PDF/file links

#### Manage Blog Posts (`admin/manage-blogpost.php`, `admin/add-blogpost.php`)
- Create/edit/delete blog articles
- Assign categories
- Upload featured images
- Set active/inactive status
- Supports WYSIWYG editor (TinyMCE)

#### Manage Quiz (`admin/manage-quiz.php`, `admin/add-quiz_question.php`)
- Create quiz questions
- Assign to level and trade
- Multiple choice format
- Set correct answers

**Note:** Quiz files are auto-generated per level/trade (e.g., `QuizL3sod.php`). This is a legacy structure — plan to consolidate into parameterized routes.

#### Manage Gallery (`admin/manage-gallerypost.php`, `admin/add-gallerypost.php`)
- Upload images to school gallery
- Set as active/inactive
- Images stored in `admin/Gallery Images/`

#### Manage Trainers (`admin/manage-trainers.php`)
- Add/edit/delete trainer accounts
- Assign trainer to trades

#### Manage Modules (`admin/manage-modules.php`, `admin/add-modules.php`)
- Create training modules
- Attach to trades/levels

#### Announcements (`admin/announce.php`)
- Edit homepage scrolling announcement
- Updates immediately on save

#### Student Messages (`admin/studentMessage.php`)
- View contact form submissions
- Mark as read/respond

#### Change Password (`admin/changePassword.php`)
- Admin can update own password
- **Important:** Passwords are stored in plaintext — hash before saving!

---

## 5. Trainer Panel Guide

**Access:** http://localhost/Giheke/trainer/login.php

### 5.1 Trainer Functions
- **Manage Students** — View assigned students
- **Manage Modules** — Create/edit training modules
- **Manage Books** — Upload trade-specific books/past papers
- **Manage Quiz** — Create and manage quiz questions
- **Marks Management** — Enter and update student marks

### 5.2 Trainer Sidebar Navigation
- Dashboard
- School Library (nested by Level → Trade)
- Student Management
- Quiz Management
- Module Management
- Marks Entry

---

## 6. Student Portal

**Access:** http://localhost/Giheke/student-login.php  
**Main Dashboard:** /Student Task/student.php

### 6.1 Student Features
- **Books by Trade** — Access trade-specific textbooks
- **Past Papers** — National exam papers by level/trade
- **Quizzes** — Interactive quizzes per module
- **Marks** — View exam results and progress
- **Profile** — Update personal information

### 6.2 File Structure
Books and quizzes are organized by:
```
/Student Task/
├── BooksL3sod.php    (Level 3, Software Development)
├── BooksL4net.php    (Level 4, Computer Networks)
├── BooksL5elc.php    (Level 5, Electrical Technology)
├── QuizL3sod.php     (Level 3, Software Dev Quiz)
├── QuizL4net.php     (Level 4, Networks Quiz)
└── ...
```

---

## 7. E-Learning System

### 7.1 Current Features
- **Dual Filtering:** Users can filter by "Book/Past Paper" AND by trade simultaneously
- **7 Trade Categories:**
  1. Software Development
  2. Computer Networks
  3. Computer Systems and Architecture
  4. Electrical Technology
  5. Electronics and Telecommunication Services
  6. Building Construction
  7. Professional Accounting

### 7.2 Adding New Books/Papers
1. Login to Admin Panel
2. Go to **Books** → **Add Book**
3. Fill in:
   - Title
   - Trade/Department (select from dropdown)
   - Level (L3, L4, L5)
   - Category (Book or Past Paper)
   - File path/URL (upload to server and link)
4. Save — book will automatically appear in E-Learning page with correct filters

---

## 8. Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| "Swiper is not defined" error | Swiper library loads but element not on page — safe to ignore, fixed in v2. |
| jQuery 404 on elearning.php | Fixed — now uses CDN. If occurs, check `elearning.php` line 190. |
| SMTP email not sending | Ensure `GIHEKE_SMTP_PASS` environment variable is set, or SMTP credentials added to `Approved.php`. |
| Images not loading on blog | Check relative paths — `../img/` vs `img/`. Blog pages now use dynamic base_url. |
| Design broken on admin | Clear browser cache. New theme is in `admin/assets/css/admin-2027-theme.css`. |
| Login fails with correct credentials | Check `tbl_admins` or `tbl_trainers` table — passwords are plaintext (legacy). |

### Database Connection Issues
If site shows "Database connection failed":
1. Verify XAMPP/MySQL is running
2. Check `admin/includes/connection.php`:
   - Host: `localhost`
   - User: `root`
   - Password: `` (empty for XAMPP default)
   - Database: `giheke_tss_db`

---

## 9. Security Best Practices

### Immediate Actions Required

1. **Hash All Passwords**
   ```php
   // When creating/updating admin/trainer/student passwords:
   $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
   ```
   Apply to:
   - `admin/add-student.php`
   - `admin/add-trainers.php`
   - `admin/changePassword.php`
   - `trainer/change-image.php`
   - Login verification scripts

2. **Add CSRF Tokens**
   ```php
   // At top of form pages:
   session_start();
   if (empty($_SESSION['csrf_token'])) {
       $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
   }
   ```
   Add hidden field: `<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">`

3. **Restrict Google Maps API Key**
   - Go to Google Cloud Console
   - Restrict key to: `localhost/Giheke/*` and your production domain
   - Enable only Maps JavaScript API and Maps Embed API

4. **Initialize Git Repository**
   ```bash
   cd C:\xampp\htdocs\Giheke
   git init
   git add .
   git commit -m "Initial commit: GIHEKE TSS website with 2027 admin theme"
   ```

5. **Create .env File**
   Move credentials out of PHP files:
   ```
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=
   DB_NAME=giheke_tss_db
   SMTP_USER=giheketss@gmail.com
   SMTP_PASS=your_app_password_here
   GOOGLE_MAPS_KEY=AIzaSy...
   ```

---

## 10. Quick Reference — File Locations

| Feature | Main File(s) |
|---------|--------------|
| Homepage | `index.php` |
| Gallery | `gallery.php` |
| Blog | `blog/blog.php` |
| E-Learning | `elearning.php` |
| School Application | `SchoolApplication.php` |
| Admin Dashboard | `admin/index.php` |
| Admin Login | `admin/login.php` |
| Trainer Login | `trainer/login.php` |
| Student Login | `student-login.php` |
| Header/Nav | `includes/haip-header.php` |
| Footer | `includes/haip-footer.php` |
| Student Sidebar | `includes/sidebar.php` |
| Admin Theme | `admin/assets/css/admin-2027-theme.css` |
| Public Theme | `assets/haip-theme.css` |
| SEO Meta | `assets/seo-meta.php` |
| Database | Connection in each module's `includes/connection.php` |

---

## 11. Maintenance Checklist

- [ ] Weekly: Check student applications and approve/reject
- [ ] Weekly: Review and respond to student messages
- [ ] Monthly: Upload new blog posts and news
- [ ] Monthly: Add new books/past papers to library
- [ ] Monthly: Update quiz questions for current modules
- [ ] Monthly: Review gallery and add new photos
- [ ] Quarterly: Update announcement bar text
- [ ] Before exams: Upload latest past papers

---

## Presentation Talking Points

1. **Security**: We've patched SQL injection vulnerabilities and externalized SMTP credentials
2. **Performance**: Eliminated 404 errors, optimized asset loading, added CDN fallbacks
3. **User Experience**: Implemented dual-axis filtering in E-Learning (type + trade)
4. **Professional Admin Panel**: New 2027 modern theme with collapsible sidebar
5. **Scalability**: Identified consolidation opportunities for 40+ quiz/book files
6. **SEO**: Added structured data, Open Graph, and Twitter Card support
7. **Training**: Created comprehensive user guide for admins, trainers, and students

---

*Document prepared for GIHEKE TSS website presentation on June 7, 2026.*  
*For technical support, contact the development team.*
