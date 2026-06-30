<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya catat request GET, bukan asset/admin/api
        if ($request->isMethod('GET')
            && ! $request->is('admin/*')
            && ! $request->is('api/*')
            && ! $request->is('build/*')
            && ! str_contains($request->path(), '.')) {

            PageView::create([
                'url'        => $request->fullUrl(),
                'path'       => '/' . $request->path(),
                'ip_hash'    => hash('sha256', $request->ip() . config('app.key')),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'referrer'   => $request->headers->get('referer'),
                'viewed_at'  => now(),
            ]);
        }

        return $response;
    }
}