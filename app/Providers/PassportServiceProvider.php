<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PassportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // $user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);
        // return $user;
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
