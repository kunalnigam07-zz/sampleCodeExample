<?php

namespace App\Providers;

use App\Validation\ClassTimeValidator;
use Illuminate\Support\ServiceProvider;
use Blade;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Admin
        view()->composer(
            'admin.partials.menu', 'App\Http\ViewComposers\Admin\MenuComposer'
        );

        view()->composer(
            'admin.users.manage', 'App\Http\ViewComposers\Admin\AdminPermissionComposer'
        );

        view()->composer(
            'admin.dashboard.partial-counters', 'App\Http\ViewComposers\Admin\DashboardCountersComposer'
        );

        // Web
        view()->composer(
            ['web.layouts.base', 'web.home.index', 'web.join.index', 'web.instructor-bulk.index', 'web.instructor-classes.create', 'web.live.rate', 'web.layouts.live', 'web.live.index', 'web.hiw.partial-js', 'web.partials.system-checks-js'], 'App\Http\ViewComposers\Web\BaseComposer'
        );

        view()->composer(
            ['web.partials.header'], 'App\Http\ViewComposers\Web\UserComposer'
        );

        // Email
        view()->composer(
            'emails.web.template-html', 'App\Http\ViewComposers\Email\EmailComposer'
        );

        // Blade Directives
        Blade::directive('set', function($expression) {
            return "<?php $expression; ?>";
        });

        Validator::extend('class_event_time', ClassTimeValidator::create());
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
}
