'use client';

import { useState, useEffect } from 'react';
import PublicLayout from '@/components/layout/PublicLayout';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { elearningApi } from '@/lib/api';
import { ElearningMaterial } from '@/types';
import { TRADES } from '@/lib/constants';
import { formatDate } from '@/lib/utils';
import { HiSearch, HiDocumentDownload, HiBookOpen, HiDownload, HiFilm, HiDocumentText, HiFolder, HiChevronDown } from 'react-icons/hi';

const FILE_TYPE_ICONS: Record<string, React.ComponentType<{ className?: string }>> = {
  pdf: HiDocumentText,
  document: HiDocumentText,
  video: HiFilm,
  other: HiFolder,
};

const CATEGORIES = ['Lesson Notes', 'Past Papers', 'Assignments', 'Reference Books', 'Syllabus', 'Other'];

function HeroBanner() {
  return (
    <section className="relative py-24 md:py-32 overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950" />
      <div className="absolute inset-0 opacity-20" style={{
        backgroundImage: 'radial-gradient(circle at 30% 70%, rgba(59,130,246,0.3) 0%, transparent 50%), radial-gradient(circle at 70% 30%, rgba(22,163,74,0.2) 0%, transparent 50%)'
      }} />
      <div className="relative max-w-7xl mx-auto px-4 text-center">
        <h1 className="text-4xl md:text-6xl font-heading font-extrabold text-white mb-4">E-Learning</h1>
        <div className="w-20 h-1 bg-accent-400 mx-auto rounded-full mb-4" />
        <p className="text-lg text-primary-100/90 max-w-2xl mx-auto">Access learning materials, notes, past papers, and resources for all our programs.</p>
      </div>
      <div className="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white to-transparent" />
    </section>
  );
}

function MaterialCard({ material }: { material: ElearningMaterial }) {
  const Icon = FILE_TYPE_ICONS[material.fileType] || HiDocumentText;
  const isPdf = material.fileType === 'pdf' || material.fileUrl?.endsWith('.pdf');

  const handleClick = () => {
    if (material.fileUrl) {
      window.open(material.fileUrl, '_blank', 'noopener,noreferrer');
    }
  };

  return (
    <div
      onClick={handleClick}
      className="card p-5 border border-gray-100 hover:border-primary-200 hover:shadow-xl transition-all cursor-pointer group"
    >
      <div className="flex items-start gap-4">
        <div className="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-600 group-hover:text-white transition-all">
          <Icon className="w-6 h-6" />
        </div>
        <div className="flex-1 min-w-0">
          <h3 className="font-heading font-semibold text-gray-900 text-sm group-hover:text-primary-600 transition-colors line-clamp-2">
            {material.title}
          </h3>
          {material.description && (
            <p className="text-xs text-gray-500 mt-1 line-clamp-2">{material.description}</p>
          )}
          <div className="flex flex-wrap items-center gap-2 mt-3">
            {material.program && (
              <span className="text-xs px-2 py-0.5 rounded-full bg-primary-50 text-primary-700">
                {material.program.name}
              </span>
            )}
            <span className="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
              {material.category}
            </span>
            <span className="flex items-center gap-1 text-xs text-gray-400 ml-auto">
              <HiDownload className="w-3.5 h-3.5" />
              {material.downloads || 0}
            </span>
          </div>
          <p className="text-xs text-gray-400 mt-2">{formatDate(material.createdAt)}</p>
        </div>
      </div>
    </div>
  );
}

export default function ElearningPage() {
  const [materials, setMaterials] = useState<ElearningMaterial[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedProgram, setSelectedProgram] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('');

  useEffect(() => {
    async function fetchMaterials() {
      try {
        const params: Record<string, string> = {};
        if (searchQuery) params.search = searchQuery;
        if (selectedProgram) params.programId = selectedProgram;
        if (selectedCategory) params.category = selectedCategory;

        const res = await elearningApi.getAll(params);
        const data = res.data?.data || res.data?.materials || res.data || [];
        setMaterials(Array.isArray(data) ? data : []);
      } catch {
        setMaterials([]);
      } finally {
        setLoading(false);
      }
    }
    fetchMaterials();
  }, [searchQuery, selectedProgram, selectedCategory]);

  return (
    <PublicLayout>
      <HeroBanner />

      <section className="py-12 bg-white border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex flex-col md:flex-row gap-4">
            <div className="flex-1 relative">
              <HiSearch className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search materials..."
                className="input-field pl-12"
              />
            </div>
            <select
              value={selectedProgram}
              onChange={(e) => setSelectedProgram(e.target.value)}
              className="input-field md:w-64"
            >
              <option value="">All Programs</option>
              {TRADES.map((trade) => (
                <option key={trade.code} value={trade.code}>{trade.name}</option>
              ))}
            </select>
            <select
              value={selectedCategory}
              onChange={(e) => setSelectedCategory(e.target.value)}
              className="input-field md:w-48"
            >
              <option value="">All Categories</option>
              {CATEGORIES.map((cat) => (
                <option key={cat} value={cat}>{cat}</option>
              ))}
            </select>
          </div>
        </div>
      </section>

      <section className="py-16 bg-gray-50 min-h-[50vh]">
        <div className="max-w-7xl mx-auto px-4">
          {loading ? (
            <div className="py-20">
              <LoadingSpinner size="lg" />
            </div>
          ) : materials.length === 0 ? (
            <div className="text-center py-20">
              <HiBookOpen className="w-20 h-20 text-gray-300 mx-auto mb-4" />
              <h3 className="text-xl font-heading font-semibold text-gray-500 mb-2">No Materials Found</h3>
              <p className="text-gray-400 max-w-md mx-auto">
                {searchQuery || selectedProgram || selectedCategory
                  ? 'No materials match your search criteria. Try adjusting your filters.'
                  : 'There are no learning materials available yet. Please check back later.'}
              </p>
              {(searchQuery || selectedProgram || selectedCategory) && (
                <button
                  onClick={() => { setSearchQuery(''); setSelectedProgram(''); setSelectedCategory(''); }}
                  className="mt-4 text-primary-600 font-medium text-sm hover:underline"
                >
                  Clear all filters
                </button>
              )}
            </div>
          ) : (
            <>
              <p className="text-sm text-gray-500 mb-6">{materials.length} material{materials.length !== 1 ? 's' : ''} found</p>
              <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {materials.map((material) => (
                  <MaterialCard key={material.id} material={material} />
                ))}
              </div>
            </>
          )}
        </div>
      </section>
    </PublicLayout>
  );
}
