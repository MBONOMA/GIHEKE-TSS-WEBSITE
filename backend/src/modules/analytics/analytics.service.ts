import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, Between } from 'typeorm';
import { VisitorAnalytics } from '../../database/entities/visitor-analytics.entity';
import { Application, ApplicationStatus } from '../../database/entities/application.entity';
import { Student } from '../../database/entities/student.entity';
import { Teacher } from '../../database/entities/teacher.entity';
import { News } from '../../database/entities/news.entity';
import { Event } from '../../database/entities/event.entity';
import { User } from '../../database/entities/user.entity';

@Injectable()
export class AnalyticsService {
  constructor(
    @InjectRepository(VisitorAnalytics)
    private readonly visitorRepository: Repository<VisitorAnalytics>,
    @InjectRepository(Application)
    private readonly applicationRepository: Repository<Application>,
    @InjectRepository(Student)
    private readonly studentRepository: Repository<Student>,
    @InjectRepository(Teacher)
    private readonly teacherRepository: Repository<Teacher>,
    @InjectRepository(News)
    private readonly newsRepository: Repository<News>,
    @InjectRepository(Event)
    private readonly eventRepository: Repository<Event>,
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
  ) {}

  async recordVisit(data: { page: string; ipAddress?: string; userAgent?: string; referrer?: string }) {
    const visit = this.visitorRepository.create(data);
    return this.visitorRepository.save(visit);
  }

  async getOverview() {
    const now = new Date();
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
    const totalVisitors = await this.visitorRepository.count();
    const monthlyVisitors = await this.visitorRepository.count({
      where: { visitedAt: Between(startOfMonth, now) },
    });
    const totalStudents = await this.studentRepository.count();
    const totalTeachers = await this.teacherRepository.count();
    const totalApplications = await this.applicationRepository.count();
    const pendingApplications = await this.applicationRepository.count({
      where: { status: ApplicationStatus.PENDING },
    });
    const totalNews = await this.newsRepository.count();
    const totalEvents = await this.eventRepository.count();
    const totalUsers = await this.userRepository.count();

    return {
      visitors: { total: totalVisitors, monthly: monthlyVisitors },
      students: { total: totalStudents },
      teachers: { total: totalTeachers },
      applications: { total: totalApplications, pending: pendingApplications },
      content: { news: totalNews, events: totalEvents },
      users: { total: totalUsers },
    };
  }

  async getVisitors(days = 30) {
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - days);
    const visits = await this.visitorRepository.find({
      where: { visitedAt: Between(startDate, new Date()) },
      order: { visitedAt: 'ASC' },
    });
    const grouped: Record<string, number> = {};
    visits.forEach((v) => {
      const key = v.visitedAt.toISOString().split('T')[0];
      grouped[key] = (grouped[key] || 0) + 1;
    });
    const labels = Object.keys(grouped).sort();
    const values = labels.map((l) => grouped[l]);
    return { labels, values, total: visits.length };
  }

  async getRecentActivities(limit = 10) {
    const recentApplications = await this.applicationRepository.find({
      order: { createdAt: 'DESC' },
      take: limit,
    });
    const recentNews = await this.newsRepository.find({
      order: { createdAt: 'DESC' },
      take: limit,
    });
    const activities: any[] = [];
    recentApplications.forEach((app) => {
      activities.push({
        type: 'application',
        message: `${app.firstName} ${app.lastName} applied`,
        date: app.createdAt,
      });
    });
    recentNews.forEach((news) => {
      activities.push({
        type: 'news',
        message: `News published: ${news.title}`,
        date: news.createdAt,
      });
    });
    activities.sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime());
    return activities.slice(0, limit);
  }
}
