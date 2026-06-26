import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { HomepageSection } from '../../database/entities/homepage-section.entity';
import { AboutPageContent } from '../../database/entities/about-page-content.entity';
import { Leader } from '../../database/entities/leader.entity';
import { Achievement } from '../../database/entities/achievement.entity';
import { Testimonial } from '../../database/entities/testimonial.entity';
import { Partner } from '../../database/entities/partner.entity';
import { HomepageController } from './homepage.controller';
import { AboutController } from './about.controller';
import { LeadersController } from './leaders.controller';
import { AchievementsController } from './achievements.controller';
import { TestimonialsController } from './testimonials.controller';
import { PartnersController } from './partners.controller';
import { SiteManagementService } from './site-management.service';

@Module({
  imports: [
    TypeOrmModule.forFeature([
      HomepageSection,
      AboutPageContent,
      Leader,
      Achievement,
      Testimonial,
      Partner,
    ]),
  ],
  controllers: [
    HomepageController,
    AboutController,
    LeadersController,
    AchievementsController,
    TestimonialsController,
    PartnersController,
  ],
  providers: [SiteManagementService],
  exports: [SiteManagementService],
})
export class SiteManagementModule {}
