'use client';

import { useState, useEffect } from 'react';
import { HiUser, HiAcademicCap, HiClipboardCheck, HiCurrencyDollar, HiChartBar } from 'react-icons/hi';
import { parentsApi } from '@/lib/api';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { Parent, Student, Result, Attendance, Fee } from '@/types';

interface ChildData {
  student: Student;
  performance?: Result[];
  attendance?: Attendance[];
  fees?: Fee[];
}

export default function ParentDashboardPage() {
  const [loading, setLoading] = useState(true);
  const [parent, setParent] = useState<Parent | null>(null);
  const [children, setChildren] = useState<ChildData[]>([]);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [profileRes, childrenRes] = await Promise.all([
        parentsApi.getMyProfile(),
        parentsApi.getChildren(),
      ]);
      setParent(profileRes.data.data);
      const childList = childrenRes.data.data || [];
      const childDataPromises = childList.map(async (c: any) => {
        const child: ChildData = { student: c };
        try {
          const [perf, att, fees] = await Promise.all([
            parentsApi.getChildPerformance(c.id),
            parentsApi.getChildAttendance(c.id),
            parentsApi.getChildFees(c.id),
          ]);
          child.performance = perf.data.data || [];
          child.attendance = att.data.data || [];
          child.fees = fees.data.data || [];
        } catch {}
        return child;
      });
      setChildren(await Promise.all(childDataPromises));
    } catch {
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">
          Welcome back, {parent?.user?.firstName || 'Parent'}
        </h1>
        <p className="text-gray-500 text-sm">
          {children.length > 0
            ? `You have ${children.length} child${children.length > 1 ? 'ren' : ''} enrolled`
            : 'No children linked to your account'}
        </p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {children.length === 0 ? (
          <div className="col-span-full bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <HiUser className="w-12 h-12 text-gray-300 mx-auto mb-3" />
            <p className="text-gray-500">No children linked to your account. Please contact the school.</p>
          </div>
        ) : (
          children.map((child) => {
            const presentCount = child.attendance?.filter((a) => a.status === 'present').length || 0;
            const totalAtt = child.attendance?.length || 1;
            const attPercent = Math.round((presentCount / totalAtt) * 100);
            const unpaidFees = child.fees?.filter((f) => f.status === 'unpaid' || f.status === 'overdue').length || 0;
            const avgScore = child.performance?.length
              ? Math.round(child.performance.reduce((s, r) => s + r.score, 0) / child.performance.length)
              : 0;

            return (
              <div key={child.student.id} className="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div className="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                  <div className="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-bold">
                    {child.student.user?.firstName?.[0] || '?'}
                  </div>
                  <div>
                    <h3 className="text-sm font-heading font-semibold text-gray-900">
                      {child.student.user?.firstName} {child.student.user?.lastName}
                    </h3>
                    <p className="text-xs text-gray-500">{child.student.program?.name || 'No program'}</p>
                  </div>
                </div>
                <div className="grid grid-cols-3 gap-3">
                  <div className="text-center">
                    <p className="text-lg font-bold text-primary-600">{avgScore}%</p>
                    <p className="text-xs text-gray-500">Avg Score</p>
                  </div>
                  <div className="text-center">
                    <p className={`text-lg font-bold ${attPercent >= 75 ? 'text-green-600' : 'text-red-600'}`}>{attPercent}%</p>
                    <p className="text-xs text-gray-500">Attendance</p>
                  </div>
                  <div className="text-center">
                    <p className={`text-lg font-bold ${unpaidFees > 0 ? 'text-red-600' : 'text-green-600'}`}>
                      {unpaidFees > 0 ? `${unpaidFees}` : 'OK'}
                    </p>
                    <p className="text-xs text-gray-500">Fees Due</p>
                  </div>
                </div>
              </div>
            );
          })
        )}
      </div>
    </div>
  );
}
