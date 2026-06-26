import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { Parent } from '../../database/entities/parent.entity';
import { User } from '../../database/entities/user.entity';
import { Student } from '../../database/entities/student.entity';
import { Result } from '../../database/entities/result.entity';
import { Attendance } from '../../database/entities/attendance.entity';
import { Fee } from '../../database/entities/fee.entity';
import { ParentsController } from './parents.controller';
import { ParentsService } from './parents.service';

@Module({
  imports: [
    TypeOrmModule.forFeature([Parent, User, Student, Result, Attendance, Fee]),
  ],
  controllers: [ParentsController],
  providers: [ParentsService],
  exports: [ParentsService],
})
export class ParentsModule {}
