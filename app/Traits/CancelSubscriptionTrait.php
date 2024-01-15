<?php

namespace App\Traits;

use App\Models\Subscription;
use Illuminate\Support\Facades\Http;

trait CancelSubscriptionTrait
{
    public function cancelStaleSubscription(Subscription $subscription)
    {
        if ($subscription->stripe_subscription) {
            $subscription->user->stripe()->subscriptions->cancel($subscription->stripe_subscription);
        }
    }
}
