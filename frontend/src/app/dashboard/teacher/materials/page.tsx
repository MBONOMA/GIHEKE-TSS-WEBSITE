'use client';

import { useState, useEffect } from 'react';
import { HiBookOpen, HiUpload, HiTrash, HiDownload, HiDocument } from 'react-icons/hi';
import { teachersApi, elearningApi } from '@/lib/api';
import toast from 'react-hot-toast';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import Modal from '@/components/ui/Modal';
import { formatDate } from '@/lib/utils';
import { ElearningMaterial } from '@/types';

export default function TeacherMaterialsPage() {
  const [loading, setLoading] = useState(true);
  const [materials, setMaterials] = useState<ElearningMaterial[]>([]);
  const [uploadModal, setUploadModal] = useState(false);
  const [form, setForm] = useState({ title: '', description: '', classId: '', subject: '' });
  const [file, setFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);
  const [classes, setClasses] = useState<any[]>([]);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [materialsRes, classesRes] = await Promise.all([
        elearningApi.getAll({ limit: 50 }),
        teachersApi.getMyClasses(),
      ]);
      setMaterials(materialsRes.data.data || []);
      setClasses(classesRes.data.data || []);
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const handleUpload = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!file || !form.title) {
      toast.error('Title and file are required');
      return;
    }
    setUploading(true);
    try {
      const formData = new FormData();
      formData.append('file', file);
      formData.append('title', form.title);
      formData.append('description', form.description);
      formData.append('classId', form.classId);
      formData.append('subject', form.subject);
      await teachersApi.uploadMaterial(formData);
      toast.success('Material uploaded successfully');
      setUploadModal(false);
      setForm({ title: '', description: '', classId: '', subject: '' });
      setFile(null);
      loadData();
    } catch {
    } finally {
      setUploading(false);
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure you want to delete this material?')) return;
    try {
      await elearningApi.delete(id);
      toast.success('Material deleted');
      setMaterials((prev) => prev.filter((m) => m.id !== id));
    } catch {
    }
  };

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-heading font-bold text-gray-900">Teaching Materials</h1>
          <p className="text-gray-500 text-sm">Upload and manage course materials</p>
        </div>
        <button onClick={() => setUploadModal(true)} className="btn-primary flex items-center gap-2">
          <HiUpload className="w-4 h-4" /> Upload Material
        </button>
      </div>

      <div className="space-y-4">
        {materials.length === 0 ? (
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <HiBookOpen className="w-12 h-12 text-gray-300 mx-auto mb-3" />
            <p className="text-gray-500">No materials uploaded yet.</p>
          </div>
        ) : (
          materials.map((m) => (
            <div key={m.id} className="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
              <div className="flex items-start justify-between gap-4">
                <div className="flex items-start gap-3 flex-1 min-w-0">
                  <div className="p-2 bg-primary-50 rounded-lg text-primary-600">
                    <HiDocument className="w-5 h-5" />
                  </div>
                  <div className="min-w-0">
                    <h3 className="text-sm font-heading font-semibold text-gray-900">{m.title}</h3>
                    {m.description && <p className="text-sm text-gray-500 mt-0.5">{m.description}</p>}
                    <div className="flex items-center gap-3 mt-2 text-xs text-gray-400">
                      {m.subject && <span>{m.subject}</span>}
                      {m.downloads !== undefined && (
                        <span className="flex items-center gap-1">
                          <HiDownload className="w-3.5 h-3.5" /> {m.downloads} downloads
                        </span>
                      )}
                      <span>{formatDate(m.createdAt)}</span>
                    </div>
                  </div>
                </div>
                <button
                  onClick={() => handleDelete(m.id)}
                  className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg"
                >
                  <HiTrash className="w-4 h-4" />
                </button>
              </div>
            </div>
          ))
        )}
      </div>

      <Modal isOpen={uploadModal} onClose={() => setUploadModal(false)} title="Upload Material" size="md">
        <form onSubmit={handleUpload} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Title *</label>
            <input
              type="text"
              value={form.title}
              onChange={(e) => setForm((p) => ({ ...p, title: e.target.value }))}
              className="input-field"
              required
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              value={form.description}
              onChange={(e) => setForm((p) => ({ ...p, description: e.target.value }))}
              className="input-field"
              rows={3}
            />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Class</label>
              <select
                value={form.classId}
                onChange={(e) => setForm((p) => ({ ...p, classId: e.target.value }))}
                className="input-field text-sm"
              >
                <option value="">Select Class</option>
                {classes.map((c) => (
                  <option key={c.id} value={c.id}>{c.name}</option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Subject</label>
              <input
                type="text"
                value={form.subject}
                onChange={(e) => setForm((p) => ({ ...p, subject: e.target.value }))}
                className="input-field"
              />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">File *</label>
            <input
              type="file"
              onChange={(e) => setFile(e.target.files?.[0] || null)}
              className="input-field"
              required
            />
          </div>
          <button type="submit" disabled={uploading} className="btn-primary w-full flex items-center justify-center gap-2">
            <HiUpload className="w-4 h-4" /> {uploading ? 'Uploading...' : 'Upload'}
          </button>
        </form>
      </Modal>
    </div>
  );
}
