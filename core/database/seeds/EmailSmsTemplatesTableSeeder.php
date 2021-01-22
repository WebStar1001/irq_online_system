<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailSmsTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('email_sms_templates')->insert([
            'act'        => 'ACCOUNT_RECOVERY_CODE',
            'name'       => 'Account recovery code send',
            'subj'       => 'Account recovery code',
            'email_body'       => 'Your account recovery code is: {{code}}',
            'sms_body'       => 'Your account recovery code is: {{code}}',
            'shortcodes' => json_encode([
                'code'       => 'Recovery code',
            ]),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
    }
}
