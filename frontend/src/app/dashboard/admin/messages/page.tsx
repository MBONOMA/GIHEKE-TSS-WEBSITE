'use client';

import { useState, useEffect, useCallback } from 'react';
import toast from 'react-hot-toast';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { HiPlus, HiMail, HiMailOpen, HiChevronLeft } from 'react-icons/hi';
import { messagesApi, usersApi } from '@/lib/api';
import { Message, User } from '@/types';
import Modal from '@/components/ui/Modal';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { formatDateTime, timeAgo } from '@/lib/utils';

const messageSchema = z.object({
  receiverId: z.string().min(1, 'Recipient is required'),
  subject: z.string().min(1, 'Subject is required'),
  content: z.string().min(1, 'Content is required'),
});

export default function MessagesPage() {
  const [loading, setLoading] = useState(true);
  const [messages, setMessages] = useState<Message[]>([]);
  const [selectedMessage, setSelectedMessage] = useState<Message | null>(null);
  const [users, setUsers] = useState<User[]>([]);
  const [composeOpen, setComposeOpen] = useState(false);
  const [sending, setSending] = useState(false);

  const form = useForm<z.infer<typeof messageSchema>>({
    resolver: zodResolver(messageSchema),
    defaultValues: { receiverId: '', subject: '', content: '' },
  });

  const fetchMessages = useCallback(async () => {
    try {
      setLoading(true);
      const res = await messagesApi.getAll({ limit: 100 });
      setMessages(res.data.data || []);
    } catch {} finally { setLoading(false); }
  }, []);

  const fetchUsers = useCallback(async () => {
    try {
      const res = await usersApi.getAll({ limit: 200 });
      setUsers(res.data.data || []);
    } catch {}
  }, []);

  useEffect(() => {
    fetchMessages();
    fetchUsers();
  }, [fetchMessages, fetchUsers]);

  const openMessage = async (msg: Message) => {
    setSelectedMessage(msg);
    if (!msg.isRead) {
      try {
        await messagesApi.markAsRead(msg.id);
        setMessages((prev) => prev.map((m) => m.id === msg.id ? { ...m, isRead: true } : m));
      } catch {}
    }
  };

  const openCompose = () => {
    form.reset({ receiverId: '', subject: '', content: '' });
    setComposeOpen(true);
  };

  const onSubmit = async (data: z.infer<typeof messageSchema>) => {
    try {
      setSending(true);
      await messagesApi.send(data);
      toast.success('Message sent');
      setComposeOpen(false);
      fetchMessages();
    } catch {} finally { setSending(false); }
  };

  if (loading) {
    return <LoadingSpinner size="lg" className="min-h-[60vh]" />;
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-heading font-bold text-gray-900">Messages</h1>
          <p className="text-gray-500 mt-1">Inbox and compose messages</p>
        </div>
        <button onClick={openCompose} className="btn-primary flex items-center gap-2 px-4 py-2 rounded-lg text-sm bg-primary-600 text-white hover:bg-primary-700">
          <HiPlus className="w-4 h-4" /> Compose
        </button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div className="p-3 border-b border-gray-200 bg-gray-50">
            <h3 className="text-sm font-semibold text-gray-700">Inbox ({messages.filter((m) => !m.isRead).length} unread)</h3>
          </div>
          <div className="divide-y divide-gray-200 max-h-[70vh] overflow-y-auto">
            {messages.length === 0 ? (
              <p className="p-4 text-sm text-gray-400 text-center">No messages</p>
            ) : (
              messages.map((msg) => (
                <button
                  key={msg.id}
                  onClick={() => openMessage(msg)}
                  className={`w-full text-left p-3 hover:bg-gray-50 transition-colors ${!msg.isRead ? 'bg-primary-50/50' : ''}`}
                >
                  <div className="flex items-start justify-between gap-2">
                    <div className="flex items-center gap-2 min-w-0">
                      {!msg.isRead ? (
                        <HiMail className="w-4 h-4 text-primary-600 flex-shrink-0" />
                      ) : (
                        <HiMailOpen className="w-4 h-4 text-gray-400 flex-shrink-0" />
                      )}
                      <span className={`text-sm truncate ${!msg.isRead ? 'font-semibold text-gray-900' : 'text-gray-600'}`}>
                        {msg.sender?.firstName || 'Unknown'} {msg.sender?.lastName || ''}
                      </span>
                    </div>
                    <span className="text-xs text-gray-400 flex-shrink-0">{timeAgo(msg.createdAt)}</span>
                  </div>
                  <p className={`text-sm mt-1 truncate pl-6 ${!msg.isRead ? 'font-medium text-gray-800' : 'text-gray-500'}`}>
                    {msg.subject || '(No subject)'}
                  </p>
                </button>
              ))
            )}
          </div>
        </div>

        <div className="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6 min-h-[400px]">
          {selectedMessage ? (
            <div>
              <button onClick={() => setSelectedMessage(null)} className="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
                <HiChevronLeft className="w-4 h-4" /> Back to inbox
              </button>
              <div className="flex items-center gap-3 mb-4">
                <div className="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 font-semibold">
                  {selectedMessage.sender?.firstName?.[0] || 'U'}
                </div>
                <div>
                  <p className="font-medium">{selectedMessage.sender?.firstName} {selectedMessage.sender?.lastName}</p>
                  <p className="text-xs text-gray-500">{selectedMessage.sender?.email}</p>
                </div>
                <div className="ml-auto text-xs text-gray-400">{formatDateTime(selectedMessage.createdAt)}</div>
              </div>
              <h3 className="text-lg font-heading font-semibold mb-4">{selectedMessage.subject || '(No subject)'}</h3>
              <div className="text-gray-700 whitespace-pre-wrap leading-relaxed">{selectedMessage.content}</div>
            </div>
          ) : (
            <div className="flex items-center justify-center h-full text-gray-400">
              <p>Select a message to view</p>
            </div>
          )}
        </div>
      </div>

      <Modal isOpen={composeOpen} onClose={() => setComposeOpen(false)} title="Compose Message" size="lg">
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Recipient</label>
            <select {...form.register('receiverId')} className="input-field w-full">
              <option value="">Select a recipient</option>
              {users.filter((u) => u.isActive).map((u) => (
                <option key={u.id} value={u.id}>{u.firstName} {u.lastName} ({u.email})</option>
              ))}
            </select>
            {form.formState.errors.receiverId && <p className="text-red-500 text-xs mt-1">{form.formState.errors.receiverId.message}</p>}
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Subject</label>
            <input {...form.register('subject')} className="input-field w-full" />
            {form.formState.errors.subject && <p className="text-red-500 text-xs mt-1">{form.formState.errors.subject.message}</p>}
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Message</label>
            <textarea {...form.register('content')} rows={8} className="input-field w-full" />
            {form.formState.errors.content && <p className="text-red-500 text-xs mt-1">{form.formState.errors.content.message}</p>}
          </div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={() => setComposeOpen(false)} className="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
            <button type="submit" disabled={sending} className="btn-primary px-4 py-2 text-sm rounded-lg bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50">
              {sending ? 'Sending...' : 'Send Message'}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
}
