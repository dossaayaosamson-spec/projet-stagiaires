@inject('notificationService', 'App\Services\NotificationService')

@php
    $notifications = $notificationService->getNotificationsForUser();
    $unreadCount = $notifications->count();
@endphp

<div class="relative inline-block text-left" id="notification-center-root">
    <!-- Bell Button -->
    <button id="notification-bell-btn" type="button" class="hover:text-agau-blue transition-colors relative bg-transparent border-none cursor-pointer p-1.5 rounded-full hover:bg-agau-border/10 focus:outline-none" title="Notifications">
        <i class="bi bi-bell text-[1.3rem] text-agau-text hover:text-agau-blue transition-colors"></i>
        @if($unreadCount > 0)
            <span id="notif-badge" class="absolute top-2 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[9px] font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full ring-2 ring-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Menu -->
    <div id="notification-dropdown-menu" class="hidden absolute right-0 mt-3 w-80 px-2 sm:w-96 bg-white border border-agau-border/60 rounded-2xl shadow-xl z-50 overflow-hidden animate-in fade-in slide-in-from-top-2 duration-200">
        <!-- Header -->
        <div class="px-4 py-3 bg-agau-off-white/80 border-b border-agau-border/40 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-bell-fill text-agau-blue text-sm"></i>
                <span class="font-bold text-xs text-agau-text uppercase tracking-wider">Centre de Notifications</span>
            </div>
            @if($unreadCount > 0)
                <button id="mark-all-read-btn" class="text-[10px] bg-agau-blue/10 text-agau-blue font-bold px-2 py-0.5 rounded-full hover:bg-agau-blue hover:text-white transition-colors border-none cursor-pointer">
                    Tout marquer comme lu
                </button>
            @endif
        </div>

        <!-- List -->
        <div id="notif-list" class="max-h-80 overflow-y-auto divide-y divide-agau-border/30">
            @forelse($notifications as $notif)
                <a href="{{ $notif['url'] }}"
                   data-notif-id="{{ $notif['id'] }}"
                   class="notif-item p-3.5 flex gap-3 hover:bg-agau-off-white/60 transition-colors no-underline group block">
                    <div class="w-8 h-8 rounded-xl bg-{{ $notif['color'] }}-100 text-{{ $notif['color'] }}-600 flex items-center justify-center flex-shrink-0 text-sm mt-0.5 group-hover:scale-110 transition-transform">
                        <i class="bi {{ $notif['icon'] }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-0.5">
                            <strong class="text-xs font-bold text-agau-text truncate group-hover:text-agau-blue transition-colors">{{ $notif['title'] }}</strong>
                            <span class="text-[9px] text-agau-muted flex-shrink-0">{{ $notif['time'] }}</span>
                        </div>
                        <p class="text-[11px] text-agau-muted m-0 line-clamp-2 leading-relaxed">{{ $notif['message'] }}</p>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-agau-muted">
                    <i class="bi bi-bell-slash text-3xl text-agau-muted/40 block mb-2"></i>
                    <p class="text-xs m-0 font-medium">Aucune nouvelle notification pour le moment.</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="p-2 bg-agau-off-white/50 border-t border-agau-border/40 text-center">
            <button onclick="document.getElementById('notification-dropdown-menu').classList.add('hidden')" class="text-[11px] font-bold text-agau-muted hover:text-agau-text bg-transparent border-none cursor-pointer py-1">
                Fermer
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const bellBtn = document.getElementById('notification-bell-btn');
        const menu = document.getElementById('notification-dropdown-menu');
        const badge = document.getElementById('notif-badge');
        const markAllBtn = document.getElementById('mark-all-read-btn');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (bellBtn && menu) {
            bellBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
            });
            document.addEventListener('click', function (e) {
                if (!menu.contains(e.target) && !bellBtn.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        }

        // Marquer une notification comme lue au clic puis suivre le lien
        document.querySelectorAll('.notif-item').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const notifId = this.dataset.notifId;
                const href = this.href;

                fetch('{{ route("notifications.mark-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ id: notifId }),
                }).finally(function () {
                    // Retirer la notification de la liste
                    link.closest('.notif-item')?.remove();
                    updateBadge();
                    // Naviguer vers la page cible
                    window.location.href = href;
                });
            });
        });

        // Marquer toutes comme lues
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function () {
                fetch('{{ route("notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                }).then(function () {
                    const list = document.getElementById('notif-list');
                    if (list) {
                        list.innerHTML = '<div class="p-8 text-center text-agau-muted"><i class="bi bi-bell-slash text-3xl text-agau-muted/40 block mb-2"></i><p class="text-xs m-0 font-medium">Aucune nouvelle notification pour le moment.</p></div>';
                    }
                    if (badge) badge.remove();
                    markAllBtn.remove();
                });
            });
        }

        function updateBadge() {
            const remaining = document.querySelectorAll('.notif-item').length;
            if (badge) {
                if (remaining === 0) {
                    badge.remove();
                } else {
                    badge.textContent = remaining > 9 ? '9+' : remaining;
                }
            }
        }
    })();
</script>
