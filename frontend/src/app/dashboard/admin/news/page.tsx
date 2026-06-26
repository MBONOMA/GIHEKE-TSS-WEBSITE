'use client';

import { useState, useEffect, useCallback } from 'react';
import toast from 'react-hot-toast';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { HiPlus, HiPencil, HiTrash, HiEye } from 'react-icons/hi';
import { newsApi } from '@/lib/api';
import { NewsPost } from '@/types';
import DataTable from '@/components/ui/DataTable';
import Modal from '@/components/ui/Modal';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { formatDate, generateSlug } from '@/lib/utils';

const newsSchema = z.object({
  title: z.string().min(1, 'Title is required'),
  content: z.string().min(1, 'Content is required'),
  excerpt: z.string().optional(),
  featuredImage: z.string().optional(),
  isPublished: z.boolean(),
  tags: z.string().optional(),
});

export default function NewsPage() {
  const [loading, setLoading] = useState(true);
  const [posts, setPosts] = useState<NewsPost[]>([]);
  const [modalOpen, setModalOpen] = useState(false);
  const [previewOpen, setPreviewOpen] = useState(false);
  const [previewContent, setPreviewContent] = useState<NewsPost | null>(null);
  const [editingId, setEditingId] = useState<string | null>(null);

  const form = useForm<z.infer<typeof newsSchema>>({
    resolver: zodResolver(newsSchema),
    defaultValues: { title: '', content: '', excerpt: '', featuredImage: '', isPublished: true, tags: '' },
  });

  const fetchPosts = useCallback(async () => {
    try {
      setLoading(true);
      const res = await newsApi.getAllAdmin({ limit: 100 });
      setPosts(res.data.data || []);
    } catch {} finally { setLoading(false); }
  }, []);

  useEffect(() => { fetchPosts(); }, [fetchPosts]);

  const openAdd = () => {
    setEditingId(null);
    form.reset({ title: '', content: '', excerpt: '', featuredImage: '', isPublished: true, tags: '' });
    setModalOpen(true);
  };

  const openEdit = (id: string) => {
    const item = posts.find((p) => p.id === id);
    if (!item) return;
    setEditingId(id);
    form.reset({
      title: item.title,
      content: item.content,
      excerpt: item.excerpt || '',
      featuredImage: item.featuredImage || '',
      isPublished: item.isPublished,
      tags: (item.tags || []).join(', '),
    });
    setModalOpen(true);
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure?')) return;
    try { await newsApi.delete(id); toast.success('News post deleted'); fetchPosts(); } catch {}
  };

  const openPreview = (post: NewsPost) => {
    setPreviewContent(post);
    setPreviewOpen(true);
  };

  const onSubmit = async (data: z.infer<typeof newsSchema>) => {
    try {
      const payload = {
        ...data,
        slug: generateSlug(data.title),
        tags: data.tags ? data.tags.split(',').map((t) => t.trim()).filter(Boolean) : [],
      };
      if (editingId) {
        await newsApi.update(editingId, payload);
        toast.success('News post updated');
      } else {
        await newsApi.create(payload);
        toast.success('News post created');
      }
      setModalOpen(false);
      fetchPosts();
    } catch {}
  };

  const columns = [
    { key: 'title', label: 'Title' },
    { key: 'isPublished', label: 'Status', render: (v: boolean) => (
      <span className={`px-2 py-1 rounded-full text-xs font-medium ${v ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}`}>
        {v ? 'Published' : 'Draft'}
      </span>
    )},
    { key: 'viewCount', label: 'Views', render: (v: number) => v || 0 },
    { key: 'createdAt', label: 'Date', render: (v: string) => formatDate(v) },
    { key: 'actions', label: 'Actions', render: (_: any, row: NewsPost) => (
      <div className="flex items-center gap-1">
        <button onClick={() => openPreview(row)} className="p-1 text-gray-500 hover:text-gray-700"><HiEye className="w-4 h-4" /></button>
        <button onClick={() => openEdit(row.id)} className="p-1 text-blue-500 hover:text-blue-700"><HiPencil className="w-4 h-4" /></button>
        <button onClick={() => handleDelete(row.id)} className="p-1 text-red-500 hover:text-red-700"><HiTrash className="w-4 h-4" /></button>
      </div>
    )},
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-heading font-bold text-gray-900">News Management</h1>
          <p className="text-gray-500 mt-1">Create and manage news posts</p>
        </div>
        <button onClick={openAdd} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm bg-primary-600 text-white hover:bg-primary-700">
          <HiPlus className="w-4 h-4" /> Create News
        </button>
      </div>

      <DataTable columns={columns} data={posts} loading={loading} />

      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editingId ? 'Edit News Post' : 'Create News Post'} size="xl">
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input {...form.register('title')} className="input-field w-full" />
            {form.formState.errors.title && <p className="text-red-500 text-xs mt-1">{form.formState.errors.title.message}</p>}
            <p className="text-xs text-gray-400 mt-1">Slug: {generateSlug(form.watch('title') || '')}</p>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
            <input {...form.register('excerpt')} className="input-field w-full" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Content</label>
            <textarea {...form.register('content')} rows={10} className="input-field w-full font-mono text-sm" />
            {form.formState.errors.content && <p className="text-red-500 text-xs mt-1">{form.formState.errors.content.message}</p>}
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Featured Image URL</label>
              <input {...form.register('featuredImage')} className="input-field w-full" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Tags (comma-separated)</label>
              <input {...form.register('tags')} placeholder="e.g., school, event, sports" className="input-field w-full" />
            </div>
          </div>
          <div className="flex items-center gap-2">
            <input type="checkbox" {...form.register('isPublished')} className="w-4 h-4" />
            <label className="text-sm font-medium text-gray-700">Publish immediately</label>
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={() => setModalOpen(false)} className="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
            <button type="submit" className="btn-primary px-4 py-2 text-sm rounded-lg bg-primary-600 text-white hover:bg-primary-700">{editingId ? 'Update' : 'Create'}</button>
          </div>
        </form>
      </Modal>

      <Modal isOpen={previewOpen} onClose={() => setPreviewOpen(false)} title="News Preview" size="lg">
        {previewContent && (
          <div className="space-y-4">
            {previewContent.featuredImage && (
              <img src={previewContent.featuredImage} alt={previewContent.title} className="w-full h-48 object-cover rounded-lg" />
            )}
            <h2 className="text-xl font-heading font-bold">{previewContent.title}</h2>
            <div className="flex items-center gap-2 text-sm text-gray-500">
              {previewContent.author && <span>By {previewContent.author.firstName} {previewContent.author.lastName}</span>}
              <span>&middot;</span>
              <span>{formatDate(previewContent.createdAt)}</span>
              {previewContent.isPublished && <><span>&middot;</span><span className="text-green-600">Published</span></>}
            </div>
            {previewContent.excerpt && <p className="text-gray-600 italic">{previewContent.excerpt}</p>}
            <div className="prose max-w-none text-gray-700 whitespace-pre-wrap">{previewContent.content}</div>
            {previewContent.tags?.length > 0 && (
              <div className="flex flex-wrap gap-2 pt-2">
                {previewContent.tags.map((tag) => (
                  <span key={tag} className="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">{tag}</span>
                ))}
              </div>
            )}
          </div>
        )}
      </Modal>
    </div>
  );
}
