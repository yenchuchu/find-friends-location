<?php

namespace App\Providers;

use App\Repositories\Api\User\ShareUserEloquentRepository;
use App\Repositories\Api\User\ShareUserRepositoryInterface;
use App\Repositories\Api\User\UserEloquentRepository;
use App\Repositories\Api\User\UserRepositoryInterface;
use App\Repositories\Web\User\UserEloquentRepository as UserEloquentRepositoryWeb;
use App\Repositories\Web\User\UserRepositoryInterface as UserRepositoryInterfaceWeb;
use App\Repositories\Post\PostEloquentRepository;
use App\Repositories\Post\PostRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            PostRepositoryInterface::class,
            PostEloquentRepository::class
        );

        // BACKEN
        $this->app->singleton(
            UserRepositoryInterfaceWeb::class,
            UserEloquentRepositoryWeb::class
        );

        // FRONTEN
        $this->app->singleton(
            UserRepositoryInterface::class,
            UserEloquentRepository::class
        );
        $this->app->singleton(
            ShareUserRepositoryInterface::class,
            ShareUserEloquentRepository::class
        );

//        $this->app->bind('App\MyRepoInterface', 'App\EloquentMyRepo');
    }
}
