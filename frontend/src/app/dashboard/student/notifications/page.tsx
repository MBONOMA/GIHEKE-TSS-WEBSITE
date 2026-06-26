'use client';

import { useState, useEffect } from 'react';
import {
  HiBell, HiInformationCircle, HiExclamation, HiCheckCircle, HiXCircle,
  HiMailOpen, HiCheck,
} from 'react-icons/hi';
import { notificationsApi } from '@/lib/api';
import toast from 'react-hot-toast';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { formatDate, timeAgo } from '@/lib/utils';
import { Notification } from '@/types';

const TYPE_CONFIG: Record<string, { icon: any; color: string; bg: string }> = {
  info: { icon: HiInformationCircle, color: 'text-blue-600', bg: 'bg-blue-50' },
  warning: { icon: HiExclamation, color: 'text-yellow-600', bg: 'bg-yellow-50' },
  success: { icon: HiCheckCircle, color: 'text-green-600', bg: 'bg-green-50' },
  error: { icon: HiXCircle, color: 'text-red-600', bg: 'bg-red-50' },
};

export default function StudentNotificationsPage() {
  const [loading, setLoading] = useState(true);
  const [notifications, setNotifications] = useState<Notification[]>([]);

  useEffect(() => {
    loadNotifications();
  }, []);

  const loadNotifications = async () => {
    try {
      const res = await notificationsApi.getAll({ limit: 50 });
      setNotifications(res.data.data || []);
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const handleMarkAsRead = async (id: string) => {
    try {
      await notificationsApi.markAsRead(id);
      setNotifications((prev) =>
        prev.map((n) => (n.id === id ? { ...n, isRead: true } : n))
      );
      toast.success('Marked as read');
    } catch {
    }
  };

  const handleMarkAllAsRead = async () => {
    try {
      await notificationsApi.markAllAsRead();
      setNotifications((prev) => prev.map((n) => ({ ...n, isRead: true })));
      toast.success('All notifications marked as read');
    } catch {
    }
  };

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  const unreadCount = notifications.filter((n) => !n.isRead).length;

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-heading font-bold text-gray-900">Notifications</h1>
          <p className="text-gray-500 text-sm">{unreadCount} unread notifications</p>
        </div>
        {unreadCount > 0 && (
          <button onClick={handleMarkAllAsRead} className="btn-secondary flex items-center gap-2">
            <HiCheck className="w-4 h-4" /> Mark All as Read
          </button>
        )}
      </div>

      <div className="space-y-2">
        {notifications.length === 0 ? (
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <HiBell className="w-12 h-12 text-gray-300 mx-auto mb-3" />
            <p className="text-gray-500">No notifications yet.</p>
          </div>
        ) : (
          notifications.map((n) => {
            const config = TYPE_CONFIG[n.type] || TYPE_CONFIG.info;
            const Icon = config.icon;
            return (
              <div
                key={n.id}
                className={`bg-white rounded-xl shadow-sm border p-4 transition-colors ${
                  !n.isRead ? 'border-primary-200 bg-primary-50/30' : 'border-gray-200'
                }`}
              >
                <div className="flex items-start gap-3">
                  <div className={`p-2 rounded-lg ${config.bg} ${config.color}`}>
                    <Icon className="w-5 h-5" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-start justify-between gap-2">
                      <div>
                        <p className={`text-sm ${!n.isRead ? 'font-semibold' : 'font-medium'} text-gray-900`}>
                          {n.title}
                        </p>
                        <p className="text-sm text-gray-600 mt-0.5">{n.message}</p>
                      </div>
                      {!n.isRead && (
                        <button
                          onClick={() => handleMarkAsRead(n.id)}
                          className="p-1.5 text-primary-600 hover:bg-primary-50 rounded-lg flex-shrink-0"
                          title="Mark as read"
                        >
                          <HiMailOpen className="w-4 h-4" />
                        </button>
                      )}
                    </div>
                    <p className="text-xs text-gray-400 mt-1">{timeAgo(n.createdAt)}</p>
                  </div>
                </div>
              </div>
            );
          })
        )}
      </div>
    </div>
  );
}
