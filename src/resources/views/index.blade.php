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
                                {{ $user->name }}
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
                            <input type="text" class="form-control" id="message-input" placeholder="Type your message...">
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

    // Initialize Pusher
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        encrypted: true
    });

    // Subscribe to private channel
    const channel = pusher.subscribe('presence-chat.' + {{ auth()->id() }});
    channel.bind('message-sent', function(data) {
        if (currentReceiverId === data.message.user_id) {
            appendMessage(data.message);
        }
    });

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
        $.get(`/chat/messages/${userId}`)
            .done(function(messages) {
                messages.forEach(appendMessage);
            });
    }

    function appendMessage(message) {
        const isOwn = message.user_id === {{ auth()->id() }};
        const html = `
            <div class="message ${isOwn ? 'text-right' : 'text-left'} mb-2">
                <small class="text-muted">${message.sender.name}</small>
                <div class="p-2 rounded ${isOwn ? 'bg-primary text-white' : 'bg-light'}">
                    ${message.message}
                </div>
            </div>
        `;
        $('#messages').append(html);
        $('#messages').scrollTop($('#messages')[0].scrollHeight);
    }
</script>
@endpush