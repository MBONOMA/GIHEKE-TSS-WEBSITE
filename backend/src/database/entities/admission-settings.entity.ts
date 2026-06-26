import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
  ManyToOne,
  JoinColumn,
} from 'typeorm';
import { User } from './user.entity';

@Entity('admission_settings')
export class AdmissionSettings {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @Column({ name: 'is_open', default: false })
  isOpen: boolean;

  @Column({ name: 'open_from', type: 'date', nullable: true })
  openFrom: Date;

  @Column({ name: 'open_until', type: 'date', nullable: true })
  openUntil: Date;

  @Column({ name: 'auto_close', default: false })
  autoClose: boolean;

  @Column({ name: 'max_applications', type: 'int', nullable: true })
  maxApplications: number;

  @Column({ name: 'current_applications', type: 'int', default: 0 })
  currentApplications: number;

  @Column({ type: 'text', nullable: true })
  message: string;

  @ManyToOne(() => User, { eager: true })
  @JoinColumn({ name: 'updated_by' })
  updatedBy: User;

  @CreateDateColumn({ name: 'created_at' })
  createdAt: Date;

  @UpdateDateColumn({ name: 'updated_at' })
  updatedAt: Date;
}
