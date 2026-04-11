<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Постоянный идентификатор гостя в cookie (для стабильного voter_key оценок).
 */
final class EnsureGuestIdCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->cookies->has('guest_id')) {
            return $response;
        }

        $id = Str::uuid()->toString();

        // cookie($name, $value, $minutes)
        return $response->withCookie(cookie('guest_id', $id, 525600));
    }
}
