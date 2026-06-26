import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Notification } from '../../database/entities/notification.entity';

@Injectable()
export class NotificationsService {
  constructor(
    @InjectRepository(Notification)
    private readonly repository: Repository<Notification>,
  ) {}

  async findAll(userId: string, page = 1, limit = 20) {
    const [data, total] = await this.repository.findAndCount({
      where: { userId: { id: userId } as any },
      skip: (page - 1) * limit,
      take: limit,
      order: { createdAt: 'DESC' },
    });
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async getUnreadCount(userId: string) {
    return this.repository.count({
      where: { userId: { id: userId } as any, isRead: false },
    });
  }

  async markAsRead(id: string, userId: string) {
    const notification = await this.repository.findOne({ where: { id } });
    if (!notification) throw new NotFoundException('Notification not found');
    const notifUserId = (notification.userId as any)?.id;
    if (notifUserId !== userId) {
      throw new NotFoundException('Notification not found');
    }
    await this.repository.update(id, { isRead: true });
    return this.repository.findOne({ where: { id } });
  }

  async markAllAsRead(userId: string) {
    await this.repository.update(
      { userId: { id: userId } as any, isRead: false },
      { isRead: true },
    );
    return { message: 'All notifications marked as read' };
  }

  async remove(id: string, userId: string) {
    const notification = await this.repository.findOne({ where: { id } });
    if (!notification) throw new NotFoundException('Notification not found');
    const notifUserId = (notification.userId as any)?.id;
    if (notifUserId !== userId) {
      throw new NotFoundException('Notification not found');
    }
    await this.repository.remove(notification);
    return { message: 'Notification deleted' };
  }

  async create(data: Partial<Notification>) {
    const notification = this.repository.create(data);
    return this.repository.save(notification);
  }
}
