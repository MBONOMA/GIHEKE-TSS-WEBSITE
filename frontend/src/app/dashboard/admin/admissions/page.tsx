'use client';

import { useState, useEffect, useCallback } from 'react';
import toast from 'react-hot-toast';
import {
  HiDocumentText, HiFilter, HiSearch, HiDownload, HiCalendar, HiChevronDown,
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
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [toggleLoading, setToggleLoading] = useState(false);

  const fetchData = useCallback(async () => {
    try {
      setLoading(true);
      const params: any = { page, limit: 20 };
      if (statusFilter) params.status = statusFilter;
      if (search) params.search = search;
      const [appsRes, settingsRes] = await Promise.all([
        admissionsApi.getAll(params),
        admissionsApi.getSettings(),
      ]);
      setApplications(appsRes.data.data || []);
      setTotalPages(appsRes.data.meta?.totalPages || 1);
      setSettings(settingsRes.data.data);
    } catch {
    } finally {
      setLoading(false);
    }
  }, [page, statusFilter, search]);

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

      <div className="flex flex-wrap items-center gap-3">
        <div className="relative flex-1 max-w-sm">
          <HiSearch className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
          <input
            type="text"
            placeholder="Search by name or email..."
            value={search}
            onChange={(e) => { setSearch(e.target.value); setPage(1); }}
            className="input-field pl-10 w-full"
          />
        </div>
        <div className="flex items-center gap-2">
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
