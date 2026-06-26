'use client';

import { useState, useEffect, useRef, useCallback } from 'react';
import PublicLayout from '@/components/layout/PublicLayout';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { elearningApi } from '@/lib/api';
import { ElearningMaterial } from '@/types';
import { TRADES } from '@/lib/constants';
import { formatDate } from '@/lib/utils';
import { HiSearch, HiDocumentDownload, HiBookOpen, HiDownload, HiFilm, HiDocumentText, HiFolder, HiX, HiClock, HiTrendingUp, HiFilter } from 'react-icons/hi';

const FILE_TYPE_ICONS: Record<string, React.ComponentType<{ className?: string }>> = {
  pdf: HiDocumentText,
  document: HiDocumentText,
  video: HiFilm,
  other: HiFolder,
};

const CATEGORIES = ['Lesson Notes', 'Past Papers', 'Assignments', 'Reference Books', 'Syllabus', 'Other'];

const RECENT_SEARCHES = ['Mathematics Notes', 'Physics Past Paper', 'Grade 10 Syllabus', 'Computer Science Textbook', 'Chemistry Lab Manual'];
const TRENDING_SEARCHES = ['End of Year Exam', 'Revision Notes', 'Practical Assignments', 'Project Guidelines', 'Study Guide'];

function MaterialCard({ material }: { material: ElearningMaterial }) {
  const Icon = FILE_TYPE_ICONS[material.fileType] || HiDocumentText;

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
  const [suggestions, setSuggestions] = useState<string[]>([]);
  const [recentSearches, setRecentSearches] = useState<string[]>([]);
  const [trendingSearches] = useState<string[]>(TRENDING_SEARCHES);
  const [showDropdown, setShowDropdown] = useState(false);
  const [noResults, setNoResults] = useState(false);
  const [showFilters, setShowFilters] = useState(false);
  const debounceTimer = useRef<NodeJS.Timeout | null>(null);
  const searchRef = useRef<HTMLDivElement>(null);

  const popularCategories = [...CATEGORIES].sort(() => Math.random() - 0.5).slice(0, 3);

  useEffect(() => {
    const stored = localStorage.getItem('recentSearches');
    if (stored) {
      try {
        setRecentSearches(JSON.parse(stored).slice(0, 5));
      } catch {}
    }
  }, []);

  const saveRecentSearch = (query: string) => {
    const updated = [query, ...recentSearches.filter(s => s !== query)].slice(0, 5);
    setRecentSearches(updated);
    localStorage.setItem('recentSearches', JSON.stringify(updated));
  };

  const highlightMatch = (text: string, query: string) => {
    if (!query) return text;
    const index = text.toLowerCase().indexOf(query.toLowerCase());
    if (index === -1) return text;
    const before = text.slice(0, index);
    const match = text.slice(index, index + query.length);
    const after = text.slice(index + query.length);
    return (
      <span>
        {before}<strong className="text-primary-700">{match}</strong>{after}
      </span>
    );
  };

  const fetchMaterials = useCallback(async (query: string) => {
    try {
      setLoading(true);
      const params: Record<string, string> = {};
      if (query) params.search = query;
      if (selectedProgram) params.programId = selectedProgram;
      if (selectedCategory) params.category = selectedCategory;

      const res = await elearningApi.getAll(params);
      const data = res.data?.data || res.data?.materials || res.data || [];
      const items = Array.isArray(data) ? data : [];
      setMaterials(items);
      setNoResults(items.length === 0 && query.length > 0);
    } catch {
      setMaterials([]);
      setNoResults(query.length > 0);
    } finally {
      setLoading(false);
    }
  }, [selectedProgram, selectedCategory]);

  useEffect(() => {
    if (debounceTimer.current) clearTimeout(debounceTimer.current);
    debounceTimer.current = setTimeout(() => {
      if (searchQuery.trim()) {
        fetchMaterials(searchQuery.trim());
      } else {
        fetchMaterials('');
        setNoResults(false);
      }
    }, 300);
    return () => {
      if (debounceTimer.current) clearTimeout(debounceTimer.current);
    };
  }, [searchQuery, fetchMaterials]);

  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (searchRef.current && !searchRef.current.contains(event.target as Node)) {
        setShowDropdown(false);
      }
    }
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setSearchQuery(value);
    if (value.trim()) {
      const filtered = [
        ...recentSearches.filter(s => s.toLowerCase().includes(value.toLowerCase())),
        ...TRENDING_SEARCHES.filter(s => s.toLowerCase().includes(value.toLowerCase()))
      ].slice(0, 5);
      setSuggestions(filtered);
      setShowDropdown(true);
    } else {
      setSuggestions([]);
      setShowDropdown(true);
    }
  };

  const handleInputFocus = () => {
    setShowDropdown(true);
  };

  const handleClear = () => {
    setSearchQuery('');
    setSuggestions([]);
    setShowDropdown(false);
    fetchMaterials('');
    setNoResults(false);
  };

  const handleSuggestionClick = (suggestion: string) => {
    setSearchQuery(suggestion);
    saveRecentSearch(suggestion);
    setShowDropdown(false);
    fetchMaterials(suggestion);
  };

  const handleRecentClick = (recent: string) => {
    setSearchQuery(recent);
    saveRecentSearch(recent);
    setShowDropdown(false);
    fetchMaterials(recent);
  };

  const handleCategoryClick = (category: string) => {
    setSelectedCategory(category);
    setSearchQuery('');
    setShowDropdown(false);
  };

  return (
    <PublicLayout>
      {/* Search Bar - Prominent under navigation */}
      <div className="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
        <div className="max-w-7xl mx-auto px-4 py-4">
          <div className="relative max-w-3xl mx-auto" ref={searchRef}>
            <HiSearch className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" />
            <input
              type="text"
              value={searchQuery}
              onChange={handleInputChange}
              onFocus={handleInputFocus}
              placeholder="Search materials, notes, past papers..."
              className={`input-field pl-12 pr-10 w-full text-base transition-all duration-300 ${showDropdown ? 'ring-2 ring-primary-500 border-primary-500' : ''}`}
            />
            {searchQuery && (
              <button
                type="button"
                onClick={handleClear}
                className="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 transition-colors"
                aria-label="Clear search"
              >
                <HiX className="w-4 h-4" />
              </button>
            )}

            {showDropdown && (
              <div className="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 mt-2 max-h-96 overflow-y-auto">
                {suggestions.length > 0 ? (
                  <div className="p-2">
                    {suggestions.map((suggestion, idx) => (
                      <button
                        key={idx}
                        type="button"
                        onClick={() => handleSuggestionClick(suggestion)}
                        className="w-full text-left px-4 py-3 hover:bg-primary-50 rounded-lg text-sm text-gray-700 transition-colors"
                      >
                        {highlightMatch(suggestion, searchQuery)}
                      </button>
                    ))}
                  </div>
                ) : searchQuery.length === 0 ? (
                  <div>
                    <div className="p-3 border-b border-gray-100">
                      <div className="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        <HiClock className="w-4 h-4" /> Recent Searches
                      </div>
                      {recentSearches.length > 0 ? (
                        <div className="flex flex-wrap gap-2">
                          {recentSearches.map((recent, idx) => (
                            <button
                              key={idx}
                              type="button"
                              onClick={() => handleRecentClick(recent)}
                              className="px-3 py-1.5 bg-gray-100 hover:bg-primary-100 hover:text-primary-700 rounded-full text-xs text-gray-700 transition-colors"
                            >
                              {recent}
                            </button>
                          ))}
                        </div>
                      ) : (
                        <p className="text-xs text-gray-400">No recent searches</p>
                      )}
                    </div>
                    <div className="p-3">
                      <div className="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        <HiTrendingUp className="w-4 h-4" /> Trending Searches
                      </div>
                      <div className="flex flex-wrap gap-2">
                        {trendingSearches.map((trending, idx) => (
                          <button
                            key={idx}
                            type="button"
                            onClick={() => handleSuggestionClick(trending)}
                            className="px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-orange-700 rounded-full text-xs font-medium transition-colors"
                          >
                            {trending}
                          </button>
                        ))}
                      </div>
                    </div>
                  </div>
                ) : null}
              </div>
            )}
          </div>

          {/* Filter Toggle Button (Mobile) */}
          <div className="flex justify-center mt-3 md:hidden">
            <button
              onClick={() => setShowFilters(!showFilters)}
              className="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 transition-colors"
            >
              <HiFilter className="w-4 h-4" />
              {showFilters ? 'Hide Filters' : 'Show Filters'}
            </button>
          </div>
        </div>
      </div>

      {/* Filters Section */}
      <section className={`bg-gray-50 border-b border-gray-200 transition-all duration-300 ${showFilters ? 'py-4' : 'py-0 hidden md:block'}`}>
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex flex-wrap items-center gap-3">
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
            {(selectedProgram || selectedCategory) && (
              <button
                onClick={() => { setSelectedProgram(''); setSelectedCategory(''); }}
                className="text-sm text-primary-600 font-medium hover:underline"
              >
                Clear filters
              </button>
            )}
          </div>
        </div>
      </section>

      {/* Results Section */}
      <section className="py-16 bg-gray-50 min-h-[50vh]">
        <div className="max-w-7xl mx-auto px-4">
          {loading ? (
            <div className="py-20">
              <LoadingSpinner size="lg" />
            </div>
          ) : noResults ? (
            <div className="text-center py-20">
              <HiBookOpen className="w-20 h-20 text-gray-300 mx-auto mb-4" />
              <h3 className="text-xl font-heading font-semibold text-gray-500 mb-2">
                No results found for &ldquo;{searchQuery}&rdquo;
              </h3>
              <p className="text-gray-400 max-w-md mx-auto mb-6">
                Try browsing one of our popular categories below or adjust your search.
              </p>
              <div className="flex flex-wrap justify-center gap-3">
                {popularCategories.map((cat) => (
                  <button
                    key={cat}
                    onClick={() => handleCategoryClick(cat)}
                    className="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 hover:border-primary-300 hover:text-primary-700 transition-colors shadow-sm"
                  >
                    {cat}
                  </button>
                ))}
              </div>
              <button
                onClick={() => { setSearchQuery(''); setSelectedProgram(''); setSelectedCategory(''); }}
                className="mt-6 text-primary-600 font-medium text-sm hover:underline"
              >
                Clear all filters
              </button>
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
