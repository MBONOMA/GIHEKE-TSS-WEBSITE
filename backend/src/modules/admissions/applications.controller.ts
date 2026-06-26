import { Controller, Get, Post, Patch, Body, Param, Query, UseGuards, HttpCode, HttpStatus, Res } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { Response } from 'express';
import { AdmissionsService } from './admissions.service';
import { CreateApplicationDto, UpdateStatusDto, UpdateNotesDto, ApplicationQueryDto } from './dto/application.dto';
import { Public } from '../../common/decorators/public.decorator';
import { CurrentUser } from '../../common/decorators/current-user.decorator';
import { RolesGuard } from '../../common/guards/roles.guard';
import { Roles } from '../../common/decorators/roles.decorator';

@Controller('admissions')
export class ApplicationsController {
  constructor(private readonly admissionsService: AdmissionsService) {}

  @Public()
  @Post('apply')
  @HttpCode(HttpStatus.CREATED)
  apply(@Body() dto: CreateApplicationDto) {
    return this.admissionsService.apply(dto);
  }

  @Get('applications')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  findAll(@Query() query: ApplicationQueryDto) {
    return this.admissionsService.findAll(query);
  }

  @Get('applications/export')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  async export(@Query() query: ApplicationQueryDto, @Res() res: Response) {
    const data = await this.admissionsService.exportData(query);
    res.setHeader('Content-Type', 'application/json');
    res.setHeader('Content-Disposition', 'attachment; filename=applications-export.json');
    res.json(data);
  }

  @Get('applications/:id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  findOne(@Param('id') id: string) {
    return this.admissionsService.findOne(id);
  }

  @Patch('applications/:id/status')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  updateStatus(
    @Param('id') id: string,
    @Body() dto: UpdateStatusDto,
    @CurrentUser('id') userId: string,
  ) {
    return this.admissionsService.updateStatus(id, dto, userId);
  }

  @Patch('applications/:id/notes')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  updateNotes(@Param('id') id: string, @Body() dto: UpdateNotesDto) {
    return this.admissionsService.updateNotes(id, dto);
  }

  @Get('applications/:id/history')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  getHistory(@Param('id') id: string) {
    return this.admissionsService.getHistory(id);
  }
}
