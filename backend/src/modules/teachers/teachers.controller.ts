import { Controller, Get, Post, Patch, Body, Param, Query, UseGuards } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { TeachersService } from './teachers.service';
import { CurrentUser } from '../../common/decorators/current-user.decorator';
import { RolesGuard } from '../../common/guards/roles.guard';
import { Roles } from '../../common/decorators/roles.decorator';
import { AttendanceStatus } from '../../database/entities/attendance.entity';

@Controller('teachers')
export class TeachersController {
  constructor(private readonly teachersService: TeachersService) {}

  @Get()
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  findAll(@Query('page') page = 1, @Query('limit') limit = 10) {
    return this.teachersService.findAll(+page, +limit);
  }

  @Get('me')
  @UseGuards(AuthGuard('jwt'))
  getMyProfile(@CurrentUser() user: any) {
    return this.teachersService.getMyProfile(user.id);
  }

  @Get(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  findOne(@Param('id') id: string) {
    return this.teachersService.findOne(id);
  }

  @Patch(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  update(@Param('id') id: string, @Body() body: any) {
    return this.teachersService.update(id, body);
  }

  @Get(':id/classes')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin', 'teacher')
  getClasses(@Param('id') id: string) {
    return this.teachersService.getClasses(id);
  }

  @Post('marks')
  @UseGuards(AuthGuard('jwt'))
  uploadMarks(
    @CurrentUser() user: any,
    @Body() body: { studentId: string; subject: string; score: number; grade: string; term: number; academicYear: string },
  ) {
    return this.teachersService.uploadMarks(user.id, body);
  }

  @Post('attendance')
  @UseGuards(AuthGuard('jwt'))
  markAttendance(
    @CurrentUser() user: any,
    @Body() body: { studentId: string; date: string; status: AttendanceStatus; remarks?: string },
  ) {
    return this.teachersService.markAttendance(user.id, body);
  }

  @Post('materials')
  @UseGuards(AuthGuard('jwt'))
  uploadMaterial(@CurrentUser() user: any, @Body() body: any) {
    return this.teachersService.uploadMaterial(user.id, body);
  }
}
