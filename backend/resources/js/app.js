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
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'X-Requested-With': 'XMLHttpRequest',
        },
    },
});

const userId = document.querySelector('meta[name="user-id"]')?.content;
if (userId) {
    window.Echo.private(`notifications.${userId}`)
        .listen('.notification.new', (data) => {
            window.dispatchEvent(new CustomEvent('new-notification', { detail: data }));
        });
}
