'use client';

import { useState, useEffect, useRef, useCallback } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import toast from 'react-hot-toast';
import { HiPlus, HiPencil, HiTrash, HiSearch, HiX, HiClock, HiTrendingUp, HiFilter } from 'react-icons/hi';
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

const RECENT_SEARCHES_ADMIN = ['Grade 10 Notes', 'Exam Papers', 'Syllabus 2024', 'Assignment PDF', 'Textbook Physics'];
const TRENDING_SEARCHES_ADMIN = ['New Uploads', 'Pending Review', 'Deleted Files', 'Archived Materials', 'Recent Downloads'];

export default function ElearningPage() {
  const [loading, setLoading] = useState(true);
  const [materials, setMaterials] = useState<ElearningMaterial[]>([]);
  const [programs, setPrograms] = useState<Program[]>([]);
  const [search, setSearch] = useState('');
  const [suggestions, setSuggestions] = useState<string[]>([]);
  const [recentSearches, setRecentSearches] = useState<string[]>([]);
  const [trendingSearches] = useState<string[]>(TRENDING_SEARCHES_ADMIN);
  const [showDropdown, setShowDropdown] = useState(false);
  const [noResults, setNoResults] = useState(false);
  const [programFilter, setProgramFilter] = useState('');
  const [showFilters, setShowFilters] = useState(false);
  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [uploading, setUploading] = useState(false);
  const debounceTimer = useRef<NodeJS.Timeout | null>(null);
  const searchRef = useRef<HTMLDivElement>(null);

  const form = useForm<z.infer<typeof materialSchema>>({
    resolver: zodResolver(materialSchema),
    defaultValues: { title: '', description: '', programId: '', category: '', subject: '', year: new Date().getFullYear(), isPublic: false },
  });

  useEffect(() => {
    const stored = localStorage.getItem('adminRecentSearches');
    if (stored) {
      try {
        setRecentSearches(JSON.parse(stored).slice(0, 5));
      } catch {}
    }
  }, []);

  const saveRecentSearch = (query: string) => {
    const updated = [query, ...recentSearches.filter(s => s !== query)].slice(0, 5);
    setRecentSearches(updated);
    localStorage.setItem('adminRecentSearches', JSON.stringify(updated));
  };

  const highlightMatch = (text: string, query: string) => {
    if (!query) return text;
    const index = text.toLowerCase().indexOf(query.toLowerCase());
    if (index === -1) return text;
    const before = text.slice(0, index);
    const match = text.slice(index, index + query.length);
    const after = text.slice(index + query.length);
    return (
      <span>
        {before}<strong className="text-primary-700">{match}</strong>{after}
      </span>
    );
  };

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
      const items = matRes.data.data || [];
      setMaterials(items);
      setNoResults(items.length === 0 && search.length > 0);
      setPrograms(progRes.data.data || []);
    } catch {
      setMaterials([]);
      setNoResults(search.length > 0);
    } finally {
      setLoading(false);
    }
  }, [search, programFilter]);

  useEffect(() => { fetchData(); }, [fetchData]);

  useEffect(() => {
    if (debounceTimer.current) clearTimeout(debounceTimer.current);
    debounceTimer.current = setTimeout(() => {
      fetchData();
    }, 300);
    return () => {
      if (debounceTimer.current) clearTimeout(debounceTimer.current);
    };
  }, [search, fetchData]);

  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (searchRef.current && !searchRef.current.contains(event.target as Node)) {
        setShowDropdown(false);
      }
    }
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setSearch(value);
    if (value.trim()) {
      const filtered = [
        ...recentSearches.filter(s => s.toLowerCase().includes(value.toLowerCase())),
        ...trendingSearches.filter(s => s.toLowerCase().includes(value.toLowerCase()))
      ].slice(0, 5);
      setSuggestions(filtered);
      setShowDropdown(true);
    } else {
      setSuggestions([]);
      setShowDropdown(true);
    }
  };

  const handleInputFocus = () => {
    setShowDropdown(true);
  };

  const handleClear = () => {
    setSearch('');
    setSuggestions([]);
    setShowDropdown(false);
    setNoResults(false);
  };

  const handleSuggestionClick = (suggestion: string) => {
    setSearch(suggestion);
    saveRecentSearch(suggestion);
    setShowDropdown(false);
  };

  const handleRecentClick = (recent: string) => {
    setSearch(recent);
    saveRecentSearch(recent);
    setShowDropdown(false);
  };

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

      {/* Search Bar - Under Navigation */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div className="relative max-w-3xl" ref={searchRef}>
          <HiSearch className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" />
          <input
            type="text"
            placeholder="Search materials..."
            value={search}
            onChange={handleInputChange}
            onFocus={handleInputFocus}
            className={`input-field pl-12 pr-10 w-full text-base transition-all duration-300 ${showDropdown ? 'ring-2 ring-primary-500 border-primary-500' : ''}`}
          />
          {search && (
            <button
              type="button"
              onClick={handleClear}
              className="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 transition-colors"
              aria-label="Clear search"
            >
              <HiX className="w-4 h-4" />
            </button>
          )}

          {showDropdown && (
            <div className="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 mt-2 max-h-96 overflow-y-auto">
              {suggestions.length > 0 ? (
                <div className="p-2">
                  {suggestions.map((suggestion, idx) => (
                    <button
                      key={idx}
                      type="button"
                      onClick={() => handleSuggestionClick(suggestion)}
                      className="w-full text-left px-4 py-3 hover:bg-primary-50 rounded-lg text-sm text-gray-700 transition-colors"
                    >
                      {highlightMatch(suggestion, search)}
                    </button>
                  ))}
                </div>
              ) : search.length === 0 ? (
                <div>
                  <div className="p-3 border-b border-gray-100">
                    <div className="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                      <HiClock className="w-4 h-4" /> Recent Searches
                    </div>
                    {recentSearches.length > 0 ? (
                      <div className="flex flex-wrap gap-2">
                        {recentSearches.map((recent, idx) => (
                          <button
                            key={idx}
                            type="button"
                            onClick={() => handleRecentClick(recent)}
                            className="px-3 py-1.5 bg-gray-100 hover:bg-primary-100 hover:text-primary-700 rounded-full text-xs text-gray-700 transition-colors"
                          >
                            {recent}
                          </button>
                        ))}
                      </div>
                    ) : (
                      <p className="text-xs text-gray-400">No recent searches</p>
                    )}
                  </div>
                  <div className="p-3">
                    <div className="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                      <HiTrendingUp className="w-4 h-4" /> Trending Searches
                    </div>
                    <div className="flex flex-wrap gap-2">
                      {trendingSearches.map((trending, idx) => (
                        <button
                          key={idx}
                          type="button"
                          onClick={() => handleSuggestionClick(trending)}
                          className="px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-orange-700 rounded-full text-xs font-medium transition-colors"
                        >
                          {trending}
                        </button>
                      ))}
                    </div>
                  </div>
                </div>
              ) : null}
            </div>
          )}
        </div>

        {/* Program Filter */}
        <div className="mt-3 flex items-center gap-2">
          <select value={programFilter} onChange={(e) => setProgramFilter(e.target.value)} className="input-field">
            <option value="">All Programs</option>
            {programs.map((p) => <option key={p.id} value={p.id}>{p.name}</option>)}
          </select>
          {programFilter && (
            <button
              onClick={() => setProgramFilter('')}
              className="text-sm text-primary-600 font-medium hover:underline"
            >
              Clear
            </button>
          )}
        </div>
      </div>

      {noResults && !loading && (
        <div className="text-center py-12 bg-white rounded-xl border border-gray-200">
          <HiSearch className="w-16 h-16 text-gray-300 mx-auto mb-4" />
          <h3 className="text-lg font-heading font-semibold text-gray-500 mb-2">
            No results found for &ldquo;{search}&rdquo;
          </h3>
          <p className="text-gray-400 text-sm max-w-md mx-auto mb-6">
            Try browsing one of our popular categories below or adjust your search.
          </p>
          <div className="flex flex-wrap justify-center gap-3">
            {['Lesson Notes', 'Past Papers', 'Assignments'].map((cat) => (
              <button
                key={cat}
                onClick={() => { setProgramFilter(''); }}
                className="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-primary-300 hover:text-primary-700 transition-colors shadow-sm"
              >
                {cat}
              </button>
            ))}
          </div>
          <button
            onClick={() => { setSearch(''); setProgramFilter(''); }}
            className="mt-6 text-primary-600 font-medium text-sm hover:underline"
          >
            Clear all filters
          </button>
        </div>
      )}

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
