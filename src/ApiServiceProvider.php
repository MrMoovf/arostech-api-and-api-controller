<?php

namespace Arostech\Api;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 
        echo 'Hi there, I should be working -- FROM API Service Provider!';
        echo 'Hi there, I should be working -- FROM API Service Provider!';
        echo 'Hi there, I should be working -- FROM API Service Provider!';
        echo 'Hi there, I should be working -- FROM API Service Provider!';
        $this->loadRoutesFrom(__DIR__.'routes/api.php');
    }
}