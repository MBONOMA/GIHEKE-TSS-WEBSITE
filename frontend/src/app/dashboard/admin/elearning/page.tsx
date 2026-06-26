'use client';

import { useState, useEffect, useCallback } from 'react';
import toast from 'react-hot-toast';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { HiPlus, HiPencil, HiTrash, HiSearch } from 'react-icons/hi';
import { elearningApi, programsApi } from '@/lib/api';
import { ElearningMaterial, Program } from '@/types';
import DataTable from '@/components/ui/DataTable';
import Modal from '@/components/ui/Modal';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { formatDate, FILE_TYPE_OPTIONS } from '@/lib/utils';

const materialSchema = z.object({
  title: z.string().min(1, 'Title is required'),
  description: z.string().optional(),
  programId: z.string().optional(),
  category: z.string().min(1, 'Category is required'),
  subject: z.string().optional(),
  year: z.coerce.number().optional(),
  isPublic: z.boolean(),
});

export default function ElearningPage() {
  const [loading, setLoading] = useState(true);
  const [materials, setMaterials] = useState<ElearningMaterial[]>([]);
  const [programs, setPrograms] = useState<Program[]>([]);
  const [search, setSearch] = useState('');
  const [programFilter, setProgramFilter] = useState('');
  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [uploading, setUploading] = useState(false);

  const form = useForm<z.infer<typeof materialSchema>>({
    resolver: zodResolver(materialSchema),
    defaultValues: { title: '', description: '', programId: '', category: '', subject: '', year: new Date().getFullYear(), isPublic: false },
  });

  const fetchData = useCallback(async () => {
    try {
      setLoading(true);
      const params: any = {};
      if (search) params.search = search;
      if (programFilter) params.programId = programFilter;
      const [matRes, progRes] = await Promise.all([
        elearningApi.getAll(params),
        programsApi.getAll(),
      ]);
      setMaterials(matRes.data.data || []);
      setPrograms(progRes.data.data || []);
    } catch {} finally { setLoading(false); }
  }, [search, programFilter]);

  useEffect(() => { fetchData(); }, [fetchData]);

  const openAdd = () => {
    setEditingId(null);
    form.reset({ title: '', description: '', programId: '', category: '', subject: '', year: new Date().getFullYear(), isPublic: false });
    setModalOpen(true);
  };

  const openEdit = (id: string) => {
    const item = materials.find((m) => m.id === id);
    if (!item) return;
    setEditingId(id);
    form.reset({ title: item.title, description: item.description || '', programId: item.programId || '', category: item.category, subject: item.subject || '', year: item.year || new Date().getFullYear(), isPublic: item.isPublic });
    setModalOpen(true);
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure?')) return;
    try { await elearningApi.delete(id); toast.success('Material deleted'); fetchData(); } catch {}
  };

  const onSubmit = async (data: z.infer<typeof materialSchema>) => {
    try {
      setUploading(true);
      const formData = new FormData();
      Object.entries(data).forEach(([key, value]) => {
        if (value !== undefined && value !== '') formData.append(key, String(value));
      });

      const fileInput = document.getElementById('file-upload') as HTMLInputElement;
      if (fileInput?.files?.[0]) formData.append('file', fileInput.files[0]);

      if (editingId) {
        await elearningApi.update(editingId, data);
        toast.success('Material updated');
      } else {
        await elearningApi.create(formData);
        toast.success('Material created');
      }
      setModalOpen(false);
      fetchData();
    } catch {} finally { setUploading(false); }
  };

  const columns = [
    { key: 'title', label: 'Title' },
    { key: 'program', label: 'Program', render: (_: any, row: ElearningMaterial) => row.program?.name || '-' },
    { key: 'category', label: 'Category' },
    { key: 'fileType', label: 'File Type', render: (v: string) => <span className="uppercase text-xs font-medium">{v}</span> },
    { key: 'downloads', label: 'Downloads', render: (v: number) => v || 0 },
    { key: 'actions', label: 'Actions', render: (_: any, row: ElearningMaterial) => (
      <div className="flex items-center gap-1">
        <button onClick={() => openEdit(row.id)} className="p-1 text-blue-500 hover:text-blue-700"><HiPencil className="w-4 h-4" /></button>
        <button onClick={() => handleDelete(row.id)} className="p-1 text-red-500 hover:text-red-700"><HiTrash className="w-4 h-4" /></button>
      </div>
    )},
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-heading font-bold text-gray-900">E-Learning Management</h1>
          <p className="text-gray-500 mt-1">Manage learning materials</p>
        </div>
        <button onClick={openAdd} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm bg-primary-600 text-white hover:bg-primary-700">
          <HiPlus className="w-4 h-4" /> Upload Material
        </button>
      </div>

      <div className="flex flex-wrap items-center gap-3">
        <div className="relative flex-1 max-w-sm">
          <HiSearch className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
          <input type="text" placeholder="Search materials..." value={search} onChange={(e) => setSearch(e.target.value)} className="input-field pl-10 w-full" />
        </div>
        <select value={programFilter} onChange={(e) => setProgramFilter(e.target.value)} className="input-field">
          <option value="">All Programs</option>
          {programs.map((p) => <option key={p.id} value={p.id}>{p.name}</option>)}
        </select>
      </div>

      <DataTable columns={columns} data={materials} loading={loading} />

      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editingId ? 'Edit Material' : 'Upload Material'} size="lg">
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input {...form.register('title')} className="input-field w-full" />
            {form.formState.errors.title && <p className="text-red-500 text-xs mt-1">{form.formState.errors.title.message}</p>}
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea {...form.register('description')} rows={3} className="input-field w-full" />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Program</label>
              <select {...form.register('programId')} className="input-field w-full">
                <option value="">Select Program</option>
                {programs.map((p) => <option key={p.id} value={p.id}>{p.name}</option>)}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <input {...form.register('category')} placeholder="e.g., Notes, Past Papers" className="input-field w-full" />
              {form.formState.errors.category && <p className="text-red-500 text-xs mt-1">{form.formState.errors.category.message}</p>}
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Subject</label>
              <input {...form.register('subject')} className="input-field w-full" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Year</label>
              <input type="number" {...form.register('year')} className="input-field w-full" />
            </div>
          </div>
          {!editingId && (
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">File</label>
              <input id="file-upload" type="file" className="input-field w-full" />
            </div>
          )}
          <div className="flex items-center gap-2">
            <input type="checkbox" {...form.register('isPublic')} className="w-4 h-4" />
            <label className="text-sm font-medium text-gray-700">Public (visible to all)</label>
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={() => setModalOpen(false)} className="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
            <button type="submit" disabled={uploading} className="btn-primary px-4 py-2 text-sm rounded-lg bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50">
              {uploading ? 'Uploading...' : editingId ? 'Update' : 'Upload'}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
}
