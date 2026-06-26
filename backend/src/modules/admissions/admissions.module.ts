import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { Application } from '../../database/entities/application.entity';
import { AdmissionSettings } from '../../database/entities/admission-settings.entity';
import { ApplicationStatusHistory } from '../../database/entities/application-status-history.entity';
import { Student } from '../../database/entities/student.entity';
import { User } from '../../database/entities/user.entity';
import { ApplicationsController } from './applications.controller';
import { AdmissionsSettingsController } from './admissions-settings.controller';
import { AdmissionsService } from './admissions.service';

@Module({
  imports: [
    TypeOrmModule.forFeature([
      Application,
      AdmissionSettings,
      ApplicationStatusHistory,
      Student,
      User,
    ]),
  ],
  controllers: [ApplicationsController, AdmissionsSettingsController],
  providers: [AdmissionsService],
  exports: [AdmissionsService],
})
export class AdmissionsModule {}
