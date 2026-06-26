import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Gallery } from '../../database/entities/gallery.entity';

@Injectable()
export class GalleryService {
  constructor(
    @InjectRepository(Gallery)
    private readonly repository: Repository<Gallery>,
  ) {}

  async findAll() {
    return this.repository.find({
      where: { isPublished: true },
      order: { createdAt: 'DESC' },
    });
  }

  async findAllAdmin() {
    return this.repository.find({ order: { createdAt: 'DESC' } });
  }

  async findOne(id: string) {
    const item = await this.repository.findOne({ where: { id } });
    if (!item) throw new NotFoundException('Gallery item not found');
    return item;
  }

  async create(data: Partial<Gallery>) {
    const item = this.repository.create(data);
    return this.repository.save(item);
  }

  async update(id: string, data: Partial<Gallery>) {
    await this.findOne(id);
    await this.repository.update(id, data);
    return this.findOne(id);
  }

  async remove(id: string) {
    const item = await this.findOne(id);
    await this.repository.remove(item);
    return { message: 'Gallery item deleted successfully' };
  }
}
