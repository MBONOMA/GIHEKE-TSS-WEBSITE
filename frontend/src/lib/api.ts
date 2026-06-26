import axios, { AxiosInstance, InternalAxiosRequestConfig } from 'axios';
import Cookies from 'js-cookie';
import toast from 'react-hot-toast';

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:4000/api/v1';

const api: AxiosInstance = axios.create({
  baseURL: API_URL,
  headers: { 'Content-Type': 'application/json' },
  timeout: 30000,
});

api.interceptors.request.use((config: InternalAxiosRequestConfig) => {
  const token = Cookies.get('token');
  if (token && config.headers) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  (response) => response,
  (error) => {
    const message = error.response?.data?.message || error.message || 'Something went wrong';
    if (error.response?.status === 401) {
      Cookies.remove('token');
      if (typeof window !== 'undefined' && !window.location.pathname.includes('/login')) {
        window.location.href = '/login';
      }
    }
    if (error.response?.status !== 401) {
      toast.error(message);
    }
    return Promise.reject(error);
  },
);

export default api;

// Auth API
export const authApi = {
  login: (data: { email: string; password: string }) => api.post('/auth/login', data),
  register: (data: any) => api.post('/auth/register', data),
  getProfile: () => api.get('/auth/profile'),
  changePassword: (data: { currentPassword: string; newPassword: string }) =>
    api.post('/auth/change-password', data),
};

// Users API
export const usersApi = {
  getAll: (params?: any) => api.get('/users', { params }),
  getById: (id: string) => api.get(`/users/${id}`),
  create: (data: any) => api.post('/users', data),
  update: (id: string, data: any) => api.patch(`/users/${id}`, data),
  delete: (id: string) => api.delete(`/users/${id}`),
};

// Site Management API
export const siteManagementApi = {
  // Homepage Sections
  getHomepageSections: () => api.get('/site-management/homepage'),
  getHomepageSection: (id: string) => api.get(`/site-management/homepage/${id}`),
  createHomepageSection: (data: any) => api.post('/site-management/homepage', data),
  updateHomepageSection: (id: string, data: any) => api.patch(`/site-management/homepage/${id}`, data),
  deleteHomepageSection: (id: string) => api.delete(`/site-management/homepage/${id}`),
  reorderSections: (data: { sectionIds: string[] }) =>
    api.patch('/site-management/homepage/reorder', data),

  // About Page Content
  getAboutContent: () => api.get('/site-management/about'),
  getAboutSection: (key: string) => api.get(`/site-management/about/${key}`),
  createAboutContent: (data: any) => api.post('/site-management/about', data),
  updateAboutContent: (id: string, data: any) => api.patch(`/site-management/about/${id}`, data),
  deleteAboutContent: (id: string) => api.delete(`/site-management/about/${id}`),

  // Leaders
  getLeaders: () => api.get('/site-management/leaders'),
  createLeader: (data: any) => api.post('/site-management/leaders', data),
  updateLeader: (id: string, data: any) => api.patch(`/site-management/leaders/${id}`, data),
  deleteLeader: (id: string) => api.delete(`/site-management/leaders/${id}`),

  // Achievements
  getAchievements: () => api.get('/site-management/achievements'),
  createAchievement: (data: any) => api.post('/site-management/achievements', data),
  updateAchievement: (id: string, data: any) => api.patch(`/site-management/achievements/${id}`, data),
  deleteAchievement: (id: string) => api.delete(`/site-management/achievements/${id}`),

  // Testimonials
  getTestimonials: () => api.get('/site-management/testimonials'),
  createTestimonial: (data: any) => api.post('/site-management/testimonials', data),
  updateTestimonial: (id: string, data: any) => api.patch(`/site-management/testimonials/${id}`, data),
  deleteTestimonial: (id: string) => api.delete(`/site-management/testimonials/${id}`),

  // Partners
  getPartners: () => api.get('/site-management/partners'),
  createPartner: (data: any) => api.post('/site-management/partners', data),
  updatePartner: (id: string, data: any) => api.patch(`/site-management/partners/${id}`, data),
  deletePartner: (id: string) => api.delete(`/site-management/partners/${id}`),

  // Combined public data
  getPublicHomepageData: () => api.get('/site-management/public/homepage'),
};

// Admissions API
export const admissionsApi = {
  apply: (data: FormData) =>
    api.post('/admissions/apply', data, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  getAll: (params?: any) => api.get('/admissions/applications', { params }),
  getById: (id: string) => api.get(`/admissions/applications/${id}`),
  updateStatus: (id: string, data: any) => api.patch(`/admissions/applications/${id}/status`, data),
  updateNotes: (id: string, data: any) => api.patch(`/admissions/applications/${id}/notes`, data),
  getHistory: (id: string) => api.get(`/admissions/applications/${id}/history`),
  exportData: () => api.get('/admissions/applications/export', { responseType: 'blob' }),

  // Settings
  getSettings: () => api.get('/admissions/settings'),
  updateSettings: (data: any) => api.patch('/admissions/settings', data),
  getStatus: () => api.get('/admissions/settings/status'),
};

// E-learning API
export const elearningApi = {
  getAll: (params?: any) => api.get('/elearning/materials', { params }),
  getById: (id: string) => api.get(`/elearning/materials/${id}`),
  create: (data: FormData) =>
    api.post('/elearning/materials', data, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  update: (id: string, data: any) => api.patch(`/elearning/materials/${id}`, data),
  delete: (id: string) => api.delete(`/elearning/materials/${id}`),
  incrementDownload: (id: string) => api.post(`/elearning/materials/${id}/download`),
};

// News API
export const newsApi = {
  getAll: (params?: any) => api.get('/news', { params }),
  getBySlug: (slug: string) => api.get(`/news/${slug}`),
  getById: (id: string) => api.get(`/news/admin/${id}`),
  create: (data: any) => api.post('/news', data),
  update: (id: string, data: any) => api.patch(`/news/${id}`, data),
  delete: (id: string) => api.delete(`/news/${id}`),
  getAllAdmin: (params?: any) => api.get('/news/admin/all', { params }),
};

// Events API
export const eventsApi = {
  getAll: (params?: any) => api.get('/events', { params }),
  getById: (id: string) => api.get(`/events/${id}`),
  create: (data: any) => api.post('/events', data),
  update: (id: string, data: any) => api.patch(`/events/${id}`, data),
  delete: (id: string) => api.delete(`/events/${id}`),
};

// Gallery API
export const galleryApi = {
  getAll: (params?: any) => api.get('/gallery', { params }),
  getById: (id: string) => api.get(`/gallery/${id}`),
  create: (data: FormData) =>
    api.post('/gallery', data, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  update: (id: string, data: any) => api.patch(`/gallery/${id}`, data),
  delete: (id: string) => api.delete(`/gallery/${id}`),
};

// Students API
export const studentsApi = {
  getAll: (params?: any) => api.get('/students', { params }),
  getById: (id: string) => api.get(`/students/${id}`),
  update: (id: string, data: any) => api.patch(`/students/${id}`, data),
  getMyProfile: () => api.get('/students/me'),
  getResults: (id: string) => api.get(`/students/${id}/results`),
  getAttendance: (id: string) => api.get(`/students/${id}/attendance`),
  getTimetable: (id: string) => api.get(`/students/${id}/timetable`),
  getAssignments: (id: string) => api.get(`/students/${id}/assignments`),
};

// Teachers API
export const teachersApi = {
  getAll: (params?: any) => api.get('/teachers', { params }),
  getById: (id: string) => api.get(`/teachers/${id}`),
  update: (id: string, data: any) => api.patch(`/teachers/${id}`, data),
  getMyProfile: () => api.get('/teachers/me'),
  getMyClasses: () => api.get('/teachers/me/classes'),
  uploadMarks: (data: any) => api.post('/teachers/marks', data),
  markAttendance: (data: any) => api.post('/teachers/attendance', data),
  uploadMaterial: (data: FormData) =>
    api.post('/teachers/materials', data, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
};

// Parents API
export const parentsApi = {
  getMyProfile: () => api.get('/parents/me'),
  getChildren: () => api.get('/parents/me/children'),
  getChildPerformance: (id: string) => api.get(`/parents/me/children/${id}/performance`),
  getChildAttendance: (id: string) => api.get(`/parents/me/children/${id}/attendance`),
  getChildFees: (id: string) => api.get(`/parents/me/children/${id}/fees`),
};

// Messages API
export const messagesApi = {
  getAll: (params?: any) => api.get('/messages', { params }),
  getById: (id: string) => api.get(`/messages/${id}`),
  send: (data: any) => api.post('/messages', data),
  markAsRead: (id: string) => api.patch(`/messages/${id}/read`),
  getUnreadCount: () => api.get('/messages/unread-count'),
};

// Fees API
export const feesApi = {
  getAll: (params?: any) => api.get('/fees', { params }),
  getByStudent: (studentId: string) => api.get(`/fees/student/${studentId}`),
  create: (data: any) => api.post('/fees', data),
  update: (id: string, data: any) => api.patch(`/fees/${id}`, data),
  getMyFees: () => api.get('/fees/me'),
};

// Notifications API
export const notificationsApi = {
  getAll: (params?: any) => api.get('/notifications', { params }),
  getUnreadCount: () => api.get('/notifications/unread-count'),
  markAsRead: (id: string) => api.patch(`/notifications/${id}/read`),
  markAllAsRead: () => api.patch('/notifications/read-all'),
  delete: (id: string) => api.delete(`/notifications/${id}`),
};

// Analytics API
export const analyticsApi = {
  recordVisit: (data: { page: string }) => api.post('/analytics/visit', data),
  getOverview: () => api.get('/analytics/overview'),
  getVisitors: (params?: any) => api.get('/analytics/visitors', { params }),
  getRecentActivities: () => api.get('/analytics/recent-activities'),
};

// Programs API
export const programsApi = {
  getAll: (params?: any) => api.get('/programs', { params }),
  getById: (id: string) => api.get(`/programs/${id}`),
  create: (data: any) => api.post('/programs', data),
  update: (id: string, data: any) => api.patch(`/programs/${id}`, data),
  delete: (id: string) => api.delete(`/programs/${id}`),
};
