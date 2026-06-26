import { IsString, IsOptional, IsEnum, IsBoolean, IsInt, IsUUID } from 'class-validator';
import { Type } from 'class-transformer';
import { FileType } from '../../../database/entities/elearning-material.entity';

export class CreateElearningDto {
  @IsString()
  title: string;

  @IsString()
  @IsOptional()
  description?: string;

  @IsString()
  fileUrl: string;

  @IsEnum(FileType)
  fileType: FileType;

  @IsUUID()
  @IsOptional()
  programId?: string;

  @IsString()
  @IsOptional()
  category?: string;

  @IsString()
  @IsOptional()
  subject?: string;

  @IsInt()
  @IsOptional()
  year?: number;

  @IsBoolean()
  @IsOptional()
  isPublic?: boolean;
}

export class UpdateElearningDto {
  @IsString()
  @IsOptional()
  title?: string;

  @IsString()
  @IsOptional()
  description?: string;

  @IsString()
  @IsOptional()
  fileUrl?: string;

  @IsEnum(FileType)
  @IsOptional()
  fileType?: FileType;

  @IsUUID()
  @IsOptional()
  programId?: string;

  @IsString()
  @IsOptional()
  category?: string;

  @IsString()
  @IsOptional()
  subject?: string;

  @IsInt()
  @IsOptional()
  year?: number;

  @IsBoolean()
  @IsOptional()
  isPublic?: boolean;
}

export class ElearningQueryDto {
  @IsString()
  @IsOptional()
  search?: string;

  @IsUUID()
  @IsOptional()
  programId?: string;

  @IsString()
  @IsOptional()
  category?: string;

  @IsString()
  @IsOptional()
  subject?: string;

  @IsInt()
  @IsOptional()
  year?: number;

  @IsOptional()
  @Type(() => Number)
  page?: number = 1;

  @IsOptional()
  @Type(() => Number)
  limit?: number = 10;
}
