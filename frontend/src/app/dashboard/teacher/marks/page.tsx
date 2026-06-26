'use client';

import { useState, useEffect } from 'react';
import { HiChartBar, HiSave, HiEye } from 'react-icons/hi';
import { teachersApi } from '@/lib/api';
import toast from 'react-hot-toast';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import Modal from '@/components/ui/Modal';

interface StudentMark {
  studentId: string;
  studentName: string;
  score: string;
  grade: string;
}

export default function TeacherMarksPage() {
  const [loading, setLoading] = useState(true);
  const [classes, setClasses] = useState<any[]>([]);
  const [selectedClass, setSelectedClass] = useState('');
  const [selectedSubject, setSelectedSubject] = useState('');
  const [students, setStudents] = useState<StudentMark[]>([]);
  const [saving, setSaving] = useState(false);
  const [historyModal, setHistoryModal] = useState(false);
  const [existingMarks, setExistingMarks] = useState<any[]>([]);

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
        setSelectedSubject(classData[0].subject || '');
        loadStudents(classData[0]);
      }
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const loadStudents = (cls: any) => {
    const studentList = cls.students || [];
    setStudents(
      studentList.map((s: any) => ({
        studentId: s.id,
        studentName: `${s.user?.firstName || ''} ${s.user?.lastName || ''}`,
        score: '',
        grade: '',
      }))
    );
  };

  const handleClassChange = (classId: string) => {
    setSelectedClass(classId);
    const cls = classes.find((c) => c.id === classId);
    if (cls) {
      setSelectedSubject(cls.subject || '');
      loadStudents(cls);
    }
  };

  const updateScore = (studentId: string, score: string) => {
    setStudents((prev) =>
      prev.map((s) =>
        s.studentId === studentId
          ? { ...s, score, grade: calculateGrade(parseFloat(score) || 0) }
          : s
      )
    );
  };

  const calculateGrade = (score: number): string => {
    if (score >= 80) return 'A';
    if (score >= 70) return 'B';
    if (score >= 60) return 'C';
    if (score >= 50) return 'D';
    return 'F';
  };

  const handleSave = async () => {
    if (!selectedClass || !selectedSubject) {
      toast.error('Select class and subject');
      return;
    }
    setSaving(true);
    try {
      await teachersApi.uploadMarks({
        classId: selectedClass,
        subject: selectedSubject,
        marks: students.map((s) => ({
          studentId: s.studentId,
          score: parseFloat(s.score) || 0,
          grade: s.grade,
        })),
      });
      toast.success('Marks saved successfully');
    } catch {
    } finally {
      setSaving(false);
    }
  };

  const viewHistory = () => {
    setExistingMarks(students.filter((s) => s.score));
    setHistoryModal(true);
  };

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-heading font-bold text-gray-900">Marks Management</h1>
          <p className="text-gray-500 text-sm">Enter and manage student marks</p>
        </div>
        <button onClick={viewHistory} className="btn-secondary flex items-center gap-2">
          <HiEye className="w-4 h-4" /> View History
        </button>
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
            <label className="block text-xs text-gray-500 mb-1">Subject</label>
            <input
              type="text"
              value={selectedSubject}
              onChange={(e) => setSelectedSubject(e.target.value)}
              className="input-field text-sm"
              placeholder="Subject name"
            />
          </div>
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div className="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
          <h2 className="text-lg font-heading font-semibold text-gray-900 flex items-center gap-2">
            <HiChartBar className="w-5 h-5 text-primary-600" /> Student Marks
          </h2>
          <span className="text-sm text-gray-500">{students.length} students</span>
        </div>

        {students.length === 0 ? (
          <div className="p-12 text-center text-gray-500">Select a class to view students.</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="bg-gray-50 border-b border-gray-200">
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Student</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Score</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Grade</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {students.map((s) => (
                  <tr key={s.studentId} className="hover:bg-gray-50">
                    <td className="px-4 py-3 text-sm font-medium text-gray-900">{s.studentName}</td>
                    <td className="px-4 py-3">
                      <input
                        type="number"
                        min="0"
                        max="100"
                        value={s.score}
                        onChange={(e) => updateScore(s.studentId, e.target.value)}
                        className="input-field text-sm w-24"
                        placeholder="Score"
                      />
                    </td>
                    <td className="px-4 py-3 text-sm">
                      {s.grade && (
                        <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${
                          s.grade === 'A' ? 'bg-green-100 text-green-800' :
                          s.grade === 'B' ? 'bg-blue-100 text-blue-800' :
                          s.grade === 'C' ? 'bg-yellow-100 text-yellow-800' :
                          'bg-red-100 text-red-800'
                        }`}>{s.grade}</span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        {students.length > 0 && (
          <div className="p-4 border-t border-gray-200 flex justify-end">
            <button onClick={handleSave} disabled={saving} className="btn-primary flex items-center gap-2">
              <HiSave className="w-4 h-4" /> {saving ? 'Saving...' : 'Save Marks'}
            </button>
          </div>
        )}
      </div>

      <Modal isOpen={historyModal} onClose={() => setHistoryModal(false)} title="Previously Entered Marks" size="lg">
        {existingMarks.length === 0 ? (
          <p className="text-gray-500 text-sm py-8 text-center">No marks have been entered yet.</p>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="bg-gray-50 border-b border-gray-200">
                  <th className="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Student</th>
                  <th className="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Score</th>
                  <th className="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Grade</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {existingMarks.map((s) => (
                  <tr key={s.studentId}>
                    <td className="px-3 py-2 text-sm text-gray-900">{s.studentName}</td>
                    <td className="px-3 py-2 text-sm text-gray-700">{s.score || '-'}</td>
                    <td className="px-3 py-2 text-sm">{s.grade || '-'}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </Modal>
    </div>
  );
}
