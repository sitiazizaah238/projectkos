@php
    $chatUnreadCount = \App\Models\Chat::totalUnreadFor(Auth::id());
@endphp

<div class="position-relative">
    <a href="{{ route('penyewa.chat.index') }}" class="btn text-white position-relative" data-no-loader>
        <i class="bi bi-chat-dots fs-4"></i>

        <span id="chatBadgeTopbar" class="position-absolute start-50 translate-middle badge rounded-pill bg-danger"
            style="top:10px; font-size:10px; {{ $chatUnreadCount > 0 ? '' : 'display:none;' }}">
            {{ $chatUnreadCount > 0 ? ($chatUnreadCount > 99 ? '99+' : $chatUnreadCount) : '' }}
        </span>
    </a>
</div>

<script>
    // Polling unread count untuk badge chat (topbar + sidebar)
    (function() {
        if (window._chatBadgePolling) return; // cegah duplikat
        window._chatBadgePolling = true;

        function updateChatBadges() {
            fetch('/chat-unread-count', {
                headers: { 'Accept': 'application/json' },
            })
            .then(res => res.json())
            .then(data => {
                const count = data.count || 0;
                const label = count > 99 ? '99+' : count.toString();

                // Update topbar badge
                const topbar = document.getElementById('chatBadgeTopbar');
                if (topbar) {
                    topbar.textContent = label;
                    topbar.style.display = count > 0 ? '' : 'none';
                }

                // Update sidebar badge
                const sidebar = document.getElementById('chatBadgeSidebar');
                if (sidebar) {
                    sidebar.textContent = count;
                    sidebar.style.display = count > 0 ? '' : 'none';
                }
            })
            .catch(() => {});
        }

        setInterval(updateChatBadges, 3000);
    })();
</script>
