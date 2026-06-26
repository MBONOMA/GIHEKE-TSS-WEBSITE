'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { authApi } from '@/lib/api';
import { setToken, getUserRole } from '@/lib/auth';
import { SCHOOL_INFO } from '@/lib/constants';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { HiAcademicCap, HiUser, HiShieldCheck, HiUserGroup } from 'react-icons/hi';
import toast from 'react-hot-toast';

const TABS = [
  { id: 'student', label: 'Student', icon: HiAcademicCap },
  { id: 'teacher', label: 'Teacher', icon: HiUser },
  { id: 'parent', label: 'Parent', icon: HiUserGroup },
  { id: 'admin', label: 'Admin', icon: HiShieldCheck },
];

const ROLE_MAP: Record<string, string> = {
  student: 'student',
  teacher: 'teacher',
  parent: 'parent',
  admin: 'admin',
  super_admin: 'admin',
  staff: 'admin',
};

const DASHBOARD_ROUTES: Record<string, string> = {
  student: '/dashboard/student',
  teacher: '/dashboard/teacher',
  parent: '/dashboard/parent',
  admin: '/dashboard/admin',
  super_admin: '/dashboard/admin',
  staff: '/dashboard/admin',
};

export default function LoginPage() {
  const router = useRouter();
  const [activeTab, setActiveTab] = useState('student');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [sdmsCode, setSdmsCode] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    if (!email || !password) {
      setError('Please enter email/username and password');
      return;
    }

    setLoading(true);
    try {
      const payload: any = { email, password, role: activeTab };
      if ((activeTab === 'student' || activeTab === 'parent') && sdmsCode) {
        payload.sdmsCode = sdmsCode;
      }
      const res = await authApi.login(payload);
      const { token, user } = res.data?.data || res.data;
      setToken(token);

      const role = user?.role || getUserRole();
      const normalizedRole = ROLE_MAP[role] || 'student';
      const dashboardRoute = DASHBOARD_ROUTES[role] || DASHBOARD_ROUTES[normalizedRole];

      toast.success(`Welcome back, ${user?.firstName || 'User'}!`);
      router.push(dashboardRoute);
    } catch (err: any) {
      const message = err.response?.data?.message || 'Invalid credentials. Please try again.';
      setError(message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex">
      {/* Left Panel - Branding */}
      <div className="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 relative overflow-hidden items-center justify-center">
        <div className="absolute inset-0 opacity-20" style={{
          backgroundImage: 'radial-gradient(circle at 30% 40%, rgba(59,130,246,0.4) 0%, transparent 50%), radial-gradient(circle at 70% 60%, rgba(22,163,74,0.3) 0%, transparent 50%)'
        }} />
        <div className="relative text-center px-12">
          <div className="w-24 h-24 bg-white/10 backdrop-blur-sm rounded-3xl flex items-center justify-center mx-auto mb-8 border border-white/20">
            <span className="text-5xl font-heading font-bold text-white">G</span>
          </div>
          <h2 className="text-4xl font-heading font-bold text-white mb-4">{SCHOOL_INFO.name}</h2>
          <p className="text-xl text-primary-200 mb-2">{SCHOOL_INFO.motto}</p>
          <p className="text-primary-300/70">{SCHOOL_INFO.slogan}</p>
          <div className="mt-12 space-y-4 text-left max-w-sm mx-auto">
            {[
              'Access your academic dashboard',
              'View results and attendance',
              'Download learning materials',
              'Communicate with teachers',
            ].map((text) => (
              <div key={text} className="flex items-center gap-3">
                <div className="w-2 h-2 rounded-full bg-accent-400" />
                <span className="text-primary-200 text-sm">{text}</span>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Right Panel - Login Form */}
      <div className="flex-1 flex items-center justify-center p-6 bg-white">
        <div className="w-full max-w-md">
          <div className="text-center mb-8 lg:hidden">
            <div className="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <span className="text-3xl font-heading font-bold text-white">G</span>
            </div>
            <h2 className="text-2xl font-heading font-bold text-gray-900">{SCHOOL_INFO.name}</h2>
            <p className="text-sm text-gray-500 mt-1">Sign in to your account</p>
          </div>

          <h2 className="hidden lg:block text-2xl font-heading font-bold text-gray-900 mb-2">Welcome Back</h2>
          <p className="hidden lg:block text-gray-500 mb-8">Sign in to access your dashboard</p>

          {/* Role Tabs */}
          <div className="grid grid-cols-4 gap-2 mb-8">
            {TABS.map((tab) => {
              const Icon = tab.icon;
              return (
                <button
                  key={tab.id}
                  onClick={() => { setActiveTab(tab.id); setError(''); }}
                  className={`flex flex-col items-center gap-1.5 p-3 rounded-xl text-xs font-medium transition-all ${
                    activeTab === tab.id
                      ? 'bg-primary-50 text-primary-700 ring-2 ring-primary-500/20'
                      : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'
                  }`}
                >
                  <Icon className={`w-5 h-5 ${activeTab === tab.id ? 'text-primary-600' : 'text-gray-400'}`} />
                  {tab.label}
                </button>
              );
            })}
          </div>

          <form onSubmit={handleSubmit} className="space-y-5">
            {(activeTab === 'student' || activeTab === 'parent') && (
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">SDMS Code</label>
                <input
                  type="text"
                  value={sdmsCode}
                  onChange={(e) => setSdmsCode(e.target.value)}
                  placeholder="Enter your SDMS code"
                  className="input-field"
                />
              </div>
            )}

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Email / Username</label>
              <input
                type="text"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="you@example.com"
                required
                className="input-field"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Password</label>
              <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="Enter your password"
                required
                className="input-field"
              />
            </div>

            <div className="flex items-center justify-end">
              <button type="button" className="text-sm text-primary-600 hover:text-primary-700 font-medium">
                Forgot Password?
              </button>
            </div>

            {error && (
              <div className="p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm">
                {error}
              </div>
            )}

            <button
              type="submit"
              disabled={loading}
              className="btn-primary w-full py-4 text-base"
            >
              {loading ? (
                <span className="flex items-center justify-center gap-2">
                  <LoadingSpinner size="sm" /> Signing in...
                </span>
              ) : (
                'Sign In'
              )}
            </button>
          </form>

          <p className="mt-8 text-center text-sm text-gray-500">
            Back to{' '}
            <Link href="/" className="text-primary-600 hover:text-primary-700 font-medium">
              Homepage
            </Link>
          </p>
        </div>
      </div>
    </div>
  );
}
