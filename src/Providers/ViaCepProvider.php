<?php
namespace Rotaz\GeoData\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class ViaCepProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    public function searchByCep(string $cep): ?array
    {
        $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
        if ($response->successful() && !isset($response['erro'])) {
            return $response->json();
        }

        return null;
    }
}
