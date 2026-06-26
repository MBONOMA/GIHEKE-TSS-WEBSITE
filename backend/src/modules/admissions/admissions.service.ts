import { Injectable, NotFoundException, BadRequestException, ForbiddenException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, Like } from 'typeorm';
import { Application, ApplicationStatus } from '../../database/entities/application.entity';
import { AdmissionSettings } from '../../database/entities/admission-settings.entity';
import { ApplicationStatusHistory } from '../../database/entities/application-status-history.entity';
import { Student } from '../../database/entities/student.entity';
import { User } from '../../database/entities/user.entity';
import { CreateApplicationDto, UpdateStatusDto, UpdateNotesDto, ApplicationQueryDto } from './dto/application.dto';
import { UpdateAdmissionSettingsDto } from './dto/admission-settings.dto';

@Injectable()
export class AdmissionsService {
  constructor(
    @InjectRepository(Application)
    private readonly applicationRepository: Repository<Application>,
    @InjectRepository(AdmissionSettings)
    private readonly settingsRepository: Repository<AdmissionSettings>,
    @InjectRepository(ApplicationStatusHistory)
    private readonly historyRepository: Repository<ApplicationStatusHistory>,
    @InjectRepository(Student)
    private readonly studentRepository: Repository<Student>,
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
  ) {}

  async apply(createApplicationDto: CreateApplicationDto) {
    const settings = await this.getFirstSettings();
    if (!settings?.isOpen) {
      throw new ForbiddenException('Admissions are currently closed');
    }
    if (settings.maxApplications && settings.currentApplications >= settings.maxApplications) {
      throw new BadRequestException('Maximum applications reached');
    }
    const existing = await this.applicationRepository.findOne({
      where: { email: createApplicationDto.email },
    });
    if (existing) {
      throw new BadRequestException('An application with this email already exists');
    }
    const application = this.applicationRepository.create({
      ...createApplicationDto,
      dateOfBirth: new Date(createApplicationDto.dateOfBirth),
      programId: { id: createApplicationDto.programId } as any,
    });
    await this.applicationRepository.save(application);
    if (settings) {
      await this.settingsRepository.update(settings.id, {
        currentApplications: settings.currentApplications + 1,
      });
    }
    return application;
  }

  async findAll(query: ApplicationQueryDto) {
    const { search, status, programId, page = 1, limit = 10 } = query;
    const where: any = {};
    if (status) where.status = status;
    if (programId) where.programId = { id: programId };
    if (search) {
      where.OR = [
        { firstName: Like(`%${search}%`) },
        { lastName: Like(`%${search}%`) },
        { email: Like(`%${search}%`) },
      ];
    }
    const [data, total] = await this.applicationRepository.findAndCount({
      where,
      relations: ['statusHistory', 'statusHistory.changedBy'],
      skip: (page - 1) * limit,
      take: limit,
      order: { createdAt: 'DESC' },
    });
    return {
      data,
      total,
      page,
      limit,
      totalPages: Math.ceil(total / limit),
    };
  }

  async findOne(id: string) {
    const application = await this.applicationRepository.findOne({
      where: { id },
      relations: ['statusHistory', 'statusHistory.changedBy'],
    });
    if (!application) throw new NotFoundException('Application not found');
    return application;
  }

  async updateStatus(id: string, updateStatusDto: UpdateStatusDto, userId: string) {
    const application = await this.findOne(id);
    const fromStatus = application.status;
    const toStatus = updateStatusDto.status;

    const historyRecord = this.historyRepository.create({
      applicationId: application,
      fromStatus,
      toStatus,
      notes: updateStatusDto.notes ?? null,
      changedBy: { id: userId } as any,
    } as any);
    await this.historyRepository.save(historyRecord);

    await this.applicationRepository.update(id, {
      status: toStatus,
      interviewDate: updateStatusDto.interviewDate
        ? new Date(updateStatusDto.interviewDate)
        : application.interviewDate,
    });

    if (toStatus === ApplicationStatus.ACCEPTED) {
      const existingUser = await this.userRepository.findOne({
        where: { email: application.email },
      });
      if (!existingUser) {
        const user = this.userRepository.create({
          email: application.email,
          password: '$2a$12$defaultpassword', 
          firstName: application.firstName,
          lastName: application.lastName,
          phone: application.phone,
          role: 'student' as any,
        });
        await this.userRepository.save(user);
      }
    }

    return this.findOne(id);
  }

  async updateNotes(id: string, updateNotesDto: UpdateNotesDto) {
    await this.findOne(id);
    await this.applicationRepository.update(id, updateNotesDto);
    return this.findOne(id);
  }

  async getHistory(id: string) {
    return this.historyRepository.find({
      where: { applicationId: { id } },
      relations: ['changedBy'],
      order: { createdAt: 'DESC' },
    });
  }

  async getFirstSettings() {
    const settings = await this.settingsRepository.find({ take: 1 });
    return settings[0] || null;
  }

  async getSettings() {
    let settings = await this.getFirstSettings();
    if (!settings) {
      settings = this.settingsRepository.create({});
      await this.settingsRepository.save(settings);
    }
    return settings;
  }

  async updateSettings(updateDto: UpdateAdmissionSettingsDto, userId: string) {
    let settings = await this.getFirstSettings();
    if (!settings) {
      settings = this.settingsRepository.create({
        ...updateDto,
        updatedBy: { id: userId } as any,
      });
      return this.settingsRepository.save(settings);
    }
    await this.settingsRepository.update(settings.id, {
      ...updateDto,
      updatedBy: { id: userId } as any,
    });
    return this.getSettings();
  }

  async getStatus() {
    const settings = await this.getFirstSettings();
    if (!settings) {
      return { isOpen: false, message: 'Admissions are currently closed' };
    }
    const now = new Date();
    let isOpen = settings.isOpen;
    if (settings.openFrom && settings.openUntil) {
      isOpen = isOpen && now >= settings.openFrom && now <= settings.openUntil;
    }
    if (settings.autoClose && settings.maxApplications) {
      isOpen = isOpen && settings.currentApplications < settings.maxApplications;
    }
    return { isOpen, message: settings.message };
  }

  async exportData(query: ApplicationQueryDto) {
    const { search, status, programId } = query;
    const where: any = {};
    if (status) where.status = status;
    if (programId) where.programId = { id: programId };
    if (search) {
      where.OR = [
        { firstName: Like(`%${search}%`) },
        { lastName: Like(`%${search}%`) },
        { email: Like(`%${search}%`) },
      ];
    }
    const applications = await this.applicationRepository.find({
      where,
      order: { createdAt: 'DESC' },
    });
    return applications.map((app) => ({
      'First Name': app.firstName,
      'Last Name': app.lastName,
      Email: app.email,
      Phone: app.phone,
      Status: app.status,
      'Date of Birth': app.dateOfBirth,
      Gender: app.gender,
      'Applied At': app.createdAt,
    }));
  }
}
