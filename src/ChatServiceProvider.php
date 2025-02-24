<?php 

namespace NNixon\LaravelRealtimeChat;

use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'laravel-chat');
        
        $this->publishes([
            __DIR__ . '/config/chat.php' => config_path('chat.php'),
            __DIR__ . '/resources/views' => resource_path('views/vendor/laravel-chat'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/chat.php', 'chat'
        );
    }
}