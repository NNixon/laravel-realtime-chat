<?php 

return [
    /*
    |--------------------------------------------------------------------------
    | Chat Routes
    |--------------------------------------------------------------------------
    |
    | Here you can specify the route prefix for all chat routes.
    | If you change this, remember to update your JavaScript accordingly.
    |
    */
    'route_prefix' => 'chat',

    /*
    |--------------------------------------------------------------------------
    | Message Settings
    |--------------------------------------------------------------------------
    |
    | Configure various message-related settings here.
    |
    */
    'messages_per_page' => 20,
    'max_message_length' => 1000,
    'file_uploads' => false,
    'allowed_file_types' => ['image/jpeg', 'image/png', 'image/gif'],
    'max_file_size' => 5120, // in KB (5MB)

    /*
    |--------------------------------------------------------------------------
    | User Settings
    |--------------------------------------------------------------------------
    |
    | Configure user-related settings here.
    |
    */
    'user_model' => \App\Models\User::class,
    'user_display_name' => 'name', // column to use for displaying user names
    'online_status' => true,
    'typing_indicator' => true,

    /*
    |--------------------------------------------------------------------------
    | Broadcasting Settings
    |--------------------------------------------------------------------------
    |
    | Configure broadcasting-related settings here.
    |
    */
    'broadcast_driver' => 'pusher', // supports 'pusher', 'ably', 'redis'
    'presence_channel_name' => 'chat.presence',
    'private_channel_prefix' => 'chat.conversation.',

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure notification-related settings here.
    |
    */
    'notifications' => [
        'enabled' => true,
        'sound' => true,
        'desktop' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Settings
    |--------------------------------------------------------------------------
    |
    | Configure UI-related settings here.
    |
    */
    'ui' => [
        'theme' => 'light', // light, dark, or auto
        'refresh_interval' => 60, // seconds
        'timezone' => 'UTC',
        'date_format' => 'Y-m-d H:i:s',
        'enable_emojis' => true,
    ],
];