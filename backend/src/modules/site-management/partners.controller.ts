import { Controller, Get, Post, Patch, Delete, Body, Param, UseGuards } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { SiteManagementService } from './site-management.service';
import { CreatePartnerDto, UpdatePartnerDto } from './dto/partner.dto';
import { RolesGuard } from '../../common/guards/roles.guard';
import { Roles } from '../../common/decorators/roles.decorator';
import { Public } from '../../common/decorators/public.decorator';

@Controller('site-management/partners')
export class PartnersController {
  constructor(private readonly siteManagementService: SiteManagementService) {}

  @Public()
  @Get()
  findAll() {
    return this.siteManagementService.findAllPartners();
  }

  @Public()
  @Get(':id')
  findOne(@Param('id') id: string) {
    return this.siteManagementService.findPartner(id);
  }

  @Post()
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  create(@Body() dto: CreatePartnerDto) {
    return this.siteManagementService.createPartner(dto);
  }

  @Patch(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  update(@Param('id') id: string, @Body() dto: UpdatePartnerDto) {
    return this.siteManagementService.updatePartner(id, dto);
  }

  @Delete(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  remove(@Param('id') id: string) {
    return this.siteManagementService.deletePartner(id);
  }
}
