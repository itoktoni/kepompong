<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
    <title>Reverb Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: monospace; background: #111; color: #0f0; padding: 20px; }
        #log { background: #000; border: 1px solid #333; padding: 16px; max-height: 60vh; overflow: auto; font-size: 13px; white-space: pre-wrap; }
        input { background: #222; color: #0f0; border: 1px solid #555; padding: 6px 10px; font-family: monospace; }
        button { background: #333; color: #0f0; border: 1px solid #555; padding: 6px 14px; cursor: pointer; font-family: monospace; }
        button:hover { background: #444; }
        .row { margin: 10px 0; display: flex; gap: 8px; align-items: center; }
    </style>
</head>
<body>
    <h1>Reverb WebSocket Test</h1>

    @auth
    <p>Logged in as user ID: <strong>{{ auth()->id() }}</strong></p>
    <div class="row">
        <button onclick="testConnect()">Connect Echo</button>
        <button onclick="testDisconnect()">Disconnect</button>
        <span id="status" style="color:#888">Not connected</span>
    </div>
    @else
    <p style="color:red">You must be <a href="/login" style="color:#f88">logged in</a> first.</p>
    @endauth

    <div id="log"></div>

    <script>
        const logEl = document.getElementById('log');
        const statusEl = document.getElementById('status');
        function log(msg) {
            const time = new Date().toLocaleTimeString();
            const line = document.createElement('div');
            line.textContent = `[${time}] ${msg}`;
            line.style.color = msg.includes('ERROR') ? '#f44' : msg.includes('SUCCESS') ? '#4f4' : '#0f0';
            logEl.appendChild(line);
            logEl.scrollTop = logEl.scrollHeight;
            console.log('[ReverbTest]', msg);
        }

        function testConnect() {
            @auth
            const userId = {{ auth()->id() }};
            log('Checking if window.Echo exists...');

            if (!window.Echo) {
                log('ERROR: window.Echo is not defined. Vite app.js may not have loaded.');
                return;
            }

            log('window.Echo found! Configuring...');

            window.Echo.connector.pusher.connection.bind('state_change', (states) => {
                log(`State: ${states.previous} -> ${states.current}`);
                statusEl.textContent = states.current;
                statusEl.style.color = states.current === 'connected' ? '#4f4' : '#f44';
            });

            window.Echo.connector.pusher.connection.bind('connected', () => {
                log('SUCCESS: WebSocket connected to Reverb!');
            });

            window.Echo.connector.pusher.connection.bind('error', (err) => {
                log('ERROR: ' + JSON.stringify(err));
            });

            log('Subscribing to private channel: notifications.' + userId);

            try {
                window.Echo.private('notifications.' + userId)
                    .listen('.notification.new', (data) => {
                        log('SUCCESS: EVENT RECEIVED!');
                        log('Data: ' + JSON.stringify(data, null, 2));
                    });
                log('Listening for .notification.new events...');
                log('Run: php artisan notify:send --user=' + userId + ' --title="Test"');
            } catch (e) {
                log('ERROR subscribing: ' + e.message);
            }
            @endauth
        }

        function testDisconnect() {
            if (window.Echo) {
                window.Echo.disconnect();
                log('Disconnected.');
                statusEl.textContent = 'Disconnected';
                statusEl.style.color = '#888';
            }
        }
    </script>
</body>
</html>
