import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
  ManyToOne,
  JoinColumn,
} from 'typeorm';
import { Program } from './program.entity';
import { User } from './user.entity';

export enum FileType {
  PDF = 'pdf',
  DOCUMENT = 'document',
  VIDEO = 'video',
  OTHER = 'other',
}

@Entity('elearning_materials')
export class ElearningMaterial {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @Column()
  title: string;

  @Column({ type: 'text', nullable: true })
  description: string;

  @Column({ name: 'file_url' })
  fileUrl: string;

  @Column({ type: 'enum', enum: FileType })
  fileType: FileType;

  @ManyToOne(() => Program, { nullable: true })
  @JoinColumn({ name: 'program_id' })
  programId: Program;

  @Column({ nullable: true })
  category: string;

  @Column({ nullable: true })
  subject: string;

  @Column({ type: 'int', nullable: true })
  year: number;

  @Column({ name: 'is_public', default: true })
  isPublic: boolean;

  @Column({ type: 'int', default: 0 })
  downloads: number;

  @ManyToOne(() => User, { eager: true })
  @JoinColumn({ name: 'uploaded_by' })
  uploadedBy: User;

  @CreateDateColumn({ name: 'created_at' })
  createdAt: Date;

  @UpdateDateColumn({ name: 'updated_at' })
  updatedAt: Date;
}
