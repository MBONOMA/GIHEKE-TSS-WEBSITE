'use client';

import { useState, useEffect, useCallback } from 'react';
import toast from 'react-hot-toast';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { HiPlus, HiPencil, HiTrash } from 'react-icons/hi';
import { usersApi } from '@/lib/api';
import { User } from '@/types';
import DataTable from '@/components/ui/DataTable';
import Modal from '@/components/ui/Modal';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { formatDate } from '@/lib/utils';

const userSchema = z.object({
  firstName: z.string().min(1, 'First name is required'),
  lastName: z.string().min(1, 'Last name is required'),
  email: z.string().email('Invalid email'),
  password: z.string().optional(),
  phone: z.string().optional(),
  role: z.enum(['admin', 'teacher', 'staff']),
  isActive: z.boolean(),
});

type UserFormData = z.infer<typeof userSchema>;

export default function UsersPage() {
  const [loading, setLoading] = useState(true);
  const [users, setUsers] = useState<User[]>([]);
  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);

  const form = useForm<UserFormData>({
    resolver: zodResolver(userSchema),
    defaultValues: { firstName: '', lastName: '', email: '', password: '', phone: '', role: 'teacher', isActive: true },
  });

  const fetchUsers = useCallback(async () => {
    try {
      setLoading(true);
      const res = await usersApi.getAll({ limit: 100 });
      setUsers(res.data.data || []);
    } catch {} finally { setLoading(false); }
  }, []);

  useEffect(() => { fetchUsers(); }, [fetchUsers]);

  const openAdd = () => {
    setEditingId(null);
    form.reset({ firstName: '', lastName: '', email: '', password: '', phone: '', role: 'teacher', isActive: true });
    setModalOpen(true);
  };

  const openEdit = (id: string) => {
    const user = users.find((u) => u.id === id);
    if (!user) return;
    setEditingId(id);
    form.reset({
      firstName: user.firstName,
      lastName: user.lastName,
      email: user.email,
      password: '',
      phone: user.phone || '',
      role: (user.role === 'super_admin' ? 'admin' : user.role) as 'admin' | 'teacher' | 'staff',
      isActive: user.isActive,
    });
    setModalOpen(true);
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure you want to delete this user?')) return;
    try { await usersApi.delete(id); toast.success('User deleted'); fetchUsers(); } catch {}
  };

  const onSubmit = async (data: UserFormData) => {
    try {
      if (editingId) {
        const payload: any = { ...data };
        if (!payload.password) delete payload.password;
        await usersApi.update(editingId, payload);
        toast.success('User updated');
      } else {
        if (!data.password) { toast.error('Password is required for new users'); return; }
        await usersApi.create(data);
        toast.success('User created');
      }
      setModalOpen(false);
      fetchUsers();
    } catch {}
  };

  const columns = [
    { key: 'firstName', label: 'Name', render: (_: any, row: User) => `${row.firstName} ${row.lastName}` },
    { key: 'email', label: 'Email' },
    { key: 'role', label: 'Role', render: (v: string) => (
      <span className="capitalize px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{v.replace(/_/g, ' ')}</span>
    )},
    { key: 'isActive', label: 'Status', render: (v: boolean) => (
      <span className={`px-2 py-1 rounded-full text-xs font-medium ${v ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
        {v ? 'Active' : 'Inactive'}
      </span>
    )},
    { key: 'actions', label: 'Actions', render: (_: any, row: User) => (
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
          <h1 className="text-2xl font-heading font-bold text-gray-900">User Management</h1>
          <p className="text-gray-500 mt-1">Manage system users</p>
        </div>
        <button onClick={openAdd} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm bg-primary-600 text-white hover:bg-primary-700">
          <HiPlus className="w-4 h-4" /> Add User
        </button>
      </div>

      <DataTable columns={columns} data={users} loading={loading} />

      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editingId ? 'Edit User' : 'Add User'} size="lg">
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">First Name</label>
              <input {...form.register('firstName')} className="input-field w-full" />
              {form.formState.errors.firstName && <p className="text-red-500 text-xs mt-1">{form.formState.errors.firstName.message}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
              <input {...form.register('lastName')} className="input-field w-full" />
              {form.formState.errors.lastName && <p className="text-red-500 text-xs mt-1">{form.formState.errors.lastName.message}</p>}
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input type="email" {...form.register('email')} className="input-field w-full" />
              {form.formState.errors.email && <p className="text-red-500 text-xs mt-1">{form.formState.errors.email.message}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Phone</label>
              <input {...form.register('phone')} className="input-field w-full" />
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Password {editingId && <span className="text-gray-400 font-normal">(leave blank to keep)</span>}
              </label>
              <input type="password" {...form.register('password')} className="input-field w-full" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Role</label>
              <select {...form.register('role')} className="input-field w-full">
                <option value="admin">Admin</option>
                <option value="teacher">Teacher</option>
                <option value="staff">Staff</option>
              </select>
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
