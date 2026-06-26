'use client';

import { useState, useEffect } from 'react';
import { HiChartBar, HiDownload, HiUser } from 'react-icons/hi';
import { parentsApi } from '@/lib/api';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { Result, Student } from '@/types';

export default function ParentPerformancePage() {
  const [loading, setLoading] = useState(true);
  const [children, setChildren] = useState<Student[]>([]);
  const [selectedChildId, setSelectedChildId] = useState('');
  const [results, setResults] = useState<Result[]>([]);

  useEffect(() => {
    loadChildren();
  }, []);

  useEffect(() => {
    if (selectedChildId) loadPerformance();
  }, [selectedChildId]);

  const loadChildren = async () => {
    try {
      const res = await parentsApi.getChildren();
      const childList = res.data.data || [];
      setChildren(childList);
      if (childList.length > 0) {
        setSelectedChildId(childList[0].id);
      }
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const loadPerformance = async () => {
    try {
      const res = await parentsApi.getChildPerformance(selectedChildId);
      setResults(res.data.data || []);
    } catch {
    }
  };

  const handleDownload = () => {
    window.print();
  };

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  const selectedChild = children.find((c) => c.id === selectedChildId);
  const chartData = results.reduce<Record<string, { total: number; count: number }>>((acc, r) => {
    const term = `Term ${r.term}`;
    if (!acc[term]) acc[term] = { total: 0, count: 0 };
    acc[term].total += r.score;
    acc[term].count += 1;
    return acc;
  }, {});
  const chartEntries = Object.entries(chartData);

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-heading font-bold text-gray-900">Performance</h1>
          <p className="text-gray-500 text-sm">View your child&apos;s academic performance</p>
        </div>
        <button onClick={handleDownload} className="btn-secondary flex items-center gap-2">
          <HiDownload className="w-4 h-4" /> Download Report
        </button>
      </div>

      {children.length > 1 && (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
          <div className="flex items-center gap-3">
            <HiUser className="w-5 h-5 text-gray-400" />
            <select
              value={selectedChildId}
              onChange={(e) => setSelectedChildId(e.target.value)}
              className="input-field text-sm"
            >
              {children.map((c) => (
                <option key={c.id} value={c.id}>{c.user?.firstName} {c.user?.lastName}</option>
              ))}
            </select>
          </div>
        </div>
      )}

      {selectedChild && (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-bold">
              {selectedChild.user?.firstName?.[0] || '?'}
            </div>
            <div>
              <p className="text-sm font-medium text-gray-900">{selectedChild.user?.firstName} {selectedChild.user?.lastName}</p>
              <p className="text-xs text-gray-500">{selectedChild.program?.name} &middot; {selectedChild.sdmsCode}</p>
            </div>
          </div>
        </div>
      )}

      {chartEntries.length > 0 && (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
          <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4">Performance Overview</h2>
          <div className="flex items-end gap-4 h-40">
            {chartEntries.map(([term, data]) => {
              const avg = Math.round(data.total / data.count);
              const height = Math.max(20, avg);
              return (
                <div key={term} className="flex-1 flex flex-col items-center gap-2">
                  <div className="w-full bg-primary-100 rounded-t-lg relative" style={{ height: `${height}%` }}>
                    <div
                      className="absolute bottom-0 w-full bg-primary-500 rounded-t-lg transition-all"
                      style={{ height: `${avg}%` }}
                    />
                  </div>
                  <span className="text-xs font-medium text-gray-700">{avg}%</span>
                  <span className="text-xs text-gray-500">{term}</span>
                </div>
              );
            })}
          </div>
        </div>
      )}

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div className="p-4 border-b border-gray-200 bg-gray-50">
          <h2 className="text-lg font-heading font-semibold text-gray-900 flex items-center gap-2">
            <HiChartBar className="w-5 h-5 text-primary-600" /> Results
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
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {results.length === 0 ? (
                <tr>
                  <td colSpan={4} className="px-4 py-12 text-center text-gray-500">No results available.</td>
                </tr>
              ) : (
                results.map((r) => (
                  <tr key={r.id} className="hover:bg-gray-50">
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
