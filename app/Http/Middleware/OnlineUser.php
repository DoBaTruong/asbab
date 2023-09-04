<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Cache;
use Auth;
use Carbon\Carbon;

class OnlineUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(1); 
            Cache::put('user-is-online-' . Auth::user()->id, true, $expiresAt);
            // last seen
            User::where('id', Auth::user()->id)->update(['online' => (new \DateTime())->format("Y-m-d H:i:s")]);
        }
        return $next($request);
    }
}
