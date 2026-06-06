<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use App\Http\Responses\LogoutResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            FortifyLoginRequest::class,
            \App\Http\Requests\LoginRequest::class
        );

        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::registerView(function () {
          return view('auth.register');
      });

      Fortify::loginView(function () {
          if (request()->is('admin/login')) {
              return view('admin.login');
          }

          return view('auth.login');
      });

      RateLimiter::for('login', function (Request $request) {
          $email = (string) $request->email;

          return Limit::perMinute(10)->by($email . $request->ip());
      });

      $this->app->instance(LoginResponse::class, new class implements LoginResponse {
        public function toResponse($request)
        {
            if ($request->is('admin/*')) {
                return redirect('admin/attendance/list');
            }

            return redirect ('/attendance');
        }
      });

      Fortify::createUsersUsing(CreateNewUser::class);
    }
}
