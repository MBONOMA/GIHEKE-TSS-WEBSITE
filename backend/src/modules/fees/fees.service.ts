import { Injectable, NotFoundException, UnauthorizedException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Fee } from '../../database/entities/fee.entity';
import { Student } from '../../database/entities/student.entity';
import { User } from '../../database/entities/user.entity';

@Injectable()
export class FeesService {
  constructor(
    @InjectRepository(Fee)
    private readonly repository: Repository<Fee>,
    @InjectRepository(Student)
    private readonly studentRepository: Repository<Student>,
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
  ) {}

  async findAll(page = 1, limit = 20) {
    const [data, total] = await this.repository.findAndCount({
      skip: (page - 1) * limit,
      take: limit,
      order: { createdAt: 'DESC' },
    });
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async findByStudent(studentId: string) {
    return this.repository.find({
      where: { studentId: { id: studentId } as any },
      order: { dueDate: 'DESC' },
    });
  }

  async findOne(id: string) {
    const fee = await this.repository.findOne({ where: { id } });
    if (!fee) throw new NotFoundException('Fee record not found');
    return fee;
  }

  async create(data: Partial<Fee>) {
    const fee = this.repository.create(data);
    return this.repository.save(fee);
  }

  async update(id: string, data: Partial<Fee>) {
    await this.findOne(id);
    await this.repository.update(id, {
      ...data,
      paidAt: data.status === 'paid' ? new Date() : undefined,
    });
    return this.findOne(id);
  }

  async getMyFees(userId: string) {
    const user = await this.userRepository.findOne({ where: { id: userId } });
    if (!user || user.role !== 'student') {
      throw new UnauthorizedException('Access denied');
    }
    const student = await this.studentRepository.findOne({
      where: { userId: { id: userId } as any },
    });
    if (!student) throw new NotFoundException('Student profile not found');
    return this.findByStudent(student.id);
  }
}
