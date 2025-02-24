@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Users</div>
                <div class="card-body">
                    <ul class="list-group" id="users-list">
                        @foreach($users as $user)
                            <li class="list-group-item user-item" data-user-id="{{ $user->id }}">
                                {{ $user->{config('chat.user_display_name')} }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Messages</div>
                <div class="card-body">
                    <div id="messages" style="height: 300px; overflow-y: scroll;"></div>
                    <form id="message-form" class="mt-3">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="message-input" 
                                   placeholder="Type your message..."
                                   maxlength="{{ config('chat.max_message_length') }}">
                            <button class="btn btn-primary" type="submit">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentReceiverId = null;
    const config = @json([
        'refreshInterval' => config('chat.ui.refresh_interval'),
        'typingIndicator' => config('chat.typing_indicator'),
        'notifications' => config('chat.notifications'),
        'timezone' => config('chat.ui.timezone'),
        'dateFormat' => config('chat.ui.date_format'),
        'enableEmojis' => config('chat.ui.enable_emojis'),
        'theme' => config('chat.ui.theme'),
    ]);

    // Initialize Pusher if using Pusher driver
    @if(config('chat.broadcast_driver') === 'pusher')
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        encrypted: true
    });

    const channel = pusher.subscribe('{{ config('chat.presence_channel_name') }}.' + {{ auth()->id() }});
    channel.bind('message-sent', function(data) {
        if (currentReceiverId === data.message.user_id) {
            appendMessage(data.message);
            
            // Show notification if enabled
            if (config.notifications.enabled && config.notifications.desktop) {
                showNotification(data.message);
            }
        }
    });
    @endif

    // Load messages when clicking on a user
    $('.user-item').click(function() {
        const userId = $(this).data('user-id');
        currentReceiverId = userId;
        loadMessages(userId);
    });

    // Send message
    $('#message-form').submit(function(e) {
        e.preventDefault();
        if (!currentReceiverId) return;

        const message = $('#message-input').val();
        if (!message) return;

        $.post('{{ route('chat.send') }}', {
            receiver_id: currentReceiverId,
            message: message,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            appendMessage(response);
            $('#message-input').val('');
        });
    });

    function loadMessages(userId) {
        $('#messages').empty();
        $.get(`/{{ config('chat.route_prefix') }}/messages/${userId}`)
            .done(function(response) {
                response.data.forEach(appendMessage);
            });
    }

    function appendMessage(message) {
        const isOwn = message.user_id === {{ auth()->id() }};
        const formattedDate = formatDate(message.created_at);
        
        const html = `
            <div class="message ${isOwn ? 'text-right' : 'text-left'} mb-2">
                <small class="text-muted">${message.sender.${config('chat.user_display_name')}} - ${formattedDate}</small>
                <div class="p-2 rounded ${isOwn ? 'bg-primary text-white' : 'bg-light'}">
                    ${config.enableEmojis ? convertEmojis(message.message) : message.message}
                </div>
            </div>
        `;
        $('#messages').append(html);
        $('#messages').scrollTop($('#messages')[0].scrollHeight);
        
        // Play sound if enabled
        if (!isOwn && config.notifications.enabled && config.notifications.sound) {
            playNotificationSound();
        }
    }

    function formatDate(date) {
        return moment(date).tz(config.timezone).format(config.dateFormat);
    }

    function showNotification(message) {
        if ("Notification" in window && Notification.permission === "granted") {
            new Notification(`New message from ${message.sender.${config('chat.user_display_name')}}`, {
                body: message.message
            });
        }
    }

    // Optional: Add emoji support if enabled
    if (config.enableEmojis) {
        function convertEmojis(text) {
            return text.replace(/:\w+:/g, match => emojis[match] || match);
        }
    }

    // Apply theme
    if (config.theme !== 'auto') {
        document.body.classList.toggle('dark-theme', config.theme === 'dark');
    }

    // Auto-refresh messages
    if (config.refreshInterval > 0) {
        setInterval(() => {
            if (currentReceiverId) {
                loadMessages(currentReceiverId);
            }
        }, config.refreshInterval * 1000);
    }
</script>
@endpush