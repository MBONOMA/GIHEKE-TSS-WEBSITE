import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
  ManyToOne,
  JoinColumn,
  OneToOne,
  OneToMany,
} from 'typeorm';
import { Program } from './program.entity';
import { Teacher } from './teacher.entity';
import { Timetable } from './timetable.entity';
import { Assignment } from './assignment.entity';

@Entity('classes')
export class Class {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @Column()
  name: string;

  @Column({ unique: true })
  code: string;

  @ManyToOne(() => Program, { eager: true })
  @JoinColumn({ name: 'program_id' })
  programId: Program;

  @OneToOne(() => Teacher, (teacher) => teacher.classId)
  @JoinColumn({ name: 'teacher_id' })
  teacherId: Teacher;

  @Column({ name: 'academic_year', nullable: true })
  academicYear: string;

  @CreateDateColumn({ name: 'created_at' })
  createdAt: Date;

  @UpdateDateColumn({ name: 'updated_at' })
  updatedAt: Date;

  @OneToMany(() => Timetable, (timetable) => timetable.classId)
  timetables: Timetable[];

  @OneToMany(() => Assignment, (assignment) => assignment.classId)
  assignments: Assignment[];
}
