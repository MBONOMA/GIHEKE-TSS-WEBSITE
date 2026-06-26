'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import PublicLayout from '@/components/layout/PublicLayout';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { programsApi } from '@/lib/api';
import { Program } from '@/types';
import { TRADES, SCHOOL_INFO } from '@/lib/constants';
import { HiAcademicCap, HiArrowRight, HiClock, HiCode, HiBookOpen, HiChip, HiCash, HiLightningBolt, HiOfficeBuilding, HiDesktopComputer } from 'react-icons/hi';

const ICON_MAP: Record<string, React.ComponentType<{ className?: string }>> = {
  SWD: HiCode,
  NIT: HiChip,
  ETS: HiLightningBolt,
  PAC: HiCash,
  ELT: HiLightningBolt,
  BLC: HiOfficeBuilding,
  CSA: HiDesktopComputer,
};

function HeroBanner() {
  return (
    <section className="relative py-24 md:py-32 overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950" />
      <div className="absolute inset-0 opacity-20" style={{
        backgroundImage: 'radial-gradient(circle at 30% 70%, rgba(59,130,246,0.3) 0%, transparent 50%), radial-gradient(circle at 70% 30%, rgba(22,163,74,0.2) 0%, transparent 50%)'
      }} />
      <div className="relative max-w-7xl mx-auto px-4 text-center">
        <h1 className="text-4xl md:text-6xl font-heading font-extrabold text-white mb-4">Our Programs</h1>
        <div className="w-20 h-1 bg-accent-400 mx-auto rounded-full mb-4" />
        <p className="text-lg text-primary-100/90 max-w-2xl mx-auto">Explore our comprehensive range of technical and vocational training programs designed to equip you with practical skills for the modern workforce.</p>
      </div>
      <div className="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white to-transparent" />
    </section>
  );
}

function ProgramCard({ program, index }: { program: Program; index: number }) {
  const Icon = ICON_MAP[program.code] || HiAcademicCap;

  return (
    <div className="card p-6 border border-gray-100 hover:border-primary-200 hover:shadow-xl transition-all group">
      <div className="flex items-start gap-4">
        <div className="w-14 h-14 rounded-xl bg-gradient-to-br from-primary-50 to-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0 group-hover:from-primary-600 group-hover:to-primary-700 group-hover:text-white transition-all">
          <Icon className="w-7 h-7" />
        </div>
        <div className="flex-1 min-w-0">
          <h3 className="text-lg font-heading font-semibold text-gray-900 group-hover:text-primary-600 transition-colors">
            {program.name}
          </h3>
          <div className="flex items-center gap-3 mt-1">
            <span className="text-xs font-medium px-2 py-0.5 rounded-full bg-primary-50 text-primary-700">
              {program.code}
            </span>
            <span className="flex items-center gap-1 text-xs text-gray-500">
              <HiClock className="w-3.5 h-3.5" />
              {program.duration}
            </span>
          </div>
        </div>
      </div>
      <p className="mt-4 text-gray-600 text-sm leading-relaxed">{program.description}</p>
      <div className="mt-4 pt-4 border-t border-gray-100">
        <Link
          href="/public/admissions"
          className="text-primary-600 font-medium text-sm inline-flex items-center gap-1 group-hover:gap-2 transition-all"
        >
          Apply for this Program <HiArrowRight className="w-4 h-4" />
        </Link>
      </div>
    </div>
  );
}

function CTASection() {
  return (
    <section className="py-20 bg-gradient-to-r from-primary-600 to-primary-800 relative overflow-hidden">
      <div className="absolute inset-0 opacity-10" style={{
        backgroundImage: 'radial-gradient(circle at 50% 50%, rgba(255,255,255,0.2) 0%, transparent 60%)'
      }} />
      <div className="relative max-w-4xl mx-auto px-4 text-center">
        <h2 className="text-3xl md:text-4xl font-heading font-bold text-white mb-4">
          Ready to Enroll?
        </h2>
        <p className="text-primary-100 text-lg mb-8 max-w-2xl mx-auto">
          Take the first step towards a rewarding career. Apply for admission to {SCHOOL_INFO.name} today.
        </p>
        <div className="flex flex-wrap justify-center gap-4">
          <Link href="/public/admissions" className="btn-accent text-base px-8 py-4 shadow-lg inline-flex items-center gap-2">
            Apply Now <HiArrowRight className="w-5 h-5" />
          </Link>
          <Link href="/public/contact" className="inline-flex items-center gap-2 px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-white/20 transition-all border border-white/20">
            Contact Us
          </Link>
        </div>
      </div>
    </section>
  );
}

export default function ProgramsPage() {
  const [programs, setPrograms] = useState<Program[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchPrograms() {
      try {
        const res = await programsApi.getAll();
        const data = res.data?.data || res.data || [];
        setPrograms(Array.isArray(data) ? data : []);
      } catch {
        setPrograms([]);
      } finally {
        setLoading(false);
      }
    }
    fetchPrograms();
  }, []);

  const displayPrograms = programs.length > 0
    ? programs.filter((p) => p.isActive)
    : TRADES.map((t) => ({
        id: t.code,
        name: t.name,
        code: t.code,
        description: t.description,
        duration: '3 Years',
        isActive: true,
      }));

  if (loading) {
    return (
      <PublicLayout>
        <div className="min-h-[80vh] flex items-center justify-center">
          <LoadingSpinner size="lg" />
        </div>
      </PublicLayout>
    );
  }

  return (
    <PublicLayout>
      <HeroBanner />
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-12">
            <p className="text-lg text-gray-600 max-w-3xl mx-auto">
              {SCHOOL_INFO.name} offers {displayPrograms.length} specialized trade programs designed to provide hands-on training and theoretical knowledge.
            </p>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {displayPrograms.map((program, index) => (
              <ProgramCard key={program.code || program.id} program={program} index={index} />
            ))}
          </div>
        </div>
      </section>
      <CTASection />
    </PublicLayout>
  );
}
