'use client';

import { useState, useEffect } from 'react';
import { HiMail, HiPaperAirplane, HiUser, HiChat } from 'react-icons/hi';
import { messagesApi, parentsApi } from '@/lib/api';
import toast from 'react-hot-toast';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import Modal from '@/components/ui/Modal';
import { formatDateTime, timeAgo } from '@/lib/utils';
import { Message } from '@/types';

export default function ParentMessagesPage() {
  const [loading, setLoading] = useState(true);
  const [messages, setMessages] = useState<Message[]>([]);
  const [sendModal, setSendModal] = useState(false);
  const [selectedMessage, setSelectedMessage] = useState<Message | null>(null);
  const [viewModal, setViewModal] = useState(false);
  const [form, setForm] = useState({ receiverId: '', subject: '', content: '' });
  const [sending, setSending] = useState(false);
  const [activeTab, setActiveTab] = useState<'inbox' | 'sent'>('inbox');

  useEffect(() => {
    loadMessages();
  }, []);

  const loadMessages = async () => {
    try {
      const res = await messagesApi.getAll({ limit: 50 });
      setMessages(res.data.data || []);
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const handleSend = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!form.receiverId || !form.content) {
      toast.error('Recipient and content are required');
      return;
    }
    setSending(true);
    try {
      await messagesApi.send(form);
      toast.success('Message sent successfully');
      setSendModal(false);
      setForm({ receiverId: '', subject: '', content: '' });
      loadMessages();
    } catch {
    } finally {
      setSending(false);
    }
  };

  const openMessage = (msg: Message) => {
    setSelectedMessage(msg);
    setViewModal(true);
    if (!msg.isRead) {
      messagesApi.markAsRead(msg.id).catch(() => {});
    }
  };

  const inboxMessages = messages.filter(() => activeTab === 'inbox');
  const unreadCount = messages.filter((m) => !m.isRead).length;

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-heading font-bold text-gray-900">Messages</h1>
          <p className="text-gray-500 text-sm">Communicate with teachers and school administration</p>
        </div>
        <button onClick={() => setSendModal(true)} className="btn-primary flex items-center gap-2">
          <HiPaperAirplane className="w-4 h-4" /> New Message
        </button>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div className="border-b border-gray-200">
          <div className="flex">
            <button
              onClick={() => setActiveTab('inbox')}
              className={`px-4 py-3 text-sm font-medium border-b-2 transition-colors ${
                activeTab === 'inbox'
                  ? 'border-primary-600 text-primary-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700'
              }`}
            >
              Inbox ({unreadCount})
            </button>
            <button
              onClick={() => setActiveTab('sent')}
              className={`px-4 py-3 text-sm font-medium border-b-2 transition-colors ${
                activeTab === 'sent'
                  ? 'border-primary-600 text-primary-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700'
              }`}
            >
              Sent
            </button>
          </div>
        </div>

        <div className="divide-y divide-gray-200">
          {inboxMessages.length === 0 ? (
            <div className="p-12 text-center">
              <HiMail className="w-12 h-12 text-gray-300 mx-auto mb-3" />
              <p className="text-gray-500">No messages yet.</p>
            </div>
          ) : (
            inboxMessages.map((msg) => (
              <button
                key={msg.id}
                onClick={() => openMessage(msg)}
                className="w-full text-left p-4 hover:bg-gray-50 transition-colors flex items-start gap-3"
              >
                <div className={`w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 ${
                  msg.isRead ? 'bg-gray-100 text-gray-500' : 'bg-primary-100 text-primary-600'
                }`}>
                  <HiUser className="w-5 h-5" />
                </div>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center justify-between gap-2">
                    <p className={`text-sm ${msg.isRead ? 'text-gray-900' : 'text-gray-900 font-semibold'}`}>
                      {msg.sender?.firstName} {msg.sender?.lastName}
                    </p>
                    <span className="text-xs text-gray-400 flex-shrink-0">{timeAgo(msg.createdAt)}</span>
                  </div>
                  {msg.subject && (
                    <p className={`text-sm mt-0.5 ${msg.isRead ? 'text-gray-700' : 'text-gray-900 font-medium'}`}>
                      {msg.subject}
                    </p>
                  )}
                  <p className="text-sm text-gray-500 mt-0.5 truncate">{msg.content}</p>
                </div>
                {!msg.isRead && (
                  <div className="w-2 h-2 bg-primary-600 rounded-full flex-shrink-0 mt-2" />
                )}
              </button>
            ))
          )}
        </div>
      </div>

      <Modal isOpen={sendModal} onClose={() => setSendModal(false)} title="New Message" size="md">
        <form onSubmit={handleSend} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Send To</label>
            <select
              value={form.receiverId}
              onChange={(e) => setForm((p) => ({ ...p, receiverId: e.target.value }))}
              className="input-field"
              required
            >
              <option value="">Select recipient</option>
              <option value="admin">School Administration</option>
              <option value="teacher">Teacher (General)</option>
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Subject</label>
            <input
              type="text"
              value={form.subject}
              onChange={(e) => setForm((p) => ({ ...p, subject: e.target.value }))}
              className="input-field"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Message *</label>
            <textarea
              value={form.content}
              onChange={(e) => setForm((p) => ({ ...p, content: e.target.value }))}
              className="input-field"
              rows={5}
              required
            />
          </div>
          <button type="submit" disabled={sending} className="btn-primary w-full flex items-center justify-center gap-2">
            <HiPaperAirplane className="w-4 h-4" /> {sending ? 'Sending...' : 'Send Message'}
          </button>
        </form>
      </Modal>

      <Modal isOpen={viewModal} onClose={() => setViewModal(false)} title="Message" size="md">
        {selectedMessage && (
          <div className="space-y-4">
            <div className="flex items-center gap-3 pb-4 border-b border-gray-200">
              <div className="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600">
                <HiUser className="w-5 h-5" />
              </div>
              <div>
                <p className="text-sm font-medium text-gray-900">
                  {selectedMessage.sender?.firstName} {selectedMessage.sender?.lastName}
                </p>
                <p className="text-xs text-gray-500">{formatDateTime(selectedMessage.createdAt)}</p>
              </div>
            </div>
            {selectedMessage.subject && (
              <p className="text-sm font-medium text-gray-900">{selectedMessage.subject}</p>
            )}
            <p className="text-sm text-gray-700 whitespace-pre-wrap">{selectedMessage.content}</p>
          </div>
        )}
      </Modal>
    </div>
  );
}
