import { Controller, Get, Post, Body, Query, UseGuards } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { AnalyticsService } from './analytics.service';
import { Public } from '../../common/decorators/public.decorator';
import { RolesGuard } from '../../common/guards/roles.guard';
import { Roles } from '../../common/decorators/roles.decorator';

@Controller('analytics')
export class AnalyticsController {
  constructor(private readonly analyticsService: AnalyticsService) {}

  @Public()
  @Post('visit')
  recordVisit(@Body() body: { page: string; ipAddress?: string; userAgent?: string; referrer?: string }) {
    return this.analyticsService.recordVisit(body);
  }

  @Get('overview')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  getOverview() {
    return this.analyticsService.getOverview();
  }

  @Get('visitors')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  getVisitors(@Query('days') days?: number) {
    return this.analyticsService.getVisitors(days ? +days : 30);
  }

  @Get('recent-activities')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  getRecentActivities(@Query('limit') limit?: number) {
    return this.analyticsService.getRecentActivities(limit ? +limit : 10);
  }
}
