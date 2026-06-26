'use client';

import { useState, useEffect, useCallback, useRef } from 'react';
import toast from 'react-hot-toast';
import {
  HiDocumentText, HiFilter, HiSearch, HiDownload, HiCalendar, HiChevronDown, HiX, HiClock, HiTrendingUp,
} from 'react-icons/hi';
import { admissionsApi } from '@/lib/api';
import { Application, ApplicationStatus, AdmissionSettings } from '@/types';
import DataTable from '@/components/ui/DataTable';
import Modal from '@/components/ui/Modal';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { formatDate, formatDateTime, getStatusColor, getStatusLabel, APPLICATION_STATUS_OPTIONS } from '@/lib/utils';

export default function AdmissionsPage() {
  const [loading, setLoading] = useState(true);
  const [applications, setApplications] = useState<Application[]>([]);
  const [settings, setSettings] = useState<AdmissionSettings | null>(null);
  const [selectedApp, setSelectedApp] = useState<Application | null>(null);
  const [statusHistory, setStatusHistory] = useState<any[]>([]);
  const [detailOpen, setDetailOpen] = useState(false);
  const [statusFilter, setStatusFilter] = useState<string>('');
  const [search, setSearch] = useState('');
  const [suggestions, setSuggestions] = useState<string[]>([]);
  const [recentSearches, setRecentSearches] = useState<string[]>([]);
  const [trendingSearches] = useState<string[]>(['New Applicants', 'Pending Review', 'Qualified', 'Interview', 'Accepted']);
  const [showDropdown, setShowDropdown] = useState(false);
  const [noResults, setNoResults] = useState(false);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [toggleLoading, setToggleLoading] = useState(false);
  const debounceTimer = useRef<NodeJS.Timeout | null>(null);
  const searchRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const stored = localStorage.getItem('adminAdmissionsRecent');
    if (stored) {
      try {
        setRecentSearches(JSON.parse(stored).slice(0, 5));
      } catch {}
    }
  }, []);

  const saveRecentSearch = (query: string) => {
    const updated = [query, ...recentSearches.filter(s => s !== query)].slice(0, 5);
    setRecentSearches(updated);
    localStorage.setItem('adminAdmissionsRecent', JSON.stringify(updated));
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
      const params: any = { page, limit: 20 };
      if (statusFilter) params.status = statusFilter;
      if (search.trim()) params.search = search.trim();
      const [appsRes, settingsRes] = await Promise.all([
        admissionsApi.getAll(params),
        admissionsApi.getSettings(),
      ]);
      const items = appsRes.data.data || [];
      setApplications(items);
      setTotalPages(appsRes.data.meta?.totalPages || 1);
      setSettings(settingsRes.data.data);
      setNoResults(items.length === 0 && search.trim().length > 0);
    } catch {
      setApplications([]);
      if (search.trim()) setNoResults(true);
    } finally {
      setLoading(false);
    }
  }, [page, statusFilter, search]);

  useEffect(() => {
    if (debounceTimer.current) clearTimeout(debounceTimer.current);
    debounceTimer.current = setTimeout(() => {
      setPage(1);
      fetchData();
    }, 300);
    return () => {
      if (debounceTimer.current) clearTimeout(debounceTimer.current);
    };
  }, [search, statusFilter, fetchData]);

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

  useEffect(() => { fetchData(); }, [fetchData]);

  const toggleAdmissions = async () => {
    if (!confirm(`Are you sure you want to ${settings?.isOpen ? 'CLOSE' : 'OPEN'} admissions?`)) return;
    try {
      setToggleLoading(true);
      const res = await admissionsApi.updateSettings({ isOpen: !settings?.isOpen });
      setSettings(res.data.data);
      toast.success(`Admissions ${settings?.isOpen ? 'closed' : 'opened'} successfully`);
    } catch {} finally { setToggleLoading(false); }
  };

  const openDetail = async (app: Application) => {
    setSelectedApp(app);
    setDetailOpen(true);
    try {
      const histRes = await admissionsApi.getHistory(app.id);
      setStatusHistory(histRes.data.data || []);
    } catch { setStatusHistory([]); }
  };

  const changeStatus = async (applicationId: string, newStatus: ApplicationStatus, notes?: string) => {
    try {
      await admissionsApi.updateStatus(applicationId, { status: newStatus, notes });
      toast.success(`Status updated to ${getStatusLabel(newStatus)}`);
      fetchData();
      if (selectedApp?.id === applicationId) {
        setSelectedApp((prev) => prev ? { ...prev, status: newStatus } : null);
      }
    } catch {}
  };

  const exportCSV = async () => {
    try {
      const res = await admissionsApi.exportData();
      const url = window.URL.createObjectURL(new Blob([res.data]));
      const a = document.createElement('a');
      a.href = url;
      a.download = 'admissions-export.csv';
      a.click();
      window.URL.revokeObjectURL(url);
      toast.success('Export downloaded');
    } catch {}
  };

  const stats = {
    total: applications.length,
    pending: applications.filter((a) => a.status === 'pending').length,
    accepted: applications.filter((a) => a.status === 'accepted').length,
    rejected: applications.filter((a) => a.status === 'rejected').length,
    interviewed: applications.filter((a) => a.status === 'interview_scheduled').length,
  };

  const columns = [
    { key: 'firstName', label: 'Name', render: (_: any, row: Application) => `${row.firstName} ${row.lastName}` },
    { key: 'program', label: 'Program', render: (_: any, row: Application) => row.program?.name || row.programId || '-' },
    { key: 'status', label: 'Status', render: (v: ApplicationStatus) => (
      <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(v)}`}>{getStatusLabel(v)}</span>
    )},
    { key: 'createdAt', label: 'Date', render: (v: string) => formatDate(v) },
    { key: 'actions', label: 'Actions', render: (_: any, row: Application) => (
      <button onClick={() => openDetail(row)} className="text-primary-600 hover:text-primary-800 text-sm font-medium">View</button>
    )},
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-heading font-bold text-gray-900">Admissions Management</h1>
          <p className="text-gray-500 mt-1">Manage student applications</p>
        </div>
        <div className="flex items-center gap-3">
          <button onClick={exportCSV} className="btn-outline flex items-center gap-2 px-4 py-2 rounded-lg text-sm border border-gray-300 hover:bg-gray-50">
            <HiDownload className="w-4 h-4" /> Export CSV
          </button>
          <button
            onClick={toggleAdmissions}
            disabled={toggleLoading}
            className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors ${
              settings?.isOpen ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'
            }`}
          >
            {settings?.isOpen ? <HiDocumentText className="w-4 h-4" /> : <HiDocumentText className="w-4 h-4" />}
            {settings?.isOpen ? 'CLOSE ADMISSIONS' : 'OPEN ADMISSIONS'}
          </button>
        </div>
      </div>

      {/* Search Bar - Under Navigation */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div className="relative max-w-3xl" ref={searchRef}>
          <HiSearch className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" />
          <input
            type="text"
            placeholder="Search by name or email..."
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

        {/* Status Filter */}
        <div className="mt-3 flex items-center gap-2">
          <HiFilter className="w-5 h-5 text-gray-400" />
          <select
            value={statusFilter}
            onChange={(e) => { setStatusFilter(e.target.value); setPage(1); }}
            className="input-field"
          >
            <option value="">All Status</option>
            {APPLICATION_STATUS_OPTIONS.map((opt) => (
              <option key={opt.value} value={opt.value}>{opt.label}</option>
            ))}
          </select>
          {statusFilter && (
            <button
              onClick={() => { setStatusFilter(''); setPage(1); }}
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
            {['New Applicants', 'Pending Review', 'Qualified'].map((cat) => (
              <button
                key={cat}
                onClick={() => setStatusFilter('')}
                className="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-primary-300 hover:text-primary-700 transition-colors shadow-sm"
              >
                {cat}
              </button>
            ))}
          </div>
          <button
            onClick={() => { setSearch(''); setStatusFilter(''); }}
            className="mt-6 text-primary-600 font-medium text-sm hover:underline"
          >
            Clear all filters
          </button>
        </div>
      )}

      <div className="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
          <p className="text-2xl font-bold text-gray-900">{settings?.currentApplications ?? stats.total}</p>
          <p className="text-sm text-gray-500">Total Applications</p>
        </div>
        <div className="bg-yellow-50 rounded-xl shadow-sm border border-yellow-200 p-4">
          <p className="text-2xl font-bold text-yellow-700">{stats.pending}</p>
          <p className="text-sm text-yellow-600">Pending</p>
        </div>
        <div className="bg-purple-50 rounded-xl shadow-sm border border-purple-200 p-4">
          <p className="text-2xl font-bold text-purple-700">{stats.interviewed}</p>
          <p className="text-sm text-purple-600">Interview Scheduled</p>
        </div>
        <div className="bg-green-50 rounded-xl shadow-sm border border-green-200 p-4">
          <p className="text-2xl font-bold text-green-700">{stats.accepted}</p>
          <p className="text-sm text-green-600">Accepted</p>
        </div>
        <div className="bg-red-50 rounded-xl shadow-sm border border-red-200 p-4">
          <p className="text-2xl font-bold text-red-700">{stats.rejected}</p>
          <p className="text-sm text-red-600">Rejected</p>
        </div>
      </div>

      <DataTable
        columns={columns}
        data={applications}
        loading={loading}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
      />

      <Modal isOpen={detailOpen} onClose={() => setDetailOpen(false)} title="Application Detail" size="xl">
        {selectedApp && (
          <div className="space-y-6">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <p className="text-xs text-gray-500 uppercase">Full Name</p>
                <p className="font-medium">{selectedApp.firstName} {selectedApp.lastName}</p>
              </div>
              <div>
                <p className="text-xs text-gray-500 uppercase">Email</p>
                <p className="font-medium">{selectedApp.email}</p>
              </div>
              <div>
                <p className="text-xs text-gray-500 uppercase">Phone</p>
                <p className="font-medium">{selectedApp.phone}</p>
              </div>
              <div>
                <p className="text-xs text-gray-500 uppercase">Program</p>
                <p className="font-medium">{selectedApp.program?.name || selectedApp.programId}</p>
              </div>
              <div>
                <p className="text-xs text-gray-500 uppercase">Date of Birth</p>
                <p className="font-medium">{formatDate(selectedApp.dateOfBirth)}</p>
              </div>
              <div>
                <p className="text-xs text-gray-500 uppercase">Gender</p>
                <p className="font-medium capitalize">{selectedApp.gender}</p>
              </div>
              <div>
                <p className="text-xs text-gray-500 uppercase">Previous School</p>
                <p className="font-medium">{selectedApp.previousSchool || '-'}</p>
              </div>
              <div>
                <p className="text-xs text-gray-500 uppercase">Applied On</p>
                <p className="font-medium">{formatDateTime(selectedApp.createdAt)}</p>
              </div>
              <div className="col-span-2">
                <p className="text-xs text-gray-500 uppercase">Address</p>
                <p className="font-medium">{selectedApp.address}</p>
              </div>
              <div className="col-span-2">
                <p className="text-xs text-gray-500 uppercase">Academic Background</p>
                <p className="font-medium">{selectedApp.academicBackground || '-'}</p>
              </div>
            </div>

            <div>
              <p className="text-xs text-gray-500 uppercase mb-2">Current Status</p>
              <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(selectedApp.status)}`}>
                {getStatusLabel(selectedApp.status)}
              </span>
            </div>

            <div>
              <p className="text-sm font-medium text-gray-700 mb-2">Change Status</p>
              <div className="flex flex-wrap gap-2">
                {APPLICATION_STATUS_OPTIONS.map((opt) => (
                  <button
                    key={opt.value}
                    onClick={() => changeStatus(selectedApp.id, opt.value as ApplicationStatus)}
                    disabled={selectedApp.status === opt.value}
                    className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-colors ${
                      selectedApp.status === opt.value
                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'
                    }`}
                  >
                    {opt.label}
                  </button>
                ))}
              </div>
            </div>

            {selectedApp.interviewDate && (
              <div>
                <p className="text-xs text-gray-500 uppercase">Interview Date</p>
                <p className="font-medium flex items-center gap-2">
                  <HiCalendar className="w-4 h-4 text-primary-600" />
                  {formatDateTime(selectedApp.interviewDate)}
                </p>
              </div>
            )}

            <div>
              <p className="text-sm font-medium text-gray-700 mb-2">Status History</p>
              {statusHistory.length > 0 ? (
                <div className="space-y-3">
                  {statusHistory.map((hist: any) => (
                    <div key={hist.id} className="flex items-start gap-3">
                      <div className="w-2 h-2 rounded-full bg-primary-500 mt-2 flex-shrink-0" />
                      <div>
                        <p className="text-sm">
                          <span className="font-medium">{getStatusLabel(hist.fromStatus)}</span>
                          {' → '}
                          <span className="font-medium">{getStatusLabel(hist.toStatus)}</span>
                        </p>
                        {hist.notes && <p className="text-xs text-gray-500">{hist.notes}</p>}
                        <p className="text-xs text-gray-400">{formatDateTime(hist.createdAt)}</p>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-sm text-gray-400">No history recorded</p>
              )}
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
