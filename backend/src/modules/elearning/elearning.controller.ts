import { Controller, Get, Post, Patch, Delete, Body, Param, Query, UseGuards, HttpCode, HttpStatus } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { ElearningService } from './elearning.service';
import { CreateElearningDto, UpdateElearningDto, ElearningQueryDto } from './dto/elearning.dto';
import { Public } from '../../common/decorators/public.decorator';
import { CurrentUser } from '../../common/decorators/current-user.decorator';
import { RolesGuard } from '../../common/guards/roles.guard';
import { Roles } from '../../common/decorators/roles.decorator';

@Controller('elearning')
export class ElearningController {
  constructor(private readonly elearningService: ElearningService) {}

  @Public()
  @Get('materials')
  findAll(@Query() query: ElearningQueryDto) {
    return this.elearningService.findAll(query);
  }

  @Public()
  @Get('materials/:id')
  findOne(@Param('id') id: string) {
    return this.elearningService.findOne(id);
  }

  @Post('materials')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin', 'teacher')
  create(@Body() dto: CreateElearningDto, @CurrentUser('id') userId: string) {
    return this.elearningService.create(dto, userId);
  }

  @Patch('materials/:id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin', 'teacher')
  update(@Param('id') id: string, @Body() dto: UpdateElearningDto) {
    return this.elearningService.update(id, dto);
  }

  @Delete('materials/:id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  remove(@Param('id') id: string) {
    return this.elearningService.remove(id);
  }

  @Public()
  @Post('materials/:id/download')
  @HttpCode(HttpStatus.OK)
  incrementDownload(@Param('id') id: string) {
    return this.elearningService.incrementDownload(id);
  }
}
