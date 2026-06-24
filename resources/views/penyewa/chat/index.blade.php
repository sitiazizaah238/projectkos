@extends('layouts.app')

@section('content')
    <style>
        .chat-list-container {
            border-radius: 16px;
            overflow: hidden;
        }

        .chat-list-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            border-bottom: 1px solid #f0f1f3;
            text-decoration: none;
            color: inherit;
            transition: background 0.15s;
            cursor: pointer;
        }

        .chat-list-item:hover {
            background: #f0f4ff;
            color: inherit;
        }

        .chat-list-item.has-unread {
            background: #f8f9ff;
        }

        .chat-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
            flex-shrink: 0;
        }

        .chat-info {
            flex: 1;
            min-width: 0;
        }

        .chat-name {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 2px;
        }

        .chat-kos-name {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .chat-last-msg {
            font-size: 13px;
            color: #9ca3af;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
            flex-shrink: 0;
        }

        .chat-time {
            font-size: 11px;
            color: #9ca3af;
        }

        .chat-badge {
            background: #0d6efd;
            color: white;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 600;
            min-width: 22px;
            text-align: center;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 60px;
            margin-bottom: 16px;
            display: block;
        }
    </style>

    <div class="d-flex">
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                @include('components.chat-icon-penyewa')
                @include('components.notif-penyewa')
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

            <div class="p-4" style="background:#f5f7fb;min-height:100vh;">
                <h3 class="fw-bold mb-1" style="font-size: 28px;">
                    <i class="bi bi-chat-dots me-2"></i>Chat
                </h3>
                <small class="text-muted d-block mb-4">Percakapan dengan pemilik kos</small>

                <div class="card shadow-sm border-0 chat-list-container" id="chatListContainer">
                    @forelse($chats as $chat)
                        @php
                            $unread = $chat->unreadCountFor(Auth::id());
                            $latest = $chat->latestMessage;
                        @endphp
                        <a href="{{ route('chat.show', $chat->id) }}"
                            class="chat-list-item {{ $unread > 0 ? 'has-unread' : '' }}"
                            data-chat-id="{{ $chat->id }}">
                            <div class="chat-avatar">
                                {{ strtoupper(substr($chat->pemilik->name, 0, 1)) }}
                            </div>

                            <div class="chat-info">
                                <div class="chat-name">{{ $chat->pemilik->name }}</div>
                                <div class="chat-kos-name">
                                    <i class="bi bi-house me-1"></i>{{ $chat->kos->nama_kos ?? '-' }}
                                </div>
                                <div class="chat-last-msg">
                                    @if ($latest)
                                        @if ($latest->sender_id === Auth::id())
                                            <span class="text-muted">Anda: </span>
                                        @endif
                                        {{ Str::limit($latest->message, 50) }}
                                    @else
                                        Belum ada pesan
                                    @endif
                                </div>
                            </div>

                            <div class="chat-meta">
                                @if ($latest)
                                    <span class="chat-time">
                                        {{ $latest->created_at->isToday() ? $latest->created_at->format('H:i') : $latest->created_at->format('d/m') }}
                                    </span>
                                @endif
                                @if ($unread > 0)
                                    <span class="chat-badge">{{ $unread }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="empty-state">
                            <i class="bi bi-chat-square-text"></i>
                            <h5 class="text-muted">Belum ada percakapan</h5>
                            <p class="text-muted">Mulai chat dengan pemilik kos dari halaman detail kos.</p>
                        </div>
                    @endforelse
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
                <a href="{{ route('penyewa.profile') }}" class="btn btn-primary w-100 mb-2">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-danger w-100">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('chatListContainer');

            function renderChatList(chats) {
                if (chats.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-chat-square-text"></i>
                            <h5 class="text-muted">Belum ada percakapan</h5>
                            <p class="text-muted">Mulai chat dengan pemilik kos dari halaman detail kos.</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                chats.forEach(chat => {
                    const unreadClass = chat.unread > 0 ? 'has-unread' : '';
                    const lastMsgHtml = chat.last_message
                        ? `${chat.last_message_is_mine ? '<span class="text-muted">Anda: </span>' : ''}${escapeHtml(chat.last_message)}`
                        : 'Belum ada pesan';

                    html += `
                        <a href="/chat/${chat.id}" class="chat-list-item ${unreadClass}" data-chat-id="${chat.id}">
                            <div class="chat-avatar">${chat.partner_initial}</div>
                            <div class="chat-info">
                                <div class="chat-name">${escapeHtml(chat.partner_name)}</div>
                                <div class="chat-kos-name"><i class="bi bi-house me-1"></i>${escapeHtml(chat.kos_name)}</div>
                                <div class="chat-last-msg">${lastMsgHtml}</div>
                            </div>
                            <div class="chat-meta">
                                ${chat.last_message_time ? `<span class="chat-time">${chat.last_message_time}</span>` : ''}
                                ${chat.unread > 0 ? `<span class="chat-badge">${chat.unread}</span>` : ''}
                            </div>
                        </a>
                    `;
                });
                container.innerHTML = html;
            }

            function escapeHtml(text) {
                const el = document.createElement('span');
                el.textContent = text;
                return el.innerHTML;
            }

            function pollChatList() {
                fetch('/chat-list-json', {
                    headers: { 'Accept': 'application/json' },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.chats) {
                        renderChatList(data.chats);
                    }
                })
                .catch(err => console.error('Poll chat list error:', err));
            }

            // Poll setiap 3 detik
            setInterval(pollChatList, 3000);
        });
    </script>
@endsection
