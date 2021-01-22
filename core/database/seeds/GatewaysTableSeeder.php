<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GatewaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('gateways')->insert([
            [
                'code' => 101,
                'name' => 'Paypal',
                'alias' => 'Paypal',
                'description' => 'This is an paypal payment method.',
                'status' => 1,
                'image' => 'paypal.png',
                'parameter_list' => json_encode([
                    'apn_key' => [
                        'title' => 'APN KEY',
                        'global' => true,
                        'value' => '3287wqedhsaioj',
                    ],
                    'passcode' => [
                        'title' => 'Passcode',
                        'global' => true,
                        'value' => '123456789',
                    ],
                    'track_no' => [
                        'title' => 'Track No',
                        'global' => false,
                    ]
                ]),
                'supported_currencies' => json_encode([
                    'USD' => '$',
                    'EURO' => '€',
                ]),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'code' => 102,
                'name' => 'Perfect Money',
                'alias' => 'Perfect Money',
                'description' => 'This is an paypal payment method.',
                'status' => 1,
                'image' => 'paypal.png',
                'parameter_list' => json_encode([
                    'apn_key' => [
                        'title' => 'APN KEY',
                        'global' => true,
                    ],
                    'passcode' => [
                        'title' => 'Passcode',
                        'global' => true,
                    ],
                    'track_no' => [
                        'title' => 'Track No',
                        'global' => false,
                    ]
                ]),
                'supported_currencies' => json_encode([
                    'USD' => '$',
                    'EURO' => '€',
                ]),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]
        ]);
    }
}
