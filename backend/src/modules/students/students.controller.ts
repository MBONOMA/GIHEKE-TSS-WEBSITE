import { Controller, Get, Patch, Body, Param, Query, UseGuards } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { StudentsService } from './students.service';
import { CurrentUser } from '../../common/decorators/current-user.decorator';
import { RolesGuard } from '../../common/guards/roles.guard';
import { Roles } from '../../common/decorators/roles.decorator';

@Controller('students')
export class StudentsController {
  constructor(private readonly studentsService: StudentsService) {}

  @Get()
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin', 'teacher')
  findAll(@Query('page') page = 1, @Query('limit') limit = 10) {
    return this.studentsService.findAll(+page, +limit);
  }

  @Get('me')
  @UseGuards(AuthGuard('jwt'))
  getMyProfile(@CurrentUser() user: any) {
    return this.studentsService.getMyProfile(user.id);
  }

  @Get(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin', 'teacher')
  findOne(@Param('id') id: string) {
    return this.studentsService.findOne(id);
  }

  @Patch(':id')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  update(@Param('id') id: string, @Body() body: any) {
    return this.studentsService.update(id, body);
  }

  @Get(':id/results')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin', 'teacher')
  getResults(@Param('id') id: string) {
    return this.studentsService.getResults(id);
  }

  @Get(':id/attendance')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin', 'teacher')
  getAttendance(@Param('id') id: string) {
    return this.studentsService.getAttendance(id);
  }

  @Get(':id/timetable')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin', 'teacher', 'student')
  getTimetable(@Param('id') id: string) {
    return this.studentsService.getTimetable(id);
  }

  @Get(':id/assignments')
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin', 'teacher', 'student')
  getAssignments(@Param('id') id: string) {
    return this.studentsService.getAssignments(id);
  }
}
