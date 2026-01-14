<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'], // key pencarian

            // data yang akan di-set
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('123123123'),
            ]
        );
    }
}
