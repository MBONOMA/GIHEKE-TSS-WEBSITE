'use client';

import { useState, useEffect } from 'react';
import { HiCalendar } from 'react-icons/hi';
import { studentsApi } from '@/lib/api';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { TimetableEntry } from '@/types';

const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
const HOURS = Array.from({ length: 10 }, (_, i) => `${i + 7}:00`);

export default function StudentTimetablePage() {
  const [loading, setLoading] = useState(true);
  const [timetable, setTimetable] = useState<TimetableEntry[]>([]);
  const [studentId, setStudentId] = useState<string>('');

  useEffect(() => {
    loadProfile();
  }, []);

  const loadProfile = async () => {
    try {
      const res = await studentsApi.getMyProfile();
      const sid = res.data.data?.id || '';
      setStudentId(sid);
      if (sid) {
        const ttRes = await studentsApi.getTimetable(sid);
        setTimetable(ttRes.data.data || []);
      }
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const getEntry = (day: string, hour: string) => {
    const dayIndex = DAYS.indexOf(day) + 1;
    return timetable.find(
      (e) => e.dayOfWeek === dayIndex && e.startTime <= hour && e.endTime >= hour
    );
  };

  const getCurrentDayIndex = () => {
    const day = new Date().getDay();
    return day === 0 ? 6 : day;
  };

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">Timetable</h1>
        <p className="text-gray-500 text-sm">Weekly class schedule</p>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div className="p-4 border-b border-gray-200 bg-gray-50">
          <h2 className="text-lg font-heading font-semibold text-gray-900 flex items-center gap-2">
            <HiCalendar className="w-5 h-5 text-primary-600" /> Weekly Schedule
          </h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full min-w-[600px]">
            <thead>
              <tr className="bg-gray-50 border-b border-gray-200">
                <th className="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-20">Time</th>
                {DAYS.map((day, i) => {
                  const isToday = getCurrentDayIndex() === i + 1;
                  return (
                    <th
                      key={day}
                      className={`px-3 py-3 text-center text-xs font-semibold uppercase ${
                        isToday ? 'text-primary-700 bg-primary-50' : 'text-gray-600'
                      }`}
                    >
                      {day}
                    </th>
                  );
                })}
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {HOURS.map((hour) => (
                <tr key={hour} className="hover:bg-gray-50">
                  <td className="px-3 py-3 text-xs text-gray-500 font-medium">{hour}</td>
                  {DAYS.map((day) => {
                    const entry = getEntry(day, hour);
                    const isToday = getCurrentDayIndex() === DAYS.indexOf(day) + 1;
                    return (
                      <td
                        key={`${day}-${hour}`}
                        className={`px-2 py-2 text-center border-l border-gray-100 ${
                          isToday ? 'bg-primary-50/50' : ''
                        }`}
                      >
                        {entry ? (
                          <div className="bg-primary-50 rounded-lg p-2 text-xs">
                            <p className="font-medium text-primary-800">{entry.subject}</p>
                            <p className="text-primary-600 mt-0.5">{entry.room || 'No room'}</p>
                            <p className="text-primary-500 mt-0.5">
                              {entry.startTime} - {entry.endTime}
                            </p>
                          </div>
                        ) : (
                          <span className="text-gray-300 text-xs">---</span>
                        )}
                      </td>
                    );
                  })}
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
