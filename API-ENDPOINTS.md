# GIHEKE TSS - API Endpoints Reference

**Base URL:** `http://localhost:4000/api/v1`

---

## AUTHENTICATION

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/auth/login` | Public | Login with email & password |
| POST | `/auth/register` | Admin | Create new user |
| GET | `/auth/profile` | Auth | Get current user profile |
| POST | `/auth/change-password` | Auth | Change password |

---

## USERS

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/users` | Admin | List all users (paginated, searchable) |
| GET | `/users/:id` | Admin | Get user by ID |
| POST | `/users` | Admin | Create user |
| PATCH | `/users/:id` | Admin | Update user |
| DELETE | `/users/:id` | Admin | Delete user |

---

## SITE MANAGEMENT (CMS)

### Homepage Sections

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/site-management/homepage` | Public | Get all active homepage sections |
| GET | `/site-management/homepage/:id` | Public | Get single section |
| POST | `/site-management/homepage` | Admin | Create homepage section |
| PATCH | `/site-management/homepage/:id` | Admin | Update section |
| DELETE | `/site-management/homepage/:id` | Admin | Delete section |
| PATCH | `/site-management/homepage/reorder` | Admin | Reorder sections |

### About Page

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/site-management/about` | Public | Get all about page content |
| GET | `/site-management/about/:sectionKey` | Public | Get single section by key |
| POST | `/site-management/about` | Admin | Create about content |
| PATCH | `/site-management/about/:id` | Admin | Update about content |
| DELETE | `/site-management/about/:id` | Admin | Delete about content |

### Leaders / Achievements / Testimonials / Partners

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/site-management/leaders` | Public | List leaders |
| POST | `/site-management/leaders` | Admin | Create leader |
| PATCH | `/site-management/leaders/:id` | Admin | Update leader |
| DELETE | `/site-management/leaders/:id` | Admin | Delete leader |
| GET | `/site-management/achievements` | Public | List achievements |
| POST | `/site-management/achievements` | Admin | Create achievement |
| PATCH | `/site-management/achievements/:id` | Admin | Update achievement |
| DELETE | `/site-management/achievements/:id` | Admin | Delete achievement |
| GET | `/site-management/testimonials` | Public | List testimonials |
| POST | `/site-management/testimonials` | Admin | Create testimonial |
| PATCH | `/site-management/testimonials/:id` | Admin | Update testimonial |
| DELETE | `/site-management/testimonials/:id` | Admin | Delete testimonial |
| GET | `/site-management/partners` | Public | List partners |
| POST | `/site-management/partners` | Admin | Create partner |
| PATCH | `/site-management/partners/:id` | Admin | Update partner |
| DELETE | `/site-management/partners/:id` | Admin | Delete partner |

### Public Combined

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/site-management/public/homepage` | Public | All homepage data in one call |

---

## ADMISSIONS

### Applications

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/admissions/apply` | Public | Submit application (multipart) |
| GET | `/admissions/applications` | Admin | List applications (filtered, paginated) |
| GET | `/admissions/applications/:id` | Admin | Get application details |
| PATCH | `/admissions/applications/:id/status` | Admin | Update application status |
| PATCH | `/admissions/applications/:id/notes` | Admin | Add admin notes |
| GET | `/admissions/applications/:id/history` | Admin | Get status change history |
| GET | `/admissions/applications/export` | Admin | Export as CSV |

### Settings

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/admissions/settings` | Public | Get admission settings |
| PATCH | `/admissions/settings` | Admin | Update admission settings |
| GET | `/admissions/settings/status` | Public | Quick status check (open/closed) |

---

## E-LEARNING

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/elearning/materials` | Public | List materials (filterable) |
| GET | `/elearning/materials/:id` | Public | Get material details |
| POST | `/elearning/materials` | Admin | Upload material (multipart) |
| PATCH | `/elearning/materials/:id` | Admin | Update material |
| DELETE | `/elearning/materials/:id` | Admin | Delete material |
| POST | `/elearning/materials/:id/download` | Public | Increment download counter |

---

## NEWS

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/news` | Public | List published news (paginated) |
| GET | `/news/:slug` | Public | Get news by slug |
| GET | `/news/admin/all` | Admin | List all news (incl. drafts) |
| POST | `/news` | Admin | Create news |
| PATCH | `/news/:id` | Admin | Update news |
| DELETE | `/news/:id` | Admin | Delete news |

---

## EVENTS

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/events` | Public | List upcoming events |
| GET | `/events/:id` | Public | Get event details |
| POST | `/events` | Admin | Create event |
| PATCH | `/events/:id` | Admin | Update event |
| DELETE | `/events/:id` | Admin | Delete event |

---

## GALLERY

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/gallery` | Public | List published items |
| GET | `/gallery/:id` | Public | Get gallery item |
| POST | `/gallery` | Admin | Upload item (multipart) |
| PATCH | `/gallery/:id` | Admin | Update item |
| DELETE | `/gallery/:id` | Admin | Delete item |

---

## STUDENTS

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/students` | Admin | List students |
| GET | `/students/me` | Student | Get own profile |
| GET | `/students/:id` | Admin | Get student by ID |
| PATCH | `/students/:id` | Admin | Update student |
| GET | `/students/:id/results` | Auth | Get student results |
| GET | `/students/:id/attendance` | Auth | Get attendance records |
| GET | `/students/:id/timetable` | Auth | Get timetable |
| GET | `/students/:id/assignments` | Auth | Get assignments |

---

## TEACHERS

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/teachers` | Admin | List teachers |
| GET | `/teachers/me` | Teacher | Get own profile |
| GET | `/teachers/:id` | Admin | Get teacher by ID |
| PATCH | `/teachers/:id` | Admin | Update teacher |
| GET | `/teachers/me/classes` | Teacher | Get assigned classes |
| POST | `/teachers/marks` | Teacher | Upload marks |
| POST | `/teachers/attendance` | Teacher | Mark attendance |
| POST | `/teachers/materials` | Teacher | Upload materials (multipart) |

---

## PARENTS

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/parents/me` | Parent | Get own profile |
| GET | `/parents/me/children` | Parent | List linked children |
| GET | `/parents/me/children/:id/performance` | Parent | Get child's performance |
| GET | `/parents/me/children/:id/attendance` | Parent | Get child's attendance |
| GET | `/parents/me/children/:id/fees` | Parent | Get child's fees |

---

## MESSAGES

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/messages` | Auth | List user's messages |
| GET | `/messages/:id` | Auth | Get message details |
| POST | `/messages` | Auth | Send message |
| PATCH | `/messages/:id/read` | Auth | Mark as read |
| GET | `/messages/unread-count` | Auth | Get unread count |

---

## FEES

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/fees` | Admin | List all fees |
| GET | `/fees/me` | Student | Get own fees |
| GET | `/fees/student/:studentId` | Auth | Get student's fees |
| POST | `/fees` | Admin | Create fee record |
| PATCH | `/fees/:id` | Admin | Update fee/payment |

---

## NOTIFICATIONS

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/notifications` | Auth | List user's notifications |
| GET | `/notifications/unread-count` | Auth | Get unread count |
| PATCH | `/notifications/:id/read` | Auth | Mark as read |
| PATCH | `/notifications/read-all` | Auth | Mark all as read |
| DELETE | `/notifications/:id` | Auth | Delete notification |

---

## ANALYTICS

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/analytics/visit` | Public | Record page visit |
| GET | `/analytics/overview` | Admin | Dashboard stats overview |
| GET | `/analytics/visitors` | Admin | Visitor analytics data |
| GET | `/analytics/recent-activities` | Admin | Recent activities feed |

---

## PROGRAMS

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/programs` | Public | List all programs |

---

**Total: 70+ API Endpoints**
