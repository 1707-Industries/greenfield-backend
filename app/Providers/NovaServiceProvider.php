<?php

namespace App\Providers;

use Beyondcode\TinkerTool\Tinker;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use MadWeb\NovaHorizonLink\HorizonLink;
use MadWeb\NovaTelescopeLink\TelescopeLink;
use Sbine\RouteViewer\RouteViewer;
use Skydiver\NovaUpdateCard\LaravelUpdateCard;
use Skydiver\NovaUpdateCard\NovaUpdateCard;
use Vink\NovaCacheCard\CacheCard;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (request()->root() === config('nova.url')) {
            parent::boot();
        }
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            new TelescopeLink,
            new HorizonLink,
            new Tinker,
            new RouteViewer,
        ];
    }

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
     * Configure the Nova authorization services.
     *
     * @return void
     */
    protected function authorization()
    {
        $this->gate();

        Nova::auth(function ($request) {
            return Gate::check('viewNova', [$request->user()]);
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return $user->is_admin;
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            (new LaravelUpdateCard)->width('1/2'),
            (new NovaUpdateCard)->width('1/2'),
            (new CacheCard)->width('1/2'),
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }
}
