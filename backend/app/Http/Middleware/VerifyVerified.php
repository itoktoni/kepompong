<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyVerified
{
    protected array $except = [
        'stories.generate',
        'stories.preview',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = optional($request->route())->getName();

        if (in_array($routeName, $this->except, true)) {
            return $next($request);
        }

        if(env('VERIFICATION_REGISTER_BACKEND'))
        {
            if ($request->user() && !$request->user()->verified_at) {
                return response()->json([
                    'message' => 'Akun belum terverifikasi. Silakan verifikasi terlebih dahulu.',
                    'needs_verification' => true,
                    'verification_gateway' => config('langkahkecil.verification.gateway', 'whatsapp'),
                ], 403);
            }
        }

        return $next($request);
    }
}
