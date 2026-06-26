'use client';

import { useState, useEffect } from 'react';
import toast from 'react-hot-toast';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import {
  HiPlus, HiPencil, HiTrash, HiChevronUp, HiChevronDown,
} from 'react-icons/hi';
import { siteManagementApi } from '@/lib/api';
import { HomepageSection, AboutPageContent, Leader, Achievement, Testimonial, Partner } from '@/types';
import DataTable from '@/components/ui/DataTable';
import Modal from '@/components/ui/Modal';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { formatDate } from '@/lib/utils';

type Tab = 'homepage' | 'about' | 'leaders' | 'achievements' | 'testimonials' | 'partners';

const TABS: { key: Tab; label: string }[] = [
  { key: 'homepage', label: 'Homepage' },
  { key: 'about', label: 'About' },
  { key: 'leaders', label: 'Leaders' },
  { key: 'achievements', label: 'Achievements' },
  { key: 'testimonials', label: 'Testimonials' },
  { key: 'partners', label: 'Partners' },
];

const homepageSchema = z.object({
  sectionType: z.enum(['hero', 'welcome', 'principal_message', 'featured_programs', 'statistics', 'testimonials', 'partners']),
  title: z.string().optional(),
  subtitle: z.string().optional(),
  content: z.string().optional(),
  imageUrl: z.string().optional(),
  videoUrl: z.string().optional(),
  ctaText: z.string().optional(),
  ctaLink: z.string().optional(),
  isActive: z.boolean(),
  sortOrder: z.coerce.number(),
});

const aboutSchema = z.object({
  sectionKey: z.string().min(1, 'Section key is required'),
  title: z.string().min(1, 'Title is required'),
  content: z.string().min(1, 'Content is required'),
  imageUrl: z.string().optional(),
  isActive: z.boolean(),
});

const leaderSchema = z.object({
  name: z.string().min(1, 'Name is required'),
  position: z.string().min(1, 'Position is required'),
  bio: z.string().optional(),
  imageUrl: z.string().optional(),
  email: z.string().optional(),
  phone: z.string().optional(),
  sortOrder: z.coerce.number(),
  isActive: z.boolean(),
});

const achievementSchema = z.object({
  title: z.string().min(1, 'Title is required'),
  description: z.string().min(1, 'Description is required'),
  date: z.string().optional(),
  imageUrl: z.string().optional(),
  isActive: z.boolean(),
});

const testimonialSchema = z.object({
  name: z.string().min(1, 'Name is required'),
  position: z.string().optional(),
  content: z.string().min(1, 'Content is required'),
  imageUrl: z.string().optional(),
  rating: z.coerce.number().min(1).max(5),
  isActive: z.boolean(),
  sortOrder: z.coerce.number(),
});

const partnerSchema = z.object({
  name: z.string().min(1, 'Name is required'),
  logoUrl: z.string().optional(),
  website: z.string().optional(),
  description: z.string().optional(),
  isActive: z.boolean(),
  sortOrder: z.coerce.number(),
});

export default function SiteManagementPage() {
  const [activeTab, setActiveTab] = useState<Tab>('homepage');
  const [loading, setLoading] = useState(true);

  const [homepageSections, setHomepageSections] = useState<HomepageSection[]>([]);
  const [aboutContent, setAboutContent] = useState<AboutPageContent[]>([]);
  const [leaders, setLeaders] = useState<Leader[]>([]);
  const [achievements, setAchievements] = useState<Achievement[]>([]);
  const [testimonials, setTestimonials] = useState<Testimonial[]>([]);
  const [partners, setPartners] = useState<Partner[]>([]);

  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);

  useEffect(() => {
    fetchAll();
  }, []);

  const fetchAll = async () => {
    try {
      setLoading(true);
      const [h, a, l, ach, t, p] = await Promise.all([
        siteManagementApi.getHomepageSections(),
        siteManagementApi.getAboutContent(),
        siteManagementApi.getLeaders(),
        siteManagementApi.getAchievements(),
        siteManagementApi.getTestimonials(),
        siteManagementApi.getPartners(),
      ]);
      setHomepageSections(h.data.data || []);
      setAboutContent(a.data.data || []);
      setLeaders(l.data.data || []);
      setAchievements(ach.data.data || []);
      setTestimonials(t.data.data || []);
      setPartners(p.data.data || []);
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const openAddModal = () => {
    setEditingId(null);
    setModalOpen(true);
  };

  const openEditModal = (id: string) => {
    setEditingId(id);
    setModalOpen(true);
  };

  if (loading) {
    return <LoadingSpinner size="lg" className="min-h-[60vh]" />;
  }

  const renderTabContent = () => {
    switch (activeTab) {
      case 'homepage': return <HomepageTab sections={homepageSections} onRefresh={fetchAll} />;
      case 'about': return <AboutTab items={aboutContent} onRefresh={fetchAll} />;
      case 'leaders': return <LeadersTab items={leaders} onRefresh={fetchAll} />;
      case 'achievements': return <AchievementsTab items={achievements} onRefresh={fetchAll} />;
      case 'testimonials': return <TestimonialsTab items={testimonials} onRefresh={fetchAll} />;
      case 'partners': return <PartnersTab items={partners} onRefresh={fetchAll} />;
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">Site Management</h1>
        <p className="text-gray-500 mt-1">Manage all website content</p>
      </div>

      <div className="flex flex-wrap gap-2 border-b border-gray-200 pb-2">
        {TABS.map((tab) => (
          <button
            key={tab.key}
            onClick={() => setActiveTab(tab.key)}
            className={`px-4 py-2 text-sm font-medium rounded-t-lg transition-colors ${
              activeTab === tab.key
                ? 'bg-primary-50 text-primary-700 border-b-2 border-primary-600'
                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'
            }`}
          >
            {tab.label}
          </button>
        ))}
      </div>

      {renderTabContent()}
    </div>
  );
}

function HomepageTab({ sections, onRefresh }: { sections: HomepageSection[]; onRefresh: () => void }) {
  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);

  const editingItem = editingId ? sections.find((s) => s.id === editingId) : null;

  const form = useForm<z.infer<typeof homepageSchema>>({
    resolver: zodResolver(homepageSchema),
    defaultValues: { sectionType: 'hero', isActive: true, sortOrder: 0 },
  });

  const openAdd = () => {
    setEditingId(null);
    form.reset({ sectionType: 'hero', isActive: true, sortOrder: sections.length });
    setModalOpen(true);
  };

  const openEdit = (id: string) => {
    const item = sections.find((s) => s.id === id);
    if (!item) return;
    setEditingId(id);
    form.reset({
      sectionType: item.sectionType,
      title: item.title || '',
      subtitle: item.subtitle || '',
      content: item.content || '',
      imageUrl: item.imageUrl || '',
      videoUrl: item.videoUrl || '',
      ctaText: item.ctaText || '',
      ctaLink: item.ctaLink || '',
      isActive: item.isActive,
      sortOrder: item.sortOrder,
    });
    setModalOpen(true);
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure?')) return;
    try {
      await siteManagementApi.deleteHomepageSection(id);
      toast.success('Section deleted');
      onRefresh();
    } catch {}
  };

  const handleReorder = async (id: string, direction: 'up' | 'down') => {
    const sorted = [...sections].sort((a, b) => a.sortOrder - b.sortOrder);
    const idx = sorted.findIndex((s) => s.id === id);
    if (idx < 0) return;
    const swapIdx = direction === 'up' ? idx - 1 : idx + 1;
    if (swapIdx < 0 || swapIdx >= sorted.length) return;
    const newOrder = [...sorted];
    [newOrder[idx], newOrder[swapIdx]] = [newOrder[swapIdx], newOrder[idx]];
    const sectionIds = newOrder.map((s) => s.id);
    try {
      await siteManagementApi.reorderSections({ sectionIds });
      toast.success('Order updated');
      onRefresh();
    } catch {}
  };

  const onSubmit = async (data: z.infer<typeof homepageSchema>) => {
    try {
      if (editingId) {
        await siteManagementApi.updateHomepageSection(editingId, data);
        toast.success('Section updated');
      } else {
        await siteManagementApi.createHomepageSection(data);
        toast.success('Section created');
      }
      setModalOpen(false);
      onRefresh();
    } catch {}
  };

  const columns = [
    { key: 'sectionType', label: 'Type', render: (_: string) => <span className="capitalize">{_.replace(/_/g, ' ')}</span> },
    { key: 'title', label: 'Title', render: (v: string) => v || '-' },
    { key: 'isActive', label: 'Active', render: (v: boolean) => <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${v ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>{v ? 'Yes' : 'No'}</span> },
    { key: 'sortOrder', label: 'Order' },
    {
      key: 'actions', label: 'Actions', render: (_: any, row: HomepageSection) => (
        <div className="flex items-center gap-1">
          <button onClick={() => handleReorder(row.id, 'up')} className="p-1 text-gray-400 hover:text-gray-600"><HiChevronUp className="w-4 h-4" /></button>
          <button onClick={() => handleReorder(row.id, 'down')} className="p-1 text-gray-400 hover:text-gray-600"><HiChevronDown className="w-4 h-4" /></button>
          <button onClick={() => openEdit(row.id)} className="p-1 text-blue-500 hover:text-blue-700"><HiPencil className="w-4 h-4" /></button>
          <button onClick={() => handleDelete(row.id)} className="p-1 text-red-500 hover:text-red-700"><HiTrash className="w-4 h-4" /></button>
        </div>
      ),
    },
  ];

  return (
    <div>
      <div className="flex justify-end mb-4">
        <button onClick={openAdd} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm">
          <HiPlus className="w-4 h-4" /> Add Section
        </button>
      </div>
      <DataTable columns={columns} data={sections} />

      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editingId ? 'Edit Section' : 'Add Section'} size="lg">
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Section Type</label>
            <select {...form.register('sectionType')} className="input-field w-full">
              <option value="hero">Hero</option>
              <option value="welcome">Welcome</option>
              <option value="principal_message">Principal Message</option>
              <option value="featured_programs">Featured Programs</option>
              <option value="statistics">Statistics</option>
              <option value="testimonials">Testimonials</option>
              <option value="partners">Partners</option>
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input {...form.register('title')} className="input-field w-full" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
            <input {...form.register('subtitle')} className="input-field w-full" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Content</label>
            <textarea {...form.register('content')} rows={4} className="input-field w-full" />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
              <input {...form.register('imageUrl')} className="input-field w-full" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
              <input {...form.register('videoUrl')} className="input-field w-full" />
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">CTA Text</label>
              <input {...form.register('ctaText')} className="input-field w-full" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">CTA Link</label>
              <input {...form.register('ctaLink')} className="input-field w-full" />
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div className="flex items-center gap-2">
              <input type="checkbox" {...form.register('isActive')} className="w-4 h-4" />
              <label className="text-sm font-medium text-gray-700">Active</label>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
              <input type="number" {...form.register('sortOrder')} className="input-field w-full" />
            </div>
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

function AboutTab({ items, onRefresh }: { items: AboutPageContent[]; onRefresh: () => void }) {
  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);

  const editingItem = editingId ? items.find((s) => s.id === editingId) : null;

  const form = useForm<z.infer<typeof aboutSchema>>({
    resolver: zodResolver(aboutSchema),
    defaultValues: { sectionKey: '', title: '', content: '', imageUrl: '', isActive: true },
  });

  const openAdd = () => {
    setEditingId(null);
    form.reset({ sectionKey: 'history', title: '', content: '', imageUrl: '', isActive: true });
    setModalOpen(true);
  };

  const openEdit = (id: string) => {
    const item = items.find((s) => s.id === id);
    if (!item) return;
    setEditingId(id);
    form.reset({ sectionKey: item.sectionKey, title: item.title, content: item.content, imageUrl: item.imageUrl || '', isActive: item.isActive });
    setModalOpen(true);
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure?')) return;
    try {
      await siteManagementApi.deleteAboutContent(id);
      toast.success('Content deleted');
      onRefresh();
    } catch {}
  };

  const onSubmit = async (data: z.infer<typeof aboutSchema>) => {
    try {
      if (editingId) {
        await siteManagementApi.updateAboutContent(editingId, data);
        toast.success('Content updated');
      } else {
        await siteManagementApi.createAboutContent(data);
        toast.success('Content created');
      }
      setModalOpen(false);
      onRefresh();
    } catch {}
  };

  const columns = [
    { key: 'sectionKey', label: 'Section', render: (v: string) => <span className="capitalize">{v.replace(/_/g, ' ')}</span> },
    { key: 'title', label: 'Title' },
    { key: 'isActive', label: 'Active', render: (v: boolean) => <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${v ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>{v ? 'Yes' : 'No'}</span> },
    {
      key: 'actions', label: 'Actions', render: (_: any, row: AboutPageContent) => (
        <div className="flex items-center gap-1">
          <button onClick={() => openEdit(row.id)} className="p-1 text-blue-500 hover:text-blue-700"><HiPencil className="w-4 h-4" /></button>
          <button onClick={() => handleDelete(row.id)} className="p-1 text-red-500 hover:text-red-700"><HiTrash className="w-4 h-4" /></button>
        </div>
      ),
    },
  ];

  return (
    <div>
      <div className="flex justify-end mb-4">
        <button onClick={openAdd} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm"><HiPlus className="w-4 h-4" /> Add Content</button>
      </div>
      <DataTable columns={columns} data={items} />
      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editingId ? 'Edit Content' : 'Add Content'} size="lg">
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Section Key</label>
            <select {...form.register('sectionKey')} className="input-field w-full">
              <option value="history">History</option>
              <option value="mission">Mission</option>
              <option value="vision">Vision</option>
              <option value="values">Values</option>
              <option value="profile">Profile</option>
              <option value="principal_message">Principal Message</option>
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input {...form.register('title')} className="input-field w-full" />
            {form.formState.errors.title && <p className="text-red-500 text-xs mt-1">{form.formState.errors.title.message}</p>}
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Content</label>
            <textarea {...form.register('content')} rows={6} className="input-field w-full" />
            {form.formState.errors.content && <p className="text-red-500 text-xs mt-1">{form.formState.errors.content.message}</p>}
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
            <input {...form.register('imageUrl')} className="input-field w-full" />
          </div>
          <div className="flex items-center gap-2">
            <input type="checkbox" {...form.register('isActive')} className="w-4 h-4" />
            <label className="text-sm font-medium text-gray-700">Active</label>
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

function LeadersTab({ items, onRefresh }: { items: Leader[]; onRefresh: () => void }) {
  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);

  const form = useForm<z.infer<typeof leaderSchema>>({
    resolver: zodResolver(leaderSchema),
    defaultValues: { name: '', position: '', bio: '', imageUrl: '', email: '', phone: '', sortOrder: 0, isActive: true },
  });

  const openAdd = () => { setEditingId(null); form.reset({ name: '', position: '', bio: '', imageUrl: '', email: '', phone: '', sortOrder: items.length, isActive: true }); setModalOpen(true); };
  const openEdit = (id: string) => {
    const item = items.find((s) => s.id === id);
    if (!item) return;
    setEditingId(id); form.reset({ name: item.name, position: item.position, bio: item.bio || '', imageUrl: item.imageUrl || '', email: item.email || '', phone: item.phone || '', sortOrder: item.sortOrder, isActive: item.isActive }); setModalOpen(true);
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure?')) return;
    try { await siteManagementApi.deleteLeader(id); toast.success('Leader deleted'); onRefresh(); } catch {}
  };

  const onSubmit = async (data: z.infer<typeof leaderSchema>) => {
    try {
      if (editingId) { await siteManagementApi.updateLeader(editingId, data); toast.success('Leader updated'); }
      else { await siteManagementApi.createLeader(data); toast.success('Leader created'); }
      setModalOpen(false); onRefresh();
    } catch {}
  };

  const columns = [
    { key: 'name', label: 'Name' },
    { key: 'position', label: 'Position' },
    { key: 'email', label: 'Email', render: (v: string) => v || '-' },
    { key: 'isActive', label: 'Active', render: (v: boolean) => <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${v ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>{v ? 'Yes' : 'No'}</span> },
    { key: 'sortOrder', label: 'Order' },
    { key: 'actions', label: 'Actions', render: (_: any, row: Leader) => (
      <div className="flex items-center gap-1">
        <button onClick={() => openEdit(row.id)} className="p-1 text-blue-500 hover:text-blue-700"><HiPencil className="w-4 h-4" /></button>
        <button onClick={() => handleDelete(row.id)} className="p-1 text-red-500 hover:text-red-700"><HiTrash className="w-4 h-4" /></button>
      </div>
    )},
  ];

  return (
    <div>
      <div className="flex justify-end mb-4">
        <button onClick={openAdd} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm"><HiPlus className="w-4 h-4" /> Add Leader</button>
      </div>
      <DataTable columns={columns} data={items} />
      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editingId ? 'Edit Leader' : 'Add Leader'} size="lg">
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input {...form.register('name')} className="input-field w-full" />
              {form.formState.errors.name && <p className="text-red-500 text-xs mt-1">{form.formState.errors.name.message}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Position</label>
              <input {...form.register('position')} className="input-field w-full" />
              {form.formState.errors.position && <p className="text-red-500 text-xs mt-1">{form.formState.errors.position.message}</p>}
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Bio</label>
            <textarea {...form.register('bio')} rows={4} className="input-field w-full" />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input {...form.register('email')} className="input-field w-full" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Phone</label>
              <input {...form.register('phone')} className="input-field w-full" />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
            <input {...form.register('imageUrl')} className="input-field w-full" />
          </div>
          <div className="flex items-center gap-4">
            <div className="flex items-center gap-2">
              <input type="checkbox" {...form.register('isActive')} className="w-4 h-4" />
              <label className="text-sm font-medium text-gray-700">Active</label>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
              <input type="number" {...form.register('sortOrder')} className="input-field w-24" />
            </div>
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

function AchievementsTab({ items, onRefresh }: { items: Achievement[]; onRefresh: () => void }) {
  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);

  const form = useForm<z.infer<typeof achievementSchema>>({
    resolver: zodResolver(achievementSchema),
    defaultValues: { title: '', description: '', date: '', imageUrl: '', isActive: true },
  });

  const openAdd = () => { setEditingId(null); form.reset({ title: '', description: '', date: '', imageUrl: '', isActive: true }); setModalOpen(true); };
  const openEdit = (id: string) => {
    const item = items.find((s) => s.id === id);
    if (!item) return;
    setEditingId(id); form.reset({ title: item.title, description: item.description, date: item.date ? item.date.split('T')[0] : '', imageUrl: item.imageUrl || '', isActive: item.isActive }); setModalOpen(true);
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure?')) return;
    try { await siteManagementApi.deleteAchievement(id); toast.success('Achievement deleted'); onRefresh(); } catch {}
  };

  const onSubmit = async (data: z.infer<typeof achievementSchema>) => {
    try {
      if (editingId) { await siteManagementApi.updateAchievement(editingId, data); toast.success('Achievement updated'); }
      else { await siteManagementApi.createAchievement(data); toast.success('Achievement created'); }
      setModalOpen(false); onRefresh();
    } catch {}
  };

  const columns = [
    { key: 'title', label: 'Title' },
    { key: 'description', label: 'Description', render: (v: string) => v?.substring(0, 80) + (v?.length > 80 ? '...' : '') },
    { key: 'date', label: 'Date', render: (v: string) => v ? formatDate(v) : '-' },
    { key: 'isActive', label: 'Active', render: (v: boolean) => <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${v ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>{v ? 'Yes' : 'No'}</span> },
    { key: 'actions', label: 'Actions', render: (_: any, row: Achievement) => (
      <div className="flex items-center gap-1">
        <button onClick={() => openEdit(row.id)} className="p-1 text-blue-500 hover:text-blue-700"><HiPencil className="w-4 h-4" /></button>
        <button onClick={() => handleDelete(row.id)} className="p-1 text-red-500 hover:text-red-700"><HiTrash className="w-4 h-4" /></button>
      </div>
    )},
  ];

  return (
    <div>
      <div className="flex justify-end mb-4">
        <button onClick={openAdd} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm"><HiPlus className="w-4 h-4" /> Add Achievement</button>
      </div>
      <DataTable columns={columns} data={items} />
      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editingId ? 'Edit Achievement' : 'Add Achievement'} size="lg">
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input {...form.register('title')} className="input-field w-full" />
            {form.formState.errors.title && <p className="text-red-500 text-xs mt-1">{form.formState.errors.title.message}</p>}
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea {...form.register('description')} rows={4} className="input-field w-full" />
            {form.formState.errors.description && <p className="text-red-500 text-xs mt-1">{form.formState.errors.description.message}</p>}
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Date</label>
              <input type="date" {...form.register('date')} className="input-field w-full" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
              <input {...form.register('imageUrl')} className="input-field w-full" />
            </div>
          </div>
          <div className="flex items-center gap-2">
            <input type="checkbox" {...form.register('isActive')} className="w-4 h-4" />
            <label className="text-sm font-medium text-gray-700">Active</label>
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

function TestimonialsTab({ items, onRefresh }: { items: Testimonial[]; onRefresh: () => void }) {
  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);

  const form = useForm<z.infer<typeof testimonialSchema>>({
    resolver: zodResolver(testimonialSchema),
    defaultValues: { name: '', position: '', content: '', imageUrl: '', rating: 5, isActive: true, sortOrder: 0 },
  });

  const openAdd = () => { setEditingId(null); form.reset({ name: '', position: '', content: '', imageUrl: '', rating: 5, isActive: true, sortOrder: items.length }); setModalOpen(true); };
  const openEdit = (id: string) => {
    const item = items.find((s) => s.id === id);
    if (!item) return;
    setEditingId(id); form.reset({ name: item.name, position: item.position || '', content: item.content, imageUrl: item.imageUrl || '', rating: item.rating || 5, isActive: item.isActive, sortOrder: item.sortOrder }); setModalOpen(true);
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure?')) return;
    try { await siteManagementApi.deleteTestimonial(id); toast.success('Testimonial deleted'); onRefresh(); } catch {}
  };

  const onSubmit = async (data: z.infer<typeof testimonialSchema>) => {
    try {
      if (editingId) { await siteManagementApi.updateTestimonial(editingId, data); toast.success('Testimonial updated'); }
      else { await siteManagementApi.createTestimonial(data); toast.success('Testimonial created'); }
      setModalOpen(false); onRefresh();
    } catch {}
  };

  const columns = [
    { key: 'name', label: 'Name' },
    { key: 'position', label: 'Position', render: (v: string) => v || '-' },
    { key: 'content', label: 'Content', render: (v: string) => v?.substring(0, 60) + (v?.length > 60 ? '...' : '') },
    { key: 'rating', label: 'Rating', render: (v: number) => v ? '★'.repeat(v) + '☆'.repeat(5 - v) : '-' },
    { key: 'isActive', label: 'Active', render: (v: boolean) => <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${v ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>{v ? 'Yes' : 'No'}</span> },
    { key: 'actions', label: 'Actions', render: (_: any, row: Testimonial) => (
      <div className="flex items-center gap-1">
        <button onClick={() => openEdit(row.id)} className="p-1 text-blue-500 hover:text-blue-700"><HiPencil className="w-4 h-4" /></button>
        <button onClick={() => handleDelete(row.id)} className="p-1 text-red-500 hover:text-red-700"><HiTrash className="w-4 h-4" /></button>
      </div>
    )},
  ];

  return (
    <div>
      <div className="flex justify-end mb-4">
        <button onClick={openAdd} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm"><HiPlus className="w-4 h-4" /> Add Testimonial</button>
      </div>
      <DataTable columns={columns} data={items} />
      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editingId ? 'Edit Testimonial' : 'Add Testimonial'} size="lg">
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input {...form.register('name')} className="input-field w-full" />
              {form.formState.errors.name && <p className="text-red-500 text-xs mt-1">{form.formState.errors.name.message}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Position</label>
              <input {...form.register('position')} className="input-field w-full" />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Content</label>
            <textarea {...form.register('content')} rows={4} className="input-field w-full" />
            {form.formState.errors.content && <p className="text-red-500 text-xs mt-1">{form.formState.errors.content.message}</p>}
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
              <input {...form.register('imageUrl')} className="input-field w-full" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Rating (1-5)</label>
              <input type="number" min={1} max={5} {...form.register('rating')} className="input-field w-full" />
            </div>
          </div>
          <div className="flex items-center gap-4">
            <div className="flex items-center gap-2">
              <input type="checkbox" {...form.register('isActive')} className="w-4 h-4" />
              <label className="text-sm font-medium text-gray-700">Active</label>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
              <input type="number" {...form.register('sortOrder')} className="input-field w-24" />
            </div>
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

function PartnersTab({ items, onRefresh }: { items: Partner[]; onRefresh: () => void }) {
  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);

  const form = useForm<z.infer<typeof partnerSchema>>({
    resolver: zodResolver(partnerSchema),
    defaultValues: { name: '', logoUrl: '', website: '', description: '', isActive: true, sortOrder: 0 },
  });

  const openAdd = () => { setEditingId(null); form.reset({ name: '', logoUrl: '', website: '', description: '', isActive: true, sortOrder: items.length }); setModalOpen(true); };
  const openEdit = (id: string) => {
    const item = items.find((s) => s.id === id);
    if (!item) return;
    setEditingId(id); form.reset({ name: item.name, logoUrl: item.logoUrl || '', website: item.website || '', description: item.description || '', isActive: item.isActive, sortOrder: item.sortOrder }); setModalOpen(true);
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure?')) return;
    try { await siteManagementApi.deletePartner(id); toast.success('Partner deleted'); onRefresh(); } catch {}
  };

  const onSubmit = async (data: z.infer<typeof partnerSchema>) => {
    try {
      if (editingId) { await siteManagementApi.updatePartner(editingId, data); toast.success('Partner updated'); }
      else { await siteManagementApi.createPartner(data); toast.success('Partner created'); }
      setModalOpen(false); onRefresh();
    } catch {}
  };

  const columns = [
    { key: 'name', label: 'Name' },
    { key: 'website', label: 'Website', render: (v: string) => v ? <a href={v} target="_blank" className="text-blue-600 hover:underline">{v}</a> : '-' },
    { key: 'description', label: 'Description', render: (v: string) => v?.substring(0, 60) + (v?.length > 60 ? '...' : '') || '-' },
    { key: 'isActive', label: 'Active', render: (v: boolean) => <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${v ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>{v ? 'Yes' : 'No'}</span> },
    { key: 'actions', label: 'Actions', render: (_: any, row: Partner) => (
      <div className="flex items-center gap-1">
        <button onClick={() => openEdit(row.id)} className="p-1 text-blue-500 hover:text-blue-700"><HiPencil className="w-4 h-4" /></button>
        <button onClick={() => handleDelete(row.id)} className="p-1 text-red-500 hover:text-red-700"><HiTrash className="w-4 h-4" /></button>
      </div>
    )},
  ];

  return (
    <div>
      <div className="flex justify-end mb-4">
        <button onClick={openAdd} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm"><HiPlus className="w-4 h-4" /> Add Partner</button>
      </div>
      <DataTable columns={columns} data={items} />
      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editingId ? 'Edit Partner' : 'Add Partner'} size="lg">
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input {...form.register('name')} className="input-field w-full" />
            {form.formState.errors.name && <p className="text-red-500 text-xs mt-1">{form.formState.errors.name.message}</p>}
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Logo URL</label>
              <input {...form.register('logoUrl')} className="input-field w-full" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Website</label>
              <input {...form.register('website')} className="input-field w-full" />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea {...form.register('description')} rows={3} className="input-field w-full" />
          </div>
          <div className="flex items-center gap-4">
            <div className="flex items-center gap-2">
              <input type="checkbox" {...form.register('isActive')} className="w-4 h-4" />
              <label className="text-sm font-medium text-gray-700">Active</label>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
              <input type="number" {...form.register('sortOrder')} className="input-field w-24" />
            </div>
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
