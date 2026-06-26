import { Injectable, NotFoundException, UnauthorizedException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Teacher } from '../../database/entities/teacher.entity';
import { User } from '../../database/entities/user.entity';
import { Class } from '../../database/entities/class.entity';
import { Result } from '../../database/entities/result.entity';
import { Attendance, AttendanceStatus } from '../../database/entities/attendance.entity';
import { ElearningMaterial } from '../../database/entities/elearning-material.entity';
import { Student } from '../../database/entities/student.entity';

@Injectable()
export class TeachersService {
  constructor(
    @InjectRepository(Teacher)
    private readonly teacherRepository: Repository<Teacher>,
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
    @InjectRepository(Class)
    private readonly classRepository: Repository<Class>,
    @InjectRepository(Result)
    private readonly resultRepository: Repository<Result>,
    @InjectRepository(Attendance)
    private readonly attendanceRepository: Repository<Attendance>,
    @InjectRepository(ElearningMaterial)
    private readonly materialRepository: Repository<ElearningMaterial>,
    @InjectRepository(Student)
    private readonly studentRepository: Repository<Student>,
  ) {}

  async findAll(page = 1, limit = 10) {
    const [data, total] = await this.teacherRepository.findAndCount({
      skip: (page - 1) * limit,
      take: limit,
      order: { createdAt: 'DESC' },
    });
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async findOne(id: string) {
    const teacher = await this.teacherRepository.findOne({ where: { id } });
    if (!teacher) throw new NotFoundException('Teacher not found');
    return teacher;
  }

  async findByUserId(userId: string) {
    const teacher = await this.teacherRepository.findOne({
      where: { userId: { id: userId } as any },
    });
    if (!teacher) throw new NotFoundException('Teacher profile not found');
    return teacher;
  }

  async update(id: string, data: Partial<Teacher>) {
    await this.findOne(id);
    await this.teacherRepository.update(id, data);
    return this.findOne(id);
  }

  async getMyProfile(userId: string) {
    const user = await this.userRepository.findOne({ where: { id: userId } });
    if (!user || user.role !== 'teacher') {
      throw new UnauthorizedException('Access denied');
    }
    return this.findByUserId(userId);
  }

  async getClasses(teacherId: string) {
    const teacher = await this.findOne(teacherId);
    if (teacher.isClassTeacher && teacher.classId) {
      return this.classRepository.find({
        where: { id: (teacher.classId as any).id },
        relations: ['programId'],
      });
    }
    return [];
  }

  async uploadMarks(userId: string, data: { studentId: string; subject: string; score: number; grade: string; term: number; academicYear: string }) {
    const teacher = await this.findByUserId(userId);
    const { studentId, ...rest } = data;
    const result = this.resultRepository.create({
      ...rest,
      studentId: { id: studentId } as any,
      uploadedBy: { id: userId } as any,
    });
    return this.resultRepository.save(result);
  }

  async markAttendance(
    userId: string,
    data: { studentId: string; date: string; status: AttendanceStatus; remarks?: string },
  ) {
    const existing = await this.attendanceRepository.findOne({
      where: {
        studentId: { id: data.studentId } as any,
        date: new Date(data.date) as any,
      },
    });
    if (existing) {
      await this.attendanceRepository.update(existing.id, {
        status: data.status,
        remarks: data.remarks,
        markedBy: { id: userId } as any,
      });
      return this.attendanceRepository.findOne({ where: { id: existing.id } });
    }
    const attendance = this.attendanceRepository.create({
      studentId: { id: data.studentId } as any,
      date: new Date(data.date),
      status: data.status,
      remarks: data.remarks,
      markedBy: { id: userId } as any,
    });
    return this.attendanceRepository.save(attendance);
  }

  async uploadMaterial(userId: string, data: Partial<ElearningMaterial>) {
    const material = this.materialRepository.create({
      ...data,
      uploadedBy: { id: userId } as any,
    });
    return this.materialRepository.save(material);
  }
}
