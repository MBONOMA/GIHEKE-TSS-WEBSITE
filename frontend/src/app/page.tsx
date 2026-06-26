'use client';

import { useState, useEffect, useRef, useCallback } from 'react';
import Link from 'next/link';
import PublicLayout from '@/components/layout/PublicLayout';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { siteManagementApi } from '@/lib/api';
import { HomepageSection, Testimonial, Partner } from '@/types';
import { SCHOOL_INFO, TRADES } from '@/lib/constants';
import {
  HiAcademicCap, HiUserGroup, HiBookOpen, HiCalendar,
  HiStar, HiChevronRight, HiArrowRight, HiPlay, HiPhotograph,
} from 'react-icons/hi';

interface HomepageData {
  hero?: HomepageSection;
  welcome?: HomepageSection;
  principalMessage?: HomepageSection;
  featuredPrograms?: HomepageSection;
  statistics?: HomepageSection;
  testimonials?: HomepageSection;
  partners?: HomepageSection;
}

function StarRating({ rating }: { rating?: number }) {
  const stars = rating || 5;
  return (
    <div className="flex gap-1">
      {[1, 2, 3, 4, 5].map((s) => (
        <HiStar
          key={s}
          className={`w-5 h-5 ${s <= stars ? 'text-gold-500' : 'text-gray-300'}`}
        />
      ))}
    </div>
  );
}

function AnimatedCounter({ value, suffix = '' }: { value: number; suffix?: string }) {
  const [count, setCount] = useState(0);
  const ref = useRef<HTMLSpanElement>(null);
  const hasAnimated = useRef(false);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting && !hasAnimated.current) {
          hasAnimated.current = true;
          const duration = 2000;
          const steps = 60;
          const increment = value / steps;
          let current = 0;
          const timer = setInterval(() => {
            current += increment;
            if (current >= value) {
              setCount(value);
              clearInterval(timer);
            } else {
              setCount(Math.floor(current));
            }
          }, duration / steps);
        }
      },
      { threshold: 0.5 }
    );
    if (ref.current) observer.observe(ref.current);
    return () => observer.disconnect();
  }, [value]);

  return (
    <span ref={ref} className="text-4xl md:text-5xl font-heading font-bold text-white">
      {count}{suffix}
    </span>
  );
}

function HeroSection({ data }: { data?: HomepageSection }) {
  return (
    <section className="relative min-h-[85vh] flex items-center overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950" />
      <div className="absolute inset-0 opacity-20" style={{
        backgroundImage: 'radial-gradient(circle at 25% 50%, rgba(59,130,246,0.3) 0%, transparent 50%), radial-gradient(circle at 75% 50%, rgba(22,163,74,0.2) 0%, transparent 50%)'
      }} />
      <div className="relative max-w-7xl mx-auto px-4 py-20 w-full">
        <div className="max-w-3xl">
          <div className="inline-block px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm text-primary-200 text-sm font-medium mb-6">
            {SCHOOL_INFO.motto}
          </div>
          <h1 className="text-4xl md:text-6xl lg:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
            {data?.title || SCHOOL_INFO.fullName}
          </h1>
          <p className="text-lg md:text-xl text-primary-100/90 max-w-2xl mb-8 leading-relaxed">
            {data?.content || SCHOOL_INFO.objective}
          </p>
          <div className="flex flex-wrap gap-4">
            <Link href="/public/admissions" className="btn-accent text-base px-8 py-4 shadow-lg shadow-accent-600/25 hover:shadow-accent-600/40 inline-flex items-center gap-2">
              Apply Now <HiArrowRight className="w-5 h-5" />
            </Link>
            <Link href="/public/about" className="inline-flex items-center gap-2 px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-white/20 transition-all border border-white/20">
              Learn More <HiChevronRight className="w-5 h-5" />
            </Link>
            <Link href="/public/elearning" className="inline-flex items-center gap-2 px-8 py-4 border-2 border-primary-400 text-primary-200 font-medium rounded-lg hover:bg-primary-800/50 transition-all">
              <HiPlay className="w-5 h-5" /> E-Learning
            </Link>
          </div>
        </div>
      </div>
      <div className="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-white to-transparent" />
    </section>
  );
}

function WelcomeSection({ data }: { data?: HomepageSection }) {
  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-4">
        <div className="max-w-4xl mx-auto text-center">
          <h2 className="section-title">{data?.title || `Welcome to ${SCHOOL_INFO.name}`}</h2>
          <div className="w-20 h-1 bg-primary-600 mx-auto rounded-full mb-6" />
          <p className="section-subtitle text-base md:text-lg leading-relaxed">
            {data?.content || SCHOOL_INFO.mission}
          </p>
          {data?.subtitle && (
            <p className="mt-4 text-primary-600 font-medium">{data.subtitle}</p>
          )}
        </div>
      </div>
    </section>
  );
}

function PrincipalMessageSection({ data }: { data?: HomepageSection }) {
  return (
    <section className="py-20 bg-gray-50">
      <div className="max-w-7xl mx-auto px-4">
        <div className="grid md:grid-cols-2 gap-12 items-center">
          <div className="relative">
            <div className="aspect-[4/5] rounded-2xl overflow-hidden bg-gradient-to-br from-primary-100 to-primary-200 shadow-xl">
              <div className="w-full h-full flex items-center justify-center">
                <HiPhotograph className="w-20 h-20 text-primary-400" />
              </div>
            </div>
            <div className="absolute -bottom-4 -right-4 w-32 h-32 bg-accent-500 rounded-2xl -z-10" />
          </div>
          <div>
            <h2 className="section-title">{data?.title || "Principal's Message"}</h2>
            <div className="w-20 h-1 bg-primary-600 rounded-full mb-6" />
            <div className="prose prose-lg text-gray-600 leading-relaxed">
              <p>{data?.content || 'Welcome to GIHEKE Technical Secondary School. We are committed to providing quality technical education that empowers our students with practical skills for a better future.'}</p>
            </div>
            {data?.subtitle && (
              <div className="mt-6 pt-6 border-t border-gray-200">
                <p className="font-heading font-semibold text-gray-900">{data.subtitle}</p>
                <p className="text-sm text-gray-500">Principal, {SCHOOL_INFO.name}</p>
              </div>
            )}
          </div>
        </div>
      </div>
    </section>
  );
}

function FeaturedProgramsSection() {
  const programs = TRADES.slice(0, 6);
  const icons = [HiAcademicCap, HiUserGroup, HiBookOpen, HiCalendar, HiStar, HiChevronRight];

  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-4">
        <div className="text-center mb-12">
          <h2 className="section-title">Our Programs</h2>
          <div className="w-20 h-1 bg-primary-600 mx-auto rounded-full mb-4" />
          <p className="section-subtitle">Choose from our comprehensive range of technical and vocational programs</p>
        </div>
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          {programs.map((program, index) => {
            const Icon = icons[index % icons.length];
            return (
              <div key={program.code} className="card p-6 border border-gray-100 hover:border-primary-200 hover:shadow-xl transition-all group">
                <div className="w-14 h-14 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center mb-4 group-hover:bg-primary-600 group-hover:text-white transition-all">
                  <Icon className="w-7 h-7" />
                </div>
                <h3 className="text-lg font-heading font-semibold text-gray-900 mb-2">{program.name}</h3>
                <p className="text-sm text-gray-500 mb-2">Code: {program.code}</p>
                <p className="text-gray-600 text-sm leading-relaxed mb-4">{program.description}</p>
                <Link href="/public/programs" className="text-primary-600 font-medium text-sm inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                  Learn More <HiArrowRight className="w-4 h-4" />
                </Link>
              </div>
            );
          })}
        </div>
        <div className="text-center mt-10">
          <Link href="/public/programs" className="btn-primary">
            View All Programs
          </Link>
        </div>
      </div>
    </section>
  );
}

function StatisticsSection() {
  const stats = [
    { icon: HiAcademicCap, value: 520, label: 'Students', suffix: '+', color: 'from-blue-500 to-blue-600' },
    { icon: HiUserGroup, value: 45, label: 'Teachers', suffix: '+', color: 'from-green-500 to-green-600' },
    { icon: HiBookOpen, value: 7, label: 'Programs', suffix: '', color: 'from-purple-500 to-purple-600' },
    { icon: HiCalendar, value: new Date().getFullYear() - 2010, label: 'Years of Excellence', suffix: '+', color: 'from-orange-500 to-orange-600' },
  ];

  return (
    <section className="py-20 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 relative overflow-hidden">
      <div className="absolute inset-0 opacity-10" style={{
        backgroundImage: 'radial-gradient(circle at 50% 50%, rgba(255,255,255,0.1) 0%, transparent 60%)'
      }} />
      <div className="relative max-w-7xl mx-auto px-4">
        <div className="text-center mb-12">
          <h2 className="text-3xl md:text-4xl font-heading font-bold text-white mb-4">Our Impact</h2>
          <div className="w-20 h-1 bg-accent-400 mx-auto rounded-full" />
        </div>
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-8">
          {stats.map((stat) => (
            <div key={stat.label} className="text-center p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10">
              <div className={`inline-flex p-4 rounded-xl bg-gradient-to-br ${stat.color} mb-4`}>
                <stat.icon className="w-8 h-8 text-white" />
              </div>
              <AnimatedCounter value={stat.value} suffix={stat.suffix} />
              <p className="text-primary-200 mt-2 font-medium">{stat.label}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function TestimonialsSection({ testimonials }: { testimonials: Testimonial[] }) {
  const items = testimonials.filter((t) => t.isActive);
  if (items.length === 0) return null;

  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-4">
        <div className="text-center mb-12">
          <h2 className="section-title">What People Say</h2>
          <div className="w-20 h-1 bg-primary-600 mx-auto rounded-full mb-4" />
          <p className="section-subtitle">Testimonials from our students, alumni, and partners</p>
        </div>
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          {items.slice(0, 6).map((testimonial) => (
            <div key={testimonial.id} className="card p-6 border border-gray-100 hover:border-primary-100 transition-all">
              <StarRating rating={testimonial.rating} />
              <p className="mt-4 text-gray-600 leading-relaxed text-sm italic">&ldquo;{testimonial.content}&rdquo;</p>
              <div className="mt-6 pt-4 border-t border-gray-100">
                <p className="font-heading font-semibold text-gray-900">{testimonial.name}</p>
                {testimonial.position && (
                  <p className="text-sm text-gray-500">{testimonial.position}</p>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function PartnersSection({ partners }: { partners: Partner[] }) {
  const items = partners.filter((p) => p.isActive);
  if (items.length === 0) return null;

  return (
    <section className="py-16 bg-gray-50">
      <div className="max-w-7xl mx-auto px-4">
        <div className="text-center mb-10">
          <h2 className="text-2xl md:text-3xl font-heading font-bold text-gray-900">Our Partners</h2>
          <div className="w-16 h-1 bg-primary-600 mx-auto rounded-full mt-4" />
        </div>
        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 items-center">
          {items.map((partner) => (
            <div key={partner.id} className="flex items-center justify-center p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
              {partner.logoUrl ? (
                <img src={partner.logoUrl} alt={partner.name} className="max-h-12 grayscale hover:grayscale-0 transition-all" />
              ) : (
                <div className="w-full h-12 bg-gray-100 rounded flex items-center justify-center">
                  <span className="text-sm font-medium text-gray-400">{partner.name}</span>
                </div>
              )}
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function CTASection() {
  return (
    <section className="py-20 bg-gradient-to-r from-primary-600 to-primary-800 relative overflow-hidden">
      <div className="absolute inset-0 opacity-10" style={{
        backgroundImage: 'radial-gradient(circle at 30% 50%, rgba(255,255,255,0.2) 0%, transparent 50%)'
      }} />
      <div className="relative max-w-4xl mx-auto px-4 text-center">
        <h2 className="text-3xl md:text-4xl font-heading font-bold text-white mb-4">
          Ready to Start Your Journey?
        </h2>
        <p className="text-primary-100 text-lg mb-8 max-w-2xl mx-auto">
          Join GIHEKE TSS and gain the practical skills you need to build a successful career. Applications are now open.
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

export default function HomePage() {
  const [data, setData] = useState<HomepageData>({});
  const [testimonials, setTestimonials] = useState<Testimonial[]>([]);
  const [partners, setPartners] = useState<Partner[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchData() {
      try {
        const [homepageRes, testimonialsRes, partnersRes] = await Promise.all([
          siteManagementApi.getPublicHomepageData(),
          siteManagementApi.getTestimonials(),
          siteManagementApi.getPartners(),
        ]);
        const homepageData = homepageRes.data?.data || homepageRes.data || {};
        setData({
          hero: homepageData.hero,
          welcome: homepageData.welcome,
          principalMessage: homepageData.principalMessage || homepageData.principal_message,
          featuredPrograms: homepageData.featuredPrograms || homepageData.featured_programs,
          statistics: homepageData.statistics,
          testimonials: homepageData.testimonials,
          partners: homepageData.partners,
        });
        setTestimonials(testimonialsRes.data?.data || testimonialsRes.data || []);
        setPartners(partnersRes.data?.data || partnersRes.data || []);
      } catch {
        // Fallback to empty state - sections will render with defaults
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
      <HeroSection data={data.hero} />
      <WelcomeSection data={data.welcome} />
      <PrincipalMessageSection data={data.principalMessage} />
      <FeaturedProgramsSection />
      <StatisticsSection />
      <TestimonialsSection testimonials={testimonials} />
      <PartnersSection partners={partners} />
      <CTASection />
    </PublicLayout>
  );
}
