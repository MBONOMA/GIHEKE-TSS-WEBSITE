import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
} from 'typeorm';

export enum SectionType {
  HERO = 'hero',
  WELCOME = 'welcome',
  PRINCIPAL_MESSAGE = 'principal_message',
  FEATURED_PROGRAMS = 'featured_programs',
  STATISTICS = 'statistics',
  TESTIMONIALS = 'testimonials',
  PARTNERS = 'partners',
}

@Entity('homepage_sections')
export class HomepageSection {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @Column({ type: 'enum', enum: SectionType })
  sectionType: SectionType;

  @Column({ nullable: true })
  title: string;

  @Column({ nullable: true })
  subtitle: string;

  @Column({ type: 'text', nullable: true })
  content: string;

  @Column({ name: 'image_url', nullable: true })
  imageUrl: string;

  @Column({ name: 'video_url', nullable: true })
  videoUrl: string;

  @Column({ name: 'cta_text', nullable: true })
  ctaText: string;

  @Column({ name: 'cta_link', nullable: true })
  ctaLink: string;

  @Column({ type: 'json', nullable: true })
  items: object;

  @Column({ name: 'is_active', default: true })
  isActive: boolean;

  @Column({ name: 'sort_order', type: 'int', default: 0 })
  sortOrder: number;

  @CreateDateColumn({ name: 'created_at' })
  createdAt: Date;

  @UpdateDateColumn({ name: 'updated_at' })
  updatedAt: Date;
}
