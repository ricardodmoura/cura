<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Anti-clickjacking — bloqueia embed em iframes externos.
        $response->headers->set('X-Frame-Options', 'DENY');

        // Sniffing de MIME desligado.
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referer só dentro do nosso domínio em downgrade HTTPS→HTTP.
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Sem features que não usamos (evita drift).
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // HSTS só em produção (em dev http://localhost partia).
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // CSP base — permissivo o suficiente para Tailwind CDN + scripts inline existentes.
        // TODO: tornar mais estrito quando se mudar para Vite-bundled assets em produção.
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com",
            "style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com",
            "img-src 'self' data: blob:",
            "font-src 'self' data:",
            "connect-src 'self'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            "base-uri 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
