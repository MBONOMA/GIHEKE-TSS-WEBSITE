'use client';

import { useState } from 'react';
import toast from 'react-hot-toast';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { HiSave, HiKey } from 'react-icons/hi';
import { authApi } from '@/lib/api';
import { SCHOOL_INFO, STAFF } from '@/lib/constants';

const profileSchema = z.object({
  firstName: z.string().min(1, 'First name is required'),
  lastName: z.string().min(1, 'Last name is required'),
  email: z.string().email('Invalid email'),
});

const passwordSchema = z.object({
  currentPassword: z.string().min(1, 'Current password is required'),
  newPassword: z.string().min(6, 'New password must be at least 6 characters'),
  confirmPassword: z.string().min(1, 'Confirm password is required'),
}).refine((data) => data.newPassword === data.confirmPassword, {
  message: 'Passwords do not match',
  path: ['confirmPassword'],
});

const smtpSchema = z.object({
  host: z.string().optional(),
  port: z.coerce.number().optional(),
  username: z.string().optional(),
  password: z.string().optional(),
  fromEmail: z.string().optional(),
});

export default function SettingsPage() {
  const [savingProfile, setSavingProfile] = useState(false);
  const [savingPassword, setSavingPassword] = useState(false);
  const [savingSmtp, setSavingSmtp] = useState(false);

  const profileForm = useForm<z.infer<typeof profileSchema>>({
    resolver: zodResolver(profileSchema),
    defaultValues: { firstName: '', lastName: '', email: '' },
  });

  const passwordForm = useForm<z.infer<typeof passwordSchema>>({
    resolver: zodResolver(passwordSchema),
    defaultValues: { currentPassword: '', newPassword: '', confirmPassword: '' },
  });

  const smtpForm = useForm<z.infer<typeof smtpSchema>>({
    resolver: zodResolver(smtpSchema),
    defaultValues: { host: 'smtp.gmail.com', port: 587, username: '', password: '', fromEmail: '' },
  });

  const onProfileSubmit = async (data: z.infer<typeof profileSchema>) => {
    try {
      setSavingProfile(true);
      toast.success('Profile updated (simulated)');
    } catch {} finally { setSavingProfile(false); }
  };

  const onPasswordSubmit = async (data: z.infer<typeof passwordSchema>) => {
    try {
      setSavingPassword(true);
      await authApi.changePassword({ currentPassword: data.currentPassword, newPassword: data.newPassword });
      toast.success('Password changed');
      passwordForm.reset();
    } catch {} finally { setSavingPassword(false); }
  };

  const onSmtpSubmit = async (data: z.infer<typeof smtpSchema>) => {
    try {
      setSavingSmtp(true);
      toast.success('SMTP settings saved (simulated)');
    } catch {} finally { setSavingSmtp(false); }
  };

  return (
    <div className="space-y-6 max-w-3xl">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">Settings</h1>
        <p className="text-gray-500 mt-1">Manage system settings</p>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4">School Information</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <p className="text-xs text-gray-500 uppercase">School Name</p>
            <p className="font-medium">{SCHOOL_INFO.name}</p>
          </div>
          <div>
            <p className="text-xs text-gray-500 uppercase">Full Name</p>
            <p className="font-medium">{SCHOOL_INFO.fullName}</p>
          </div>
          <div>
            <p className="text-xs text-gray-500 uppercase">Motto</p>
            <p className="font-medium">{SCHOOL_INFO.motto}</p>
          </div>
          <div>
            <p className="text-xs text-gray-500 uppercase">Slogan</p>
            <p className="font-medium">{SCHOOL_INFO.slogan}</p>
          </div>
          <div>
            <p className="text-xs text-gray-500 uppercase">Email</p>
            <p className="font-medium">{SCHOOL_INFO.email}</p>
          </div>
          <div>
            <p className="text-xs text-gray-500 uppercase">Phone</p>
            <p className="font-medium">{SCHOOL_INFO.phone}</p>
          </div>
          <div className="col-span-2">
            <p className="text-xs text-gray-500 uppercase">Location</p>
            <p className="font-medium">{SCHOOL_INFO.location}</p>
          </div>
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4">Staff Directory</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {Object.entries(STAFF).map(([key, member]) => (
            <div key={key} className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
              <div className="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 font-semibold">
                {member.name.charAt(0)}
              </div>
              <div>
                <p className="text-sm font-medium">{member.name}</p>
                <p className="text-xs text-gray-500">{member.position}</p>
                <p className="text-xs text-gray-400">{member.phone}</p>
              </div>
            </div>
          ))}
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4">Update Profile</h2>
        <form onSubmit={profileForm.handleSubmit(onProfileSubmit)} className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">First Name</label>
              <input {...profileForm.register('firstName')} className="input-field w-full" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
              <input {...profileForm.register('lastName')} className="input-field w-full" />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" {...profileForm.register('email')} className="input-field w-full" />
          </div>
          <button type="submit" disabled={savingProfile} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50">
            <HiSave className="w-4 h-4" /> {savingProfile ? 'Saving...' : 'Save Profile'}
          </button>
        </form>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4">Change Password</h2>
        <form onSubmit={passwordForm.handleSubmit(onPasswordSubmit)} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
            <input type="password" {...passwordForm.register('currentPassword')} className="input-field w-full" />
            {passwordForm.formState.errors.currentPassword && <p className="text-red-500 text-xs mt-1">{passwordForm.formState.errors.currentPassword.message}</p>}
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">New Password</label>
              <input type="password" {...passwordForm.register('newPassword')} className="input-field w-full" />
              {passwordForm.formState.errors.newPassword && <p className="text-red-500 text-xs mt-1">{passwordForm.formState.errors.newPassword.message}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
              <input type="password" {...passwordForm.register('confirmPassword')} className="input-field w-full" />
              {passwordForm.formState.errors.confirmPassword && <p className="text-red-500 text-xs mt-1">{passwordForm.formState.errors.confirmPassword.message}</p>}
            </div>
          </div>
          <button type="submit" disabled={savingPassword} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50">
            <HiKey className="w-4 h-4" /> {savingPassword ? 'Changing...' : 'Change Password'}
          </button>
        </form>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-heading font-semibold text-gray-900 mb-4">SMTP Settings</h2>
        <form onSubmit={smtpForm.handleSubmit(onSmtpSubmit)} className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
              <input {...smtpForm.register('host')} className="input-field w-full" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Port</label>
              <input type="number" {...smtpForm.register('port')} className="input-field w-full" />
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Username</label>
              <input {...smtpForm.register('username')} className="input-field w-full" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <input type="password" {...smtpForm.register('password')} className="input-field w-full" />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">From Email</label>
            <input type="email" {...smtpForm.register('fromEmail')} placeholder="noreply@giheketss.com" className="input-field w-full" />
          </div>
          <button type="submit" disabled={savingSmtp} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50">
            <HiSave className="w-4 h-4" /> {savingSmtp ? 'Saving...' : 'Save SMTP Settings'}
          </button>
        </form>
      </div>
    </div>
  );
}
