import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
} from 'typeorm';

@Entity('visitor_analytics')
export class VisitorAnalytics {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @Column()
  page: string;

  @Column({ name: 'ip_address', nullable: true })
  ipAddress: string;

  @Column({ name: 'user_agent', nullable: true })
  userAgent: string;

  @Column({ nullable: true })
  referrer: string;

  @Column({ name: 'visited_at', type: 'timestamp', default: () => 'CURRENT_TIMESTAMP' })
  visitedAt: Date;
}
