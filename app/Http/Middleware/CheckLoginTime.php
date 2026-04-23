<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckLoginTime
{
    /**
     * Handle an incoming request.
     * 检查员工登录时间是否在允许范围内
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Only apply to staff (role_id = 4)
        if ($user && $user->role_id == 4) {

            // If no time restriction is set, allow login
            if (is_null($user->login_time_start) || is_null($user->login_time_end)) {
                return $next($request);
            }

            // If allow_outside_hours is enabled, skip the check
            if ($user->allow_outside_hours) {
                return $next($request);
            }

            $now   = Carbon::now();
            $start = Carbon::createFromTimeString($user->login_time_start);
            $end   = Carbon::createFromTimeString($user->login_time_end);

            if ($now->lt($start) || $now->gt($end)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $startFormatted = Carbon::createFromTimeString($user->login_time_start)->format('h:i A');
                $endFormatted   = Carbon::createFromTimeString($user->login_time_end)->format('h:i A');

                return redirect()->route('login')
                    ->with('login_time_error', "Access denied. You may only log in between {$startFormatted} and {$endFormatted}.");
            }
        }

        return $next($request);
    }
}