'use client';

import { useState, useEffect } from 'react';
import { HiClipboardCheck } from 'react-icons/hi';
import { format, startOfMonth, endOfMonth, eachDayOfInterval, getDay, isSameDay, parse } from 'date-fns';
import { studentsApi } from '@/lib/api';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { Attendance } from '@/types';

const STATUS_COLORS: Record<string, string> = {
  present: 'bg-green-500',
  absent: 'bg-red-500',
  late: 'bg-yellow-500',
  excused: 'bg-blue-500',
};

const STATUS_LABELS: Record<string, string> = {
  present: 'Present',
  absent: 'Absent',
  late: 'Late',
  excused: 'Excused',
};

export default function StudentAttendancePage() {
  const [loading, setLoading] = useState(true);
  const [attendance, setAttendance] = useState<Attendance[]>([]);
  const [studentId, setStudentId] = useState<string>('');
  const [currentMonth, setCurrentMonth] = useState(format(new Date(), 'yyyy-MM'));

  useEffect(() => {
    loadProfile();
  }, []);

  useEffect(() => {
    if (studentId) loadAttendance();
  }, [studentId, currentMonth]);

  const loadProfile = async () => {
    try {
      const res = await studentsApi.getMyProfile();
      const sid = res.data.data?.id || '';
      setStudentId(sid);
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const loadAttendance = async () => {
    try {
      const res = await studentsApi.getAttendance(studentId);
      setAttendance(res.data.data || []);
    } catch {
    }
  };

  const monthDate = parse(currentMonth, 'yyyy-MM', new Date());
  const start = startOfMonth(monthDate);
  const end = endOfMonth(monthDate);
  const days = eachDayOfInterval({ start, end });
  const startDay = getDay(start);

  const getStatusForDate = (date: Date) => {
    return attendance.find((a) => isSameDay(new Date(a.date), date));
  };

  const presentCount = attendance.filter((a) => a.status === 'present').length;
  const absentCount = attendance.filter((a) => a.status === 'absent').length;
  const lateCount = attendance.filter((a) => a.status === 'late').length;
  const excusedCount = attendance.filter((a) => a.status === 'excused').length;
  const total = attendance.length || 1;

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">Attendance</h1>
        <p className="text-gray-500 text-sm">Track your attendance record</p>
      </div>

      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div className="bg-green-50 rounded-xl p-4 border border-green-200">
          <p className="text-2xl font-bold text-green-700">{presentCount}</p>
          <p className="text-sm text-green-600">Present</p>
        </div>
        <div className="bg-red-50 rounded-xl p-4 border border-red-200">
          <p className="text-2xl font-bold text-red-700">{absentCount}</p>
          <p className="text-sm text-red-600">Absent</p>
        </div>
        <div className="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
          <p className="text-2xl font-bold text-yellow-700">{lateCount}</p>
          <p className="text-sm text-yellow-600">Late</p>
        </div>
        <div className="bg-blue-50 rounded-xl p-4 border border-blue-200">
          <p className="text-2xl font-bold text-blue-700">{excusedCount}</p>
          <p className="text-sm text-blue-600">Excused</p>
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-heading font-semibold text-gray-900 flex items-center gap-2">
            <HiClipboardCheck className="w-5 h-5 text-primary-600" /> Attendance Calendar
          </h2>
          <input
            type="month"
            value={currentMonth}
            onChange={(e) => setCurrentMonth(e.target.value)}
            className="input-field text-sm w-48"
          />
        </div>

        <div className="flex items-center gap-4 mb-4 text-xs text-gray-500">
          {Object.entries(STATUS_LABELS).map(([key, label]) => (
            <div key={key} className="flex items-center gap-1">
              <div className={`w-3 h-3 rounded-full ${STATUS_COLORS[key]}`} />
              <span>{label}</span>
            </div>
          ))}
        </div>

        <div className="grid grid-cols-7 gap-1">
          {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map((d) => (
            <div key={d} className="text-center text-xs font-semibold text-gray-500 py-2">{d}</div>
          ))}
          {Array.from({ length: startDay }).map((_, i) => (
            <div key={`empty-${i}`} />
          ))}
          {days.map((day) => {
            const record = getStatusForDate(day);
            return (
              <div
                key={day.toISOString()}
                className={`aspect-square rounded-lg flex flex-col items-center justify-center text-sm p-1 ${
                  record ? 'text-white' : 'text-gray-700'
                } ${record ? STATUS_COLORS[record.status] : 'bg-gray-50'}`}
              >
                <span className="font-medium">{format(day, 'd')}</span>
                {record && (
                  <span className="text-[10px] leading-tight mt-0.5 opacity-90">
                    {STATUS_LABELS[record.status]}
                  </span>
                )}
              </div>
            );
          })}
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <h3 className="font-heading font-semibold text-gray-900 mb-2">Summary</h3>
        <div className="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
          <div
            className="h-full bg-green-500 rounded-full transition-all"
            style={{ width: `${Math.round((presentCount / total) * 100)}%` }}
          />
        </div>
        <p className="text-sm text-gray-500 mt-2">{Math.round((presentCount / total) * 100)}% attendance rate</p>
      </div>
    </div>
  );
}
