'use client';

import { useState, useEffect, useRef } from 'react';
import { HiFilter, HiPrinter, HiAcademicCap } from 'react-icons/hi';
import { studentsApi } from '@/lib/api';
import { useAuth } from '@/hooks/useAuth';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { Result } from '@/types';

export default function StudentResultsPage() {
  const { user } = useAuth();
  const [loading, setLoading] = useState(true);
  const [results, setResults] = useState<Result[]>([]);
  const [filteredResults, setFilteredResults] = useState<Result[]>([]);
  const [studentId, setStudentId] = useState<string>('');
  const [filterTerm, setFilterTerm] = useState<string>('');
  const [filterYear, setFilterYear] = useState<string>('');
  const printRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    loadProfile();
  }, []);

  useEffect(() => {
    applyFilters();
  }, [results, filterTerm, filterYear]);

  const loadProfile = async () => {
    try {
      const res = await studentsApi.getMyProfile();
      const sid = res.data.data?.id || res.data.data?.studentId || '';
      setStudentId(sid);
      if (sid) {
        const resultsRes = await studentsApi.getResults(sid);
        setResults(resultsRes.data.data || []);
      }
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const applyFilters = () => {
    let filtered = [...results];
    if (filterTerm) filtered = filtered.filter((r) => r.term.toString() === filterTerm);
    if (filterYear) filtered = filtered.filter((r) => r.academicYear === filterYear);
    setFilteredResults(filtered);
  };

  const handlePrint = () => {
    window.print();
  };

  const years = [...new Set(results.map((r) => r.academicYear))];
  const terms = [...new Set(results.map((r) => r.term))];

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-heading font-bold text-gray-900">My Results</h1>
          <p className="text-gray-500 text-sm">View your academic performance</p>
        </div>
        <button onClick={handlePrint} className="btn-secondary flex items-center gap-2">
          <HiPrinter className="w-4 h-4" /> Print
        </button>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div className="flex flex-wrap items-center gap-4">
          <div className="flex items-center gap-2">
            <HiFilter className="w-5 h-5 text-gray-400" />
            <span className="text-sm text-gray-600">Filters:</span>
          </div>
          <select
            value={filterTerm}
            onChange={(e) => setFilterTerm(e.target.value)}
            className="input-field text-sm"
          >
            <option value="">All Terms</option>
            {terms.map((t) => (
              <option key={t} value={t}>Term {t}</option>
            ))}
          </select>
          <select
            value={filterYear}
            onChange={(e) => setFilterYear(e.target.value)}
            className="input-field text-sm"
          >
            <option value="">All Years</option>
            {years.map((y) => (
              <option key={y} value={y}>{y}</option>
            ))}
          </select>
        </div>
      </div>

      <div ref={printRef} className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div className="p-4 border-b border-gray-200 bg-gray-50 print:bg-white">
          <h2 className="text-lg font-heading font-semibold text-gray-900 flex items-center gap-2">
            <HiAcademicCap className="w-5 h-5 text-primary-600" /> Academic Records
          </h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="bg-gray-50 border-b border-gray-200">
                <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Subject</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Score</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Grade</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Term</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Academic Year</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {filteredResults.length === 0 ? (
                <tr>
                  <td colSpan={5} className="px-4 py-12 text-center text-gray-500">No results found.</td>
                </tr>
              ) : (
                filteredResults.map((r) => (
                  <tr key={r.id} className="hover:bg-gray-50 transition-colors">
                    <td className="px-4 py-3 text-sm font-medium text-gray-900">{r.subject}</td>
                    <td className="px-4 py-3 text-sm text-gray-700">{r.score}</td>
                    <td className="px-4 py-3">
                      <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${
                        r.grade === 'A' ? 'bg-green-100 text-green-800' :
                        r.grade === 'B' ? 'bg-blue-100 text-blue-800' :
                        r.grade === 'C' ? 'bg-yellow-100 text-yellow-800' :
                        'bg-red-100 text-red-800'
                      }`}>{r.grade}</span>
                    </td>
                    <td className="px-4 py-3 text-sm text-gray-700">Term {r.term}</td>
                    <td className="px-4 py-3 text-sm text-gray-700">{r.academicYear}</td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
