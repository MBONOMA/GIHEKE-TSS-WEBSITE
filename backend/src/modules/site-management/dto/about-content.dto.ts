import { IsString, IsOptional, IsBoolean } from 'class-validator';

export class CreateAboutContentDto {
  @IsString()
  sectionKey: string;

  @IsString()
  title: string;

  @IsString()
  content: string;

  @IsString()
  @IsOptional()
  imageUrl?: string;

  @IsBoolean()
  @IsOptional()
  isActive?: boolean;
}

export class UpdateAboutContentDto {
  @IsString()
  @IsOptional()
  sectionKey?: string;

  @IsString()
  @IsOptional()
  title?: string;

  @IsString()
  @IsOptional()
  content?: string;

  @IsString()
  @IsOptional()
  imageUrl?: string;

  @IsBoolean()
  @IsOptional()
  isActive?: boolean;
}
