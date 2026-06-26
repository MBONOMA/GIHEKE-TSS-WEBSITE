import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { Student } from '../../database/entities/student.entity';
import { User } from '../../database/entities/user.entity';
import { Result } from '../../database/entities/result.entity';
import { Attendance } from '../../database/entities/attendance.entity';
import { Timetable } from '../../database/entities/timetable.entity';
import { Assignment } from '../../database/entities/assignment.entity';
import { AssignmentSubmission } from '../../database/entities/assignment-submission.entity';
import { StudentsController } from './students.controller';
import { StudentsService } from './students.service';

@Module({
  imports: [
    TypeOrmModule.forFeature([
      Student,
      User,
      Result,
      Attendance,
      Timetable,
      Assignment,
      AssignmentSubmission,
    ]),
  ],
  controllers: [StudentsController],
  providers: [StudentsService],
  exports: [StudentsService],
})
export class StudentsModule {}
