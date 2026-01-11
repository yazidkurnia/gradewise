<?php

namespace App\Providers;

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
        // Bind LectureRepository interface to implementation
        $this->app->bind(
            \App\Contracts\LectureRepositoryInterface::class,
            \App\Repositories\LectureRepository::class
        );

        // Bind ThesisRepository interface to implementation
        $this->app->bind(
            \App\Contracts\ThesisRepositoryInterface::class,
            \App\Repositories\ThesisRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
