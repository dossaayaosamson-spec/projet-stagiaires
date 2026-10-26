<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Content Security Policy
        $scriptSrc = "'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com https://cdn.fedapay.com https://cdnjs.cloudflare.com";
        $styleSrc = "'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com https://cdn.jsdelivr.net";
        $fontSrc = "'self' https://fonts.bunny.net https://fonts.gstatic.com https://cdn.jsdelivr.net";
        $connectSrc = "'self' https://www.google-analytics.com https://stats.g.doubleclick.net https://agaueditions-ia.saeicubetech.com";

        // Add Vite development support
        if (app()->isLocal()) {
            $viteHosts = 'http://localhost:5173 http://localhost:5174 http://127.0.0.1:5173 http://127.0.0.1:5174 http://localhost:8000 http://127.0.0.1:8000';
            $scriptSrc .= ' '.$viteHosts;
            $styleSrc .= ' '.$viteHosts;
            $fontSrc .= ' '.$viteHosts;
            $connectSrc .= ' ws://localhost:5173 ws://localhost:5174 ws://127.0.0.1:5173 ws://127.0.0.1:5174 ws://localhost:8000 ws://127.0.0.1:8000 wss://localhost:5173 wss://localhost:5174 '.$viteHosts;
        }

        $csp = "default-src 'self'; ".
               "script-src $scriptSrc; ".
               "style-src $styleSrc; ".
               "img-src 'self' data: https://www.googletagmanager.com https://images.unsplash.com; ".
               "font-src $fontSrc; ".
               "connect-src $connectSrc; ".
               "worker-src 'self' blob: https://cdnjs.cloudflare.com; ".
               "frame-src 'self' https://www.googletagmanager.com https://checkout.fedapay.com;";

        $response->headers->set('Content-Security-Policy', $csp);

        // Remove X-Powered-By header if PHP added it natively
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
