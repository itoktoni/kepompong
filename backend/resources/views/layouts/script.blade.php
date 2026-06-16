<script>
    function warehouseApp() {
        return {
            drawerOpen: false,
            sidebarOpen: true,
            notifications: [],
            unreadCount: 0,

            init() {
                this.fetchNotifications();
                setInterval(() => this.pollNotifications(), 15000);
            },

            async pollNotifications() {
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
                    const newNotifs = data.notifications || [];
                    const existingIds = new Set(this.notifications.map(n => n.id));
                    const added = newNotifs.filter(n => !existingIds.has(n.id));
                    this.notifications = newNotifs;
                    this.unreadCount = data.unread_count || 0;
                    added.forEach(n => {
                        if (window.showToast) window.showToast(n.title, n.body);
                    });
                } catch (e) {}
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
        toast.className = 'bg-surface-container-lowest border border-outline-variant rounded-lg p-4 shadow-lg max-w-sm';
        toast.innerHTML = '<div class="flex items-start gap-3"><span class="material-symbols-outlined text-primary">notifications</span><div><p class="font-body-sm font-semibold text-on-surface">' + title + '</p><p class="font-body-sm text-on-surface-variant text-sm">' + (body || '') + '</p></div></div>';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    };
</script>
