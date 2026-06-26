import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { Teacher } from '../../database/entities/teacher.entity';
import { User } from '../../database/entities/user.entity';
import { Class } from '../../database/entities/class.entity';
import { Result } from '../../database/entities/result.entity';
import { Attendance } from '../../database/entities/attendance.entity';
import { ElearningMaterial } from '../../database/entities/elearning-material.entity';
import { Student } from '../../database/entities/student.entity';
import { TeachersController } from './teachers.controller';
import { TeachersService } from './teachers.service';

@Module({
  imports: [
    TypeOrmModule.forFeature([
      Teacher,
      User,
      Class,
      Result,
      Attendance,
      ElearningMaterial,
      Student,
    ]),
  ],
  controllers: [TeachersController],
  providers: [TeachersService],
  exports: [TeachersService],
})
export class TeachersModule {}
