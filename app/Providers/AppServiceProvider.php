<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Hashing\WordPressHasher;
use Illuminate\Support\Facades\Hash;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Hash::extend('wordpress', function () {
                return new WordPressHasher();
            });


    }
}
