import { Controller, Get, Post, Patch, Delete, Body, Param, UseGuards } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { SiteManagementService } from './site-management.service';
import { CreateAchievementDto, UpdateAchievementDto } from './dto/achievement.dto';
import { Achievement } from '../../database/entities/achievement.entity';
import { RolesGuard } from '../../common/guards/roles.guard';
import { Roles } from '../../common/decorators/roles.decorator';
import { Public } from '../../common/decorators/public.decorator';

@Controller('site-management/achievements')
export class AchievementsController {
  constructor(private readonly siteManagementService: SiteManagementService) {}

  @Public()
  @Get()
  findAll() {
    return this.siteManagementService.findAllAchievements();
  }

  @Public()
  @Get(':id')
  findOne(@Param('id') id: string) {
    return this.siteManagementService.findAchievement(id);
  }

  @Post()
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  create(@Body() dto: CreateAchievementDto) {
    return this.siteManagementService.createAchievement({
      ...dto,
      date: dto.date ? new Date(dto.date) : undefined,
    } as Partial<Achievement>);
  }

  @Patch(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  update(@Param('id') id: string, @Body() dto: UpdateAchievementDto) {
    return this.siteManagementService.updateAchievement(id, {
      ...dto,
      date: dto.date ? new Date(dto.date) : undefined,
    } as Partial<Achievement>);
  }

  @Delete(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  remove(@Param('id') id: string) {
    return this.siteManagementService.deleteAchievement(id);
  }
}
