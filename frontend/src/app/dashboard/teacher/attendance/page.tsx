'use client';

import { useState, useEffect } from 'react';
import { HiClipboardCheck, HiCheck, HiX, HiClock, HiInformationCircle } from 'react-icons/hi';
import { teachersApi } from '@/lib/api';
import { format, parseISO } from 'date-fns';
import toast from 'react-hot-toast';
import LoadingSpinner from '@/components/ui/LoadingSpinner';

interface AttendanceRecord {
  studentId: string;
  studentName: string;
  status: 'present' | 'absent' | 'late' | 'excused' | '';
}

export default function TeacherAttendancePage() {
  const [loading, setLoading] = useState(true);
  const [classes, setClasses] = useState<any[]>([]);
  const [selectedClass, setSelectedClass] = useState('');
  const [selectedDate, setSelectedDate] = useState(format(new Date(), 'yyyy-MM-dd'));
  const [records, setRecords] = useState<AttendanceRecord[]>([]);
  const [submitting, setSubmitting] = useState(false);
  const [viewHistory, setViewHistory] = useState(false);

  useEffect(() => {
    loadClasses();
  }, []);

  const loadClasses = async () => {
    try {
      const res = await teachersApi.getMyClasses();
      const classData = res.data.data || [];
      setClasses(classData);
      if (classData.length > 0) {
        setSelectedClass(classData[0].id);
        loadStudents(classData[0]);
      }
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const loadStudents = (cls: any) => {
    const studentList = cls.students || [];
    setRecords(
      studentList.map((s: any) => ({
        studentId: s.id,
        studentName: `${s.user?.firstName || ''} ${s.user?.lastName || ''}`,
        status: '' as any,
      }))
    );
  };

  const handleClassChange = (classId: string) => {
    setSelectedClass(classId);
    const cls = classes.find((c) => c.id === classId);
    if (cls) loadStudents(cls);
  };

  const setStatus = (studentId: string, status: AttendanceRecord['status']) => {
    setRecords((prev) =>
      prev.map((r) => (r.studentId === studentId ? { ...r, status } : r))
    );
  };

  const handleSubmit = async () => {
    const unmarked = records.filter((r) => !r.status);
    if (unmarked.length > 0) {
      toast.error(`Please mark attendance for all students (${unmarked.length} remaining)`);
      return;
    }
    setSubmitting(true);
    try {
      await teachersApi.markAttendance({
        classId: selectedClass,
        date: selectedDate,
        records: records.map((r) => ({
          studentId: r.studentId,
          status: r.status,
        })),
      });
      toast.success('Attendance submitted successfully');
    } catch {
    } finally {
      setSubmitting(false);
    }
  };

  const STATUS_BUTTONS: { status: AttendanceRecord['status']; icon: any; label: string; color: string }[] = [
    { status: 'present', icon: HiCheck, label: 'Present', color: 'green' },
    { status: 'absent', icon: HiX, label: 'Absent', color: 'red' },
    { status: 'late', icon: HiClock, label: 'Late', color: 'yellow' },
    { status: 'excused', icon: HiInformationCircle, label: 'Excused', color: 'blue' },
  ];

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">Attendance</h1>
        <p className="text-gray-500 text-sm">Mark student attendance</p>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div className="flex flex-wrap items-center gap-4">
          <div>
            <label className="block text-xs text-gray-500 mb-1">Class</label>
            <select
              value={selectedClass}
              onChange={(e) => handleClassChange(e.target.value)}
              className="input-field text-sm"
            >
              <option value="">Select Class</option>
              {classes.map((c) => (
                <option key={c.id} value={c.id}>{c.name}</option>
              ))}
            </select>
          </div>
          <div>
            <label className="block text-xs text-gray-500 mb-1">Date</label>
            <input
              type="date"
              value={selectedDate}
              onChange={(e) => setSelectedDate(e.target.value)}
              className="input-field text-sm"
            />
          </div>
          <button
            onClick={() => setViewHistory(!viewHistory)}
            className="btn-secondary text-sm mt-auto"
          >
            {viewHistory ? 'Mark Attendance' : 'View History'}
          </button>
        </div>
      </div>

      {viewHistory ? (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
          <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4">Attendance History</h2>
          {records.length === 0 ? (
            <p className="text-gray-500 text-sm">No attendance records found.</p>
          ) : (
            <div className="space-y-2">
              {records.map((r) => (
                <div key={r.studentId} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <span className="text-sm font-medium text-gray-900">{r.studentName}</span>
                  <span className={`text-xs px-2 py-1 rounded-full ${
                    r.status === 'present' ? 'bg-green-100 text-green-800' :
                    r.status === 'absent' ? 'bg-red-100 text-red-800' :
                    r.status === 'late' ? 'bg-yellow-100 text-yellow-800' :
                    r.status === 'excused' ? 'bg-blue-100 text-blue-800' :
                    'bg-gray-100 text-gray-800'
                  }`}>
                    {r.status ? r.status.charAt(0).toUpperCase() + r.status.slice(1) : 'Not marked'}
                  </span>
                </div>
              ))}
            </div>
          )}
        </div>
      ) : (
        <>
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div className="p-4 border-b border-gray-200 bg-gray-50">
              <h2 className="text-lg font-heading font-semibold text-gray-900 flex items-center gap-2">
                <HiClipboardCheck className="w-5 h-5 text-primary-600" /> Mark Attendance
              </h2>
              <p className="text-xs text-gray-500 mt-1">{format(parseISO(selectedDate), 'MMMM dd, yyyy')}</p>
            </div>

            {records.length === 0 ? (
              <div className="p-12 text-center text-gray-500">No students in this class.</div>
            ) : (
              <div className="divide-y divide-gray-200">
                {records.map((r) => (
                  <div key={r.studentId} className="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50">
                    <span className="text-sm font-medium text-gray-900">{r.studentName}</span>
                    <div className="flex items-center gap-2">
                      {STATUS_BUTTONS.map((btn) => {
                        const Icon = btn.icon;
                        const isActive = r.status === btn.status;
                        return (
                          <button
                            key={btn.status}
                            onClick={() => setStatus(r.studentId, btn.status)}
                            className={`flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium transition-all ${
                              isActive
                                ? `bg-${btn.color}-100 text-${btn.color}-800 ring-2 ring-${btn.color}-400`
                                : 'bg-gray-50 text-gray-600 hover:bg-gray-100'
                            }`}
                          >
                            <Icon className="w-3.5 h-3.5" />
                            {btn.label}
                          </button>
                        );
                      })}
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>

          {records.length > 0 && (
            <div className="flex justify-end">
              <button onClick={handleSubmit} disabled={submitting} className="btn-primary flex items-center gap-2">
                <HiClipboardCheck className="w-4 h-4" /> {submitting ? 'Submitting...' : 'Submit Attendance'}
              </button>
            </div>
          )}
        </>
      )}
    </div>
  );
}
