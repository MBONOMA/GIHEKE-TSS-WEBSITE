/** @type {import('next').NextConfig} */
const nextConfig = {
  images: {
    domains: ['localhost', 'res.cloudinary.com', 'storage.googleapis.com'],
  },
  env: {
    NEXT_PUBLIC_API_URL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:4000/api/v1',
    NEXT_PUBLIC_SCHOOL_NAME: 'GIHEKE TSS',
    NEXT_PUBLIC_SCHOOL_EMAIL: 'giheketss@gmail.com',
    NEXT_PUBLIC_SCHOOL_PHONE: '+250 788 876 460',
  },
};

module.exports = nextConfig;
