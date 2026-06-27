import { Centrifuge } from 'centrifuge';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
const userId = document.querySelector('meta[name="user-id"]')?.content;

async function fetchToken(channel) {
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
    return data.token;
}

const centrifuge = new Centrifuge('ws://localhost:8000/connection/websocket', {
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

centrifuge.connect();

window.centrifuge = centrifuge;
