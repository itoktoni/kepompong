import { appConfig } from '../config/appConfig.js'

export const sidebarNav = [
  { id: 'activity', icon: '🏠', label: 'Home' },
  { id: 'pilar', icon: '🎯', label: 'Soft Skills' },
  { id: 'progress', icon: '📈', label: 'Progress' },
  { id: 'challenge', icon: '🏆', label: 'Challenge' },
  { id: 'jadwal', icon: '⏰', label: 'Jadwal Harian' },
  { id: 'checklist', icon: '✅', label: 'Checklist' },
  ...(appConfig.creatorActive ? [{ id: 'creator', icon: '🏪', label: 'Creator' }] : []),
  { id: 'generate-idea', icon: '✨', label: 'Generate Idea' },
]
