import { Controller, Get, Patch, Body, UseGuards } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { AdmissionsService } from './admissions.service';
import { UpdateAdmissionSettingsDto } from './dto/admission-settings.dto';
import { Public } from '../../common/decorators/public.decorator';
import { RolesGuard } from '../../common/guards/roles.guard';
import { Roles } from '../../common/decorators/roles.decorator';
import { CurrentUser } from '../../common/decorators/current-user.decorator';

@Controller('admissions/settings')
export class AdmissionsSettingsController {
  constructor(private readonly admissionsService: AdmissionsService) {}

  @Public()
  @Get()
  getSettings() {
    return this.admissionsService.getSettings();
  }

  @Public()
  @Get('status')
  getStatus() {
    return this.admissionsService.getStatus();
  }

  @Patch()
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Roles('super_admin', 'admin')
  updateSettings(
    @Body() dto: UpdateAdmissionSettingsDto,
    @CurrentUser('id') userId: string,
  ) {
    return this.admissionsService.updateSettings(dto, userId);
  }
}
