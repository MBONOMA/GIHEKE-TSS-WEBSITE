'use client';

import { useState, useEffect } from 'react';
import { HiAcademicCap, HiUsers, HiBookOpen, HiCalendar, HiClipboardList } from 'react-icons/hi';
import { teachersApi } from '@/lib/api';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import StatCard from '@/components/ui/StatCard';
import { Teacher } from '@/types';

interface ClassItem {
  id: string;
  name: string;
  studentCount: number;
  subject: string;
}

export default function TeacherDashboardPage() {
  const [loading, setLoading] = useState(true);
  const [teacher, setTeacher] = useState<Teacher | null>(null);
  const [classes, setClasses] = useState<ClassItem[]>([]);
  const [timetable, setTimetable] = useState<any[]>([]);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [profileRes, classesRes] = await Promise.all([
        teachersApi.getMyProfile(),
        teachersApi.getMyClasses(),
      ]);
      setTeacher(profileRes.data.data);
      const classData = classesRes.data.data || [];
      setClasses(classData);
      if (classData[0]?.timetable) setTimetable(classData[0].timetable);
    } catch {
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  const totalStudents = classes.reduce((sum, c) => sum + (c.studentCount || 0), 0);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">
          Welcome back, {teacher?.user?.firstName || 'Teacher'}
        </h1>
        <p className="text-gray-500 text-sm">{teacher?.employeeCode && `Employee Code: ${teacher.employeeCode}`}</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <StatCard icon={<HiUsers className="w-6 h-6" />} label="Total Students" value={totalStudents} color="primary" />
        <StatCard icon={<HiAcademicCap className="w-6 h-6" />} label="Classes" value={classes.length} color="accent" />
        <StatCard icon={<HiBookOpen className="w-6 h-6" />} label="Subjects" value={new Set(classes.map((c) => c.subject)).size} color="gold" />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
          <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <HiAcademicCap className="w-5 h-5 text-primary-600" /> My Classes
          </h2>
          {classes.length === 0 ? (
            <p className="text-gray-500 text-sm">No classes assigned.</p>
          ) : (
            <div className="space-y-3">
              {classes.map((c) => (
                <div key={c.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div>
                    <p className="text-sm font-medium text-gray-900">{c.name}</p>
                    <p className="text-xs text-gray-500">{c.subject} &middot; {c.studentCount} students</p>
                  </div>
                  <span className="text-xs bg-primary-50 text-primary-700 px-2 py-1 rounded-full">{c.subject}</span>
                </div>
              ))}
            </div>
          )}
        </div>

        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
          <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <HiCalendar className="w-5 h-5 text-primary-600" /> Today&apos;s Schedule
          </h2>
          {timetable.length === 0 ? (
            <p className="text-gray-500 text-sm">No classes scheduled today.</p>
          ) : (
            <div className="space-y-3">
              {timetable.map((entry: any, i: number) => (
                <div key={i} className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                  <HiClipboardList className="w-5 h-5 text-primary-600" />
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-gray-900">{entry.subject}</p>
                    <p className="text-xs text-gray-500">{entry.startTime} - {entry.endTime} | {entry.room || 'No room'}</p>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
