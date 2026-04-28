<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $tenantDomain = config('wms.tenant_domain', env('TENANT_DOMAIN', 'gestionale.it'));

        // Skip tenant identification on auth routes that don't need it (registration)
        if ($host === $tenantDomain || $host === 'localhost' || $host === '127.0.0.1') {
            // In local dev, auto-load the first active company as the demo tenant
            $company = Company::where('is_active', true)->first();
            if ($company) {
                app()->instance('currentCompany', $company);
                view()->share('currentCompany', $company);
            }
            return $next($request);
        }

        // Extract subdomain
        $subdomain = str_replace('.' . $tenantDomain, '', $host);

        if (empty($subdomain) || $subdomain === $host) {
            abort(404, 'Azienda non trovata.');
        }

        $company = Company::where('slug', $subdomain)->where('is_active', true)->first();

        if (! $company) {
            abort(404, 'Azienda non trovata o non attiva.');
        }

        app()->instance('currentCompany', $company);

        // Share company with all views
        view()->share('currentCompany', $company);

        return $next($request);
    }
}
