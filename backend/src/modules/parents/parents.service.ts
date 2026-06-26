import { Injectable, NotFoundException, UnauthorizedException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Parent } from '../../database/entities/parent.entity';
import { User } from '../../database/entities/user.entity';
import { Student } from '../../database/entities/student.entity';
import { Result } from '../../database/entities/result.entity';
import { Attendance } from '../../database/entities/attendance.entity';
import { Fee } from '../../database/entities/fee.entity';

@Injectable()
export class ParentsService {
  constructor(
    @InjectRepository(Parent)
    private readonly parentRepository: Repository<Parent>,
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
    @InjectRepository(Student)
    private readonly studentRepository: Repository<Student>,
    @InjectRepository(Result)
    private readonly resultRepository: Repository<Result>,
    @InjectRepository(Attendance)
    private readonly attendanceRepository: Repository<Attendance>,
    @InjectRepository(Fee)
    private readonly feeRepository: Repository<Fee>,
  ) {}

  async findByUserId(userId: string) {
    const parent = await this.parentRepository.findOne({
      where: { userId: { id: userId } as any },
    });
    if (!parent) throw new NotFoundException('Parent profile not found');
    return parent;
  }

  async getMyProfile(userId: string) {
    const user = await this.userRepository.findOne({ where: { id: userId } });
    if (!user || user.role !== 'parent') {
      throw new UnauthorizedException('Access denied');
    }
    return this.findByUserId(userId);
  }

  async getChildren(userId: string) {
    const parent = await this.findByUserId(userId);
    if (parent.studentId) {
      return [parent.studentId];
    }
    const allParents = await this.parentRepository.find({
      where: { userId: { id: userId } as any },
      relations: ['studentId'],
    });
    return allParents.map((p) => p.studentId).filter(Boolean);
  }

  async getChildPerformance(parentUserId: string, childId: string) {
    await this.validateChild(parentUserId, childId);
    return this.resultRepository.find({
      where: { studentId: { id: childId } as any },
      order: { academicYear: 'DESC', term: 'DESC' },
    });
  }

  async getChildAttendance(parentUserId: string, childId: string) {
    await this.validateChild(parentUserId, childId);
    return this.attendanceRepository.find({
      where: { studentId: { id: childId } as any },
      order: { date: 'DESC' },
    });
  }

  async getChildFees(parentUserId: string, childId: string) {
    await this.validateChild(parentUserId, childId);
    return this.feeRepository.find({
      where: { studentId: { id: childId } as any },
      order: { dueDate: 'DESC' },
    });
  }

  private async validateChild(parentUserId: string, childId: string) {
    const parent = await this.findByUserId(parentUserId);
    const child = await this.studentRepository.findOne({ where: { id: childId } });
    if (!child) throw new NotFoundException('Student not found');
    const isLinked =
      (parent.studentId && (parent.studentId as any).id === childId) ||
      (await this.parentRepository.findOne({
        where: {
          userId: { id: parentUserId } as any,
          studentId: { id: childId } as any,
        },
      }));
    if (!isLinked) {
      throw new UnauthorizedException('This student is not your child');
    }
  }
}
