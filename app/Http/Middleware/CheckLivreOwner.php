<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Livre;

class CheckLivreOwner
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $livreslug = $request->input('slug') ?? ($request->input('variables')['slug'] ?? null);

        if (!$livreslug) {
            abort(400, "Livre slug is required.");
        }

        $livre = Livre::find($livreslug);
        if (!$livre) {
            abort(404, "Livre not found.");
        }

        if ($livre->user_slug !== $user->slug) {
            abort(403, "Unauthorized: You are not the owner of this livre.");
        }

        return $next($request);
    }
}
