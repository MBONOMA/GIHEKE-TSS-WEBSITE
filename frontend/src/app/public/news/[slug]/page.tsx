'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { useParams } from 'next/navigation';
import PublicLayout from '@/components/layout/PublicLayout';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { newsApi } from '@/lib/api';
import { NewsPost } from '@/types';
import { formatDate } from '@/lib/utils';
import { HiArrowLeft, HiCalendar, HiUser, HiEye, HiTag, HiShare, HiPhotograph } from 'react-icons/hi';

function ShareButton({ label, onClick }: { label: string; onClick: () => void }) {
  return (
    <button
      onClick={onClick}
      className="flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-primary-50 hover:text-primary-600 transition-all text-sm font-medium"
    >
      <HiShare className="w-4 h-4" />
      {label}
    </button>
  );
}

export default function NewsDetailPage() {
  const params = useParams();
  const slug = params?.slug as string;
  const [post, setPost] = useState<NewsPost | null>(null);
  const [related, setRelated] = useState<NewsPost[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!slug) return;
    async function fetchPost() {
      try {
        const res = await newsApi.getBySlug(slug);
        const postData = res.data?.data || res.data?.post || res.data;
        setPost(postData);
      } catch {
        setPost(null);
      } finally {
        setLoading(false);
      }
    }
    fetchPost();
  }, [slug]);

  useEffect(() => {
    if (!post) return;
    async function fetchRelated() {
      try {
        const res = await newsApi.getAll({ limit: 3 });
        const items = res.data?.data || res.data?.news || res.data || [];
        const relatedList = (Array.isArray(items) ? items : []).filter((n: NewsPost) => n.id !== post.id);
        setRelated(relatedList.slice(0, 3));
      } catch {
        setRelated([]);
      }
    }
    fetchRelated();
  }, [post]);

  const handleShare = (platform: string) => {
    const url = window.location.href;
    const title = post?.title || '';
    const urls: Record<string, string> = {
      facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
      twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`,
      linkedin: `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}`,
    };
    if (urls[platform]) {
      window.open(urls[platform], '_blank', 'noopener,noreferrer');
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

  if (!post) {
    return (
      <PublicLayout>
        <div className="min-h-[80vh] flex items-center justify-center">
          <div className="text-center max-w-md">
            <HiPhotograph className="w-20 h-20 text-gray-300 mx-auto mb-4" />
            <h1 className="text-2xl font-heading font-bold text-gray-900 mb-2">Article Not Found</h1>
            <p className="text-gray-500 mb-6">The article you are looking for does not exist or has been removed.</p>
            <Link href="/public/news" className="btn-primary">
              Back to News
            </Link>
          </div>
        </div>
      </PublicLayout>
    );
  }

  return (
    <PublicLayout>
      <article>
        <div className="relative h-[40vh] md:h-[50vh] bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 overflow-hidden">
          {post.featuredImage ? (
            <img src={post.featuredImage} alt={post.title} className="w-full h-full object-cover opacity-40" />
          ) : (
            <div className="absolute inset-0 flex items-center justify-center opacity-20">
              <HiPhotograph className="w-32 h-32 text-white" />
            </div>
          )}
          <div className="absolute inset-0 bg-gradient-to-t from-primary-900/90 via-primary-900/50 to-transparent" />
          <div className="absolute bottom-0 left-0 right-0 p-8 md:p-16">
            <div className="max-w-4xl mx-auto">
              <Link href="/public/news" className="inline-flex items-center gap-2 text-primary-200 hover:text-white mb-4 transition-colors text-sm">
                <HiArrowLeft className="w-4 h-4" /> Back to News
              </Link>
              <h1 className="text-3xl md:text-5xl font-heading font-bold text-white leading-tight">{post.title}</h1>
            </div>
          </div>
        </div>

        <div className="max-w-4xl mx-auto px-4 py-8">
          <div className="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-8 pb-6 border-b border-gray-200">
            <span className="flex items-center gap-1.5">
              <HiCalendar className="w-4 h-4" />
              {formatDate(post.publishedAt || post.createdAt)}
            </span>
            {post.author && (
              <span className="flex items-center gap-1.5">
                <HiUser className="w-4 h-4" />
                {post.author.firstName} {post.author.lastName}
              </span>
            )}
            <span className="flex items-center gap-1.5">
              <HiEye className="w-4 h-4" />
              {post.viewCount || 0} views
            </span>
            {post.tags && post.tags.length > 0 && (
              <div className="flex items-center gap-2 flex-wrap">
                <HiTag className="w-4 h-4" />
                {post.tags.map((tag) => (
                  <span key={tag} className="px-2 py-0.5 rounded-full bg-primary-50 text-primary-700 text-xs font-medium">
                    {tag}
                  </span>
                ))}
              </div>
            )}
          </div>

          <div
            className="prose prose-lg max-w-none prose-headings:font-heading prose-headings:text-gray-900 prose-p:text-gray-600 prose-a:text-primary-600 prose-img:rounded-xl prose-img:shadow-lg"
            dangerouslySetInnerHTML={{ __html: post.content }}
          />

          <div className="mt-12 pt-8 border-t border-gray-200">
            <p className="text-sm font-medium text-gray-500 mb-3">Share this article:</p>
            <div className="flex flex-wrap gap-3">
              <ShareButton label="Facebook" onClick={() => handleShare('facebook')} />
              <ShareButton label="Twitter" onClick={() => handleShare('twitter')} />
              <ShareButton label="LinkedIn" onClick={() => handleShare('linkedin')} />
              <ShareButton label="Copy Link" onClick={() => {
                navigator.clipboard.writeText(window.location.href);
              }} />
            </div>
          </div>
        </div>
      </article>

      {related.length > 0 && (
        <section className="py-16 bg-gray-50">
          <div className="max-w-7xl mx-auto px-4">
            <h2 className="text-2xl font-heading font-bold text-gray-900 mb-8">Related News</h2>
            <div className="grid md:grid-cols-3 gap-8">
              {related.map((item) => (
                <Link
                  key={item.id}
                  href={`/public/news/${item.slug}`}
                  className="card border border-gray-100 hover:border-primary-200 hover:shadow-xl transition-all p-6"
                >
                  <div className="flex items-center gap-2 text-xs text-gray-500 mb-2">
                    <HiCalendar className="w-3.5 h-3.5" />
                    {formatDate(item.publishedAt || item.createdAt)}
                  </div>
                  <h3 className="font-heading font-semibold text-gray-900 hover:text-primary-600 transition-colors line-clamp-2 mb-2">
                    {item.title}
                  </h3>
                  <p className="text-sm text-gray-600 line-clamp-2">{item.excerpt || item.content.replace(/<[^>]*>/g, '').substring(0, 120)}</p>
                </Link>
              ))}
            </div>
          </div>
        </section>
      )}
    </PublicLayout>
  );
}
