<?php

namespace App\Providers;

use App\Models\CmsSetting;
use App\Models\Notification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Inject CMS settings into every view
        View::composer('*', function ($view) {
            try {
                $settings = CmsSetting::pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                $settings = [];
            }

            $unreadNotifs = 0;
            if (Auth::check()) {
                try {
                    $unreadNotifs = Notification::where('user_id', Auth::id())
                        ->where('is_read', false)
                        ->count();
                } catch (\Exception $e) {}
            }

            $view->with('settings', $settings)
                 ->with('unread_notifs', $unreadNotifs);
        });

        // Custom Blade helpers
        Blade::directive('stars', function ($expression) {
            return "<?php for(\$_i=1;\$_i<=5;\$_i++): ?>";
        });

        // @imageUrl($url)  — return a real URL or a placeholder
        Blade::directive('imageUrl', function ($expression) {
            return "<?php echo \\App\\Helpers\\Helpers::imageUrl({$expression}); ?>";
        });
    }
}
