export const appConfig = {
  name: import.meta.env.VITE_APP_NAME || 'Jejak Tumbuh',
  tagline: import.meta.env.VITE_APP_TAGLINE || 'Pendamping Anak',
  backendName: import.meta.env.VITE_BACKEND_NAME || 'Startok',
  connectedText: import.meta.env.VITE_CONNECTED_TEXT || 'Terhubung ke Server',
  offlineText: import.meta.env.VITE_OFFLINE_TEXT || 'Mode offline',
  loginText: import.meta.env.VITE_LOGIN_TEXT || 'Login ke Server',
  syncTitle: import.meta.env.VITE_SYNC_TITLE || 'Sinkronisasi Data',
  syncDescription: import.meta.env.VITE_SYNC_DESCRIPTION || 'Backup & restore data anak ke cloud',
  loginDescription: import.meta.env.VITE_LOGIN_DESCRIPTION || 'Masuk untuk sinkronisasi data',
  defaultTab: import.meta.env.VITE_DEFAULT_TAB || 'activity',
  creatorActive: import.meta.env.VITE_CREATOR_ACTIVE === 'true',
}
