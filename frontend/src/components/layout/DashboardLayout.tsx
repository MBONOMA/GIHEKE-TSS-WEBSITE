'use client';

import { ReactNode, useState } from 'react';
import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { useAuth } from '@/hooks/useAuth';
import { SCHOOL_INFO, SIDEBAR_LINKS } from '@/lib/constants';
import { isAdmin, isTeacher, isStudent, isParent } from '@/lib/auth';
import {
  HiMenu, HiX, HiHome, HiPencil, HiDocumentText, HiBookOpen, HiNewspaper,
  HiCalendar, HiPhotograph, HiUsers, HiMail, HiCog, HiAcademicCap,
  HiChartBar, HiClipboardCheck, HiBell, HiUser, HiCurrencyDollar,
  HiFolder, HiLogout, HiChevronDown, HiSelector,
} from 'react-icons/hi';

const iconMap: Record<string, any> = {
  HiHome, HiPencil, HiDocumentText, HiBookOpen, HiNewspaper,
  HiCalendar, HiPhotograph, HiUsers, HiMail, HiCog, HiAcademicCap,
  HiChartBar, HiClipboardCheck, HiBell, HiUser, HiCurrencyDollar, HiFolder,
};

interface DashboardLayoutProps {
  children: ReactNode;
}

export default function DashboardLayout({ children }: DashboardLayoutProps) {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [userMenuOpen, setUserMenuOpen] = useState(false);
  const pathname = usePathname();
  const router = useRouter();
  const { user, logout, loading } = useAuth();

  let links: { label: string; href: string; icon: string }[] = [];
  if (isAdmin()) links = SIDEBAR_LINKS.admin;
  else if (isTeacher()) links = SIDEBAR_LINKS.teacher;
  else if (isStudent()) links = SIDEBAR_LINKS.student;
  else if (isParent()) links = SIDEBAR_LINKS.parent;

  const handleLogout = () => {
    logout();
    router.push('/');
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Mobile Sidebar Overlay */}
      {sidebarOpen && (
        <div className="fixed inset-0 bg-black/50 z-40 lg:hidden" onClick={() => setSidebarOpen(false)} />
      )}

      {/* Sidebar */}
      <aside
        className={`fixed top-0 left-0 z-50 h-full w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 lg:translate-x-0 ${
          sidebarOpen ? 'translate-x-0' : '-translate-x-full'
        }`}
      >
        <div className="flex items-center justify-between h-16 px-4 border-b border-gray-200">
          <Link href="/" className="flex items-center gap-2">
            <div className="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
              G
            </div>
            <span className="font-heading font-bold text-primary-900 text-sm">{SCHOOL_INFO.name}</span>
          </Link>
          <button className="lg:hidden p-1 text-gray-500 hover:text-gray-700" onClick={() => setSidebarOpen(false)}>
            <HiX className="w-5 h-5" />
          </button>
        </div>

        <nav className="p-3 space-y-1 overflow-y-auto h-[calc(100%-4rem)]">
          {links.map((link) => {
            const Icon = iconMap[link.icon] || HiHome;
            const isActive = pathname === link.href || pathname.startsWith(link.href + '/');
            return (
              <Link
                key={link.href}
                href={link.href}
                onClick={() => setSidebarOpen(false)}
                className={`flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors ${
                  isActive
                    ? 'bg-primary-50 text-primary-700'
                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
                }`}
              >
                <Icon className="w-5 h-5 flex-shrink-0" />
                {link.label}
              </Link>
            );
          })}
        </nav>
      </aside>

      {/* Main Content Area */}
      <div className="lg:pl-64">
        {/* Top Header */}
        <header className="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
          <div className="flex items-center justify-between h-16 px-4 lg:px-6">
            <button className="lg:hidden p-2 text-gray-500 hover:text-gray-700" onClick={() => setSidebarOpen(true)}>
              <HiMenu className="w-6 h-6" />
            </button>

            <div className="flex-1" />

            <div className="flex items-center gap-4">
              <Link
                href={isAdmin() ? '/dashboard/admin/messages' : isTeacher() ? '/dashboard/teacher/messages' : '#'}
                className="p-2 text-gray-400 hover:text-gray-600 relative"
              >
                <HiMail className="w-5 h-5" />
              </Link>
              <Link
                href={isAdmin() ? '/dashboard/admin/site-management' : '/dashboard/student/notifications'}
                className="p-2 text-gray-400 hover:text-gray-600 relative"
              >
                <HiBell className="w-5 h-5" />
                <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full" />
              </Link>

              {/* User Menu */}
              <div className="relative">
                <button
                  onClick={() => setUserMenuOpen(!userMenuOpen)}
                  className="flex items-center gap-2 p-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg"
                >
                  <div className="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 font-semibold">
                    {user?.firstName?.[0] || 'U'}
                  </div>
                  <div className="hidden md:block text-left">
                    <p className="text-sm font-medium">{user?.firstName} {user?.lastName}</p>
                    <p className="text-xs text-gray-500 capitalize">{user?.role?.replace('_', ' ')}</p>
                  </div>
                  <HiChevronDown className="w-4 h-4 text-gray-400 hidden md:block" />
                </button>

                {userMenuOpen && (
                  <>
                    <div className="fixed inset-0 z-10" onClick={() => setUserMenuOpen(false)} />
                    <div className="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                      <Link
                        href="/dashboard/admin/settings"
                        className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        onClick={() => setUserMenuOpen(false)}
                      >
                        Settings
                      </Link>
                      <Link
                        href="/"
                        className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        onClick={() => setUserMenuOpen(false)}
                      >
                        View Website
                      </Link>
                      <hr className="my-1 border-gray-200" />
                      <button
                        onClick={handleLogout}
                        className="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2"
                      >
                        <HiLogout className="w-4 h-4" /> Logout
                      </button>
                    </div>
                  </>
                )}
              </div>
            </div>
          </div>
        </header>

        {/* Page Content */}
        <main className="p-4 lg:p-6">{children}</main>
      </div>
    </div>
  );
}
