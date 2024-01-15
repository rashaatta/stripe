@extends('layouts.app')
@section('content')
    <div class="navbar-sidebar-aside-content content-space-t-3 content-space-b-2 px-lg-5 px-xl-10">
        <div class="container">
            <div class="row">
                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header d-sm-flex justify-content-sm-between align-items-sm-center border-bottom">
                        <h5 class="card-header-title">{{ trans('global.overview') }}</h5>
                        <span
                            class="badge {{ $subscription->status == 'active' ? 'bg-success' : 'bg-danger' }}  ">{{ App\Models\Subscription::STATUS_SELECT[$subscription->status] }}</span>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md mb-4 mb-md-0">
                                <div class="mb-4">
                                    <span
                                        class="card-subtitle">{{ trans('global.your_plan') }}{{ $subscription->plan->isPaid ? ($subscription->isMonthly ? trans('global.billed_monthly') : trans('global.billed_yearly')) : '' }}:</span>
                                    <h5>{{ $subscription->plan->title }} - {{ $startAt }}</h5>
                                    @if ($subscription->isCanceled)
                                        <span class="card-subtitle text-danger"><strong>{{ trans('global.ends_at') }}
                                                {{ $endAt }}</strong></span>
                                    @endif
                                </div>

                                @if ($subscription->plan->isPaid)
                                    <div>
                                        <span class="card-subtitle">{{ trans('global.total_per') }}
                                            {{ $subscription->isMonthly ? 'month' : 'year' }}</span>
                                        <h3 class="text-primary">
                                            ${{ $subscription->isMonthly ? $subscription->plan->price_monthly : $subscription->plan->price_yearly }}
                                            USD</h3>
                                    </div>
                                @endif
                            </div>
                            <!-- End Col -->

                            <div class="col-md-auto">
                                <div class="d-grid d-md-flex gap-3">
                                    @if ($subscription->plan->isPaid && $subscription->isActive)
                                        <a id="btn-cancel-subscription" class="btn btn-white btn-sm"
                                           href="#">{{ trans('global.cancel_subscription') }}</a>
                                    @endif
                                    <a id="btn-upgrade"
                                       class="btn btn-primary btn-sm btn-transition">{{ trans('global.update_plan') }}</a>
                                </div>
                            </div>
                            <!-- End Col -->
                        </div>
                        <!-- End Row -->
                    </div>
                </div>
                <!-- End Card -->

                <!-- Pricing -->
                <div id="upgrade" class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header d-sm-flex justify-content-sm-between align-items-sm-center border-bottom">
                        <div class="card-header-title">
                            <div>
                                <h5>{{ trans('global.upgrade') }}</h5>
                            </div>
                            <div>{{ trans('global.upgrade_message') }}</div>
                        </div>
                    </div>

                    <div class="card-body mt-8">
                        <div class="d-flex justify-content-center mb-5">
                            <div class="form-check form-switch form-switch-between">
                                <label class="form-check-label">{{ trans('global.monthly') }}</label>
                                <input class="js-toggle-switch form-check-input" id="paymentFrequencySwitch"
                                       type="checkbox"
                                       data-hs-toggle-switch-options='{"targetSelector": "#pricingCount"}'>
                                <label class="form-check-label form-switch-promotion">
                                    {{ trans('global.yearly') }}
                                    <span class="form-switch-promotion-container">
                                            <span class="form-switch-promotion-body">
                                                <svg class="form-switch-promotion-arrow"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     x="0px" y="0px" viewBox="0 0 99.3 57" width="48">
                                                    <path fill="none" stroke="#bdc5d1" stroke-width="4"
                                                          stroke-linecap="round" stroke-miterlimit="10"
                                                          d="M2,39.5l7.7,14.8c0.4,0.7,1.3,0.9,2,0.4L27.9,42"></path>
                                                    <path fill="none" stroke="#bdc5d1" stroke-width="4"
                                                          stroke-linecap="round" stroke-miterlimit="10"
                                                          d="M11,54.3c0,0,10.3-65.2,86.3-50"></path>
                                                </svg>
                                            </span>
                                        </span>
                                </label>
                            </div>
                        </div>

                        <div class="row mb-7">
                            @foreach ($plans as $plan)
                                <div class="col-md mb-3 mb-md-0 d-flex align-items-stretch">
                                    <div class="position-relative">
                                        <!-- Card -->
                                        <div class="card h-100 zi-1">
                                            <div class="card-header text-center">
                                                <div class="mb-2">
                                                    <span
                                                        class="fs-5 align-top text-dark fw-semibold">$</span>
                                                    <span id="pricingCount" class="fs-1 text-dark fw-semibold"
                                                          data-hs-toggle-switch-item-options='{
                         "min": {{ number_format($plan->price_monthly, 0, '.', '') }},
                         "max": {{ number_format($plan->price_yearly / 12, 0, '.', '') }}
                       }'>{{ number_format($plan->price_monthly, 0, '.', '') }}</span>
                                                    <span>/mo</span>
                                                </div>

                                                <h3 class="card-title">{{ $plan->title }}</h3>
                                                <p class="card-text">{{ $plan->description }}</p>
                                            </div>

                                            <div class="card-body d-flex justify-content-center py-0">
                                                <!-- List Checked -->
                                                <ul class="list-checked list-checked-soft-bg-primary">

                                                    <li class="list-checked-item">{{ trans('global.access_to_tools') }}
                                                    </li>
                                                    <li class="list-checked-item">{{ trans('global.genie_editor_tool') }}
                                                    </li>
                                                    <li class="list-checked-item">{{ trans('global.product_support') }}
                                                    </li>
                                                </ul>
                                                <!-- End List Checked -->
                                            </div>

                                            <div class="card-footer text-center">
                                                <div class="d-grid mb-2">
                                                    <button type="button" href="javascript:;" data-bs-toggle="modal"
                                                            data-bs-target="#subscribeModal"
                                                            data-plan-id='{{ $plan->id }}'
                                                            class="btn btn-primary">{{ trans('global.upgrade_now') }}
                                                    </button>
                                                </div>
                                                <p class="card-text text-muted small">{{ trans('global.cancel_anytime') }}
                                                </p>
                                            </div>
                                        </div>
                                        <!-- End Card -->
                                    </div>
                                </div>
                                <!-- End Col -->
                            @endforeach
                        </div>
                        <!-- End Row -->
                    </div>
                </div>
                <!-- End Pricing -->
            </div>
        </div>
    </div>

    <!-- Choose Payment Method Modal -->
    <div class="modal fade" id="subscribeModal" tabindex="-1" aria-labelledby="subscribeModalLabel" role="dialog"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <h4 class="modal-title" id="subscribeModalLabel">{{ trans('global.choose_payment_method') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="card col col-5 bg-primary p-3 m-3 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                 fill="currentColor" class="bi bi-credit-card" viewBox="0 0 16 16">
                                <path
                                    d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z"/>
                                <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z"/>
                            </svg>

                            <h5 class="mt-3 text-white">{{ trans('global.payment_method_card') }}</h5>
                            <a id="checkout-stripe" class="stretched-link btn"></a>
                        </div>
                    </div>
                </div>
                <!-- End Body -->

                <div class="modal-footer justify-content-between">
                    <h5 class="text-info">{{ trans('global.update_plan_notice') }}</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- End Choose Payment Method Modal -->

    <!-- Activating plans Modal -->
    <div class="modal fade" id="activationModal" tabindex="-1" aria-labelledby="subscribeModalLabel"
         data-bs-backdrop="static" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="modal-body">
                    <!-- Heading -->
                    <div class="text-center mb-7">
                        <h3 class="modal-title">{{ trans('global.thanks_subscription_title') }}</h3>
                        <p>{{ trans('global.thanks_subscription_subtitle') }}</p>

                        <div class="mb-2">
                            <img class="avatar avatar-xxl avatar-4x3 mt-7 mb-5"
                                 src="{{ asset('svg/illustrations/account-creation.svg') }}" alt="SVG">
                        </div>

                        <p>{{ trans('global.activating_subscription') }}</p>
                    </div>
                    <!-- End Heading -->
                </div>
                <!-- End Body -->
            </div>
        </div>
    </div>
    <!-- End Activating plan Modal -->

    <!-- Cancel Subscription Modal -->
    <div id="cancelSubscriptionModal" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="cancelSubscriptionModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelSubscriptionModalTitle">
                        {{ trans('global.cancelling_subscription_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="cancelSubscriptionModalSubTitle">{{ trans('global.cancelling_subscription_subtitle') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white"
                            data-bs-dismiss="modal">{{ trans('global.close') }}</button>
                    <a id="btn-confirm-cancel-subscription" type="button" class="btn btn-primary">
                        {{ trans('global.cancelling_subscription_confirm') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- End Cancel Subscription Modal -->
@endsection

@section('scripts')
    <script>

        // ACTIVATE STRIPE SUBSCRIPTION
        // =======================================================
        function activateStripeSubscription(stripeSessionId) {
            return fetch('{{ route('subscription.stripe.activate') }}', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    "session_id": stripeSessionId,
                })
            })
                .then(response => response.json())
                .then(data => {
                    return data
                })
                .catch(error => console.error("Error:", error))
        }

        // CANCEL PAYPAL SUBSCRIPTION
        // =======================================================
        function cancelSubscription() {
            return fetch('{{ route('subscription.cancel') }}', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    "subscription_id": {{ $subscription->id }}
                })
            })
                .then(response => response.json())
                .then(data => {
                    return data
                })
                .catch(error => console.error("Error:", error))
        }

        function checkOnStripeSubscription() {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);

            if (urlParams.has("success") && urlParams.has("session_id")) {
                const success = urlParams.get("success");
                const sessionId = urlParams.get("session_id");

                if (success) {
                    $('#activationModal').modal('show');

                    activateStripeSubscription(sessionId).then(data => {
                        window.location.href = '{{ route('home') }}'
                    });
                }
            }
        }

        $(function () {
            checkOnStripeSubscription()

            document.querySelector("#btn-upgrade").addEventListener("click", function () {
                document.querySelector("#upgrade").scrollIntoView({
                    behavior: "smooth"
                });
            });

            $("#btn-cancel-subscription").click(function () {
                $('#cancelSubscriptionModal').modal('show')
            });

            $("#btn-confirm-cancel-subscription").click(function () {
                this.innerHTML = "{{ trans('global.cancelling') }}";
                this.classList.add("disabled");

                cancelSubscription().then(data => {
                    this.style.display = "none";

                    document.querySelector("#cancelSubscriptionModalTitle").innerHTML =
                        "{{ trans('global.subscription_canceled_title') }}";
                    document.querySelector("#cancelSubscriptionModalSubTitle").innerHTML =
                        "{{ trans('global.subscription_canceled_subtitle') }}";

                    $('#cancelSubscriptionModal').on('hide.bs.modal', function (event) {
                        window.location.href = '{{ route('subscription') }}'
                    });
                });
            });

            $('#subscribeModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const planId = button.data('plan-id');
                const paymentFrequency = document.getElementById("paymentFrequencySwitch").checked ?
                    'yearly' : 'monthly';

                const routeUrl =
                    "{{ route('subscription.stripe.checkout', ['planId' => ':planId', 'paymentFrequency' => ':paymentFrequency']) }}";

                    console.log(11);
                    console.log(routeUrl);
                window.location.href = routeUrl.replace(':planId', planId).replace(':paymentFrequency', paymentFrequency);

            });
        });
    </script>
@endsection
