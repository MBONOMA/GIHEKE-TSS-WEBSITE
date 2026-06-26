import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { VisitorAnalytics } from '../../database/entities/visitor-analytics.entity';
import { Application } from '../../database/entities/application.entity';
import { Student } from '../../database/entities/student.entity';
import { Teacher } from '../../database/entities/teacher.entity';
import { News } from '../../database/entities/news.entity';
import { Event } from '../../database/entities/event.entity';
import { User } from '../../database/entities/user.entity';
import { AnalyticsController } from './analytics.controller';
import { AnalyticsService } from './analytics.service';

@Module({
  imports: [
    TypeOrmModule.forFeature([
      VisitorAnalytics,
      Application,
      Student,
      Teacher,
      News,
      Event,
      User,
    ]),
  ],
  controllers: [AnalyticsController],
  providers: [AnalyticsService],
  exports: [AnalyticsService],
})
export class AnalyticsModule {}
