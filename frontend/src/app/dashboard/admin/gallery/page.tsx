'use client';

import { useState, useEffect, useCallback } from 'react';
import toast from 'react-hot-toast';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { HiPlus, HiTrash, HiPhotograph } from 'react-icons/hi';
import { galleryApi } from '@/lib/api';
import { GalleryItem } from '@/types';
import Modal from '@/components/ui/Modal';
import LoadingSpinner from '@/components/ui/LoadingSpinner';

const gallerySchema = z.object({
  title: z.string().optional(),
  description: z.string().optional(),
  category: z.string().optional(),
  isPublished: z.boolean(),
});

export default function GalleryPage() {
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<GalleryItem[]>([]);
  const [modalOpen, setModalOpen] = useState(false);
  const [uploading, setUploading] = useState(false);

  const form = useForm<z.infer<typeof gallerySchema>>({
    resolver: zodResolver(gallerySchema),
    defaultValues: { title: '', description: '', category: '', isPublished: true },
  });

  const fetchItems = useCallback(async () => {
    try {
      setLoading(true);
      const res = await galleryApi.getAll({ limit: 100 });
      setItems(res.data.data || []);
    } catch {} finally { setLoading(false); }
  }, []);

  useEffect(() => { fetchItems(); }, [fetchItems]);

  const openAdd = () => {
    form.reset({ title: '', description: '', category: '', isPublished: true });
    setModalOpen(true);
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure?')) return;
    try { await galleryApi.delete(id); toast.success('Gallery item deleted'); fetchItems(); } catch {}
  };

  const onSubmit = async (data: z.infer<typeof gallerySchema>) => {
    try {
      setUploading(true);
      const formData = new FormData();
      formData.append('title', data.title || '');
      formData.append('description', data.description || '');
      formData.append('category', data.category || '');
      formData.append('isPublished', String(data.isPublished));

      const fileInput = document.getElementById('gallery-file') as HTMLInputElement;
      if (fileInput?.files?.[0]) {
        formData.append('file', fileInput.files[0]);
      } else {
        toast.error('Please select a file');
        setUploading(false);
        return;
      }

      await galleryApi.create(formData);
      toast.success('Gallery item uploaded');
      setModalOpen(false);
      fetchItems();
    } catch {} finally { setUploading(false); }
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-heading font-bold text-gray-900">Gallery Management</h1>
          <p className="text-gray-500 mt-1">Manage photo and video gallery</p>
        </div>
        <button onClick={openAdd} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm bg-primary-600 text-white hover:bg-primary-700">
          <HiPlus className="w-4 h-4" /> Upload
        </button>
      </div>

      {loading ? (
        <LoadingSpinner size="lg" className="min-h-[40vh]" />
      ) : items.length === 0 ? (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
          <HiPhotograph className="w-12 h-12 text-gray-300 mx-auto mb-3" />
          <p className="text-gray-500">No gallery items yet. Click Upload to add one.</p>
        </div>
      ) : (
        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
          {items.map((item) => (
            <div key={item.id} className="group relative bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
              {item.fileType === 'image' ? (
                <img src={item.fileUrl} alt={item.title || 'Gallery'} className="w-full h-40 object-cover" />
              ) : (
                <div className="w-full h-40 bg-gray-100 flex items-center justify-center">
                  <HiPhotograph className="w-8 h-8 text-gray-400" />
                </div>
              )}
              <div className="p-2">
                <p className="text-sm font-medium truncate">{item.title || 'Untitled'}</p>
                <p className="text-xs text-gray-400">{item.category || 'Uncategorized'}</p>
              </div>
              <button
                onClick={() => handleDelete(item.id)}
                className="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
              >
                <HiTrash className="w-4 h-4" />
              </button>
              <div className={`absolute top-2 left-2 px-2 py-0.5 rounded-full text-xs font-medium ${item.isPublished ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}`}>
                {item.isPublished ? 'Published' : 'Draft'}
              </div>
            </div>
          ))}
        </div>
      )}

      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title="Upload Gallery Item" size="md">
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">File</label>
            <input id="gallery-file" type="file" accept="image/*,video/*" className="input-field w-full" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input {...form.register('title')} className="input-field w-full" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea {...form.register('description')} rows={3} className="input-field w-full" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <input {...form.register('category')} placeholder="e.g., Events, Sports, Academics" className="input-field w-full" />
          </div>
          <div className="flex items-center gap-2">
            <input type="checkbox" {...form.register('isPublished')} className="w-4 h-4" />
            <label className="text-sm font-medium text-gray-700">Published</label>
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={() => setModalOpen(false)} className="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
            <button type="submit" disabled={uploading} className="btn-primary px-4 py-2 text-sm rounded-lg bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50">
              {uploading ? 'Uploading...' : 'Upload'}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
}
