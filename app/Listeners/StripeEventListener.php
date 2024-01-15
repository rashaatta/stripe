<?php

namespace App\Listeners;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Traits\CancelSubscriptionTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Exceptions\InvalidArgumentException;
use Laravel\Cashier\Events\WebhookReceived;

class StripeEventListener
{
    use CancelSubscriptionTrait;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        $customerStripeId = $event->payload['data']['object']['customer'];
        $customer = User::where('stripe_id', $customerStripeId)->firstOrFail();


        Log::info($event->payload['type']);
        if ($event->payload['type'] === 'customer.subscription.created') {
            $stripeSubscriptionId = $event->payload['data']['object']['id'];
            $staleSubscription = $customer->userSubscription; // to be canceled after creating new one

            $stripePlanInterval = $event->payload['data']['object']['plan']['interval'];
            $paymentFrequency = match ($stripePlanInterval) {
                'month' => 'monthly',
                'year' => 'yearly',
                default => throw new InvalidArgumentException('Invalid interval'),
            };

            $stripePlanId = $event->payload['data']['object']['plan']['id'];
            $plan = Plan::where('stripe_monthly_plan', $stripePlanId)
                ->orWhere('stripe_yearly_plan', $stripePlanId)
                ->firstOrFail();
            Subscription::create([
                'user_id' => $customer->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'start_at' => now(),
                'payment_frequency' => $paymentFrequency,
                'stripe_subscription' => $stripeSubscriptionId
            ]);

            $this->cancelStaleSubscription($staleSubscription);
        }

        if ($event->payload['type'] === 'invoice.payment_succeeded') {
            $stripeSubscriptionId = $event->payload['data']['object']['subscription'];
            $paidAmount = $event->payload['data']['object']['amount_paid'];

            $subscription = Subscription::where('stripe_subscription', $stripeSubscriptionId)->firstOrFail();

            Payment::create([
                'user_id' => $customer->id,
                'currency' => '$',
                'payment_method' => 'Stripe',
                'subscription_id' => $subscription->id,
                'amount' => number_format($paidAmount / 100, 2),
                'status' => 'success'
            ]);
        }

        if ($event->payload['type'] === 'customer.subscription.deleted') {
            $stripeSubscriptionId = $event->payload['data']['object']['id'];
            $customer->userSubscription()
                ->where('stripe_subscription', $stripeSubscriptionId)
                ->first()
                ->update([
                    'status' => 'expired',
                    'end_at' => now()
                ]);
        }
    }
}
