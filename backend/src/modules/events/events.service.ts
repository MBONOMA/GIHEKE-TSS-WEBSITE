import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, MoreThan } from 'typeorm';
import { Event } from '../../database/entities/event.entity';

@Injectable()
export class EventsService {
  constructor(
    @InjectRepository(Event)
    private readonly repository: Repository<Event>,
  ) {}

  async findAll() {
    return this.repository.find({
      where: { isPublished: true, eventDate: MoreThan(new Date()) },
      order: { eventDate: 'ASC' },
    });
  }

  async findAllAdmin() {
    return this.repository.find({ order: { eventDate: 'DESC' } });
  }

  async findOne(id: string) {
    const event = await this.repository.findOne({ where: { id } });
    if (!event) throw new NotFoundException('Event not found');
    return event;
  }

  async create(data: Partial<Event>) {
    const event = this.repository.create(data);
    return this.repository.save(event);
  }

  async update(id: string, data: Partial<Event>) {
    await this.findOne(id);
    await this.repository.update(id, data);
    return this.findOne(id);
  }

  async remove(id: string) {
    const event = await this.findOne(id);
    await this.repository.remove(event);
    return { message: 'Event deleted successfully' };
  }
}
