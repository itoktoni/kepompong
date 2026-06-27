<script>
    function warehouseApp() {
        return {
            drawerOpen: false,
            sidebarOpen: true,
            notifications: [],
            unreadCount: 0,

            init() {
                this.fetchNotifications();
                window.addEventListener('new-notification', (e) => this.handleRealtimeNotification(e.detail));
            },

            handleRealtimeNotification(notif) {
                const existingIds = new Set(this.notifications.map(n => n.id));
                if (!existingIds.has(notif.id)) {
                    this.notifications.unshift(notif);
                    this.unreadCount = this.notifications.filter(n => !n.read).length;
                    if (window.showToast) window.showToast(notif.title, notif.body);
                }
            },

            async fetchNotifications() {
                try {
                    const res = await fetch('/notifications-web', {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.notifications = data.notifications || [];
                    this.unreadCount = data.unread_count || 0;
                } catch (e) {
                    console.warn('Failed to fetch notifications:', e);
                }
            },

            async markRead(notif) {
                if (notif.read) return;
                notif.read = true;
                this.unreadCount = this.notifications.filter(n => !n.read).length;
                try {
                    await fetch('/notifications-web/' + notif.id + '/read', {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                } catch (e) {
                    console.warn('Failed to mark read:', e);
                }
            },

            async markAllRead() {
                this.notifications.forEach(n => n.read = true);
                this.unreadCount = 0;
                try {
                    await fetch('/notifications-web/read-all', {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                } catch (e) {
                    console.warn('Failed to mark all read:', e);
                }
            },
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('nav .bg-primary').forEach(function(el) {
            el.scrollIntoView({ block: 'center', behavior: 'smooth' });
        });
    });

    window.showToast = function(title, body) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 z-[100] bg-surface-container-lowest border border-outline-variant rounded-lg p-4 shadow-lg max-w-sm transition-all duration-300';
        toast.innerHTML = '<div class="flex items-start gap-3"><span class="text-primary">🔔</span><div><p class="font-body-sm font-semibold text-on-surface">' + title + '</p><p class="font-body-sm text-on-surface-variant text-sm">' + (body || '') + '</p></div></div>';
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    };
</script>
