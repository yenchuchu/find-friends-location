<?php

namespace App\Providers;

use App\Repositories\Api\User\UserEloquentRepository;
use App\Repositories\Api\User\UserRepositoryInterface;
use App\Repositories\Backend\User\UserEloquentRepository as UserEloquentRepositoryBackend;
use App\Repositories\Backend\User\UserRepositoryInterface as UserRepositoryInterfaceBackend;
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
        $this->app->singleton(
            UserRepositoryInterface::class,
            UserEloquentRepository::class
        );
        $this->app->singleton(
            UserRepositoryInterfaceBackend::class,
            UserEloquentRepositoryBackend::class
        );
//        $this->app->bind('App\MyRepoInterface', 'App\EloquentMyRepo');
    }
}
