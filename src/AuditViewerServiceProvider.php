<?php

namespace SeinOxygen\AuditViewer;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SeinOxygen\AuditViewer\Views\Components\Author;
use SeinOxygen\AuditViewer\Views\Components\Badge;

class AuditViewerServiceProvider extends ServiceProvider
{
    /**
     * Register the Swift Transport instance.
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/audit-viewer.php', 'audit-viewer');

        $this->app->singleton('AuditViewer', function () {
            return new AuditViewer();
        });
    }

    public function boot()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });

        $this->loadViewComponentsAs('audit-viewer', [
            Author::class,
            Badge::class,
        ]);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'audit-viewer');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'audit-viewer');

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => config('audit-viewer.prefix'),
            'middleware' => config('audit-viewer.middleware')
        ];
    }

    private function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/../config/audit-viewer.php' => config_path('audit-viewer.php'),
          ], 'config');

          $this->publishes([
              __DIR__.'/../resources/views' => resource_path('views/vendor/audit-viewer'),
          ], 'views');

          $this->publishes([
              __DIR__.'/../src/Views/Components/' => app_path('View/Components'),
              __DIR__.'/../resources/views/components/' => resource_path('views/components'),
          ], 'view-components');

          $this->publishes([
              __DIR__.'/../resources/lang' => resource_path('lang'),
          ], 'translations');
    }
}
