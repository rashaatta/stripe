<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Traits\CancelSubscriptionTrait;
use Carbon\Carbon;

;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Exceptions\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends Controller
{
    use CancelSubscriptionTrait;

    public function show()
    {
        $user = auth()->user();
        $subscription = $user->userSubscription;

        $startAtDate = Carbon::parse($subscription->start_at);
        $startAt = $startAtDate->format('F Y');
        $endAtDate = Carbon::parse($subscription->end_at);
        $endAt = $endAtDate->format('d F Y');
        $plans = Plan::paid()->get();
        return view('subscription', compact('subscription', 'startAt', 'endAt','plans'));
    }

    public function checkout(Request $request, $planId, $paymentFrequency)
    {
        $plan = Plan::find($planId);
        $stripePlanId = $paymentFrequency == 'monthly' ? $plan->stripe_monthly_plan : $plan->stripe_yearly_plan;

        return $request->user()
            ->newSubscription($plan->title, $stripePlanId)
            ->checkout([
                'success_url' => route('subscription') . '?success=true&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription') . '?success=false',
            ]);
    }

    public function activateStripeSubscription(Request $request): Response
    {
        $stripeSessionId = $request->get('session_id');
        $checkoutSession = $request->user()->stripe()->checkout->sessions->retrieve($stripeSessionId);

        if ($checkoutSession->payment_status != "paid" ?? true) {
            return \response(['message' => 'Subscription is not approved yet'], 400);
        }

        sleep(2);
        return \response(['message' => 'Subscription activated'], Response::HTTP_OK);
    }

    public function cancelSubscription(Request $request): Response
    {
        $user = auth()->user();
        $subscription = Subscription::find($request->get('subscription_id'));
        $paymentMethod = $subscription->payments()->latest()->first()->payment_method->title;

        switch ($paymentMethod) {
            case 'Stripe':
                $stripeSubscription = $user->subscription($subscription->plan->title)->cancel();
                if (!$stripeSubscription) {
                    return \response(['message' => 'Failed to cancel Stripe subscription'], Response::HTTP_BAD_REQUEST);
                }
                break;
            case 'PayPal':
                $response = Http::paypal()
                    ->post('/v1/billing/subscriptions/' . $subscription->pp_subscription . '/cancel', [
                        "reason" => "Not satisfied with the service"
                    ]);

                if ($response->failed()) {
                    return \response(['message' => 'Failed to cancel PayPal subscription'], $response->status);
                }
                break;
            default:
                throw new InvalidArgumentException('Payment method not supported');
        }

        $subscriptionStartAt = Carbon::parse($subscription->start_at);
        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'end_at' => $subscription->isMonthly ? $subscriptionStartAt->addMonth() : $subscriptionStartAt->addYear(),
        ]);

        return \response(['message' => 'Subscription canceled'], Response::HTTP_OK);
    }
}
