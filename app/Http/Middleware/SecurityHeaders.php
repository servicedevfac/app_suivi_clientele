<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Headers de sécurité HTTP pour protéger contre les attaques courantes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Protection contre le clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Empêcher le MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Protection XSS côté navigateur
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Forcer HTTPS (HSTS) — 1 an avec sous-domaines
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // Content Security Policy — restreindre les sources de contenu
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data:; font-src 'self' data: https://fonts.gstatic.com; connect-src 'self'");

        // Politique de référent
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Contrôle des permissions du navigateur
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Retirer les headers qui divulguent la version du serveur
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('server');

        return $response;
    }
}
