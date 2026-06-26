'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import PublicLayout from '@/components/layout/PublicLayout';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { newsApi, eventsApi } from '@/lib/api';
import { NewsPost, Event } from '@/types';
import { formatDate } from '@/lib/utils';
import { HiCalendar, HiClock, HiLocationMarker, HiUser, HiChevronRight, HiArrowRight, HiPhotograph, HiTag } from 'react-icons/hi';

function HeroBanner() {
  return (
    <section className="relative py-24 md:py-32 overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950" />
      <div className="absolute inset-0 opacity-20" style={{
        backgroundImage: 'radial-gradient(circle at 30% 70%, rgba(59,130,246,0.3) 0%, transparent 50%), radial-gradient(circle at 70% 30%, rgba(22,163,74,0.2) 0%, transparent 50%)'
      }} />
      <div className="relative max-w-7xl mx-auto px-4 text-center">
        <h1 className="text-4xl md:text-6xl font-heading font-extrabold text-white mb-4">News & Events</h1>
        <div className="w-20 h-1 bg-accent-400 mx-auto rounded-full mb-4" />
        <p className="text-lg text-primary-100/90 max-w-2xl mx-auto">Stay updated with the latest news, announcements, and upcoming events at our school.</p>
      </div>
      <div className="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white to-transparent" />
    </section>
  );
}

function NewsCard({ post }: { post: NewsPost }) {
  return (
    <Link href={`/public/news/${post.slug}`} className="card border border-gray-100 hover:border-primary-200 hover:shadow-xl transition-all group block overflow-hidden">
      <div className="aspect-[16/9] bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center overflow-hidden">
        {post.featuredImage ? (
          <img src={post.featuredImage} alt={post.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
        ) : (
          <HiPhotograph className="w-16 h-16 text-primary-300" />
        )}
      </div>
      <div className="p-6">
        <div className="flex items-center gap-3 text-xs text-gray-500 mb-3">
          <span className="flex items-center gap-1">
            <HiCalendar className="w-3.5 h-3.5" />
            {formatDate(post.publishedAt || post.createdAt)}
          </span>
          {post.author && (
            <span className="flex items-center gap-1">
              <HiUser className="w-3.5 h-3.5" />
              {post.author.firstName} {post.author.lastName}
            </span>
          )}
        </div>
        <h3 className="font-heading font-semibold text-gray-900 group-hover:text-primary-600 transition-colors line-clamp-2 mb-2">
          {post.title}
        </h3>
        <p className="text-sm text-gray-600 line-clamp-3 mb-4">{post.excerpt || post.content.replace(/<[^>]*>/g, '').substring(0, 150)}</p>
        <div className="flex items-center gap-2 text-primary-600 font-medium text-sm">
          Read More <HiChevronRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
        </div>
      </div>
    </Link>
  );
}

function EventCard({ event }: { event: Event }) {
  const eventDate = new Date(event.eventDate);
  const day = eventDate.getDate();
  const month = eventDate.toLocaleString('default', { month: 'short' });

  return (
    <div className="card p-6 border border-gray-100 hover:border-accent-200 hover:shadow-xl transition-all flex gap-4">
      <div className="flex-shrink-0 w-16 h-16 rounded-xl bg-accent-50 text-accent-600 flex flex-col items-center justify-center">
        <span className="text-xl font-heading font-bold leading-none">{day}</span>
        <span className="text-xs font-medium mt-1">{month}</span>
      </div>
      <div className="flex-1 min-w-0">
        <h3 className="font-heading font-semibold text-gray-900 mb-1">{event.title}</h3>
        <div className="flex flex-wrap items-center gap-3 text-xs text-gray-500 mb-2">
          <span className="flex items-center gap-1">
            <HiClock className="w-3.5 h-3.5" />
            {event.eventTime || 'All day'}
          </span>
          <span className="flex items-center gap-1">
            <HiLocationMarker className="w-3.5 h-3.5" />
            {event.location}
          </span>
        </div>
        <p className="text-sm text-gray-600 line-clamp-2">{event.description}</p>
      </div>
    </div>
  );
}

function Pagination({ current, total, onPage }: { current: number; total: number; onPage: (page: number) => void }) {
  if (total <= 1) return null;
  return (
    <div className="flex items-center justify-center gap-2 mt-12">
      {Array.from({ length: total }, (_, i) => i + 1).map((page) => (
        <button
          key={page}
          onClick={() => onPage(page)}
          className={`w-10 h-10 rounded-lg text-sm font-medium transition-all ${
            page === current
              ? 'bg-primary-600 text-white shadow-md'
              : 'bg-white text-gray-600 border border-gray-200 hover:border-primary-300 hover:text-primary-600'
          }`}
        >
          {page}
        </button>
      ))}
    </div>
  );
}

export default function NewsPage() {
  const [activeTab, setActiveTab] = useState<'news' | 'events'>('news');
  const [news, setNews] = useState<NewsPost[]>([]);
  const [events, setEvents] = useState<Event[]>([]);
  const [loading, setLoading] = useState(true);
  const [newsPage, setNewsPage] = useState(1);
  const [eventsPage, setEventsPage] = useState(1);
  const [newsTotalPages, setNewsTotalPages] = useState(1);
  const [eventsTotalPages, setEventsTotalPages] = useState(1);
  const limit = 9;

  useEffect(() => {
    async function fetchNews() {
      try {
        const res = await newsApi.getAll({ page: newsPage, limit });
        const responseData = res.data;
        const items = responseData?.data || responseData?.news || responseData || [];
        setNews(Array.isArray(items) ? items : []);
        if (responseData?.meta) {
          setNewsTotalPages(responseData.meta.totalPages || 1);
        }
      } catch {
        setNews([]);
      }
    }

    async function fetchEvents() {
      try {
        const res = await eventsApi.getAll({ page: eventsPage, limit });
        const responseData = res.data;
        const items = responseData?.data || responseData?.events || responseData || [];
        setEvents(Array.isArray(items) ? items : []);
        if (responseData?.meta) {
          setEventsTotalPages(responseData.meta.totalPages || 1);
        }
      } catch {
        setEvents([]);
      }
    }

    async function fetchAll() {
      setLoading(true);
      await Promise.all([fetchNews(), fetchEvents()]);
      setLoading(false);
    }
    fetchAll();
  }, [newsPage, eventsPage]);

  return (
    <PublicLayout>
      <HeroBanner />

      <section className="py-12 bg-white border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex items-center gap-1 p-1 bg-gray-100 rounded-xl w-fit mx-auto">
            <button
              onClick={() => setActiveTab('news')}
              className={`px-6 py-2.5 rounded-lg text-sm font-medium transition-all ${
                activeTab === 'news' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-600 hover:text-gray-900'
              }`}
            >
              News
            </button>
            <button
              onClick={() => setActiveTab('events')}
              className={`px-6 py-2.5 rounded-lg text-sm font-medium transition-all ${
                activeTab === 'events' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-600 hover:text-gray-900'
              }`}
            >
              Events
            </button>
          </div>
        </div>
      </section>

      <section className="py-16 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4">
          {loading ? (
            <div className="py-20">
              <LoadingSpinner size="lg" />
            </div>
          ) : activeTab === 'news' ? (
            news.length === 0 ? (
              <div className="text-center py-20">
                <HiPhotograph className="w-16 h-16 text-gray-300 mx-auto mb-4" />
                <h3 className="text-xl font-heading font-semibold text-gray-500 mb-2">No News Yet</h3>
                <p className="text-gray-400">Check back later for the latest updates and announcements.</p>
              </div>
            ) : (
              <>
                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                  {news.map((post) => (
                    <NewsCard key={post.id} post={post} />
                  ))}
                </div>
                <Pagination current={newsPage} total={newsTotalPages} onPage={setNewsPage} />
              </>
            )
          ) : events.length === 0 ? (
            <div className="text-center py-20">
              <HiCalendar className="w-16 h-16 text-gray-300 mx-auto mb-4" />
              <h3 className="text-xl font-heading font-semibold text-gray-500 mb-2">No Events Scheduled</h3>
              <p className="text-gray-400">There are no upcoming events at the moment. Please check again later.</p>
            </div>
          ) : (
            <>
              <div className="grid md:grid-cols-2 gap-6">
                {events.map((event) => (
                  <EventCard key={event.id} event={event} />
                ))}
              </div>
              <Pagination current={eventsPage} total={eventsTotalPages} onPage={setEventsPage} />
            </>
          )}
        </div>
      </section>
    </PublicLayout>
  );
}
