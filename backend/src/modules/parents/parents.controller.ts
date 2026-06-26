import { Controller, Get, Param, UseGuards } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { ParentsService } from './parents.service';
import { CurrentUser } from '../../common/decorators/current-user.decorator';

@Controller('parents')
@UseGuards(AuthGuard('jwt'))
export class ParentsController {
  constructor(private readonly parentsService: ParentsService) {}

  @Get('me')
  getMyProfile(@CurrentUser() user: any) {
    return this.parentsService.getMyProfile(user.id);
  }

  @Get('me/children')
  getChildren(@CurrentUser() user: any) {
    return this.parentsService.getChildren(user.id);
  }

  @Get('me/children/:id/performance')
  getChildPerformance(@CurrentUser() user: any, @Param('id') childId: string) {
    return this.parentsService.getChildPerformance(user.id, childId);
  }

  @Get('me/children/:id/attendance')
  getChildAttendance(@CurrentUser() user: any, @Param('id') childId: string) {
    return this.parentsService.getChildAttendance(user.id, childId);
  }

  @Get('me/children/:id/fees')
  getChildFees(@CurrentUser() user: any, @Param('id') childId: string) {
    return this.parentsService.getChildFees(user.id, childId);
  }
}
