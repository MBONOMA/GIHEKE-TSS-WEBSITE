import { Injectable, NotFoundException, UnauthorizedException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Student } from '../../database/entities/student.entity';
import { User } from '../../database/entities/user.entity';
import { Result } from '../../database/entities/result.entity';
import { Attendance } from '../../database/entities/attendance.entity';
import { Timetable } from '../../database/entities/timetable.entity';
import { Assignment } from '../../database/entities/assignment.entity';
import { AssignmentSubmission } from '../../database/entities/assignment-submission.entity';

@Injectable()
export class StudentsService {
  constructor(
    @InjectRepository(Student)
    private readonly studentRepository: Repository<Student>,
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
    @InjectRepository(Result)
    private readonly resultRepository: Repository<Result>,
    @InjectRepository(Attendance)
    private readonly attendanceRepository: Repository<Attendance>,
    @InjectRepository(Timetable)
    private readonly timetableRepository: Repository<Timetable>,
    @InjectRepository(Assignment)
    private readonly assignmentRepository: Repository<Assignment>,
    @InjectRepository(AssignmentSubmission)
    private readonly submissionRepository: Repository<AssignmentSubmission>,
  ) {}

  async findAll(page = 1, limit = 10) {
    const [data, total] = await this.studentRepository.findAndCount({
      skip: (page - 1) * limit,
      take: limit,
      order: { createdAt: 'DESC' },
    });
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async findOne(id: string) {
    const student = await this.studentRepository.findOne({
      where: { id },
      relations: ['results', 'attendance', 'fees'],
    });
    if (!student) throw new NotFoundException('Student not found');
    return student;
  }

  async findByUserId(userId: string) {
    const student = await this.studentRepository.findOne({
      where: { userId: { id: userId } as any },
      relations: ['results', 'attendance', 'fees'],
    });
    if (!student) throw new NotFoundException('Student profile not found');
    return student;
  }

  async update(id: string, data: Partial<Student>) {
    await this.findOne(id);
    await this.studentRepository.update(id, data);
    return this.findOne(id);
  }

  async getMyProfile(userId: string) {
    const user = await this.userRepository.findOne({ where: { id: userId } });
    if (!user || (user.role !== 'student')) {
      throw new UnauthorizedException('Access denied');
    }
    return this.findByUserId(userId);
  }

  async getResults(studentId: string) {
    return this.resultRepository.find({
      where: { studentId: { id: studentId } as any },
      order: { academicYear: 'DESC', term: 'DESC' },
    });
  }

  async getAttendance(studentId: string) {
    return this.attendanceRepository.find({
      where: { studentId: { id: studentId } as any },
      order: { date: 'DESC' },
    });
  }

  async getTimetable(studentId: string) {
    const student = await this.findOne(studentId);
    const program = student.programId;
    if (!program) return [];
    return this.timetableRepository.find({
      where: { classId: { programId: { id: program.id } } as any },
      relations: ['teacherId'],
      order: { dayOfWeek: 'ASC', startTime: 'ASC' },
    });
  }

  async getAssignments(studentId: string) {
    const student = await this.findOne(studentId);
    const program = student.programId;
    if (!program) return [];
    const assignments = await this.assignmentRepository.find({
      where: { classId: { programId: { id: program.id } } as any },
      relations: ['submissions'],
      order: { createdAt: 'DESC' },
    });
    const submissions = await this.submissionRepository.find({
      where: { studentId: { id: studentId } as any },
    });
    return assignments.map((assignment) => {
      const submission = submissions.find(
        (s) => s.assignmentId && (s.assignmentId as any).id === assignment.id,
      );
      return { ...assignment, submission: submission || null };
    });
  }
}
