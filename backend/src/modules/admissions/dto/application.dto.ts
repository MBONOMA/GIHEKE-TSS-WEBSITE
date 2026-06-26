import { IsString, IsEmail, IsEnum, IsOptional, IsDateString, IsArray } from 'class-validator';
import { Gender, ApplicationStatus } from '../../../database/entities/application.entity';

export class CreateApplicationDto {
  @IsString()
  firstName: string;

  @IsString()
  lastName: string;

  @IsDateString()
  dateOfBirth: string;

  @IsEnum(Gender)
  gender: Gender;

  @IsString()
  @IsOptional()
  previousSchool?: string;

  @IsString()
  programId: string;

  @IsString()
  @IsOptional()
  academicBackground?: string;

  @IsEmail()
  email: string;

  @IsString()
  phone: string;

  @IsString()
  @IsOptional()
  address?: string;

  @IsArray()
  @IsOptional()
  documents?: string[];
}

export class UpdateStatusDto {
  @IsEnum(ApplicationStatus)
  status: ApplicationStatus;

  @IsString()
  @IsOptional()
  notes?: string;

  @IsDateString()
  @IsOptional()
  interviewDate?: string;
}

export class UpdateNotesDto {
  @IsString()
  @IsOptional()
  adminNotes?: string;
}

export class ApplicationQueryDto {
  @IsString()
  @IsOptional()
  search?: string;

  @IsEnum(ApplicationStatus)
  @IsOptional()
  status?: ApplicationStatus;

  @IsString()
  @IsOptional()
  programId?: string;

  @IsOptional()
  page?: number = 1;

  @IsOptional()
  limit?: number = 10;
}
