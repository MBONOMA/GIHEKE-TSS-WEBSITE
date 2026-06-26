import type { Metadata } from 'next';
import '@/styles/globals.css';
import Providers from './providers';

export const metadata: Metadata = {
  title: 'GIHEKE TSS - Technical Secondary School',
  description: "GIHEKE Technical Secondary School - We're our country's solutions providers. From training to doing.",
  keywords: ['GIHEKE TSS', 'technical school', 'secondary school', 'Rusizi', 'Rwanda', 'education', 'vocational training'],
  openGraph: {
    title: 'GIHEKE TSS - Technical Secondary School',
    description: "From training to doing - Empowering practical skills for better future.",
    type: 'website',
  },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <body>
        <Providers>{children}</Providers>
      </body>
    </html>
  );
}
