import { DataSource } from 'typeorm';
import bcrypt from 'bcryptjs';
import { config } from 'dotenv';

config();

import { User, UserRole } from './entities/user.entity';
import { Program } from './entities/program.entity';
import { HomepageSection, SectionType } from './entities/homepage-section.entity';
import { AboutPageContent } from './entities/about-page-content.entity';
import { Leader } from './entities/leader.entity';
import { Achievement } from './entities/achievement.entity';
import { Testimonial } from './entities/testimonial.entity';
import { Partner } from './entities/partner.entity';
import { AdmissionSettings } from './entities/admission-settings.entity';

async function seed(): Promise<void> {
  const dataSource = new DataSource({
    type: 'mysql',
    host: process.env.DB_HOST || 'localhost',
    port: parseInt(process.env.DB_PORT || '3306', 10),
    username: process.env.DB_USERNAME || 'giheke_admin',
    password: process.env.DB_PASSWORD || 'Giheke2024!',
    database: process.env.DB_NAME || 'giheke_tss',
    charset: 'utf8mb4',
    entities: [__dirname + '/entities/*.entity{.ts,.js}'],
  });

  await dataSource.initialize();
  console.log('[Seed] Database connected successfully');

  const userRepo = dataSource.getRepository(User);
  const programRepo = dataSource.getRepository(Program);
  const homepageSectionRepo = dataSource.getRepository(HomepageSection);
  const aboutPageContentRepo = dataSource.getRepository(AboutPageContent);
  const leaderRepo = dataSource.getRepository(Leader);
  const achievementRepo = dataSource.getRepository(Achievement);
  const testimonialRepo = dataSource.getRepository(Testimonial);
  const partnerRepo = dataSource.getRepository(Partner);
  const admissionSettingsRepo = dataSource.getRepository(AdmissionSettings);

  try {
    const salt = await bcrypt.genSalt(12);

    // ================================================================
    // USERS
    // ================================================================
    let adminUser: User | null = null;
    let principalUser: User | null = null;

    const userCount = await userRepo.count();
    if (userCount === 0) {
      const adminPassword = await bcrypt.hash('Admin123!', salt);
      const principalPassword = await bcrypt.hash('Principal123!', salt);
      const deanPassword = await bcrypt.hash('Dean123!', salt);
      const disciplinePassword = await bcrypt.hash('Discipline123!', salt);
      const accountantPassword = await bcrypt.hash('Accountant123!', salt);
      const secretaryPassword = await bcrypt.hash('Secretary123!', salt);

      const users = userRepo.create([
        {
          email: 'admin@giheketss.com',
          password: adminPassword,
          firstName: 'System',
          lastName: 'Admin',
          role: UserRole.SUPER_ADMIN,
          isActive: true,
        },
        {
          email: 'principal@giheketss.com',
          password: principalPassword,
          firstName: 'Joseph Desire',
          lastName: 'KANYANDEGE',
          role: UserRole.STAFF,
          phone: '+250788885418',
          isActive: true,
        },
        {
          email: 'dean@giheketss.com',
          password: deanPassword,
          firstName: 'Syliver',
          lastName: 'HABIMANA',
          role: UserRole.STAFF,
          phone: '+250783441664',
          isActive: true,
        },
        {
          email: 'discipline@giheketss.com',
          password: disciplinePassword,
          firstName: 'Marie',
          lastName: 'MUKAMANA',
          role: UserRole.STAFF,
          phone: '+250783044000',
          isActive: true,
        },
        {
          email: 'accountant@giheketss.com',
          password: accountantPassword,
          firstName: 'Jean Pierre',
          lastName: 'NGENDAHIMANA',
          role: UserRole.STAFF,
          phone: '+250788908086',
          isActive: true,
        },
        {
          email: 'secretary@giheketss.com',
          password: secretaryPassword,
          firstName: 'Olive',
          lastName: 'IRASUBIZA',
          role: UserRole.STAFF,
          phone: '+250789387751',
          isActive: true,
        },
      ]);

      const savedUsers = await userRepo.save(users);
      adminUser = savedUsers.find((u) => u.email === 'admin@giheketss.com') || null;
      principalUser = savedUsers.find((u) => u.email === 'principal@giheketss.com') || null;
      console.log(`[Seed] ${savedUsers.length} users created`);
    } else {
      adminUser = await userRepo.findOne({ where: { email: 'admin@giheketss.com' } });
      principalUser = await userRepo.findOne({ where: { email: 'principal@giheketss.com' } });
      console.log('[Seed] Users already exist, skipping...');
    }

    // ================================================================
    // PROGRAMS (Trades)
    // ================================================================
    const programCount = await programRepo.count();
    if (programCount === 0) {
      const programs = programRepo.create([
        {
          name: 'Software Development',
          code: 'SWD',
          description:
            'This program equips students with skills in software engineering, web and mobile application development, database management, and system analysis. Students gain proficiency in modern programming languages, frameworks, and software development lifecycle methodologies.',
          duration: '3 years',
          isActive: true,
        },
        {
          name: 'Networking and Internet Technology',
          code: 'NIT',
          description:
            'Students learn to design, implement, and manage computer networks. The curriculum covers network security, routing and switching, wireless technologies, cloud computing, and internet infrastructure management.',
          duration: '3 years',
          isActive: true,
        },
        {
          name: 'Electronics and Telecommunication Services',
          code: 'ETS',
          description:
            'Focuses on electronic systems, telecommunications engineering, signal processing, and communication networks. Students gain hands-on experience with electronic devices, circuit design, and modern telecommunication systems.',
          duration: '3 years',
          isActive: true,
        },
        {
          name: 'Professional Accounting',
          code: 'PAC',
          description:
            'Designed to produce competent accounting professionals. Covers financial accounting, management accounting, taxation, auditing, and business law. Prepares students for professional accounting certifications and careers in finance.',
          duration: '3 years',
          isActive: true,
        },
        {
          name: 'Electrical Technology',
          code: 'ELT',
          description:
            'Provides comprehensive training in electrical installations, power systems, electrical machine maintenance, renewable energy technologies, and industrial automation. Students develop practical skills for the electrical engineering sector.',
          duration: '3 years',
          isActive: true,
        },
        {
          name: 'Building Construction',
          code: 'BLC',
          description:
            'Covers modern construction techniques, structural design, building materials, quantity surveying, project management, and sustainable construction practices. Prepares students for careers in the construction industry.',
          duration: '3 years',
          isActive: true,
        },
        {
          name: 'Computer Systems and Architecture',
          code: 'CSA',
          description:
            'Focuses on computer hardware, system architecture, operating systems, embedded systems, and computer maintenance. Students gain expertise in assembling, configuring, and maintaining computer systems and peripherals.',
          duration: '3 years',
          isActive: true,
        },
      ]);

      await programRepo.save(programs);
      console.log(`[Seed] ${programs.length} programs created`);
    } else {
      console.log('[Seed] Programs already exist, skipping...');
    }

    // ================================================================
    // HOMEPAGE SECTIONS
    // ================================================================
    const homepageSectionCount = await homepageSectionRepo.count();
    if (homepageSectionCount === 0) {
      const sections = homepageSectionRepo.create([
        {
          sectionType: SectionType.HERO,
          title: 'GIHEKE TSS',
          subtitle: 'From Training to Doing',
          content:
            'Empowering the next generation of technical professionals through hands-on training, innovation, and excellence in TVET education.',
          ctaText: 'Explore Programs',
          ctaLink: '/programs',
          imageUrl: '/images/hero-bg.jpg',
          isActive: true,
          sortOrder: 1,
        },
        {
          sectionType: SectionType.WELCOME,
          title: 'Welcome to GIHEKE TSS',
          subtitle: 'Groupe Scolaire IPRC Kicukiro - Giheke Technical Secondary School',
          content:
            'GIHEKE TSS is a leading Technical Secondary School in Rwanda, committed to providing quality technical and vocational education. Our mission is to equip students with practical skills, theoretical knowledge, and professional values that enable them to excel in the competitive job market and contribute meaningfully to the socio-economic development of Rwanda. With state-of-the-art workshops, experienced instructors, and a robust curriculum aligned with industry standards, we prepare our students to become innovators, entrepreneurs, and leaders in their chosen fields.',
          imageUrl: '/images/welcome.jpg',
          isActive: true,
          sortOrder: 2,
        },
        {
          sectionType: SectionType.PRINCIPAL_MESSAGE,
          title: 'Message from the Principal',
          subtitle: 'Joseph Desire KANYANDEGE',
          content:
            'Welcome to GIHEKE TSS, a center of excellence in technical and vocational education. Our school is built on the foundation of discipline, hard work, and a commitment to producing skilled professionals who can compete both locally and internationally. I am proud of our dedicated staff, talented students, and the supportive community that makes our school a wonderful place to learn and grow. Together, we are building a brighter future for Rwanda through quality education and hands-on training. I invite you to explore our programs and discover the opportunities that await you at GIHEKE TSS.',
          imageUrl: '/images/principal.jpg',
          ctaText: 'Read Full Message',
          ctaLink: '/about',
          isActive: true,
          sortOrder: 3,
        },
        {
          sectionType: SectionType.FEATURED_PROGRAMS,
          title: 'Our Programs',
          subtitle: 'Explore Our Trades and Specializations',
          content:
            'We offer a diverse range of technical and vocational programs designed to meet the demands of the modern job market. Each program combines theoretical knowledge with extensive practical training.',
          isActive: true,
          sortOrder: 4,
        },
        {
          sectionType: SectionType.STATISTICS,
          title: 'School Statistics',
          subtitle: 'GIHEKE TSS by the Numbers',
          content: 'Our school has grown steadily since its founding, making a significant impact on technical education in Rwanda.',
          items: {
            students: { value: '500+', label: 'Students Enrolled' },
            teachers: { value: '30+', label: 'Qualified Teachers' },
            programs: { value: '7', label: 'Programs Offered' },
            years: { value: '10+', label: 'Years of Excellence' },
          },
          isActive: true,
          sortOrder: 5,
        },
        {
          sectionType: SectionType.TESTIMONIALS,
          title: 'What People Say',
          subtitle: 'Testimonials from Our Community',
          content: 'Hear from our students, alumni, and partners about their experiences at GIHEKE TSS.',
          isActive: true,
          sortOrder: 6,
        },
        {
          sectionType: SectionType.PARTNERS,
          title: 'Our Partners',
          subtitle: 'Trusted by Industry Leaders',
          content: 'We collaborate with industry partners, government agencies, and educational institutions to provide the best learning experience for our students.',
          isActive: true,
          sortOrder: 7,
        },
      ]);

      await homepageSectionRepo.save(sections);
      console.log(`[Seed] ${sections.length} homepage sections created`);
    } else {
      console.log('[Seed] Homepage sections already exist, skipping...');
    }

    // ================================================================
    // ABOUT PAGE CONTENT
    // ================================================================
    const aboutPageContentCount = await aboutPageContentRepo.count();
    if (aboutPageContentCount === 0) {
      const contents = aboutPageContentRepo.create([
        {
          sectionKey: 'history',
          title: 'School History',
          content:
            'GIHEKE TSS (Groupe Scolaire IPRC Kicukiro - Giheke Technical Secondary School) was established to address the growing demand for skilled technical professionals in Rwanda. Since its founding, the school has grown from a small technical training center into a comprehensive secondary school offering multiple trades and specializations. Over the past decade, GIHEKE TSS has produced thousands of graduates who have gone on to successful careers in various technical fields, contributing to Rwanda\'s vision of becoming a knowledge-based economy. The school continues to evolve its curriculum and facilities to meet the changing needs of the industry and the job market.',
          isActive: true,
        },
        {
          sectionKey: 'mission',
          title: 'Our Mission',
          content:
            'To provide inclusive, high-quality technical and vocational education that equips students with practical skills, critical thinking abilities, and professional ethics needed to excel in the workforce, foster innovation, and contribute to Rwanda\'s sustainable development.',
          isActive: true,
        },
        {
          sectionKey: 'vision',
          title: 'Our Vision',
          content:
            'To be a leading center of excellence in technical and vocational education in Rwanda and the East African region, producing competent, innovative, and entrepreneurial graduates who drive socio-economic transformation.',
          isActive: true,
        },
        {
          sectionKey: 'values',
          title: 'Core Values',
          content:
            'Excellence: We strive for the highest standards in teaching, learning, and professional practice.\n\nIntegrity: We uphold honesty, transparency, and ethical behavior in all our endeavors.\n\nDiscipline: We foster a culture of self-discipline, respect, and responsibility among students and staff.\n\nInnovation: We encourage creativity, critical thinking, and continuous improvement.\n\nTeamwork: We promote collaboration and mutual support within the school community and with external partners.\n\nInclusivity: We ensure equal opportunities for all students regardless of their background.',
          isActive: true,
        },
        {
          sectionKey: 'profile',
          title: 'School Profile',
          content:
            'GIHEKE TSS is a government-assisted technical secondary school located in Kicukiro District, Kigali City, Rwanda. The school operates under the Rwanda Basic Education Board (REB) and follows the national TVET curriculum. With a student population of over 500 and a dedicated team of more than 30 qualified teachers, the school offers seven specialized programs. Our facilities include modern workshops, computer labs, science laboratories, a library, and recreational spaces. We are committed to providing a holistic education that balances academic excellence with practical skills development and character formation.',
          isActive: true,
        },
        {
          sectionKey: 'principal_message',
          title: 'Principal\'s Message',
          content:
            'Dear Students, Parents, and Visitors,\n\nIt is with great pride and joy that I welcome you to GIHEKE TSS. As the Principal of this esteemed institution, I am honored to lead a community of dedicated educators, talented students, and supportive parents who are all committed to the pursuit of excellence in technical education.\n\nAt GIHEKE TSS, we believe that every student has the potential to achieve greatness. Our role is to provide the guidance, resources, and environment necessary for each student to discover and develop their unique talents. We combine rigorous academics with hands-on vocational training to ensure that our graduates are not only knowledgeable but also skilled and ready to make an immediate impact in their chosen fields.\n\nI encourage all students to take full advantage of the opportunities available at our school, to work hard, to stay disciplined, and to dream big. Together, we will build a brighter future for ourselves, our communities, and our nation.\n\nJoseph Desire KANYANDEGE\nPrincipal, GIHEKE TSS',
          isActive: true,
        },
      ]);

      await aboutPageContentRepo.save(contents);
      console.log(`[Seed] ${contents.length} about page contents created`);
    } else {
      console.log('[Seed] About page content already exists, skipping...');
    }

    // ================================================================
    // LEADERS
    // ================================================================
    const leaderCount = await leaderRepo.count();
    if (leaderCount === 0) {
      const leaders = leaderRepo.create([
        {
          name: 'Joseph Desire KANYANDEGE',
          position: 'Principal',
          bio: 'Joseph Desire KANYANDEGE is the Principal of GIHEKE TSS. With extensive experience in educational leadership and administration, he is committed to advancing technical and vocational education in Rwanda. Under his leadership, the school has achieved significant milestones in academic excellence and infrastructure development.',
          email: 'principal@giheketss.com',
          phone: '+250788885418',
          sortOrder: 1,
          isActive: true,
        },
        {
          name: 'Syliver HABIMANA',
          position: 'Dean of Studies',
          bio: 'Syliver HABIMANA serves as the Dean of Studies, overseeing the academic programs and curriculum implementation at GIHEKE TSS. He is dedicated to maintaining high academic standards and ensuring that students receive quality education across all trades.',
          email: 'dean@giheketss.com',
          phone: '+250783441664',
          sortOrder: 2,
          isActive: true,
        },
        {
          name: 'Marie MUKAMANA',
          position: 'Dean of Discipline',
          bio: 'Marie MUKAMANA is the Dean of Discipline, responsible for maintaining a conducive learning environment through effective discipline management. She works closely with students, parents, and staff to promote a culture of respect, responsibility, and positive behavior.',
          email: 'discipline@giheketss.com',
          phone: '+250783044000',
          sortOrder: 3,
          isActive: true,
        },
        {
          name: 'Jean Pierre NGENDAHIMANA',
          position: 'Accountant',
          bio: 'Jean Pierre NGENDAHIMANA is the School Accountant, managing the financial operations of GIHEKE TSS. He ensures transparent and efficient financial management in compliance with government regulations and school policies.',
          email: 'accountant@giheketss.com',
          phone: '+250788908086',
          sortOrder: 4,
          isActive: true,
        },
        {
          name: 'Olive IRASUBIZA',
          position: 'Secretary',
          bio: 'Olive IRASUBIZA serves as the School Secretary, providing essential administrative support to the school leadership. She is the first point of contact for visitors and plays a key role in the smooth operation of the school administration.',
          email: 'secretary@giheketss.com',
          phone: '+250789387751',
          sortOrder: 5,
          isActive: true,
        },
      ]);

      await leaderRepo.save(leaders);
      console.log(`[Seed] ${leaders.length} leaders created`);
    } else {
      console.log('[Seed] Leaders already exist, skipping...');
    }

    // ================================================================
    // ACHIEVEMENTS
    // ================================================================
    const achievementCount = await achievementRepo.count();
    if (achievementCount === 0) {
      const achievements = achievementRepo.create([
        {
          title: 'National TVET Competition Winners 2024',
          description:
            'GIHEKE TSS students won first place in the National TVET Competition in Software Development and Networking categories, demonstrating exceptional technical skills and innovation against competitors from across Rwanda.',
          date: new Date('2024-11-15'),
          isActive: true,
        },
        {
          title: '100% Pass Rate in National Exams 2023-2024',
          description:
            'Our students achieved a 100% pass rate in the national secondary school leaving examinations, with over 60% attaining distinction grades. This remarkable achievement reflects the dedication of our teaching staff and the hard work of our students.',
          date: new Date('2024-08-20'),
          isActive: true,
        },
        {
          title: 'Partnership with Leading Tech Companies',
          description:
            'GIHEKE TSS established strategic partnerships with leading technology companies to provide students with internship opportunities, industry exposure, and access to modern equipment and software. These partnerships enhance the practical training experience and improve graduate employability.',
          date: new Date('2024-03-10'),
          isActive: true,
        },
      ]);

      await achievementRepo.save(achievements);
      console.log(`[Seed] ${achievements.length} achievements created`);
    } else {
      console.log('[Seed] Achievements already exist, skipping...');
    }

    // ================================================================
    // TESTIMONIALS
    // ================================================================
    const testimonialCount = await testimonialRepo.count();
    if (testimonialCount === 0) {
      const testimonials = testimonialRepo.create([
        {
          name: 'Jean Baptiste HABIMANA',
          position: 'Alumnus, Class of 2022 - Software Development',
          content:
            'GIHEKE TSS transformed my life. The practical skills I gained in software development during my three years at the school prepared me for the real world. I secured a job as a junior developer just two months after graduation, and I credit the excellent training and mentorship I received at GIHEKE TSS for my success. The hands-on approach to learning made all the difference.',
          rating: 5,
          sortOrder: 1,
          isActive: true,
        },
        {
          name: 'Alice UWIMANA',
          position: 'Parent',
          content:
            'I am incredibly grateful to GIHEKE TSS for the quality education and character formation my daughter received. The discipline, sense of responsibility, and technical competence she developed at the school have been invaluable. The teachers are dedicated and truly care about the students\' success. I recommend GIHEKE TSS to any parent seeking quality technical education for their child.',
          rating: 5,
          sortOrder: 2,
          isActive: true,
        },
      ]);

      await testimonialRepo.save(testimonials);
      console.log(`[Seed] ${testimonials.length} testimonials created`);
    } else {
      console.log('[Seed] Testimonials already exist, skipping...');
    }

    // ================================================================
    // PARTNERS
    // ================================================================
    const partnerCount = await partnerRepo.count();
    if (partnerCount === 0) {
      const partners = partnerRepo.create([
        {
          name: 'Rwanda Basic Education Board (REB)',
          description:
            'The government body responsible for basic education in Rwanda, providing curriculum oversight and accreditation for GIHEKE TSS.',
          sortOrder: 1,
          isActive: true,
        },
        {
          name: 'Workforce Development Authority (WDA)',
          description:
            'A Rwandan government agency that promotes technical and vocational education and training (TVET) to develop a skilled workforce aligned with industry needs.',
          sortOrder: 2,
          isActive: true,
        },
        {
          name: 'IPRC Kicukiro',
          description:
            'An integrated polytechnic regional center that collaborates with GIHEKE TSS in providing advanced technical training and pathway programs for students.',
          sortOrder: 3,
          isActive: true,
        },
      ]);

      await partnerRepo.save(partners);
      console.log(`[Seed] ${partners.length} partners created`);
    } else {
      console.log('[Seed] Partners already exist, skipping...');
    }

    // ================================================================
    // ADMISSION SETTINGS
    // ================================================================
    const admissionSettingsCount = await admissionSettingsRepo.count();
    if (admissionSettingsCount === 0) {
      if (!adminUser) {
        console.warn('[Seed] Admin user not found, skipping admission settings');
      } else {
        const admissionSettings = admissionSettingsRepo.create({
          isOpen: true,
          openFrom: new Date('2026-01-01'),
          openUntil: new Date('2026-12-31'),
          autoClose: false,
          maxApplications: 500,
          currentApplications: 0,
          message: 'Applications are currently open for the 2026 academic year',
          updatedBy: adminUser,
        });

        await admissionSettingsRepo.save(admissionSettings);
        console.log('[Seed] Admission settings created');
      }
    } else {
      console.log('[Seed] Admission settings already exist, skipping...');
    }

    console.log('[Seed] All data seeded successfully!');
  } catch (error) {
    console.error('[Seed] Error during seeding:', error);
    throw error;
  } finally {
    await dataSource.destroy();
    console.log('[Seed] Database connection closed');
  }
}

seed().catch((error) => {
  console.error('[Seed] Seed script failed:', error);
  process.exit(1);
});
