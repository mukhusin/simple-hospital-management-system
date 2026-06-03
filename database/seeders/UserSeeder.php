<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'name'     => 'System Admin',
                'level'    => 0,
                'role_id'  => 1,
                'office_id'      => 1,
            ],
            [
                'username' => 'doctor',
                'name'     => 'Doctor Test',
                'level'    => 1,
                'role_id'  => 3,
                'office_id'      => 1,
            ],
            [
                'username' => 'receptionist',
                'name'     => 'Receptionist Test',
                'level'    => 0,
                'role_id'  => 1,
                'office_id'      => 6,
            ],
        ];

        $now = now();

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['username' => $user['username']],
                array_merge($user, [
                    'password'       => Hash::make('password'),
                    'status'         => 'active',
                    'hide'           => 0,
                    'remember_token' => '',
                    'updator_id'     => 0,
                    'creator_id'     => 0,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]),
            );
        }
    }
}
