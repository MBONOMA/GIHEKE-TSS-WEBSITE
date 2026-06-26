import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Message } from '../../database/entities/message.entity';
import { User } from '../../database/entities/user.entity';

@Injectable()
export class MessagesService {
  constructor(
    @InjectRepository(Message)
    private readonly repository: Repository<Message>,
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
  ) {}

  async findAll(userId: string, page = 1, limit = 20) {
    const [data, total] = await this.repository.findAndCount({
      where: [
        { senderId: { id: userId } as any },
        { receiverId: { id: userId } as any },
      ],
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

  async findOne(id: string, userId: string) {
    const message = await this.repository.findOne({ where: { id } });
    if (!message) throw new NotFoundException('Message not found');
    const senderId = (message.senderId as any)?.id;
    const receiverId = (message.receiverId as any)?.id;
    if (senderId !== userId && receiverId !== userId) {
      throw new NotFoundException('Message not found');
    }
    return message;
  }

  async create(senderId: string, data: { receiverId: string; subject?: string; content: string }) {
    const receiver = await this.userRepository.findOne({ where: { id: data.receiverId } });
    if (!receiver) throw new NotFoundException('Receiver not found');
    const message = this.repository.create({
      senderId: { id: senderId } as any,
      receiverId: { id: data.receiverId } as any,
      subject: data.subject,
      content: data.content,
    });
    return this.repository.save(message);
  }

  async markAsRead(id: string, userId: string) {
    const message = await this.findOne(id, userId);
    const receiverId = (message.receiverId as any)?.id;
    if (receiverId !== userId) {
      throw new NotFoundException('Message not found');
    }
    await this.repository.update(id, { isRead: true, readAt: new Date() });
    return this.findOne(id, userId);
  }

  async getUnreadCount(userId: string) {
    return this.repository.count({
      where: { receiverId: { id: userId } as any, isRead: false },
    });
  }
}
