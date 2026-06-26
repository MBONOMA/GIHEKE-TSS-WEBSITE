import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ElearningMaterial } from '../../database/entities/elearning-material.entity';
import { ElearningController } from './elearning.controller';
import { ElearningService } from './elearning.service';

@Module({
  imports: [TypeOrmModule.forFeature([ElearningMaterial])],
  controllers: [ElearningController],
  providers: [ElearningService],
  exports: [ElearningService],
})
export class ElearningModule {}
