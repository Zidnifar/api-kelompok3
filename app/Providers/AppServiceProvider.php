<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
    Scramble::configure()
        ->routes(function (\Illuminate\Routing\Route $route) {
            // Sembunyikan route internal
            if (str_starts_with($route->uri(), 'api/internal')) {
                return false;
            }
            // Sembunyikan debug-token
            if (str_starts_with($route->uri(), 'api/debug-token')) {
                return false;
            }
            if (str_starts_with($route->uri(), 'api/mahasiswa/{nim}/krs-detail')) {
                return false;
            }
            return str_starts_with($route->uri(), 'api/');
        })
        ->withDocumentTransformers(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer')
            );
        });
    }
}
