<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && app()->bound('currentCompany')) {
            $user = auth()->user();
            $company = app('currentCompany');

            if ($user->company_id !== $company->id) {
                auth()->logout();
                return redirect()->route('login')->withErrors(['email' => 'Account non valido per questa azienda.']);
            }

            // Update last login
            if ($user->last_login_at?->diffInMinutes(now()) > 30 || ! $user->last_login_at) {
                $user->updateQuietly(['last_login_at' => now()]);
            }
        }

        return $next($request);
    }
}
