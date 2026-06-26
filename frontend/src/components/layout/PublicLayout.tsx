'use client';

import { ReactNode } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { SCHOOL_INFO, NAV_LINKS } from '@/lib/constants';
import { HiMenu, HiX, HiPhone, HiMail, HiLocationMarker } from 'react-icons/hi';
import { useState } from 'react';

interface PublicLayoutProps {
  children: ReactNode;
}

export default function PublicLayout({ children }: PublicLayoutProps) {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const pathname = usePathname();

  return (
    <div className="min-h-screen flex flex-col">
      {/* Top Bar */}
      <div className="bg-primary-900 text-white text-sm py-2">
        <div className="max-w-7xl mx-auto px-4 flex flex-wrap items-center justify-between">
          <div className="flex items-center gap-4">
            <a href={`tel:${SCHOOL_INFO.phone}`} className="flex items-center gap-1 hover:text-primary-200">
              <HiPhone className="w-4 h-4" /> {SCHOOL_INFO.phone}
            </a>
            <a href={`mailto:${SCHOOL_INFO.email}`} className="hidden sm:flex items-center gap-1 hover:text-primary-200">
              <HiMail className="w-4 h-4" /> {SCHOOL_INFO.email}
            </a>
          </div>
          <div className="flex items-center gap-1">
            <HiLocationMarker className="w-4 h-4" />
            <span className="hidden md:inline">Giheke, Rusizi, Western Province</span>
          </div>
        </div>
      </div>

      {/* Main Navigation */}
      <nav className="bg-white shadow-md sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex items-center justify-between h-20">
            <Link href="/" className="flex items-center gap-3">
              <div className="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                G
              </div>
              <div>
                <h1 className="text-lg font-heading font-bold text-primary-900 leading-tight">{SCHOOL_INFO.name}</h1>
                <p className="text-xs text-gray-500">{SCHOOL_INFO.motto}</p>
              </div>
            </Link>

            {/* Desktop Nav */}
            <div className="hidden lg:flex items-center gap-1">
              {NAV_LINKS.map((link) => (
                <Link
                  key={link.href}
                  href={link.href}
                  className={`px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                    pathname === link.href
                      ? 'bg-primary-50 text-primary-700'
                      : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50'
                  }`}
                >
                  {link.label}
                </Link>
              ))}
              <Link href="/login" className="ml-4 btn-primary text-sm py-2 px-4">
                Portal Login
              </Link>
            </div>

            {/* Mobile Menu Button */}
            <button
              className="lg:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            >
              {mobileMenuOpen ? <HiX className="w-6 h-6" /> : <HiMenu className="w-6 h-6" />}
            </button>
          </div>
        </div>

        {/* Mobile Menu */}
        {mobileMenuOpen && (
          <div className="lg:hidden border-t border-gray-100">
            <div className="px-4 py-4 space-y-2">
              {NAV_LINKS.map((link) => (
                <Link
                  key={link.href}
                  href={link.href}
                  onClick={() => setMobileMenuOpen(false)}
                  className={`block px-4 py-3 rounded-lg text-sm font-medium ${
                    pathname === link.href
                      ? 'bg-primary-50 text-primary-700'
                      : 'text-gray-700 hover:bg-gray-50'
                  }`}
                >
                  {link.label}
                </Link>
              ))}
              <Link
                href="/login"
                onClick={() => setMobileMenuOpen(false)}
                className="block btn-primary text-center mt-4"
              >
                Portal Login
              </Link>
            </div>
          </div>
        )}
      </nav>

      {/* Main Content */}
      <main className="flex-1">{children}</main>

      {/* Footer */}
      <footer className="bg-gray-900 text-gray-300">
        <div className="max-w-7xl mx-auto px-4 py-12">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
              <div className="flex items-center gap-3 mb-4">
                <div className="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                  G
                </div>
                <div>
                  <h3 className="text-white font-heading font-bold">{SCHOOL_INFO.name}</h3>
                </div>
              </div>
              <p className="text-sm leading-relaxed">{SCHOOL_INFO.mission}</p>
            </div>
            <div>
              <h4 className="text-white font-heading font-semibold mb-4">Quick Links</h4>
              <ul className="space-y-2 text-sm">
                {NAV_LINKS.slice(0, 5).map((link) => (
                  <li key={link.href}>
                    <Link href={link.href} className="hover:text-white transition-colors">
                      {link.label}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <h4 className="text-white font-heading font-semibold mb-4">Programs</h4>
              <ul className="space-y-2 text-sm">
                {['Software Development', 'Networking', 'Electronics', 'Accounting', 'Electrical Technology'].map(
                  (prog) => (
                    <li key={prog}>
                      <Link href="/public/programs" className="hover:text-white transition-colors">
                        {prog}
                      </Link>
                    </li>
                  )
                )}
              </ul>
            </div>
            <div>
              <h4 className="text-white font-heading font-semibold mb-4">Contact</h4>
              <ul className="space-y-3 text-sm">
                <li className="flex items-start gap-2">
                  <HiLocationMarker className="w-5 h-5 text-primary-400 flex-shrink-0 mt-0.5" />
                  <span>{SCHOOL_INFO.location}</span>
                </li>
                <li className="flex items-center gap-2">
                  <HiPhone className="w-5 h-5 text-primary-400 flex-shrink-0" />
                  <a href={`tel:${SCHOOL_INFO.phone}`} className="hover:text-white">
                    {SCHOOL_INFO.phone}
                  </a>
                </li>
                <li className="flex items-center gap-2">
                  <HiMail className="w-5 h-5 text-primary-400 flex-shrink-0" />
                  <a href={`mailto:${SCHOOL_INFO.email}`} className="hover:text-white">
                    {SCHOOL_INFO.email}
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <div className="border-t border-gray-800 mt-8 pt-8 text-center text-sm">
            <p>&copy; {new Date().getFullYear()} {SCHOOL_INFO.name}. All rights reserved.</p>
            <p className="mt-1 text-gray-500">{SCHOOL_INFO.slogan}</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
