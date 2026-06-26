'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import {
  HiUsers, HiAcademicCap, HiDocumentText, HiNewspaper, HiPlus, HiUpload,
  HiPencil, HiCalendar, HiLockOpen, HiLockClosed, HiClock,
} from 'react-icons/hi';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';
import { analyticsApi } from '@/lib/api';
import StatCard from '@/components/ui/StatCard';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { DashboardStats, VisitorData } from '@/types';
import { formatDate, timeAgo } from '@/lib/utils';

interface Activity {
  id: string;
  action: string;
  user: { firstName: string; lastName: string };
  createdAt: string;
}

export default function AdminDashboard() {
  const [loading, setLoading] = useState(true);
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [visitors, setVisitors] = useState<VisitorData[]>([]);
  const [activities, setActivities] = useState<Activity[]>([]);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      const [overviewRes, visitorsRes, activitiesRes] = await Promise.all([
        analyticsApi.getOverview(),
        analyticsApi.getVisitors({ limit: 7 }),
        analyticsApi.getRecentActivities(),
      ]);
      setStats(overviewRes.data.data);
      setVisitors(visitorsRes.data.data || []);
      setActivities(activitiesRes.data.data || []);
    } catch {
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <LoadingSpinner size="lg" className="min-h-[60vh]" />;
  }

  const quickActions = [
    { label: 'Add Student', href: '/dashboard/admin/admissions', icon: HiPlus, color: 'bg-blue-500' },
    { label: 'Add Teacher', href: '/dashboard/admin/users', icon: HiUsers, color: 'bg-green-500' },
    { label: 'Create News', href: '/dashboard/admin/news', icon: HiNewspaper, color: 'bg-purple-500' },
    { label: 'Upload Material', href: '/dashboard/admin/elearning', icon: HiUpload, color: 'bg-orange-500' },
    { label: 'Open/Close Admissions', href: '/dashboard/admin/admissions', icon: HiLockOpen, color: 'bg-red-500' },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">Admin Dashboard</h1>
        <p className="text-gray-500 mt-1">System overview and quick actions</p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard
          icon={<HiUsers className="w-6 h-6" />}
          label="Total Students"
          value={stats?.totalStudents ?? 0}
          color="primary"
        />
        <StatCard
          icon={<HiAcademicCap className="w-6 h-6" />}
          label="Total Teachers"
          value={stats?.totalTeachers ?? 0}
          color="accent"
        />
        <StatCard
          icon={<HiDocumentText className="w-6 h-6" />}
          label="Total Admissions"
          value={stats?.totalAdmissions ?? 0}
          color="gold"
        />
        <StatCard
          icon={<HiNewspaper className="w-6 h-6" />}
          label="Total News"
          value={stats?.totalNewsPosts ?? 0}
          color="red"
        />
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div className="flex flex-wrap gap-3">
          {quickActions.map((action) => {
            const Icon = action.icon;
            return (
              <Link
                key={action.label}
                href={action.href}
                className="flex items-center gap-2 px-4 py-2.5 rounded-lg text-white text-sm font-medium transition-opacity hover:opacity-90"
                style={{ backgroundColor: action.color }}
              >
                <Icon className="w-4 h-4" />
                {action.label}
              </Link>
            );
          })}
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4">Visitors (Last 7 Days)</h2>
          {visitors.length > 0 ? (
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={visitors}>
                <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                <XAxis dataKey="date" tickFormatter={(v) => formatDate(v, 'MMM dd')} tick={{ fontSize: 12 }} />
                <YAxis tick={{ fontSize: 12 }} />
                <Tooltip labelFormatter={(v) => formatDate(v as string, 'MMM dd, yyyy')} />
                <Bar dataKey="count" fill="#2563eb" radius={[4, 4, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          ) : (
            <p className="text-gray-400 text-center py-12">No visitor data available</p>
          )}
        </div>

        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4">Recent Activities</h2>
          <div className="space-y-4">
            {activities.length > 0 ? (
              activities.map((activity) => (
                <div key={activity.id} className="flex items-start gap-3">
                  <div className="p-1.5 rounded-full bg-primary-50 text-primary-600 mt-0.5">
                    <HiClock className="w-4 h-4" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-sm text-gray-700">{activity.action}</p>
                    <p className="text-xs text-gray-400 mt-0.5">
                      {activity.user?.firstName} {activity.user?.lastName} &middot; {timeAgo(activity.createdAt)}
                    </p>
                  </div>
                </div>
              ))
            ) : (
              <p className="text-gray-400 text-center py-8">No recent activities</p>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
