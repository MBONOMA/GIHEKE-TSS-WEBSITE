'use client';

import { useState, useEffect } from 'react';
import PublicLayout from '@/components/layout/PublicLayout';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { galleryApi } from '@/lib/api';
import { GalleryItem } from '@/types';
import { HiPhotograph, HiFilm, HiX, HiChevronLeft, HiChevronRight } from 'react-icons/hi';

function HeroBanner() {
  return (
    <section className="relative py-24 md:py-32 overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950" />
      <div className="absolute inset-0 opacity-20" style={{
        backgroundImage: 'radial-gradient(circle at 30% 70%, rgba(59,130,246,0.3) 0%, transparent 50%), radial-gradient(circle at 70% 30%, rgba(22,163,74,0.2) 0%, transparent 50%)'
      }} />
      <div className="relative max-w-7xl mx-auto px-4 text-center">
        <h1 className="text-4xl md:text-6xl font-heading font-extrabold text-white mb-4">Gallery</h1>
        <div className="w-20 h-1 bg-accent-400 mx-auto rounded-full mb-4" />
        <p className="text-lg text-primary-100/90 max-w-2xl mx-auto">Explore moments captured at our school - campus life, events, classrooms, and more.</p>
      </div>
      <div className="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white to-transparent" />
    </section>
  );
}

function Lightbox({ items, currentIndex, onClose, onNavigate }: {
  items: GalleryItem[];
  currentIndex: number;
  onClose: () => void;
  onNavigate: (index: number) => void;
}) {
  const item = items[currentIndex];
  if (!item) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4" onClick={onClose}>
      <button onClick={onClose} className="absolute top-4 right-4 p-2 text-white/80 hover:text-white z-10">
        <HiX className="w-8 h-8" />
      </button>
      {items.length > 1 && (
        <>
          <button
            onClick={(e) => { e.stopPropagation(); onNavigate(currentIndex - 1); }}
            className="absolute left-4 p-2 text-white/80 hover:text-white z-10 disabled:opacity-30"
            disabled={currentIndex === 0}
          >
            <HiChevronLeft className="w-8 h-8" />
          </button>
          <button
            onClick={(e) => { e.stopPropagation(); onNavigate(currentIndex + 1); }}
            className="absolute right-4 p-2 text-white/80 hover:text-white z-10 disabled:opacity-30"
            disabled={currentIndex === items.length - 1}
          >
            <HiChevronRight className="w-8 h-8" />
          </button>
        </>
      )}
      <div className="max-w-5xl max-h-[85vh]" onClick={(e) => e.stopPropagation()}>
        {item.fileType === 'video' ? (
          <video src={item.fileUrl} controls className="max-w-full max-h-[85vh] rounded-xl" />
        ) : (
          <img src={item.fileUrl} alt={item.title || ''} className="max-w-full max-h-[85vh] object-contain rounded-xl" />
        )}
        {item.title && (
          <p className="text-white text-center mt-4 text-lg font-medium">{item.title}</p>
        )}
      </div>
      <div className="absolute bottom-4 text-white/60 text-sm">
        {currentIndex + 1} / {items.length}
      </div>
    </div>
  );
}

export default function GalleryPage() {
  const [items, setItems] = useState<GalleryItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeCategory, setActiveCategory] = useState('All');
  const [lightboxIndex, setLightboxIndex] = useState<number | null>(null);

  useEffect(() => {
    async function fetchGallery() {
      try {
        const res = await galleryApi.getAll();
        const data = res.data?.data || res.data?.items || res.data || [];
        setItems(Array.isArray(data) ? data.filter((item: GalleryItem) => item.isPublished) : []);
      } catch {
        setItems([]);
      } finally {
        setLoading(false);
      }
    }
    fetchGallery();
  }, []);

  const categories = ['All', ...Array.from(new Set(items.map((item) => item.category || 'Uncategorized')))];

  const filteredItems = activeCategory === 'All'
    ? items
    : items.filter((item) => (item.category || 'Uncategorized') === activeCategory);

  const images = filteredItems.filter((item) => item.fileType === 'image');
  const videos = filteredItems.filter((item) => item.fileType === 'video');

  const openLightbox = (index: number) => setLightboxIndex(index);
  const closeLightbox = () => setLightboxIndex(null);
  const navigateLightbox = (newIndex: number) => setLightboxIndex(newIndex);

  return (
    <PublicLayout>
      <HeroBanner />

      <section className="py-12 bg-white border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex flex-wrap items-center gap-2 justify-center">
            {categories.map((cat) => (
              <button
                key={cat}
                onClick={() => setActiveCategory(cat)}
                className={`px-5 py-2 rounded-full text-sm font-medium transition-all ${
                  activeCategory === cat
                    ? 'bg-primary-600 text-white shadow-md'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                }`}
              >
                {cat}
              </button>
            ))}
          </div>
        </div>
      </section>

      <section className="py-16 bg-gray-50 min-h-[50vh]">
        <div className="max-w-7xl mx-auto px-4">
          {loading ? (
            <div className="py-20">
              <LoadingSpinner size="lg" />
            </div>
          ) : filteredItems.length === 0 ? (
            <div className="text-center py-20">
              <HiPhotograph className="w-20 h-20 text-gray-300 mx-auto mb-4" />
              <h3 className="text-xl font-heading font-semibold text-gray-500 mb-2">No Media Yet</h3>
              <p className="text-gray-400">There are no photos or videos in this category yet. Check back later.</p>
            </div>
          ) : (
            <>
              {images.length > 0 && (
                <>
                  <h3 className="text-lg font-heading font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <HiPhotograph className="w-5 h-5 text-primary-600" /> Photos ({images.length})
                  </h3>
                  <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-10">
                    {images.map((item, index) => (
                      <div
                        key={item.id}
                        onClick={() => openLightbox(filteredItems.indexOf(item))}
                        className="aspect-[4/3] rounded-xl overflow-hidden bg-gradient-to-br from-primary-50 to-primary-100 cursor-pointer group relative shadow-sm hover:shadow-xl transition-all"
                      >
                        {item.fileUrl ? (
                          <img
                            src={item.fileUrl}
                            alt={item.title || ''}
                            className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                          />
                        ) : (
                          <div className="w-full h-full flex items-center justify-center">
                            <HiPhotograph className="w-12 h-12 text-primary-300" />
                          </div>
                        )}
                        <div className="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-end">
                          {item.title && (
                            <p className="text-white text-sm p-3 opacity-0 group-hover:opacity-100 transition-opacity font-medium">
                              {item.title}
                            </p>
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                </>
              )}

              {videos.length > 0 && (
                <>
                  <h3 className="text-lg font-heading font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <HiFilm className="w-5 h-5 text-primary-600" /> Videos ({videos.length})
                  </h3>
                  <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    {videos.map((item, index) => (
                      <div
                        key={item.id}
                        onClick={() => openLightbox(filteredItems.indexOf(item))}
                        className="aspect-[4/3] rounded-xl overflow-hidden bg-gray-900 cursor-pointer group relative shadow-sm hover:shadow-xl transition-all"
                      >
                        {item.fileUrl ? (
                          <video src={item.fileUrl} className="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-all" />
                        ) : (
                          <div className="w-full h-full flex items-center justify-center">
                            <HiFilm className="w-12 h-12 text-gray-400" />
                          </div>
                        )}
                        <div className="absolute inset-0 flex items-center justify-center">
                          <div className="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover:bg-white/40 transition-all">
                            <HiFilm className="w-8 h-8 text-white" />
                          </div>
                        </div>
                        {item.title && (
                          <p className="absolute bottom-0 left-0 right-0 text-white text-sm p-3 bg-gradient-to-t from-black/60 to-transparent font-medium">
                            {item.title}
                          </p>
                        )}
                      </div>
                    ))}
                  </div>
                </>
              )}
            </>
          )}
        </div>
      </section>

      {lightboxIndex !== null && (
        <Lightbox
          items={filteredItems}
          currentIndex={lightboxIndex}
          onClose={closeLightbox}
          onNavigate={navigateLightbox}
        />
      )}
    </PublicLayout>
  );
}
