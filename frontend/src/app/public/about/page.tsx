'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import PublicLayout from '@/components/layout/PublicLayout';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { siteManagementApi } from '@/lib/api';
import { AboutPageContent, Leader, Achievement } from '@/types';
import { SCHOOL_INFO, STAFF } from '@/lib/constants';
import { HiAcademicCap, HiArrowRight, HiPhotograph, HiCalendar, HiTrophy, HiCheck } from 'react-icons/hi';

const CORE_VALUES = [
  { title: 'Excellence', description: 'We strive for the highest standards in technical education and skills development.' },
  { title: 'Integrity', description: 'We uphold honesty, transparency, and ethical behavior in all our endeavors.' },
  { title: 'Innovation', description: 'We embrace creativity and modern approaches to teaching and learning.' },
  { title: 'Teamwork', description: 'We foster collaboration among students, staff, and the community.' },
  { title: 'Discipline', description: 'We cultivate self-discipline and responsibility in our students.' },
  { title: 'Inclusivity', description: 'We provide equal opportunities for all students regardless of background.' },
];

function HeroBanner() {
  return (
    <section className="relative py-24 md:py-32 overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950" />
      <div className="absolute inset-0 opacity-20" style={{
        backgroundImage: 'radial-gradient(circle at 30% 70%, rgba(59,130,246,0.3) 0%, transparent 50%), radial-gradient(circle at 70% 30%, rgba(22,163,74,0.2) 0%, transparent 50%)'
      }} />
      <div className="relative max-w-7xl mx-auto px-4 text-center">
        <h1 className="text-4xl md:text-6xl font-heading font-extrabold text-white mb-4">About Us</h1>
        <div className="w-20 h-1 bg-accent-400 mx-auto rounded-full mb-4" />
        <p className="text-lg text-primary-100/90 max-w-2xl mx-auto">Learn about our history, mission, and the team behind {SCHOOL_INFO.name}</p>
      </div>
      <div className="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white to-transparent" />
    </section>
  );
}

function SchoolHistorySection({ content }: { content?: AboutPageContent[] }) {
  const history = content?.find((c) => c.sectionKey === 'history');

  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-4">
        <div className="grid md:grid-cols-2 gap-12 items-center">
          <div>
            <h2 className="section-title">{history?.title || 'Our History'}</h2>
            <div className="w-20 h-1 bg-primary-600 rounded-full mb-6" />
            <div className="prose prose-lg text-gray-600 leading-relaxed space-y-4">
              <p>{history?.content || `Founded in ${SCHOOL_INFO.foundedYear}, ${SCHOOL_INFO.name} has been at the forefront of technical and vocational education in Rwanda's Western Province. Our journey began with a vision to provide practical skills training that would empower young Rwandans to become solution providers in their communities.`}</p>
              <p>Over the years, we have grown from a small institution into a comprehensive technical secondary school offering seven specialized trades, serving hundreds of students annually, and producing graduates who excel in various industries.</p>
            </div>
          </div>
          <div className="relative">
            <div className="aspect-[4/3] rounded-2xl overflow-hidden bg-gradient-to-br from-primary-50 to-primary-100 shadow-xl">
              <div className="w-full h-full flex items-center justify-center">
                <HiCalendar className="w-20 h-20 text-primary-400" />
              </div>
            </div>
            <div className="absolute -bottom-4 -left-4 w-24 h-24 bg-accent-500 rounded-2xl -z-10" />
          </div>
        </div>
      </div>
    </section>
  );
}

function MissionVisionSection({ content }: { content?: AboutPageContent[] }) {
  const mission = content?.find((c) => c.sectionKey === 'mission');
  const vision = content?.find((c) => c.sectionKey === 'vision');

  return (
    <section className="py-20 bg-gray-50">
      <div className="max-w-7xl mx-auto px-4">
        <div className="grid md:grid-cols-2 gap-8">
          <div className="card p-8 border border-primary-100 hover:border-primary-200 transition-all">
            <div className="w-14 h-14 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center mb-4">
              <HiAcademicCap className="w-7 h-7" />
            </div>
            <h3 className="text-xl font-heading font-bold text-gray-900 mb-4">{mission?.title || 'Our Mission'}</h3>
            <p className="text-gray-600 leading-relaxed">{mission?.content || SCHOOL_INFO.mission}</p>
          </div>
          <div className="card p-8 border border-accent-100 hover:border-accent-200 transition-all">
            <div className="w-14 h-14 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center mb-4">
              <HiTrophy className="w-7 h-7" />
            </div>
            <h3 className="text-xl font-heading font-bold text-gray-900 mb-4">{vision?.title || 'Our Vision'}</h3>
            <p className="text-gray-600 leading-relaxed">{vision?.content || SCHOOL_INFO.vision}</p>
          </div>
        </div>
      </div>
    </section>
  );
}

function CoreValuesSection() {
  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-4">
        <div className="text-center mb-12">
          <h2 className="section-title">Core Values</h2>
          <div className="w-20 h-1 bg-primary-600 mx-auto rounded-full mb-4" />
          <p className="section-subtitle">The principles that guide everything we do</p>
        </div>
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {CORE_VALUES.map((value) => (
            <div key={value.title} className="flex items-start gap-4 p-6 rounded-xl bg-gray-50 border border-gray-100 hover:border-primary-200 hover:bg-primary-50/50 transition-all">
              <div className="w-10 h-10 rounded-full bg-accent-100 text-accent-600 flex items-center justify-center flex-shrink-0 mt-1">
                <HiCheck className="w-5 h-5" />
              </div>
              <div>
                <h3 className="font-heading font-semibold text-gray-900 mb-1">{value.title}</h3>
                <p className="text-gray-600 text-sm">{value.description}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function PrincipalMessageSection() {
  return (
    <section className="py-20 bg-gray-50">
      <div className="max-w-7xl mx-auto px-4">
        <div className="grid md:grid-cols-5 gap-12 items-center">
          <div className="md:col-span-2 relative">
            <div className="aspect-[3/4] rounded-2xl overflow-hidden bg-gradient-to-br from-primary-100 to-primary-200 shadow-xl">
              <div className="w-full h-full flex items-center justify-center">
                <HiPhotograph className="w-24 h-24 text-primary-400" />
              </div>
            </div>
          </div>
          <div className="md:col-span-3">
            <h2 className="section-title">Message from the Principal</h2>
            <div className="w-20 h-1 bg-primary-600 rounded-full mb-6" />
            <div className="space-y-4 text-gray-600 leading-relaxed">
              <p>Welcome to {SCHOOL_INFO.fullName}. It is my honor and privilege to lead this remarkable institution that is transforming lives through quality technical education.</p>
              <p>At {SCHOOL_INFO.name}, we believe that every student has the potential to become a solution provider for their community and country. Our dedicated team of educators works tirelessly to create an environment where students can discover their talents, develop practical skills, and build character.</p>
              <p>We invite you to explore our programs, visit our campus, and become part of the {SCHOOL_INFO.name} family.</p>
            </div>
            <div className="mt-6 pt-6 border-t border-gray-200">
              <p className="font-heading font-semibold text-gray-900">{STAFF.principal.name}</p>
              <p className="text-sm text-gray-500">Principal, {SCHOOL_INFO.name}</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function LeadershipTeamSection({ leaders }: { leaders: Leader[] }) {
  const allLeaders = leaders.filter((l) => l.isActive);
  const staffMembers = [
    { name: STAFF.principal.name, position: STAFF.principal.position, bio: 'Experienced education leader dedicated to technical and vocational training excellence.' },
    { name: STAFF.deanOfStudies.name, position: STAFF.deanOfStudies.position, bio: 'Oversees academic programs, curriculum development, and student assessment.' },
    { name: STAFF.deanOfDiscipline.name, position: STAFF.deanOfDiscipline.position, bio: 'Responsible for student welfare, discipline, and character development.' },
    { name: STAFF.accountant.name, position: STAFF.accountant.position, bio: 'Manages financial operations and ensures transparent resource management.' },
    { name: STAFF.secretary.name, position: STAFF.secretary.position, bio: 'Handles administrative coordination and school documentation.' },
    { name: STAFF.patron.name, position: STAFF.patron.position, bio: 'Provides guidance and support for student activities and development.' },
    { name: STAFF.matron.name, position: STAFF.matron.position, bio: 'Cares for student welfare, especially boarding students\' well-being.' },
    { name: STAFF.coach.name, position: STAFF.coach.position, bio: 'Coaches students in sports, teamwork, and leadership skills.' },
  ];

  const displayLeaders = allLeaders.length > 0 ? allLeaders : staffMembers;

  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-4">
        <div className="text-center mb-12">
          <h2 className="section-title">Leadership Team</h2>
          <div className="w-20 h-1 bg-primary-600 mx-auto rounded-full mb-4" />
          <p className="section-subtitle">Meet the dedicated team behind {SCHOOL_INFO.name}</p>
        </div>
        <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {displayLeaders.map((leader, index) => (
            <div key={index} className="card p-6 border border-gray-100 hover:shadow-xl hover:border-primary-100 transition-all group text-center">
              <div className="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center mb-4 group-hover:from-primary-500 group-hover:to-primary-600 transition-all">
                <span className="text-2xl font-heading font-bold text-primary-600 group-hover:text-white transition-all">
                  {leader.name.split(' ').map((n) => n[0]).slice(0, 2).join('')}
                </span>
              </div>
              <h3 className="font-heading font-semibold text-gray-900">{leader.name}</h3>
              <p className="text-sm text-primary-600 font-medium mt-1">{leader.position}</p>
              {'bio' in leader && leader.bio && (
                <p className="text-sm text-gray-500 mt-3 line-clamp-3">{leader.bio}</p>
              )}
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function GalleryLinkSection() {
  return (
    <section className="py-16 bg-gradient-to-r from-primary-600 to-primary-800">
      <div className="max-w-7xl mx-auto px-4 text-center">
        <h2 className="text-3xl font-heading font-bold text-white mb-4">Explore Our School Gallery</h2>
        <p className="text-primary-100 mb-8 max-w-xl mx-auto">See photos and videos of our campus, classrooms, workshops, and student activities.</p>
        <Link href="/public/gallery" className="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary-700 font-medium rounded-lg hover:bg-primary-50 transition-all shadow-lg">
          View Gallery <HiArrowRight className="w-5 h-5" />
        </Link>
      </div>
    </section>
  );
}

export default function AboutPage() {
  const [content, setContent] = useState<AboutPageContent[]>([]);
  const [leaders, setLeaders] = useState<Leader[]>([]);
  const [achievements, setAchievements] = useState<Achievement[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchData() {
      try {
        const [contentRes, leadersRes, achievementsRes] = await Promise.all([
          siteManagementApi.getAboutContent(),
          siteManagementApi.getLeaders(),
          siteManagementApi.getAchievements(),
        ]);
        setContent(contentRes.data?.data || contentRes.data || []);
        setLeaders(leadersRes.data?.data || leadersRes.data || []);
        setAchievements(achievementsRes.data?.data || achievementsRes.data || []);
      } catch {
        // Silently fail, sections will render with default content
      } finally {
        setLoading(false);
      }
    }
    fetchData();
  }, []);

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
      <SchoolHistorySection content={content} />
      <MissionVisionSection content={content} />
      <CoreValuesSection />
      <PrincipalMessageSection />
      <LeadershipTeamSection leaders={leaders} />
      <GalleryLinkSection />
    </PublicLayout>
  );
}
