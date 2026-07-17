<section id="tab-messages" class="tab">

    <div class="panel">
        <div class="panel-head">
            <div class="panel-title">
                <i class="ti ti-inbox {{ $unreadCount > 0 ? 'danger' : '' }}"></i>
                Inbox Messages
                @if($unreadCount > 0)
                    <span style="font-size: 12px; color: var(--color-danger); margin-left: 6px; font-weight: 600;">
                        ({{ $unreadCount }} unread)
                    </span>
                @endif
            </div>
        </div>
        
        @forelse($messages as $msg)
            @php $isUnread = $msg->status === 'unread'; @endphp
            <div class="inbox-row {{ $isUnread ? 'unread' : '' }}">
                @if($isUnread)
                    <div class="unread-dot"></div>
                @else
                    <div class="unread-spacer"></div>
                @endif

                <div class="inbox-avatar {{ $isUnread ? '' : 'read' }}">
                    {{ collect(explode(' ', $msg->name))->map(fn($n) => $n[0] ?? '')->take(2)->join('') }}
                </div>

                <div class="inbox-body">
                    <div class="inbox-header">
                        <span class="inbox-name {{ $isUnread ? '' : 'read' }}">{{ $msg->name }}</span>
                        @if($isUnread)
                            <span class="new-badge">NEW</span>
                        @endif
                    </div>
                    <div class="inbox-email">
                        <a href="mailto:{{ $msg->email }}">{{ $msg->email }}</a>
                    </div>
                    <div class="inbox-msg" style="margin-top: 8px;">{{ $msg->message }}</div>
                </div>

                <div class="inbox-meta" style="align-items: flex-end;">
                    <div class="inbox-time">{{ $msg->created_at->diffForHumans() }}</div>
                    <div style="display: flex; gap: 8px; margin-top: 10px;">
                        <button type="button" class="btn btn-ghost btn-sm btn-reply-trigger" data-message-id="{{ $msg->id }}">
                            <i class="ti ti-arrow-back-up"></i> Reply
                        </button>
                        @if($isUnread)
                            <form method="POST" action="/admin/messages/{{ $msg->id }}/read">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-ghost btn-sm">
                                    <i class="ti ti-check"></i> Mark read
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Serialized JSON data block for reply prefill -->
                <script type="application/json" id="message-data-{{ $msg->id }}">{!! json_encode([
                    'id' => $msg->id,
                    'name' => $msg->name,
                    'email' => $msg->email,
                    'message' => $msg->message,
                    'date' => $msg->created_at->format('M d, Y h:i A'),
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
            </div>
        @empty
            <div class="empty-panel-lg">
                Your inbox is empty.
            </div>
        @endforelse
    </div>

</section>
