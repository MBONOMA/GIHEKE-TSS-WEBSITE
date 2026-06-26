'use client';

import { useState, useEffect } from 'react';
import { HiCurrencyDollar, HiUser, HiCheckCircle, HiClock } from 'react-icons/hi';
import { parentsApi } from '@/lib/api';
import toast from 'react-hot-toast';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import Modal from '@/components/ui/Modal';
import { formatDate, getStatusColor, getStatusLabel } from '@/lib/utils';
import { Fee, Student } from '@/types';

export default function ParentFeesPage() {
  const [loading, setLoading] = useState(true);
  const [children, setChildren] = useState<Student[]>([]);
  const [selectedChildId, setSelectedChildId] = useState('');
  const [fees, setFees] = useState<Fee[]>([]);
  const [payModal, setPayModal] = useState(false);
  const [selectedFee, setSelectedFee] = useState<Fee | null>(null);

  useEffect(() => {
    loadChildren();
  }, []);

  useEffect(() => {
    if (selectedChildId) loadFees();
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

  const loadFees = async () => {
    try {
      const res = await parentsApi.getChildFees(selectedChildId);
      setFees(res.data.data || []);
    } catch {
    }
  };

  const handlePayNow = (fee: Fee) => {
    setSelectedFee(fee);
    setPayModal(true);
  };

  const selectedChild = children.find((c) => c.id === selectedChildId);
  const totalAmount = fees.reduce((s, f) => s + f.amount, 0);
  const totalPaid = fees.reduce((s, f) => s + f.amountPaid, 0);
  const totalBalance = totalAmount - totalPaid;

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">School Fees</h1>
        <p className="text-gray-500 text-sm">View and manage fee payments</p>
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
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
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

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div className="bg-primary-50 rounded-xl p-4 border border-primary-200">
          <p className="text-sm text-primary-600">Total Fees</p>
          <p className="text-2xl font-bold text-primary-700">RWF {totalAmount.toLocaleString()}</p>
        </div>
        <div className="bg-green-50 rounded-xl p-4 border border-green-200">
          <p className="text-sm text-green-600">Total Paid</p>
          <p className="text-2xl font-bold text-green-700">RWF {totalPaid.toLocaleString()}</p>
        </div>
        <div className={`rounded-xl p-4 border ${totalBalance > 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200'}`}>
          <p className={`text-sm ${totalBalance > 0 ? 'text-red-600' : 'text-green-600'}`}>Balance</p>
          <p className={`text-2xl font-bold ${totalBalance > 0 ? 'text-red-700' : 'text-green-700'}`}>
            RWF {totalBalance.toLocaleString()}
          </p>
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div className="p-4 border-b border-gray-200 bg-gray-50">
          <h2 className="text-lg font-heading font-semibold text-gray-900 flex items-center gap-2">
            <HiCurrencyDollar className="w-5 h-5 text-primary-600" /> Fee Breakdown
          </h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="bg-gray-50 border-b border-gray-200">
                <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Fee Type</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Paid</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Balance</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Due Date</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {fees.length === 0 ? (
                <tr>
                  <td colSpan={7} className="px-4 py-12 text-center text-gray-500">No fee records found.</td>
                </tr>
              ) : (
                fees.map((f) => {
                  const balance = f.amount - f.amountPaid;
                  return (
                    <tr key={f.id} className="hover:bg-gray-50">
                      <td className="px-4 py-3 text-sm font-medium text-gray-900">{f.feeType}</td>
                      <td className="px-4 py-3 text-sm text-gray-700">RWF {f.amount.toLocaleString()}</td>
                      <td className="px-4 py-3 text-sm text-gray-700">RWF {f.amountPaid.toLocaleString()}</td>
                      <td className="px-4 py-3 text-sm text-gray-700">RWF {balance.toLocaleString()}</td>
                      <td className="px-4 py-3 text-sm text-gray-700">{f.dueDate ? formatDate(f.dueDate) : '-'}</td>
                      <td className="px-4 py-3">
                        <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${getStatusColor(f.status)}`}>
                          {getStatusLabel(f.status)}
                        </span>
                      </td>
                      <td className="px-4 py-3 text-right">
                        {f.status !== 'paid' && (
                          <button onClick={() => handlePayNow(f)} className="btn-primary text-xs px-3 py-1.5">
                            Pay Now
                          </button>
                        )}
                      </td>
                    </tr>
                  );
                })
              )}
            </tbody>
          </table>
        </div>
      </div>

      <Modal isOpen={payModal} onClose={() => setPayModal(false)} title="Make Payment" size="sm">
        {selectedFee && (
          <div className="space-y-4">
            <div className="p-4 bg-gray-50 rounded-lg space-y-2">
              <div className="flex justify-between text-sm">
                <span className="text-gray-600">Fee Type:</span>
                <span className="font-medium text-gray-900">{selectedFee.feeType}</span>
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-gray-600">Amount Due:</span>
                <span className="font-medium text-gray-900">
                  RWF {(selectedFee.amount - selectedFee.amountPaid).toLocaleString()}
                </span>
              </div>
            </div>
            <p className="text-sm text-gray-500 text-center">
              Mobile money payment integration coming soon. Please visit the school finance office to complete payment.
            </p>
            <button
              onClick={() => toast.success('Payment initiated (demo)')}
              className="btn-primary w-full"
            >
              Proceed to Payment (Demo)
            </button>
          </div>
        )}
      </Modal>
    </div>
  );
}
