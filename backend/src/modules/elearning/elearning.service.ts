import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, Like } from 'typeorm';
import { ElearningMaterial } from '../../database/entities/elearning-material.entity';
import { CreateElearningDto, UpdateElearningDto, ElearningQueryDto } from './dto/elearning.dto';

@Injectable()
export class ElearningService {
  constructor(
    @InjectRepository(ElearningMaterial)
    private readonly repository: Repository<ElearningMaterial>,
  ) {}

  async findAll(query: ElearningQueryDto) {
    const { search, programId, category, subject, year, page = 1, limit = 10 } = query;
    const where: any = {};
    if (programId) where.programId = { id: programId };
    if (category) where.category = category;
    if (subject) where.subject = subject;
    if (year) where.year = year;
    if (search) {
      where.OR = [
        { title: Like(`%${search}%`) },
        { description: Like(`%${search}%`) },
      ];
    }
    const [data, total] = await this.repository.findAndCount({
      where,
      skip: (page - 1) * limit,
      take: limit,
      order: { createdAt: 'DESC' },
    });
    return {
      data,
      total,
      page,
      limit,
      totalPages: Math.ceil(total / limit),
    };
  }

  async findOne(id: string) {
    const material = await this.repository.findOne({ where: { id } });
    if (!material) throw new NotFoundException('Material not found');
    return material;
  }

  async create(createDto: CreateElearningDto, userId: string) {
    const { programId, ...rest } = createDto;
    const material = this.repository.create({
      ...rest,
      ...(programId ? { programId: { id: programId } as any } : {}),
      uploadedBy: { id: userId } as any,
    });
    return this.repository.save(material);
  }

  async update(id: string, updateDto: UpdateElearningDto) {
    await this.findOne(id);
    await this.repository.update(id, updateDto as any);
    return this.findOne(id);
  }

  async remove(id: string) {
    const material = await this.findOne(id);
    await this.repository.remove(material);
    return { message: 'Material deleted successfully' };
  }

  async incrementDownload(id: string) {
    const material = await this.findOne(id);
    await this.repository.update(id, { downloads: material.downloads + 1 });
    return this.findOne(id);
  }
}
