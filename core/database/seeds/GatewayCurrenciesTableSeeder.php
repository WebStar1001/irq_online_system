<?php

use Illuminate\Database\Seeder;

class GatewayCurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('gateway_currencies')->insert([
            [
                'name' => 'Paypal USD',
                'currency' => 'USD',
                'method_code' => 101,
                'symbol' => '$',
                'min_amount' => 0,
                'max_amount' => 100,
                'rate' => 0.82,
                'image' => 'paypal_usd.png',
                'parameter' => json_encode([
                    'apn_key' => '23uqwe9sd',
                    'passcode' => '1279',
                    'track_no' => 'paypal_usd_2qw8_92qwsjcij'
                ]),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'Paypal BTC',
                'currency' => 'BTC',
                'method_code' => 101,
                'symbol' => 'BTC',
                'max_amount' => 100,
                'min_amount' => 0,
                'rate' => 0.82,
                'image' => 'paypal_btc.png',
                'parameter' => json_encode([
                    'apn_key' => '23uqwe9sd',
                    'passcode' => '1279',
                    'track_no' => 'paypal_btc_2qw8_92qwsjcij'
                ]),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],

        ]);
    }
}
