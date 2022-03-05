<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro("api", function (int $code, string $message, $data = null) {
            return Response::json([
                "code" => $code,
                "message" => $message,
                "data" => $data
            ], $code);
        });
    }
}
