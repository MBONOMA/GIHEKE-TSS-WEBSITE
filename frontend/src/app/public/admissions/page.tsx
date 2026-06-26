'use client';

import { useState, useEffect, useRef } from 'react';
import PublicLayout from '@/components/layout/PublicLayout';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { admissionsApi } from '@/lib/api';
import { AdmissionSettings } from '@/types';
import { TRADES, SCHOOL_INFO } from '@/lib/constants';
import { HiCheckCircle, HiXCircle, HiUpload, HiDocumentText, HiArrowRight } from 'react-icons/hi';
import toast from 'react-hot-toast';

function HeroBanner() {
  return (
    <section className="relative py-24 md:py-32 overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950" />
      <div className="absolute inset-0 opacity-20" style={{
        backgroundImage: 'radial-gradient(circle at 30% 70%, rgba(59,130,246,0.3) 0%, transparent 50%), radial-gradient(circle at 70% 30%, rgba(22,163,74,0.2) 0%, transparent 50%)'
      }} />
      <div className="relative max-w-7xl mx-auto px-4 text-center">
        <h1 className="text-4xl md:text-6xl font-heading font-extrabold text-white mb-4">Admissions</h1>
        <div className="w-20 h-1 bg-accent-400 mx-auto rounded-full mb-4" />
        <p className="text-lg text-primary-100/90 max-w-2xl mx-auto">Begin your journey at {SCHOOL_INFO.name}. Apply for admission to our technical programs.</p>
      </div>
      <div className="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white to-transparent" />
    </section>
  );
}

function AdmissionsClosed({ settings }: { settings: AdmissionSettings }) {
  return (
    <section className="py-20 bg-white">
      <div className="max-w-2xl mx-auto px-4 text-center">
        <HiXCircle className="w-20 h-20 text-gray-300 mx-auto mb-6" />
        <h2 className="text-3xl font-heading font-bold text-gray-900 mb-4">Admissions Currently Closed</h2>
        <div className="w-20 h-1 bg-gray-300 mx-auto rounded-full mb-6" />
        <p className="text-gray-600 text-lg mb-4">
          {settings.message || 'Admissions are not open at this time. Please check back later for the next intake period.'}
        </p>
        {settings.openFrom && settings.openUntil && (
          <div className="card p-6 border border-gray-200 inline-block">
            <p className="text-sm text-gray-500">
              Next intake period: <span className="font-medium text-gray-900">{new Date(settings.openFrom).toLocaleDateString()}</span> to{' '}
              <span className="font-medium text-gray-900">{new Date(settings.openUntil).toLocaleDateString()}</span>
            </p>
          </div>
        )}
        <div className="mt-8">
          <p className="text-gray-500">For inquiries, please <a href="/public/contact" className="text-primary-600 hover:underline">contact us</a>.</p>
        </div>
      </div>
    </section>
  );
}

export default function AdmissionsPage() {
  const [settings, setSettings] = useState<AdmissionSettings | null>(null);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    dateOfBirth: '',
    gender: '',
    previousSchool: '',
    programId: '',
    academicBackground: '',
    email: '',
    phone: '',
    address: '',
  });
  const [documents, setDocuments] = useState<File[]>([]);

  useEffect(() => {
    async function fetchStatus() {
      try {
        const res = await admissionsApi.getStatus();
        setSettings(res.data?.data || res.data);
      } catch {
        setSettings({ isOpen: true, currentApplications: 0 } as AdmissionSettings);
      } finally {
        setLoading(false);
      }
    }
    fetchStatus();
  }, []);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    setFormData((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      setDocuments(Array.from(e.target.files));
    }
  };

  const removeFile = (index: number) => {
    setDocuments((prev) => prev.filter((_, i) => i !== index));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.firstName || !formData.lastName || !formData.email || !formData.phone || !formData.programId) {
      toast.error('Please fill in all required fields');
      return;
    }

    setSubmitting(true);
    try {
      const fd = new FormData();
      Object.entries(formData).forEach(([key, value]) => {
        fd.append(key, value);
      });
      documents.forEach((file) => {
        fd.append('documents', file);
      });
      await admissionsApi.apply(fd);
      toast.success('Application submitted successfully! We will contact you regarding the next steps.');
      setSubmitted(true);
    } catch {
      toast.error('Failed to submit application. Please try again or contact us directly.');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <PublicLayout>
        <div className="min-h-[80vh] flex items-center justify-center">
          <LoadingSpinner size="lg" />
        </div>
      </PublicLayout>
    );
  }

  if (settings && !settings.isOpen) {
    return (
      <PublicLayout>
        <HeroBanner />
        <AdmissionsClosed settings={settings} />
      </PublicLayout>
    );
  }

  if (submitted) {
    return (
      <PublicLayout>
        <HeroBanner />
        <section className="py-20 bg-white">
          <div className="max-w-2xl mx-auto px-4 text-center">
            <HiCheckCircle className="w-20 h-20 text-accent-500 mx-auto mb-6" />
            <h2 className="text-3xl font-heading font-bold text-gray-900 mb-4">Application Submitted!</h2>
            <div className="w-20 h-1 bg-accent-500 mx-auto rounded-full mb-6" />
            <p className="text-gray-600 text-lg mb-4">
              Thank you for applying to {SCHOOL_INFO.name}. Your application has been received and is under review.
            </p>
            <div className="card p-6 border border-gray-200 inline-block text-left">
              <p className="text-sm text-gray-500 mb-2">What happens next?</p>
              <ul className="text-sm text-gray-600 space-y-2">
                <li className="flex items-start gap-2">
                  <HiCheckCircle className="w-4 h-4 text-accent-500 mt-0.5 flex-shrink-0" />
                  Our admissions team will review your application
                </li>
                <li className="flex items-start gap-2">
                  <HiCheckCircle className="w-4 h-4 text-accent-500 mt-0.5 flex-shrink-0" />
                  You may be contacted for an interview
                </li>
                <li className="flex items-start gap-2">
                  <HiCheckCircle className="w-4 h-4 text-accent-500 mt-0.5 flex-shrink-0" />
                  You will receive an admission decision via email
                </li>
              </ul>
            </div>
            <div className="mt-8">
              <a href="/" className="btn-primary inline-flex items-center gap-2">
                Back to Home <HiArrowRight className="w-5 h-5" />
              </a>
            </div>
          </div>
        </section>
      </PublicLayout>
    );
  }

  return (
    <PublicLayout>
      <HeroBanner />

      <section className="py-20 bg-white">
        <div className="max-w-3xl mx-auto px-4">
          <div className="text-center mb-10">
            <h2 className="text-3xl font-heading font-bold text-gray-900">Application Form</h2>
            <div className="w-20 h-1 bg-primary-600 mx-auto rounded-full mt-4" />
            <p className="text-gray-600 mt-4">Fill in the form below to apply for admission. All fields marked with * are required.</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="grid sm:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                <input type="text" name="firstName" value={formData.firstName} onChange={handleChange} required className="input-field" placeholder="First name" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                <input type="text" name="lastName" value={formData.lastName} onChange={handleChange} required className="input-field" placeholder="Last name" />
              </div>
            </div>

            <div className="grid sm:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                <input type="date" name="dateOfBirth" value={formData.dateOfBirth} onChange={handleChange} required className="input-field" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                <select name="gender" value={formData.gender} onChange={handleChange} required className="input-field">
                  <option value="">Select gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                </select>
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Previous School</label>
              <input type="text" name="previousSchool" value={formData.previousSchool} onChange={handleChange} className="input-field" placeholder="Name of previous school attended" />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Program/Trade *</label>
              <select name="programId" value={formData.programId} onChange={handleChange} required className="input-field">
                <option value="">Select a program</option>
                {TRADES.map((trade) => (
                  <option key={trade.code} value={trade.code}>{trade.name} ({trade.code})</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Academic Background</label>
              <textarea name="academicBackground" value={formData.academicBackground} onChange={handleChange} rows={4} className="input-field resize-none" placeholder="Describe your previous academic background, achievements, and any relevant experience" />
            </div>

            <div className="grid sm:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" value={formData.email} onChange={handleChange} required className="input-field" placeholder="your@email.com" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                <input type="tel" name="phone" value={formData.phone} onChange={handleChange} required className="input-field" placeholder="+250 7XX XXX XXX" />
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Address</label>
              <textarea name="address" value={formData.address} onChange={handleChange} rows={3} className="input-field resize-none" placeholder="Your current residential address" />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Upload Documents</label>
              <div
                onClick={() => fileInputRef.current?.click()}
                className="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50/50 transition-all"
              >
                <HiUpload className="w-10 h-10 text-gray-400 mx-auto mb-3" />
                <p className="text-sm text-gray-600 font-medium">Click to upload documents</p>
                <p className="text-xs text-gray-400 mt-1">Upload academic transcripts, certificates, or ID (PDF, JPG, PNG)</p>
              </div>
              <input
                ref={fileInputRef}
                type="file"
                multiple
                onChange={handleFileChange}
                className="hidden"
                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
              />
              {documents.length > 0 && (
                <div className="mt-4 space-y-2">
                  {documents.map((file, index) => (
                    <div key={index} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                      <div className="flex items-center gap-3">
                        <HiDocumentText className="w-5 h-5 text-primary-600" />
                        <span className="text-sm text-gray-700">{file.name}</span>
                        <span className="text-xs text-gray-400">({(file.size / 1024).toFixed(1)} KB)</span>
                      </div>
                      <button type="button" onClick={() => removeFile(index)} className="text-red-500 hover:text-red-700 text-sm">Remove</button>
                    </div>
                  ))}
                </div>
              )}
            </div>

            <button type="submit" disabled={submitting} className="btn-primary w-full py-4 text-base inline-flex items-center justify-center gap-2">
              {submitting ? (
                <><LoadingSpinner size="sm" /> Submitting Application...</>
              ) : (
                <><HiDocumentText className="w-5 h-5" /> Submit Application</>
              )}
            </button>
          </form>
        </div>
      </section>
    </PublicLayout>
  );
}
