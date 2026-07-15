<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set id eksplisit HANYA saat create (event model dimatikan oleh
        // WithoutModelEvents di DatabaseSeeder, jadi hook UUID tidak jalan).
        $user = User::firstOrNew(['email' => 'superadmin@gmail.com']);

        if (!$user->exists) {
            $user->id = (string) Str::uuid();
            $user->no_wa = '081200000000'; // wajib & unik; ganti setelah login
            // Password hanya di-set saat create agar re-seed tidak mereset akun
            // yang sudah ada. Diambil dari env agar kredensial tidak di repo.
            // Set SEED_ADMIN_PASSWORD di .env server; default hanya untuk lokal.
            $user->password = Hash::make(env('SEED_ADMIN_PASSWORD', 'password'));
        }

        $user->name = 'Super Admin';
        $user->save();

        // Beri role Superadmin bila role-nya sudah ada (tidak error bila belum).
        $role = Role::where('name', 'Superadmin')->where('guard_name', 'web')->first();
        if ($role && !$user->hasRole($role)) {
            $user->assignRole($role);
        }
    }
}
