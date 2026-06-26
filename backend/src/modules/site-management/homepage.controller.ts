import { Controller, Get, Post, Patch, Delete, Body, Param, UseGuards } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { SiteManagementService } from './site-management.service';
import { CreateHomepageSectionDto, UpdateHomepageSectionDto, ReorderSectionsDto } from './dto/homepage-section.dto';
import { RolesGuard } from '../../common/guards/roles.guard';
import { Roles } from '../../common/decorators/roles.decorator';
import { Public } from '../../common/decorators/public.decorator';

@Controller('site-management/homepage')
export class HomepageController {
  constructor(private readonly siteManagementService: SiteManagementService) {}

  @Public()
  @Get()
  findAll() {
    return this.siteManagementService.findAllHomepageSections();
  }

  @Public()
  @Get(':id')
  findOne(@Param('id') id: string) {
    return this.siteManagementService.findHomepageSection(id);
  }

  @Post()
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  create(@Body() dto: CreateHomepageSectionDto) {
    return this.siteManagementService.createHomepageSection(dto);
  }

  @Patch('reorder')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  reorder(@Body() dto: ReorderSectionsDto) {
    return this.siteManagementService.reorderSections(dto.items);
  }

  @Patch(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  update(@Param('id') id: string, @Body() dto: UpdateHomepageSectionDto) {
    return this.siteManagementService.updateHomepageSection(id, dto);
  }

  @Delete(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  remove(@Param('id') id: string) {
    return this.siteManagementService.deleteHomepageSection(id);
  }
}
