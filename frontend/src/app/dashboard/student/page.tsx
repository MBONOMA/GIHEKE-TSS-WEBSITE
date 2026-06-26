'use client';

import { useState, useEffect } from 'react';
import { HiChartBar, HiClipboardCheck, HiDocumentText, HiBell, HiCalendar } from 'react-icons/hi';
import { studentsApi, notificationsApi } from '@/lib/api';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import StatCard from '@/components/ui/StatCard';
import DataTable from '@/components/ui/DataTable';
import { formatDate, getStatusColor, getStatusLabel } from '@/lib/utils';
import { Student, Result, Notification, TimetableEntry } from '@/types';

export default function StudentDashboardPage() {
  const [loading, setLoading] = useState(true);
  const [student, setStudent] = useState<Student | null>(null);
  const [results, setResults] = useState<Result[]>([]);
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [timetable, setTimetable] = useState<TimetableEntry[]>([]);
  const [attendanceData, setAttendanceData] = useState<any[]>([]);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [profileRes, notifRes] = await Promise.all([
        studentsApi.getMyProfile(),
        notificationsApi.getAll({ limit: 5 }),
      ]);
      const studentData = profileRes.data.data;
      setStudent(studentData);
      setNotifications(notifRes.data.data || []);

      if (studentData?.id) {
        const [resultsRes, timetableRes, attendanceRes] = await Promise.all([
          studentsApi.getResults(studentData.id),
          studentsApi.getTimetable(studentData.id),
          studentsApi.getAttendance(studentData.id),
        ]);
        setResults(resultsRes.data.data || []);
        setTimetable(timetableRes.data.data || []);
        setAttendanceData(attendanceRes.data.data || []);
      }
    } catch {
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  const presentCount = attendanceData.filter((a: any) => a.status === 'present').length;
  const totalAttendance = attendanceData.length || 1;
  const attendancePercent = Math.round((presentCount / totalAttendance) * 100);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">
          Welcome back, {student?.user?.firstName || 'Student'}
        </h1>
        <p className="text-gray-500 mt-1">{student?.program?.name} &mdash; {student?.sdmsCode}</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <StatCard
          icon={<HiChartBar className="w-6 h-6" />}
          label="Total Results"
          value={results.length}
          color="primary"
        />
        <StatCard
          icon={<HiClipboardCheck className="w-6 h-6" />}
          label="Attendance %"
          value={`${attendancePercent}%`}
          color={attendancePercent >= 75 ? 'accent' : 'red'}
        />
        <StatCard
          icon={<HiDocumentText className="w-6 h-6" />}
          label="Pending Assignments"
          value={0}
          color="gold"
        />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
          <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4">Recent Results</h2>
          {results.length === 0 ? (
            <p className="text-gray-500 text-sm">No results available yet.</p>
          ) : (
            <DataTable
              columns={[
                { key: 'subject', label: 'Subject' },
                { key: 'score', label: 'Score' },
                {
                  key: 'grade',
                  label: 'Grade',
                  render: (_: any, row: Result) => (
                    <span className={getStatusColor(row.grade?.toLowerCase()) + ' px-2 py-0.5 rounded-full text-xs font-medium'}>
                      {row.grade}
                    </span>
                  ),
                },
                { key: 'term', label: 'Term' },
              ]}
              data={results.slice(0, 5)}
            />
          )}
        </div>

        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
          <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4">Today&apos;s Schedule</h2>
          {timetable.length === 0 ? (
            <p className="text-gray-500 text-sm">No classes scheduled today.</p>
          ) : (
            <div className="space-y-3">
              {timetable.map((entry) => (
                <div key={entry.id} className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                  <div className="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600">
                    <HiCalendar className="w-5 h-5" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-gray-900">{entry.subject}</p>
                    <p className="text-xs text-gray-500">
                      {entry.startTime} - {entry.endTime}{entry.room ? ` | ${entry.room}` : ''}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4 flex items-center gap-2">
          <HiBell className="w-5 h-5 text-primary-600" /> Recent Notifications
        </h2>
        {notifications.length === 0 ? (
          <p className="text-gray-500 text-sm">No notifications.</p>
        ) : (
          <div className="space-y-2">
            {notifications.map((n) => (
              <div key={n.id} className="flex items-start gap-3 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                <div className={`w-2 h-2 mt-2 rounded-full flex-shrink-0 ${
                  n.type === 'info' ? 'bg-blue-500' : n.type === 'warning' ? 'bg-yellow-500' : n.type === 'success' ? 'bg-green-500' : 'bg-red-500'
                }`} />
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-gray-900">{n.title}</p>
                  <p className="text-xs text-gray-500">{n.message}</p>
                </div>
                <span className="text-xs text-gray-400 flex-shrink-0">{formatDate(n.createdAt)}</span>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
