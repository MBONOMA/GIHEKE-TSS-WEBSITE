export default () => ({
  port: parseInt(process.env.PORT ?? '', 10) || 4000,
  database: {
    host: process.env.DB_HOST || 'localhost',
    port: parseInt(process.env.DB_PORT ?? '', 10) || 3306,
    username: process.env.DB_USERNAME || 'giheke_admin',
    password: process.env.DB_PASSWORD || 'Giheke2024!',
    database: process.env.DB_NAME || 'giheke_tss',
  },
  jwt: {
    secret: process.env.JWT_SECRET || 'giheke-tss-jwt-secret-key-2026',
    expiresIn: process.env.JWT_EXPIRES_IN || '7d',
  },
  frontendUrl: process.env.FRONTEND_URL || 'http://localhost:3000',
  smtp: {
    host: process.env.SMTP_HOST || 'smtp.gmail.com',
    port: parseInt(process.env.SMTP_PORT ?? '', 10) || 587,
    user: process.env.SMTP_USER || '',
    pass: process.env.SMTP_PASS || '',
  },
  upload: {
    maxFileSize: parseInt(process.env.MAX_FILE_SIZE ?? '', 10) || 10 * 1024 * 1024,
    allowedMimeTypes: [
      'image/jpeg',
      'image/png',
      'image/gif',
      'image/webp',
      'application/pdf',
      'application/msword',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'video/mp4',
    ],
  },
});
