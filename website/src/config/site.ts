export interface SectionConfig {
  id: string;
  component: string;
  props?: Record<string, any>;
}

export interface PageConfig {
  title: string;
  lang?: string;
  description?: string;
  sections: SectionConfig[];
}

export interface SiteConfig {
  pages: Record<string, PageConfig>;
  navbar: {
    brand: string;
    links: { label: string; href: string }[];
    cta: { label: string; href: string };
  };
  footer: {
    brand: string;
    tagline: string;
    links: { label: string; href: string }[];
    socials: { icon: string; href: string; label: string }[];
  };
}

const appName = import.meta.env.APP_NAME || "Kepompong";
const appDescription =
  import.meta.env.APP_DESCRIPTION ||
  "Ubah waktu layar menjadi waktu berkualitas. Dapatkan ide aktivitas kreatif instan dan pantau perkembangan soft skill si kecil setiap hari.";

export const siteConfig: SiteConfig = {
  pages: {
    "/": {
      title: `${appName} - Solusi Ide Bermain & Tracking Karakter Anak`,
      lang: "id",
      description: appDescription,
      sections: [
        { id: "hero", component: "Hero" },
        { id: "features", component: "Features" },
        { id: "how-it-works", component: "HowItWorks" },
        { id: "pricing", component: "Pricing" },
        { id: "testimoni", component: "Testimoni" },
        { id: "faq", component: "Faq" },
        { id: "cta", component: "Cta" },
      ],
    },
    "/metamorfosis": {
      title: "Metamorfosis Karakter | Kepompong",
      lang: "id",
      description:
        "Masa kecil hanya sekali. Bangun karakter kuat anak melalui bermain bermakna bersama Kepompong.",
      sections: [
        { id: "howto-hero", component: "HowtoHero" },
        { id: "stages", component: "Stages" },
        { id: "nurturing", component: "Nurturing" },
        { id: "howto-cta", component: "HowtoCta" },
      ],
    },
    "/contact": {
      title: "Contact | Kepompong",
      lang: "id",
      description:
        "Hubungi kami untuk pertanyaan, kerja sama bisnis, atau join affiliate.",
      sections: [
        { id: "contact", component: "Contact" },
      ],
    },
    "/lms": {
      title: "Panduan Aplikasi | Kepompong",
      lang: "id",
      description:
        "Pelajari cara menggunakan aplikasi Kepompong untuk memantau perkembangan karakter anak Anda.",
      sections: [],
    },
  },
  navbar: {
    brand: appName,
    links: [
      { label: "Homepage", href: "/" },
      { label: "Metamorfosis", href: "/metamorfosis" },
      { label: "Panduan", href: "/lms" },
      { label: "Tips", href: "/blog" },
      { label: "Contact", href: "/contact" },
    ],
    cta: { label: "Get Started", href: "/#cta" },
  },
  footer: {
    brand: appName,
    tagline: `\u00a9 2024 ${appName} Edutech. Nurturing Character, One Offline Moment at a Time.`,
    links: [
      { label: "Join Affiliate", href: "#" },
      { label: "Kerja Sama Bisnis", href: "#" },
    ],
    socials: [
      { icon: "instagram", href: "#", label: "Instagram" },
      { icon: "tiktok", href: "#", label: "TikTok" },
      { icon: "facebook", href: "#", label: "Facebook" },
      { icon: "threads", href: "#", label: "Threads" },
      { icon: "email", href: "#", label: "Email" },
      { icon: "whatsapp", href: "#", label: "WhatsApp" },
    ],
  },
};
