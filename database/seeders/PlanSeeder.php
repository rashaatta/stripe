<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                'id'            => 1,
                'title'         => 'Free',
                'description'   => 'Get started with basic features, no credit card required.',
                'price_monthly' => 0,
                'price_yearly'  => 0,
                'stripe_monthly_plan' => '',
                'stripe_yearly_plan'  => '',
                'type'          => 'free',
            ],
            [
                'id'            => 2,
                'title'         => 'Pro',
                'description'   => 'Unlock advanced features and priority support.',
                'price_monthly' => 10,
                'price_yearly'  => 108,
                'stripe_monthly_plan' => 'price_1N93m8KKQk4Vuks1WyvrJU48',
                'stripe_yearly_plan'  => 'price_1N93m8KKQk4Vuks1eSWYiRWJ',
                'type'          => 'paid',
            ],
            [
                'id'            => 3,
                'title'         => 'Business',
                'description'   => 'Access premium tools and team collaboration options.',
                'price_monthly' => 20,
                'price_yearly'  => 216,
                'stripe_monthly_plan' => 'price_1N93lPKKQk4Vuks1tnWBDwhY',
                'stripe_yearly_plan'  => 'price_1N93lPKKQk4Vuks154Z7xY9P',
                'type'          => 'paid',
            ],
            [
                'id'            => 4,
                'title'         => 'Enterprise',
                'description'   => 'Customizable solutions for large projects and dedicated support.',
                'price_monthly' => 50,
                'price_yearly'  => 540,
                'stripe_monthly_plan' => 'price_1N93kPKKQk4Vuks1uglPblFP',
                'stripe_yearly_plan'  => 'price_1N93kPKKQk4Vuks1udtyvdU2',
                'type'          => 'paid',
            ]
        ];

        foreach ($plans as $data) {
            Plan::create($data);
        }
    }
}
