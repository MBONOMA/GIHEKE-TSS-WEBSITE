import { IsBoolean, IsOptional, IsDateString, IsInt, IsString } from 'class-validator';

export class UpdateAdmissionSettingsDto {
  @IsBoolean()
  @IsOptional()
  isOpen?: boolean;

  @IsDateString()
  @IsOptional()
  openFrom?: string;

  @IsDateString()
  @IsOptional()
  openUntil?: string;

  @IsBoolean()
  @IsOptional()
  autoClose?: boolean;

  @IsInt()
  @IsOptional()
  maxApplications?: number;

  @IsString()
  @IsOptional()
  message?: string;
}
