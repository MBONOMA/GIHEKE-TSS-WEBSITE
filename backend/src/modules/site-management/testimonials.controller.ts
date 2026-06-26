import { Controller, Get, Post, Patch, Delete, Body, Param, UseGuards } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { SiteManagementService } from './site-management.service';
import { CreateTestimonialDto, UpdateTestimonialDto } from './dto/testimonial.dto';
import { RolesGuard } from '../../common/guards/roles.guard';
import { Roles } from '../../common/decorators/roles.decorator';
import { Public } from '../../common/decorators/public.decorator';

@Controller('site-management/testimonials')
export class TestimonialsController {
  constructor(private readonly siteManagementService: SiteManagementService) {}

  @Public()
  @Get()
  findAll() {
    return this.siteManagementService.findAllTestimonials();
  }

  @Public()
  @Get(':id')
  findOne(@Param('id') id: string) {
    return this.siteManagementService.findTestimonial(id);
  }

  @Post()
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  create(@Body() dto: CreateTestimonialDto) {
    return this.siteManagementService.createTestimonial(dto);
  }

  @Patch(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  update(@Param('id') id: string, @Body() dto: UpdateTestimonialDto) {
    return this.siteManagementService.updateTestimonial(id, dto);
  }

  @Delete(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  remove(@Param('id') id: string) {
    return this.siteManagementService.deleteTestimonial(id);
  }
}
