'use client';

import { useState, useEffect } from 'react';
import { HiFolder, HiDownload, HiUpload, HiDocument, HiTrash } from 'react-icons/hi';
import { elearningApi } from '@/lib/api';
import toast from 'react-hot-toast';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import Modal from '@/components/ui/Modal';
import { formatDate } from '@/lib/utils';
import { ElearningMaterial } from '@/types';

export default function ParentDocumentsPage() {
  const [loading, setLoading] = useState(true);
  const [documents, setDocuments] = useState<ElearningMaterial[]>([]);
  const [uploadModal, setUploadModal] = useState(false);
  const [file, setFile] = useState<File | null>(null);
  const [form, setForm] = useState({ title: '', description: '' });
  const [uploading, setUploading] = useState(false);

  useEffect(() => {
    loadDocuments();
  }, []);

  const loadDocuments = async () => {
    try {
      const res = await elearningApi.getAll({ isPublic: true, limit: 50 });
      setDocuments(res.data.data || []);
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
      await elearningApi.create(formData);
      toast.success('Document uploaded successfully');
      setUploadModal(false);
      setForm({ title: '', description: '' });
      setFile(null);
      loadDocuments();
    } catch {
    } finally {
      setUploading(false);
    }
  };

  const handleDownload = async (doc: ElearningMaterial) => {
    try {
      await elearningApi.incrementDownload(doc.id);
      window.open(doc.fileUrl, '_blank');
    } catch {
      window.open(doc.fileUrl, '_blank');
    }
  };

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-heading font-bold text-gray-900">Documents</h1>
          <p className="text-gray-500 text-sm">Access school documents and upload forms</p>
        </div>
        <button onClick={() => setUploadModal(true)} className="btn-primary flex items-center gap-2">
          <HiUpload className="w-4 h-4" /> Upload Document
        </button>
      </div>

      <div className="space-y-4">
        {documents.length === 0 ? (
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <HiFolder className="w-12 h-12 text-gray-300 mx-auto mb-3" />
            <p className="text-gray-500">No documents available.</p>
          </div>
        ) : (
          documents.map((doc) => (
            <div key={doc.id} className="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
              <div className="flex items-start justify-between gap-4">
                <div className="flex items-start gap-3 flex-1 min-w-0">
                  <div className="p-2 bg-primary-50 rounded-lg text-primary-600">
                    <HiDocument className="w-5 h-5" />
                  </div>
                  <div className="min-w-0">
                    <h3 className="text-sm font-heading font-semibold text-gray-900">{doc.title}</h3>
                    {doc.description && (
                      <p className="text-sm text-gray-500 mt-0.5">{doc.description}</p>
                    )}
                    <div className="flex items-center gap-3 mt-2 text-xs text-gray-400">
                      <span>{doc.fileType?.toUpperCase()}</span>
                      {doc.downloads !== undefined && (
                        <span>{doc.downloads} downloads</span>
                      )}
                      <span>{formatDate(doc.createdAt)}</span>
                    </div>
                  </div>
                </div>
                <button
                  onClick={() => handleDownload(doc)}
                  className="p-2 text-primary-600 hover:bg-primary-50 rounded-lg"
                  title="Download"
                >
                  <HiDownload className="w-4 h-4" />
                </button>
              </div>
            </div>
          ))
        )}
      </div>

      <Modal isOpen={uploadModal} onClose={() => setUploadModal(false)} title="Upload Document" size="md">
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
