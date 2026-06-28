import { Centrifuge } from 'centrifuge';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
const userId = document.querySelector('meta[name="user-id"]')?.content;

let notificationEnabled = true;

async function fetchToken(channel) {
    try {
        const body = channel ? JSON.stringify({ channel }) : undefined;
        const res = await fetch('/centrifugo/token', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            body,
        });
        const data = await res.json();

        if (data.disabled) {
            notificationEnabled = false;
            return null;
        }

        return data.token;
    } catch (e) {
        notificationEnabled = false;
        return null;
    }
}

const wsUrl = document.querySelector('meta[name="centrifugo-url"]')?.content || 'ws://localhost:8000/connection/websocket';

const centrifuge = new Centrifuge(wsUrl, {
    getToken: () => fetchToken(),
});

if (userId) {
    const channel = centrifuge.newSubscription(`notifications#${userId}`, {
        getToken: () => fetchToken(`notifications#${userId}`),
    });

    channel.on('publication', (ctx) => {
        window.dispatchEvent(new CustomEvent('new-notification', { detail: ctx.data }));
    });

    channel.subscribe();
}

if (notificationEnabled && userId) {
    centrifuge.connect();
}

window.centrifuge = centrifuge;
