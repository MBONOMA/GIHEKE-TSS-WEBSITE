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
import { Student } from './student.entity';

export enum Relationship {
  FATHER = 'father',
  MOTHER = 'mother',
  GUARDIAN = 'guardian',
}

@Entity('parents')
export class Parent {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @ManyToOne(() => User, { eager: true })
  @JoinColumn({ name: 'user_id' })
  userId: User;

  @Column({ name: 'sdms_code', unique: true })
  sdmsCode: string;

  @Column({ nullable: true })
  occupation: string;

  @ManyToOne(() => Student, { eager: true })
  @JoinColumn({ name: 'student_id' })
  studentId: Student;

  @Column({ type: 'enum', enum: Relationship })
  relationship: Relationship;

  @CreateDateColumn({ name: 'created_at' })
  createdAt: Date;

  @UpdateDateColumn({ name: 'updated_at' })
  updatedAt: Date;
}
