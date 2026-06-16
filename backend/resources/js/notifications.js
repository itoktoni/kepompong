import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    cluster: 'mt1',
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    encrypted: import.meta.env.VITE_REVERB_SCHEME === 'https',
    disableStats: true,
    enabledTransports: import.meta.env.VITE_REVERB_SCHEME === 'https' ? ['wss'] : ['ws'],
});

const userId = document.querySelector('meta[name="user-id"]')?.content;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
if (userId) {
    window.Echo.connector.pusher.config.auth = {
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
    };
    window.Echo.private(`notifications.${userId}`)
        .listen('.notification.new', (data) => {
            window.dispatchEvent(new CustomEvent('new-notification', { detail: data }));
        });
}
