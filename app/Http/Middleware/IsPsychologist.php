<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsPsychologist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() &&  $request->user()->isPsychologist()) {
             return $next($request);
        }

        if (! $request->expectsJson()) {
            return route('/');
        }
        return response()->json(["error"=>401, "message"=>"authenticate failed"]);
    }
}
