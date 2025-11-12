<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Carbon;

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
        Blade::directive('dt', function ($expression) {
            return "<?php echo ($expression) ? (\\Illuminate\\Support\\Carbon::parse($expression)->timezone(config('app.timezone'))->format('Y-m-d h:i A')) : ''; ?>";
        });
    }
}
