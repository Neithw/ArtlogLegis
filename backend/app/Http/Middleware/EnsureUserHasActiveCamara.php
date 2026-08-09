<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasActiveCamara
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->isRoot()) {
            return $next($request);
        }

        if (! $user->camara || ! $user->camara->ativo) {
            abort(403, 'Sua Câmara está inativa.');
        }

        return $next($request);
    }
}
