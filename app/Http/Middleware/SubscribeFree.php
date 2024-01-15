<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Subscription;

class SubscribeFree
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!$user->userSubscription) {
            $subscription = new Subscription([
                'plan_id' => 1,
                'start_date' => now(),
                'status' => 'active'
            ]);
            $user->userSubscription()->save($subscription);
        }

        return $next($request);
    }
}
