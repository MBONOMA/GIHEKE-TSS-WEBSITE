import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { HomepageSection } from '../../database/entities/homepage-section.entity';
import { AboutPageContent } from '../../database/entities/about-page-content.entity';
import { Leader } from '../../database/entities/leader.entity';
import { Achievement } from '../../database/entities/achievement.entity';
import { Testimonial } from '../../database/entities/testimonial.entity';
import { Partner } from '../../database/entities/partner.entity';

@Injectable()
export class SiteManagementService {
  constructor(
    @InjectRepository(HomepageSection)
    private readonly homepageRepository: Repository<HomepageSection>,
    @InjectRepository(AboutPageContent)
    private readonly aboutRepository: Repository<AboutPageContent>,
    @InjectRepository(Leader)
    private readonly leaderRepository: Repository<Leader>,
    @InjectRepository(Achievement)
    private readonly achievementRepository: Repository<Achievement>,
    @InjectRepository(Testimonial)
    private readonly testimonialRepository: Repository<Testimonial>,
    @InjectRepository(Partner)
    private readonly partnerRepository: Repository<Partner>,
  ) {}

  async getPublicHomepageData() {
    const sections = await this.homepageRepository.find({
      where: { isActive: true },
      order: { sortOrder: 'ASC' },
    });
    const leaders = await this.leaderRepository.find({
      where: { isActive: true },
      order: { sortOrder: 'ASC' },
    });
    const achievements = await this.achievementRepository.find({
      where: { isActive: true },
      order: { createdAt: 'DESC' },
    });
    const testimonials = await this.testimonialRepository.find({
      where: { isActive: true },
      order: { sortOrder: 'ASC' },
    });
    const partners = await this.partnerRepository.find({
      where: { isActive: true },
      order: { sortOrder: 'ASC' },
    });
    return { sections, leaders, achievements, testimonials, partners };
  }

  async getPublicAboutData() {
    return this.aboutRepository.find({
      where: { isActive: true },
    });
  }

  async findAllHomepageSections() {
    return this.homepageRepository.find({ order: { sortOrder: 'ASC' } });
  }

  async findHomepageSection(id: string) {
    const section = await this.homepageRepository.findOne({ where: { id } });
    if (!section) throw new NotFoundException('Homepage section not found');
    return section;
  }

  async createHomepageSection(data: Partial<HomepageSection>) {
    const section = this.homepageRepository.create(data);
    return this.homepageRepository.save(section);
  }

  async updateHomepageSection(id: string, data: Partial<HomepageSection>) {
    await this.findHomepageSection(id);
    await this.homepageRepository.update(id, data);
    return this.findHomepageSection(id);
  }

  async deleteHomepageSection(id: string) {
    const section = await this.findHomepageSection(id);
    await this.homepageRepository.remove(section);
    return { message: 'Homepage section deleted' };
  }

  async reorderSections(items: { id: string; sortOrder: number }[]) {
    for (const item of items) {
      await this.homepageRepository.update(item.id, { sortOrder: item.sortOrder });
    }
    return this.findAllHomepageSections();
  }

  async findAllAboutContent() {
    return this.aboutRepository.find({ order: { createdAt: 'ASC' } });
  }

  async findAboutBySectionKey(sectionKey: string) {
    const content = await this.aboutRepository.findOne({ where: { sectionKey } });
    if (!content) throw new NotFoundException('About section not found');
    return content;
  }

  async createAboutContent(data: Partial<AboutPageContent>) {
    const content = this.aboutRepository.create(data);
    return this.aboutRepository.save(content);
  }

  async updateAboutContent(id: string, data: Partial<AboutPageContent>) {
    const content = await this.aboutRepository.findOne({ where: { id } });
    if (!content) throw new NotFoundException('About section not found');
    await this.aboutRepository.update(id, data);
    return this.aboutRepository.findOne({ where: { id } });
  }

  async deleteAboutContent(id: string) {
    const content = await this.aboutRepository.findOne({ where: { id } });
    if (!content) throw new NotFoundException('About section not found');
    await this.aboutRepository.remove(content);
    return { message: 'About section deleted' };
  }

  async findAllLeaders() {
    return this.leaderRepository.find({ order: { sortOrder: 'ASC' } });
  }

  async findLeader(id: string) {
    const leader = await this.leaderRepository.findOne({ where: { id } });
    if (!leader) throw new NotFoundException('Leader not found');
    return leader;
  }

  async createLeader(data: Partial<Leader>) {
    const leader = this.leaderRepository.create(data);
    return this.leaderRepository.save(leader);
  }

  async updateLeader(id: string, data: Partial<Leader>) {
    await this.findLeader(id);
    await this.leaderRepository.update(id, data);
    return this.findLeader(id);
  }

  async deleteLeader(id: string) {
    const leader = await this.findLeader(id);
    await this.leaderRepository.remove(leader);
    return { message: 'Leader deleted' };
  }

  async findAllAchievements() {
    return this.achievementRepository.find({ order: { createdAt: 'DESC' } });
  }

  async findAchievement(id: string) {
    const achievement = await this.achievementRepository.findOne({ where: { id } });
    if (!achievement) throw new NotFoundException('Achievement not found');
    return achievement;
  }

  async createAchievement(data: Partial<Achievement>) {
    const achievement = this.achievementRepository.create(data);
    return this.achievementRepository.save(achievement);
  }

  async updateAchievement(id: string, data: Partial<Achievement>) {
    await this.findAchievement(id);
    await this.achievementRepository.update(id, data);
    return this.findAchievement(id);
  }

  async deleteAchievement(id: string) {
    const achievement = await this.findAchievement(id);
    await this.achievementRepository.remove(achievement);
    return { message: 'Achievement deleted' };
  }

  async findAllTestimonials() {
    return this.testimonialRepository.find({ order: { sortOrder: 'ASC' } });
  }

  async findTestimonial(id: string) {
    const testimonial = await this.testimonialRepository.findOne({ where: { id } });
    if (!testimonial) throw new NotFoundException('Testimonial not found');
    return testimonial;
  }

  async createTestimonial(data: Partial<Testimonial>) {
    const testimonial = this.testimonialRepository.create(data);
    return this.testimonialRepository.save(testimonial);
  }

  async updateTestimonial(id: string, data: Partial<Testimonial>) {
    await this.findTestimonial(id);
    await this.testimonialRepository.update(id, data);
    return this.findTestimonial(id);
  }

  async deleteTestimonial(id: string) {
    const testimonial = await this.findTestimonial(id);
    await this.testimonialRepository.remove(testimonial);
    return { message: 'Testimonial deleted' };
  }

  async findAllPartners() {
    return this.partnerRepository.find({ order: { sortOrder: 'ASC' } });
  }

  async findPartner(id: string) {
    const partner = await this.partnerRepository.findOne({ where: { id } });
    if (!partner) throw new NotFoundException('Partner not found');
    return partner;
  }

  async createPartner(data: Partial<Partner>) {
    const partner = this.partnerRepository.create(data);
    return this.partnerRepository.save(partner);
  }

  async updatePartner(id: string, data: Partial<Partner>) {
    await this.findPartner(id);
    await this.partnerRepository.update(id, data);
    return this.findPartner(id);
  }

  async deletePartner(id: string) {
    const partner = await this.findPartner(id);
    await this.partnerRepository.remove(partner);
    return { message: 'Partner deleted' };
  }
}
