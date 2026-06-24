@extends('layouts.app')

@section('content')
    <style>
        .chat-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 64px);
            background: #f5f7fb;
            min-height: 0;
        }

        .chat-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .chat-header .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
            flex-shrink: 0;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-height: 0;
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .chat-date-divider {
            text-align: center;
            margin: 12px 0;
        }

        .chat-date-divider span {
            background: #e5e7eb;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            color: #6b7280;
        }

        .message-bubble {
            max-width: 70%;
            padding: 10px 16px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.5;
            word-break: break-word;
            position: relative;
            animation: fadeInUp 0.2s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-mine {
            align-self: flex-end;
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: white;
            border-bottom-right-radius: 6px;
        }

        .message-theirs {
            align-self: flex-start;
            background: white;
            color: #1f2937;
            border: 1px solid #e5e7eb;
            border-bottom-left-radius: 6px;
        }

        .message-time {
            font-size: 11px;
            margin-top: 4px;
            opacity: 0.7;
        }

        .message-mine .message-time {
            text-align: right;
            color: rgba(255, 255, 255, 0.7);
        }

        .message-theirs .message-time {
            color: #9ca3af;
        }

        .chat-input-area {
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 16px 20px;
            flex-shrink: 0;
        }

        .chat-input-area form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .chat-input-area input {
            flex: 1;
            border: 1px solid #d1d5db;
            border-radius: 24px;
            padding: 10px 20px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .chat-input-area input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }

        .chat-send-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .chat-send-btn:hover:not(:disabled) {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .chat-send-btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
            transform: none;
            background: #9ca3af;
        }

        .read-indicator {
            font-size: 10px;
            opacity: 0.7;
        }

        .empty-chat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            color: #9ca3af;
            gap: 8px;
        }

        .empty-chat i {
            font-size: 48px;
        }

        /* Wrapper agar tidak overflow */
        .chat-page-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
    </style>

    <div class="d-flex" style="height: 100vh; overflow: hidden;">
        @if ($userRole === 'penyewa')
            @include('components.sidebar-penyewa')
        @else
            @include('components.sidebar-pemilik')
        @endif

        <div class="flex-grow-1 chat-page-wrapper">
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                @if ($userRole === 'penyewa')
                    @include('components.chat-icon-penyewa')
                    @include('components.notif-penyewa')
                @else
                    @include('components.chat-icon-pemilik')
                    @include('components.notif-pemilik')
                @endif

                <button type="button" class="btn text-white d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#profileModal">
                    <span class="me-2">{{ Auth::user()->name }}</span>
                    @if (Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}"
                            style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid white;">
                    @else
                        <i class="bi bi-person-circle fs-3"></i>
                    @endif
                </button>
            </div>

            <div class="chat-container">
                {{-- Chat Header --}}
                <div class="chat-header">
                    <a href="{{ $userRole === 'penyewa' ? route('penyewa.chat.index') : route('pemilik.chat.index') }}"
                        class="btn btn-sm btn-outline-secondary rounded-pill" data-no-loader>
                        <i class="bi bi-arrow-left"></i>
                    </a>

                    <div class="avatar">
                        @php
                            $chatPartner = $userRole === 'penyewa' ? $chat->pemilik : $chat->penyewa;
                        @endphp
                        {{ strtoupper(substr($chatPartner->name, 0, 1)) }}
                    </div>

                    <div>
                        <div class="fw-semibold">{{ $chatPartner->name }}</div>
                        <small class="text-muted">
                            <i class="bi bi-house me-1"></i>{{ $chat->kos->nama_kos ?? '-' }}
                        </small>
                    </div>
                </div>

                {{-- Chat Messages (scrollable area) --}}
                <div class="chat-messages" id="chatMessages">
                    @if ($chat->messages->isEmpty())
                        <div class="empty-chat">
                            <i class="bi bi-chat-square-text"></i>
                            <p>Belum ada pesan. Mulai percakapan!</p>
                        </div>
                    @else
                        @php $lastDate = null; @endphp
                        @foreach ($chat->messages as $msg)
                            @php $msgDate = $msg->created_at->format('d M Y'); @endphp

                            @if ($msgDate !== $lastDate)
                                <div class="chat-date-divider">
                                    <span>{{ $msg->created_at->isToday() ? 'Hari Ini' : ($msg->created_at->isYesterday() ? 'Kemarin' : $msgDate) }}</span>
                                </div>
                                @php $lastDate = $msgDate; @endphp
                            @endif

                            <div class="message-bubble {{ $msg->sender_id === Auth::id() ? 'message-mine' : 'message-theirs' }}"
                                data-msg-id="{{ $msg->id }}">
                                {{ $msg->message }}
                                <div class="message-time">
                                    {{ $msg->created_at->format('H:i') }}
                                    @if ($msg->sender_id === Auth::id())
                                        <span class="read-indicator">
                                            @if ($msg->is_read)
                                                <i class="bi bi-check2-all text-info"></i>
                                            @else
                                                <i class="bi bi-check2"></i>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Chat Input --}}
                <div class="chat-input-area">
                    <form id="chatForm" data-no-loader>
                        @csrf
                        <input type="text" id="messageInput" name="message" placeholder="Ketik pesan..."
                            autocomplete="off" maxlength="1000">
                        <button type="submit" id="sendBtn" class="chat-send-btn" disabled>
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Modal --}}
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content p-3 text-center" style="border-radius:20px;">
                <div class="mb-3">
                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                </div>
                <a href="{{ $userRole === 'penyewa' ? route('penyewa.profile') : route('pemilik.profile') }}"
                    class="btn btn-primary w-100 mb-2">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-danger w-100">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatId = {{ $chat->id }};
            const userId = {{ Auth::id() }};
            const messagesContainer = document.getElementById('chatMessages');
            const messageInput = document.getElementById('messageInput');
            const sendBtn = document.getElementById('sendBtn');
            const chatForm = document.getElementById('chatForm');
            let lastMessageId = {{ $chat->messages->last()->id ?? 0 }};

            // ====== Scroll ke bawah ======
            function scrollToBottom() {
                requestAnimationFrame(() => {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                });
            }
            scrollToBottom();

            // ====== Toggle tombol kirim (disabled jika kosong) ======
            function toggleSendBtn() {
                sendBtn.disabled = messageInput.value.trim().length === 0;
            }
            messageInput.addEventListener('input', toggleSendBtn);
            toggleSendBtn(); // initial state

            // ====== Kirim pesan (optimistic UI, tanpa loading) ======
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const msg = messageInput.value.trim();
                if (!msg) return;

                // Langsung tampilkan di UI (optimistic update)
                const tempId = 'tmp_' + Date.now();
                const now = new Date();
                const timeStr = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });

                appendMessage({
                    id: tempId,
                    message: msg,
                    is_mine: true,
                    time: timeStr,
                    is_read: false,
                    _temp: true,
                });
                scrollToBottom();

                // Reset input
                messageInput.value = '';
                toggleSendBtn();
                messageInput.focus();

                // Kirim ke server di background
                fetch(`/chat/${chatId}/send-ajax`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ message: msg }),
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Ganti temp ID dengan real ID
                            const tempBubble = messagesContainer.querySelector(`[data-msg-id="${tempId}"]`);
                            if (tempBubble) {
                                tempBubble.dataset.msgId = data.message.id;
                            }
                            lastMessageId = Math.max(lastMessageId, data.message.id);
                        }
                    })
                    .catch(err => {
                        console.error('Send failed:', err);
                        // Tandai pesan gagal
                        const tempBubble = messagesContainer.querySelector(`[data-msg-id="${tempId}"]`);
                        if (tempBubble) {
                            tempBubble.style.opacity = '0.5';
                            const timeDiv = tempBubble.querySelector('.message-time');
                            if (timeDiv) timeDiv.innerHTML += ' <i class="bi bi-exclamation-circle"></i> Gagal';
                        }
                    });
            });

            // ====== Enter untuk kirim ======
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    chatForm.dispatchEvent(new Event('submit', { cancelable: true }));
                }
            });

            // ====== Append message bubble ======
            function appendMessage(msg) {
                // Hapus empty state
                const empty = messagesContainer.querySelector('.empty-chat');
                if (empty) empty.remove();

                // Cegah duplikat (kecuali temp)
                if (!msg._temp && messagesContainer.querySelector(`[data-msg-id="${msg.id}"]`)) {
                    return;
                }

                // Date divider — hanya tambahkan kalau belum ada "Hari Ini"
                const allDividers = messagesContainer.querySelectorAll('.chat-date-divider');
                const lastDivider = allDividers.length > 0 ? allDividers[allDividers.length - 1] : null;
                const lastDividerText = lastDivider ? lastDivider.querySelector('span')?.textContent.trim() : '';

                if (lastDividerText !== 'Hari Ini') {
                    const divider = document.createElement('div');
                    divider.className = 'chat-date-divider';
                    divider.innerHTML = '<span>Hari Ini</span>';
                    messagesContainer.appendChild(divider);
                }

                const bubble = document.createElement('div');
                bubble.className = `message-bubble ${msg.is_mine ? 'message-mine' : 'message-theirs'}`;
                bubble.dataset.msgId = msg.id;

                let readHtml = '';
                if (msg.is_mine) {
                    readHtml = `<span class="read-indicator">${
                        msg.is_read
                            ? '<i class="bi bi-check2-all text-info"></i>'
                            : '<i class="bi bi-check2"></i>'
                    }</span>`;
                }

                bubble.innerHTML = `
                    ${escapeHtml(msg.message)}
                    <div class="message-time">
                        ${msg.time} ${readHtml}
                    </div>
                `;

                messagesContainer.appendChild(bubble);
            }

            function escapeHtml(text) {
                const el = document.createElement('span');
                el.textContent = text;
                return el.innerHTML;
            }

            // ====== Polling pesan baru (realtime) ======
            function pollMessages() {
                fetch(`/chat/${chatId}/messages`, {
                        headers: { 'Accept': 'application/json' },
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.messages || data.messages.length === 0) return;

                        let hasNew = false;

                        data.messages.forEach(msg => {
                            // Pesan baru dari lawan bicara
                            if (msg.id > lastMessageId && !msg.is_mine) {
                                if (!messagesContainer.querySelector(`[data-msg-id="${msg.id}"]`)) {
                                    appendMessage(msg);
                                    hasNew = true;
                                }
                            }

                            // Update read indicator pada pesan kita
                            if (msg.is_mine && msg.is_read) {
                                const indicator = messagesContainer.querySelector(
                                    `[data-msg-id="${msg.id}"] .read-indicator`
                                );
                                if (indicator) {
                                    indicator.innerHTML = '<i class="bi bi-check2-all text-info"></i>';
                                }
                            }
                        });

                        const maxId = Math.max(...data.messages.map(m => m.id));
                        if (maxId > lastMessageId) lastMessageId = maxId;

                        if (hasNew) scrollToBottom();
                    })
                    .catch(err => console.error('Poll error:', err));
            }

            // Poll setiap 3 detik
            setInterval(pollMessages, 3000);
        });
    </script>
@endsection
