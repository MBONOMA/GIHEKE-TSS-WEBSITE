// Core API response types
export interface ApiResponse<T> {
  data: T;
  message?: string;
  success: boolean;
}

export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    total: number;
    page: number;
    limit: number;
    totalPages: number;
  };
}

// Auth types
export interface LoginDto {
  email: string;
  password: string;
}

export interface RegisterDto {
  email: string;
  password: string;
  firstName: string;
  lastName: string;
  phone: string;
  role: string;
}

export interface AuthResponse {
  token: string;
  user: User;
}

export interface User {
  id: string;
  email: string;
  firstName: string;
  lastName: string;
  phone: string;
  role: 'super_admin' | 'admin' | 'teacher' | 'student' | 'parent' | 'staff';
  isActive: boolean;
  avatar?: string;
  lastLogin?: string;
  createdAt: string;
}

// Program/Trade
export interface Program {
  id: string;
  name: string;
  code: string;
  description: string;
  duration: string;
  icon?: string;
  isActive: boolean;
}

// Application
export interface Application {
  id: string;
  firstName: string;
  lastName: string;
  dateOfBirth: string;
  gender: string;
  previousSchool: string;
  programId: string;
  program?: Program;
  academicBackground: string;
  email: string;
  phone: string;
  address: string;
  documents: string[];
  status: ApplicationStatus;
  adminNotes?: string;
  interviewDate?: string;
  createdAt: string;
  updatedAt: string;
}

export type ApplicationStatus = 'pending' | 'under_review' | 'interview_scheduled' | 'accepted' | 'rejected' | 'waitlisted';

// Application Status History
export interface ApplicationStatusHistory {
  id: string;
  applicationId: string;
  fromStatus: string;
  toStatus: string;
  changedBy: User;
  notes?: string;
  createdAt: string;
}

// Admission Settings
export interface AdmissionSettings {
  id: string;
  isOpen: boolean;
  openFrom?: string;
  openUntil?: string;
  autoClose: boolean;
  maxApplications?: number;
  currentApplications: number;
  message?: string;
}

// Homepage Sections
export interface HomepageSection {
  id: string;
  sectionType: 'hero' | 'welcome' | 'principal_message' | 'featured_programs' | 'statistics' | 'testimonials' | 'partners';
  title?: string;
  subtitle?: string;
  content?: string;
  imageUrl?: string;
  videoUrl?: string;
  ctaText?: string;
  ctaLink?: string;
  items?: any;
  isActive: boolean;
  sortOrder: number;
}

// About Page Content
export interface AboutPageContent {
  id: string;
  sectionKey: string;
  title: string;
  content: string;
  imageUrl?: string;
  isActive: boolean;
}

// Leader
export interface Leader {
  id: string;
  name: string;
  position: string;
  bio?: string;
  imageUrl?: string;
  email?: string;
  phone?: string;
  sortOrder: number;
  isActive: boolean;
}

// Achievement
export interface Achievement {
  id: string;
  title: string;
  description: string;
  date?: string;
  imageUrl?: string;
  isActive: boolean;
}

// Testimonial
export interface Testimonial {
  id: string;
  name: string;
  position?: string;
  content: string;
  imageUrl?: string;
  rating?: number;
  isActive: boolean;
  sortOrder: number;
}

// Partner
export interface Partner {
  id: string;
  name: string;
  logoUrl?: string;
  website?: string;
  description?: string;
  isActive: boolean;
  sortOrder: number;
}

// E-learning Material
export interface ElearningMaterial {
  id: string;
  title: string;
  description?: string;
  fileUrl: string;
  fileType: 'pdf' | 'document' | 'video' | 'other';
  programId?: string;
  program?: Program;
  category: string;
  subject?: string;
  year?: number;
  isPublic: boolean;
  downloads: number;
  uploadedBy: User;
  createdAt: string;
}

// News
export interface NewsPost {
  id: string;
  title: string;
  slug: string;
  content: string;
  excerpt?: string;
  featuredImage?: string;
  isPublished: boolean;
  publishedAt?: string;
  author: User;
  tags: string[];
  viewCount: number;
  createdAt: string;
}

// Event
export interface Event {
  id: string;
  title: string;
  description: string;
  eventDate: string;
  eventTime?: string;
  location: string;
  featuredImage?: string;
  isPublished: boolean;
}

// Gallery
export interface GalleryItem {
  id: string;
  title?: string;
  description?: string;
  fileUrl: string;
  fileType: 'image' | 'video';
  category?: string;
  isPublished: boolean;
}

// Student
export interface Student {
  id: string;
  userId: string;
  user: User;
  sdmsCode: string;
  dateOfBirth: string;
  gender: string;
  address: string;
  previousSchool: string;
  enrollmentDate: string;
  programId: string;
  program: Program;
  status: string;
  guardianName: string;
  guardianPhone: string;
  guardianEmail: string;
}

// Teacher
export interface Teacher {
  id: string;
  userId: string;
  user: User;
  employeeCode: string;
  qualification: string;
  specialization: string;
  hireDate: string;
  isClassTeacher: boolean;
}

// Parent
export interface Parent {
  id: string;
  userId: string;
  user: User;
  sdmsCode: string;
  occupation: string;
  studentId: string;
  student: Student;
  relationship: string;
}

// Result
export interface Result {
  id: string;
  studentId: string;
  subject: string;
  score: number;
  grade: string;
  term: number;
  academicYear: string;
  isPublished: boolean;
}

// Attendance
export interface Attendance {
  id: string;
  studentId: string;
  date: string;
  status: 'present' | 'absent' | 'late' | 'excused';
  remarks?: string;
}

// Timetable
export interface TimetableEntry {
  id: string;
  classId: string;
  subject: string;
  dayOfWeek: number;
  startTime: string;
  endTime: string;
  teacherId?: string;
  room?: string;
}

// Assignment
export interface Assignment {
  id: string;
  title: string;
  description: string;
  classId: string;
  subject: string;
  dueDate: string;
  fileUrl?: string;
}

// Fee
export interface Fee {
  id: string;
  studentId: string;
  feeType: string;
  amount: number;
  amountPaid: number;
  dueDate: string;
  status: 'paid' | 'partial' | 'unpaid' | 'overdue';
  paymentMethod?: string;
  transactionId?: string;
}

// Message
export interface Message {
  id: string;
  senderId: string;
  sender?: User;
  receiverId: string;
  receiver?: User;
  subject?: string;
  content: string;
  isRead: boolean;
  readAt?: string;
  createdAt: string;
}

// Notification
export interface Notification {
  id: string;
  userId: string;
  title: string;
  message: string;
  type: 'info' | 'warning' | 'success' | 'error';
  isRead: boolean;
  link?: string;
  createdAt: string;
}

// Dashboard Statistics
export interface DashboardStats {
  totalStudents: number;
  totalTeachers: number;
  totalAdmissions: number;
  totalNewsPosts: number;
  totalVisitors: number;
  recentApplications: number;
  recentMessages: number;
}

// Visitor Analytics
export interface VisitorData {
  date: string;
  count: number;
  page?: string;
}
