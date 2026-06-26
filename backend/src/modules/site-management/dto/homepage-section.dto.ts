import { IsString, IsOptional, IsEnum, IsBoolean, IsInt, IsArray, IsObject } from 'class-validator';
import { SectionType } from '../../../database/entities/homepage-section.entity';

export class CreateHomepageSectionDto {
  @IsEnum(SectionType)
  sectionType: SectionType;

  @IsString()
  @IsOptional()
  title?: string;

  @IsString()
  @IsOptional()
  subtitle?: string;

  @IsString()
  @IsOptional()
  content?: string;

  @IsString()
  @IsOptional()
  imageUrl?: string;

  @IsString()
  @IsOptional()
  videoUrl?: string;

  @IsString()
  @IsOptional()
  ctaText?: string;

  @IsString()
  @IsOptional()
  ctaLink?: string;

  @IsObject()
  @IsOptional()
  items?: object;

  @IsBoolean()
  @IsOptional()
  isActive?: boolean;

  @IsInt()
  @IsOptional()
  sortOrder?: number;
}

export class UpdateHomepageSectionDto {
  @IsEnum(SectionType)
  @IsOptional()
  sectionType?: SectionType;

  @IsString()
  @IsOptional()
  title?: string;

  @IsString()
  @IsOptional()
  subtitle?: string;

  @IsString()
  @IsOptional()
  content?: string;

  @IsString()
  @IsOptional()
  imageUrl?: string;

  @IsString()
  @IsOptional()
  videoUrl?: string;

  @IsString()
  @IsOptional()
  ctaText?: string;

  @IsString()
  @IsOptional()
  ctaLink?: string;

  @IsObject()
  @IsOptional()
  items?: object;

  @IsBoolean()
  @IsOptional()
  isActive?: boolean;

  @IsInt()
  @IsOptional()
  sortOrder?: number;
}

export class ReorderSectionsDto {
  @IsArray()
  items: { id: string; sortOrder: number }[];
}
