'use client';

import { useState, useEffect } from 'react';
import { HiAcademicCap, HiUsers, HiUser, HiEye, HiClipboardCheck, HiChartBar } from 'react-icons/hi';
import { teachersApi } from '@/lib/api';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import Modal from '@/components/ui/Modal';

interface StudentItem {
  id: string;
  user: { firstName: string; lastName: string; email: string };
  sdmsCode: string;
}

interface ClassItem {
  id: string;
  name: string;
  studentCount: number;
  subject: string;
  students?: StudentItem[];
}

export default function TeacherClassesPage() {
  const [loading, setLoading] = useState(true);
  const [classes, setClasses] = useState<ClassItem[]>([]);
  const [selectedClass, setSelectedClass] = useState<ClassItem | null>(null);
  const [classModal, setClassModal] = useState(false);

  useEffect(() => {
    loadClasses();
  }, []);

  const loadClasses = async () => {
    try {
      const res = await teachersApi.getMyClasses();
      setClasses(res.data.data || []);
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const openClass = (cls: ClassItem) => {
    setSelectedClass(cls);
    setClassModal(true);
  };

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">My Classes</h1>
        <p className="text-gray-500 text-sm">Manage your classes and students</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {classes.length === 0 ? (
          <div className="col-span-full bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <HiAcademicCap className="w-12 h-12 text-gray-300 mx-auto mb-3" />
            <p className="text-gray-500">No classes assigned to you.</p>
          </div>
        ) : (
          classes.map((cls) => (
            <button
              key={cls.id}
              onClick={() => openClass(cls)}
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-primary-200 transition-all text-left"
            >
              <div className="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 mb-3">
                <HiAcademicCap className="w-6 h-6" />
              </div>
              <h3 className="text-lg font-heading font-semibold text-gray-900">{cls.name}</h3>
              <p className="text-sm text-gray-500 mt-1">{cls.subject}</p>
              <div className="flex items-center gap-2 mt-3 text-sm text-gray-600">
                <HiUsers className="w-4 h-4" />
                <span>{cls.studentCount || 0} students</span>
              </div>
            </button>
          ))
        )}
      </div>

      <Modal isOpen={classModal} onClose={() => setClassModal(false)} title={selectedClass?.name || 'Class'} size="lg">
        {selectedClass && (
          <div className="space-y-4">
            <p className="text-sm text-gray-500">{selectedClass.subject} &middot; {selectedClass.studentCount} students</p>
            {(!selectedClass.students || selectedClass.students.length === 0) ? (
              <p className="text-gray-500 text-sm py-8 text-center">No student data available.</p>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="bg-gray-50 border-b border-gray-200">
                      <th className="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Student</th>
                      <th className="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">SDMS Code</th>
                      <th className="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-200">
                    {selectedClass.students.map((s) => (
                      <tr key={s.id} className="hover:bg-gray-50">
                        <td className="px-3 py-2.5 flex items-center gap-2 text-sm font-medium text-gray-900">
                          <HiUser className="w-4 h-4 text-gray-400" />
                          {s.user.firstName} {s.user.lastName}
                        </td>
                        <td className="px-3 py-2.5 text-sm text-gray-600">{s.sdmsCode}</td>
                        <td className="px-3 py-2.5 text-right">
                          <div className="flex items-center justify-end gap-1">
                            <button className="p-1.5 text-gray-500 hover:text-primary-600 hover:bg-primary-50 rounded-lg" title="View Profile">
                              <HiEye className="w-4 h-4" />
                            </button>
                            <button className="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg" title="Mark Attendance">
                              <HiClipboardCheck className="w-4 h-4" />
                            </button>
                            <button className="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg" title="Enter Marks">
                              <HiChartBar className="w-4 h-4" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        )}
      </Modal>
    </div>
  );
}
