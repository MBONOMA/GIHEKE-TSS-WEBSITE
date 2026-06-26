import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
  ManyToOne,
  JoinColumn,
  OneToMany,
} from 'typeorm';
import { User } from './user.entity';
import { Program } from './program.entity';
import { Result } from './result.entity';
import { Attendance } from './attendance.entity';
import { Fee } from './fee.entity';
import { AssignmentSubmission } from './assignment-submission.entity';

export enum Gender {
  MALE = 'male',
  FEMALE = 'female',
  OTHER = 'other',
}

export enum StudentStatus {
  ACTIVE = 'active',
  GRADUATED = 'graduated',
  SUSPENDED = 'suspended',
  EXPELLED = 'expelled',
}

@Entity('students')
export class Student {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @ManyToOne(() => User, { eager: true })
  @JoinColumn({ name: 'user_id' })
  userId: User;

  @Column({ name: 'sdms_code', unique: true })
  sdmsCode: string;

  @Column({ name: 'date_of_birth', type: 'date' })
  dateOfBirth: Date;

  @Column({ type: 'enum', enum: Gender })
  gender: Gender;

  @Column({ nullable: true })
  address: string;

  @Column({ name: 'previous_school', nullable: true })
  previousSchool: string;

  @Column({ name: 'enrollment_date', type: 'date' })
  enrollmentDate: Date;

  @ManyToOne(() => Program, { eager: true })
  @JoinColumn({ name: 'program_id' })
  programId: Program;

  @Column({ type: 'enum', enum: StudentStatus, default: StudentStatus.ACTIVE })
  status: StudentStatus;

  @Column({ name: 'guardian_name', nullable: true })
  guardianName: string;

  @Column({ name: 'guardian_phone', nullable: true })
  guardianPhone: string;

  @Column({ name: 'guardian_email', nullable: true })
  guardianEmail: string;

  @CreateDateColumn({ name: 'created_at' })
  createdAt: Date;

  @UpdateDateColumn({ name: 'updated_at' })
  updatedAt: Date;

  @OneToMany(() => Result, (result) => result.studentId)
  results: Result[];

  @OneToMany(() => Attendance, (attendance) => attendance.studentId)
  attendance: Attendance[];

  @OneToMany(() => Fee, (fee) => fee.studentId)
  fees: Fee[];

  @OneToMany(() => AssignmentSubmission, (submission) => submission.studentId)
  submissions: AssignmentSubmission[];
}
