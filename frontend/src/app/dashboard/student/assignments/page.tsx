'use client';

import { useState, useEffect } from 'react';
import { HiDocumentText, HiUpload, HiClock } from 'react-icons/hi';
import { format, isPast, parseISO } from 'date-fns';
import { studentsApi } from '@/lib/api';
import toast from 'react-hot-toast';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import Modal from '@/components/ui/Modal';
import { Assignment } from '@/types';

export default function StudentAssignmentsPage() {
  const [loading, setLoading] = useState(true);
  const [assignments, setAssignments] = useState<Assignment[]>([]);
  const [studentId, setStudentId] = useState<string>('');
  const [submitModal, setSubmitModal] = useState(false);
  const [selectedAssignment, setSelectedAssignment] = useState<Assignment | null>(null);
  const [selectedFile, setSelectedFile] = useState<File | null>(null);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    loadProfile();
  }, []);

  const loadProfile = async () => {
    try {
      const res = await studentsApi.getMyProfile();
      const sid = res.data.data?.id || '';
      setStudentId(sid);
      if (sid) {
        const assignRes = await studentsApi.getAssignments(sid);
        setAssignments(assignRes.data.data || []);
      }
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const handleSubmitAssignment = async () => {
    if (!selectedAssignment || !selectedFile) {
      toast.error('Please select a file to upload');
      return;
    }
    setSubmitting(true);
    try {
      const formData = new FormData();
      formData.append('file', selectedFile);
      formData.append('assignmentId', selectedAssignment.id);
      await studentsApi.update(studentId, formData);
      toast.success('Assignment submitted successfully');
      setSubmitModal(false);
      setSelectedFile(null);
    } catch {
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">Assignments</h1>
        <p className="text-gray-500 text-sm">View and submit your assignments</p>
      </div>

      <div className="space-y-4">
        {assignments.length === 0 ? (
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <HiDocumentText className="w-12 h-12 text-gray-300 mx-auto mb-3" />
            <p className="text-gray-500">No assignments available.</p>
          </div>
        ) : (
          assignments.map((a) => {
            const isOverdue = isPast(parseISO(a.dueDate));
            return (
              <div
                key={a.id}
                className={`bg-white rounded-xl shadow-sm border p-4 hover:shadow-md transition-shadow ${
                  isOverdue ? 'border-red-300 bg-red-50/30' : 'border-gray-200'
                }`}
              >
                <div className="flex items-start justify-between gap-4">
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2">
                      <HiDocumentText className={`w-5 h-5 ${isOverdue ? 'text-red-500' : 'text-primary-600'}`} />
                      <h3 className="text-base font-heading font-semibold text-gray-900">{a.title}</h3>
                      {isOverdue && (
                        <span className="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Overdue</span>
                      )}
                    </div>
                    <p className="text-sm text-gray-600 mt-1">{a.subject}</p>
                    {a.description && (
                      <p className="text-sm text-gray-500 mt-1">{a.description}</p>
                    )}
                    <div className="flex items-center gap-2 mt-2 text-xs text-gray-500">
                      <HiClock className="w-4 h-4" />
                      <span>Due: {format(parseISO(a.dueDate), 'MMM dd, yyyy')}</span>
                    </div>
                  </div>
                  <button
                    onClick={() => {
                      setSelectedAssignment(a);
                      setSubmitModal(true);
                    }}
                    className="btn-primary text-sm flex items-center gap-1.5"
                  >
                    <HiUpload className="w-4 h-4" /> Submit
                  </button>
                </div>
              </div>
            );
          })
        )}
      </div>

      <Modal isOpen={submitModal} onClose={() => setSubmitModal(false)} title="Submit Assignment" size="md">
        <div className="space-y-4">
          {selectedAssignment && (
            <div>
              <p className="text-sm font-medium text-gray-900">{selectedAssignment.title}</p>
              <p className="text-xs text-gray-500">{selectedAssignment.subject}</p>
            </div>
          )}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
            <input
              type="file"
              onChange={(e) => setSelectedFile(e.target.files?.[0] || null)}
              className="input-field"
            />
            {selectedFile && (
              <p className="text-xs text-gray-500 mt-1">Selected: {selectedFile.name}</p>
            )}
          </div>
          <button
            onClick={handleSubmitAssignment}
            disabled={submitting || !selectedFile}
            className="btn-primary w-full flex items-center justify-center gap-2"
          >
            {submitting ? 'Submitting...' : 'Submit Assignment'}
          </button>
        </div>
      </Modal>
    </div>
  );
}
