<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Routing\Router;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\ApiTokenMiddleware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WhatsAppService::class, function ($app) {
            return new WhatsAppService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        app(Router::class)->aliasMiddleware('api.token', ApiTokenMiddleware::class);

        if (Schema::hasTable('settings')) {
            // Ambil dari database jika tabel sudah ada
            view()->share('app_version', Setting::get('app_version', config('app.version')));
        } else {
            // Fallback jika masih fresh install sebelum migrate
            view()->share('app_version', config('app.version'));
        }
    }
}
