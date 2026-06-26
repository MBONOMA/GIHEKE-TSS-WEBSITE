import { Controller, Get, Post, Patch, Delete, Body, Param, UseGuards } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { SiteManagementService } from './site-management.service';
import { CreateAboutContentDto, UpdateAboutContentDto } from './dto/about-content.dto';
import { RolesGuard } from '../../common/guards/roles.guard';
import { Roles } from '../../common/decorators/roles.decorator';
import { Public } from '../../common/decorators/public.decorator';

@Controller('site-management/about')
export class AboutController {
  constructor(private readonly siteManagementService: SiteManagementService) {}

  @Public()
  @Get()
  findAll() {
    return this.siteManagementService.findAllAboutContent();
  }

  @Public()
  @Get(':sectionKey')
  findBySectionKey(@Param('sectionKey') sectionKey: string) {
    return this.siteManagementService.findAboutBySectionKey(sectionKey);
  }

  @Post()
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  create(@Body() dto: CreateAboutContentDto) {
    return this.siteManagementService.createAboutContent(dto);
  }

  @Patch(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  update(@Param('id') id: string, @Body() dto: UpdateAboutContentDto) {
    return this.siteManagementService.updateAboutContent(id, dto);
  }

  @Delete(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  remove(@Param('id') id: string) {
    return this.siteManagementService.deleteAboutContent(id);
  }
}
