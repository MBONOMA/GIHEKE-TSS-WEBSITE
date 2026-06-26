import { Injectable, NotFoundException, ConflictException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, Like } from 'typeorm';
import { News } from '../../database/entities/news.entity';
import { CreateNewsDto, UpdateNewsDto, NewsQueryDto } from './dto/news.dto';

@Injectable()
export class NewsService {
  constructor(
    @InjectRepository(News)
    private readonly repository: Repository<News>,
  ) {}

  private generateSlug(title: string): string {
    return title
      .toLowerCase()
      .replace(/[^\w\s-]/g, '')
      .replace(/[\s_]+/g, '-')
      .replace(/^-+|-+$/g, '')
      + '-' + Date.now().toString(36);
  }

  async findAllPublished(query: NewsQueryDto) {
    const { search, tag, page = 1, limit = 10 } = query;
    const where: any = { isPublished: true };
    if (tag) where.tags = Like(`%${tag}%`);
    if (search) {
      where.OR = [
        { title: Like(`%${search}%`) },
        { content: Like(`%${search}%`) },
        { excerpt: Like(`%${search}%`) },
      ];
    }
    const [data, total] = await this.repository.findAndCount({
      where,
      skip: (page - 1) * limit,
      take: limit,
      order: { publishedAt: 'DESC' },
    });
    return {
      data,
      total,
      page,
      limit,
      totalPages: Math.ceil(total / limit),
    };
  }

  async findAllAdmin(query: NewsQueryDto) {
    const { search, tag, page = 1, limit = 10 } = query;
    const where: any = {};
    if (tag) where.tags = Like(`%${tag}%`);
    if (search) {
      where.OR = [
        { title: Like(`%${search}%`) },
        { content: Like(`%${search}%`) },
        { excerpt: Like(`%${search}%`) },
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

  async findBySlug(slug: string) {
    const news = await this.repository.findOne({ where: { slug } });
    if (!news) throw new NotFoundException('News not found');
    await this.repository.update(news.id, { viewCount: news.viewCount + 1 });
    return this.repository.findOne({ where: { slug } });
  }

  async findOne(id: string) {
    const news = await this.repository.findOne({ where: { id } });
    if (!news) throw new NotFoundException('News not found');
    return news;
  }

  async create(createDto: CreateNewsDto, userId: string) {
    const slug = createDto.slug || this.generateSlug(createDto.title);
    const existing = await this.repository.findOne({ where: { slug } });
    if (existing) {
      throw new ConflictException('A news item with this slug already exists');
    }
    const news = this.repository.create({
      ...createDto,
      slug,
      author: { id: userId } as any,
      publishedAt: createDto.isPublished ? (createDto.publishedAt ? new Date(createDto.publishedAt) : new Date()) : undefined,
    });
    return this.repository.save(news);
  }

  async update(id: string, updateDto: UpdateNewsDto) {
    await this.findOne(id);
    if (updateDto.slug) {
      const existing = await this.repository.findOne({
        where: { slug: updateDto.slug },
      });
      if (existing && existing.id !== id) {
        throw new ConflictException('A news item with this slug already exists');
      }
    }
    if (updateDto.isPublished && !updateDto.publishedAt) {
      updateDto.publishedAt = new Date().toISOString();
    }
    await this.repository.update(id, {
      ...updateDto,
      publishedAt: updateDto.publishedAt ? new Date(updateDto.publishedAt) : undefined,
    });
    return this.findOne(id);
  }

  async remove(id: string) {
    const news = await this.findOne(id);
    await this.repository.remove(news);
    return { message: 'News deleted successfully' };
  }
}
