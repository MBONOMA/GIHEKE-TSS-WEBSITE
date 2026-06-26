import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
  ManyToOne,
  JoinColumn,
} from 'typeorm';
import { Student } from './student.entity';
import { User } from './user.entity';

@Entity('results')
export class Result {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @ManyToOne(() => Student, (student) => student.results)
  @JoinColumn({ name: 'student_id' })
  studentId: Student;

  @Column()
  subject: string;

  @Column({ type: 'decimal', precision: 5, scale: 2 })
  score: number;

  @Column()
  grade: string;

  @Column({ type: 'int' })
  term: number;

  @Column({ name: 'academic_year' })
  academicYear: string;

  @ManyToOne(() => User, { eager: true })
  @JoinColumn({ name: 'uploaded_by' })
  uploadedBy: User;

  @Column({ name: 'is_published', default: false })
  isPublished: boolean;

  @CreateDateColumn({ name: 'created_at' })
  createdAt: Date;

  @UpdateDateColumn({ name: 'updated_at' })
  updatedAt: Date;
}
