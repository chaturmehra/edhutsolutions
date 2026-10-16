<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') || $request->is('admin', 'admin/*', 'up')) {
            return $next($request);
        }

        $path = '/'.ltrim($request->path(), '/');
        $redirect = Redirect::query()
            ->where('from_path', $path)
            ->where('is_active', true)
            ->first();

        if ($redirect && $redirect->to_path !== $path && in_array((int) $redirect->status_code, [301, 302], true)) {
            return redirect($redirect->to_path, $redirect->status_code);
        }

        return $next($request);
    }
}
