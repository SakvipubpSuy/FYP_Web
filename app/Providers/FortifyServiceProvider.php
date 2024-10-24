<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewAdmin;
use App\Actions\Fortify\ResetAdminPassword;
use App\Actions\Fortify\UpdateAdminPassword;
use App\Actions\Fortify\UpdateAdminProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use app\Models\User;
use Illuminate\Support\Facades\Hash;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {   
        Fortify::createUsersUsing(CreateNewAdmin::class);
        Fortify::updateUserProfileInformationUsing(UpdateAdminProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateAdminPassword::class);
        Fortify::resetUserPasswordsUsing(ResetAdminPassword::class);

        Fortify::registerView(function () {
            abort(404, 'Not Found');
        });
        
        
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

    }
}
