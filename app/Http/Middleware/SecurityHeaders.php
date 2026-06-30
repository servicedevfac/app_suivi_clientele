<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Headers de sécurité HTTP pour protéger contre les attaques courantes.
     *
     * Utilise des nonces CSP pour autoriser les scripts/styles inline
     * au lieu de 'unsafe-inline', conformément aux bonnes pratiques OWASP.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Générer un nonce CSP unique pour cette requête.
        // Vite::useCspNonce() génère le nonce ET l'injecte automatiquement
        // dans les tags produits par la directive @vite() de Blade.
        $nonce = Vite::useCspNonce();

        $response = $next($request);

        // Protection contre le clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // Empêcher le MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Protection XSS côté navigateur
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Forcer HTTPS (HSTS) — 1 an avec sous-domaines et preload
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // Content Security Policy — restreindre les sources de contenu
        // Utilisation de nonces au lieu de 'unsafe-inline' pour les scripts et styles
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
            "img-src 'self' data:",
            "font-src 'self' data: https://fonts.gstatic.com",
            "connect-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "frame-src 'none'",
            "frame-ancestors 'none'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

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
