export const SCHOOL_INFO = {
  name: 'GIHEKE TSS',
  fullName: 'GIHEKE Technical Secondary School',
  motto: "We're our country's solutions providers",
  slogan: 'From training to doing',
  mission: 'To provide accessible, high-quality technical and vocational education that equips students with practical skills, critical thinking, and ethical values needed to excel in the modern workforce and contribute meaningfully to society.',
  objective: 'Empowering practical skills for better future.',
  vision: 'To provide high quality technical skills by enhancing competent trainees.',
  location: 'Western Province, Rusizi District, Giheke Sector, Giheke Cell',
  email: 'giheketss@gmail.com',
  phone: '+250 788 876 460',
  foundedYear: 2010,
};

export const STAFF = {
  principal: {
    name: 'KANYANDEGE Joseph Desire',
    position: 'Principal',
    phone: '+250 788 885 418',
  },
  deanOfStudies: {
    name: 'HABIMANA Syliver',
    position: 'Dean of Studies',
    phone: '+250 783 441 664',
  },
  deanOfDiscipline: {
    name: 'MUKAMANA Marie',
    position: 'Dean of Discipline',
    phone: '+250 783 044 000',
  },
  accountant: {
    name: 'NGENDAHIMANA Jean Pierre',
    position: 'Accountant',
    phone: '+250 788 908 086',
  },
  secretary: {
    name: 'IRASUBIZA Olive',
    position: 'Secretary',
    phone: '+250 789 387 751',
  },
  patron: {
    name: 'NGENDAHIMANA Erneste',
    position: 'Patron',
    phone: '+250 786 081 509',
  },
  matron: {
    name: 'BENEGUSENGA Jacqueline',
    position: 'Matron',
    phone: '+250 784 732 450',
  },
  coach: {
    name: 'IRIVUZUMUREMYI Nathanael',
    position: 'Coach',
    phone: '+250 795 931 713',
  },
};

export const TRADES = [
  { name: 'Software Development', code: 'SWD', description: 'Learn programming, web development, mobile apps, and software engineering.' },
  { name: 'Networking and Internet Technology', code: 'NIT', description: 'Network administration, cybersecurity, cloud computing, and infrastructure.' },
  { name: 'Electronics and Telecommunication Services', code: 'ETS', description: 'Electronic systems, telecommunications, and signal processing.' },
  { name: 'Professional Accounting', code: 'PAC', description: 'Financial accounting, taxation, auditing, and business management.' },
  { name: 'Electrical Technology', code: 'ELT', description: 'Electrical installations, power systems, and industrial electronics.' },
  { name: 'Building Construction', code: 'BLC', description: 'Construction management, structural design, and building technology.' },
  { name: 'Computer Systems and Architecture', code: 'CSA', description: 'Computer hardware, system administration, and IT support.' },
];

export const NAV_LINKS = [
  { label: 'Home', href: '/' },
  { label: 'About', href: '/public/about' },
  { label: 'Programs', href: '/public/programs' },
  { label: 'News', href: '/public/news' },
  { label: 'Admissions', href: '/public/admissions' },
  { label: 'E-Learning', href: '/public/elearning' },
  { label: 'Gallery', href: '/public/gallery' },
  { label: 'Contact', href: '/public/contact' },
];

export const SIDEBAR_LINKS = {
  admin: [
    { label: 'Dashboard', href: '/dashboard/admin', icon: 'HiHome' },
    { label: 'Site Management', href: '/dashboard/admin/site-management', icon: 'HiPencil' },
    { label: 'Admissions', href: '/dashboard/admin/admissions', icon: 'HiDocumentText' },
    { label: 'E-Learning', href: '/dashboard/admin/elearning', icon: 'HiBookOpen' },
    { label: 'News', href: '/dashboard/admin/news', icon: 'HiNewspaper' },
    { label: 'Events', href: '/dashboard/admin/events', icon: 'HiCalendar' },
    { label: 'Gallery', href: '/dashboard/admin/gallery', icon: 'HiPhotograph' },
    { label: 'Users', href: '/dashboard/admin/users', icon: 'HiUsers' },
    { label: 'Messages', href: '/dashboard/admin/messages', icon: 'HiMail' },
    { label: 'Settings', href: '/dashboard/admin/settings', icon: 'HiCog' },
  ],
  teacher: [
    { label: 'Dashboard', href: '/dashboard/teacher', icon: 'HiHome' },
    { label: 'Classes', href: '/dashboard/teacher/classes', icon: 'HiAcademicCap' },
    { label: 'Marks', href: '/dashboard/teacher/marks', icon: 'HiChartBar' },
    { label: 'Attendance', href: '/dashboard/teacher/attendance', icon: 'HiClipboardCheck' },
    { label: 'Materials', href: '/dashboard/teacher/materials', icon: 'HiBookOpen' },
    { label: 'Messages', href: '/dashboard/teacher/messages', icon: 'HiMail' },
  ],
  student: [
    { label: 'Dashboard', href: '/dashboard/student', icon: 'HiHome' },
    { label: 'Results', href: '/dashboard/student/results', icon: 'HiChartBar' },
    { label: 'Attendance', href: '/dashboard/student/attendance', icon: 'HiClipboardCheck' },
    { label: 'Timetable', href: '/dashboard/student/timetable', icon: 'HiCalendar' },
    { label: 'Assignments', href: '/dashboard/student/assignments', icon: 'HiDocumentText' },
    { label: 'Notifications', href: '/dashboard/student/notifications', icon: 'HiBell' },
    { label: 'Profile', href: '/dashboard/student/profile', icon: 'HiUser' },
  ],
  parent: [
    { label: 'Dashboard', href: '/dashboard/parent', icon: 'HiHome' },
    { label: 'Performance', href: '/dashboard/parent/performance', icon: 'HiChartBar' },
    { label: 'Attendance', href: '/dashboard/parent/attendance', icon: 'HiClipboardCheck' },
    { label: 'Fees', href: '/dashboard/parent/fees', icon: 'HiCurrencyDollar' },
    { label: 'Messages', href: '/dashboard/parent/messages', icon: 'HiMail' },
    { label: 'Calendar', href: '/dashboard/parent/calendar', icon: 'HiCalendar' },
    { label: 'Documents', href: '/dashboard/parent/documents', icon: 'HiFolder' },
  ],
};
