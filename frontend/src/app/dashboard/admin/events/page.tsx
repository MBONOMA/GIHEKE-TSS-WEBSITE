'use client';

import { useState, useEffect, useCallback } from 'react';
import toast from 'react-hot-toast';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { HiPlus, HiPencil, HiTrash } from 'react-icons/hi';
import { eventsApi } from '@/lib/api';
import { Event } from '@/types';
import DataTable from '@/components/ui/DataTable';
import Modal from '@/components/ui/Modal';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { formatDate } from '@/lib/utils';

const eventSchema = z.object({
  title: z.string().min(1, 'Title is required'),
  description: z.string().min(1, 'Description is required'),
  eventDate: z.string().min(1, 'Event date is required'),
  eventTime: z.string().optional(),
  location: z.string().min(1, 'Location is required'),
  featuredImage: z.string().optional(),
  isPublished: z.boolean(),
});

export default function EventsPage() {
  const [loading, setLoading] = useState(true);
  const [events, setEvents] = useState<Event[]>([]);
  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);

  const form = useForm<z.infer<typeof eventSchema>>({
    resolver: zodResolver(eventSchema),
    defaultValues: { title: '', description: '', eventDate: '', eventTime: '', location: '', featuredImage: '', isPublished: true },
  });

  const fetchEvents = useCallback(async () => {
    try {
      setLoading(true);
      const res = await eventsApi.getAll({ limit: 100 });
      setEvents(res.data.data || []);
    } catch {} finally { setLoading(false); }
  }, []);

  useEffect(() => { fetchEvents(); }, [fetchEvents]);

  const openAdd = () => {
    setEditingId(null);
    form.reset({ title: '', description: '', eventDate: '', eventTime: '', location: '', featuredImage: '', isPublished: true });
    setModalOpen(true);
  };

  const openEdit = (id: string) => {
    const item = events.find((e) => e.id === id);
    if (!item) return;
    setEditingId(id);
    form.reset({
      title: item.title,
      description: item.description,
      eventDate: item.eventDate.split('T')[0],
      eventTime: item.eventTime || '',
      location: item.location,
      featuredImage: item.featuredImage || '',
      isPublished: item.isPublished,
    });
    setModalOpen(true);
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure?')) return;
    try { await eventsApi.delete(id); toast.success('Event deleted'); fetchEvents(); } catch {}
  };

  const onSubmit = async (data: z.infer<typeof eventSchema>) => {
    try {
      if (editingId) {
        await eventsApi.update(editingId, data);
        toast.success('Event updated');
      } else {
        await eventsApi.create(data);
        toast.success('Event created');
      }
      setModalOpen(false);
      fetchEvents();
    } catch {}
  };

  const columns = [
    { key: 'title', label: 'Title' },
    { key: 'eventDate', label: 'Date', render: (v: string) => formatDate(v) },
    { key: 'eventTime', label: 'Time', render: (v: string) => v || '-' },
    { key: 'location', label: 'Location' },
    { key: 'isPublished', label: 'Status', render: (v: boolean) => (
      <span className={`px-2 py-1 rounded-full text-xs font-medium ${v ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}`}>
        {v ? 'Published' : 'Draft'}
      </span>
    )},
    { key: 'actions', label: 'Actions', render: (_: any, row: Event) => (
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
          <h1 className="text-2xl font-heading font-bold text-gray-900">Events Management</h1>
          <p className="text-gray-500 mt-1">Create and manage school events</p>
        </div>
        <button onClick={openAdd} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm bg-primary-600 text-white hover:bg-primary-700">
          <HiPlus className="w-4 h-4" /> Create Event
        </button>
      </div>

      <DataTable columns={columns} data={events} loading={loading} />

      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editingId ? 'Edit Event' : 'Create Event'} size="lg">
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input {...form.register('title')} className="input-field w-full" />
            {form.formState.errors.title && <p className="text-red-500 text-xs mt-1">{form.formState.errors.title.message}</p>}
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea {...form.register('description')} rows={5} className="input-field w-full" />
            {form.formState.errors.description && <p className="text-red-500 text-xs mt-1">{form.formState.errors.description.message}</p>}
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Event Date</label>
              <input type="date" {...form.register('eventDate')} className="input-field w-full" />
              {form.formState.errors.eventDate && <p className="text-red-500 text-xs mt-1">{form.formState.errors.eventDate.message}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Event Time</label>
              <input type="time" {...form.register('eventTime')} className="input-field w-full" />
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Location</label>
              <input {...form.register('location')} className="input-field w-full" />
              {form.formState.errors.location && <p className="text-red-500 text-xs mt-1">{form.formState.errors.location.message}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Featured Image URL</label>
              <input {...form.register('featuredImage')} className="input-field w-full" />
            </div>
          </div>
          <div className="flex items-center gap-2">
            <input type="checkbox" {...form.register('isPublished')} className="w-4 h-4" />
            <label className="text-sm font-medium text-gray-700">Publish</label>
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={() => setModalOpen(false)} className="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
            <button type="submit" className="btn-primary px-4 py-2 text-sm rounded-lg bg-primary-600 text-white hover:bg-primary-700">{editingId ? 'Update' : 'Create'}</button>
          </div>
        </form>
      </Modal>
    </div>
  );
}
