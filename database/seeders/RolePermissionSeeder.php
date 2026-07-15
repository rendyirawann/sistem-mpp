<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seed role & permission (guard: web) sesuai produksi.
 * Idempoten: firstOrCreate + syncPermissions.
 * Superadmin = semua permission; Admin = antrian.list & antrian.show.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        // name => category
        $permissions = [
            'user.list' => 'User Management',
            'user.show' => 'User Management',
            'user.create' => 'User Management',
            'user.edit' => 'User Management',
            'user.delete' => 'User Management',
            'user.massdelete' => 'User Management',

            'role.list' => 'Role Management',
            'role.show' => 'Role Management',
            'role.create' => 'Role Management',
            'role.edit' => 'Role Management',
            'role.delete' => 'Role Management',
            'role.massdelete' => 'Role Management',

            'skpd.list' => 'Skpd Management',
            'skpd.show' => 'Skpd Management',
            'skpd.create' => 'Skpd Management',
            'skpd.edit' => 'Skpd Management',
            'skpd.delete' => 'Skpd Management',
            'skpd.massdelete' => 'Skpd Management',

            'antrian.list' => 'Antrian Management',
            'antrian.show' => 'Antrian Management',
            'antrian.create' => 'Antrian Management',
            'antrian.edit' => 'Antrian Management',
            'antrian.delete' => 'Antrian Management',
            'antrian.massdelete' => 'Antrian Management',
            'antrian.call' => 'Antrian Management',

            'loket.list' => 'Loket Management',
            'loket.show' => 'Loket Management',
            'loket.create' => 'Loket Management',
            'loket.edit' => 'Loket Management',
            'loket.delete' => 'Loket Management',
            'loket.massdelete' => 'Loket Management',

            'layanan_skm.list' => 'Layanan SKM',
            'layanan_skm.show' => 'Layanan SKM',
            'layanan_skm.create' => 'Layanan SKM',
            'layanan_skm.edit' => 'Layanan SKM',
            'layanan_skm.delete' => 'Layanan SKM',
            'layanan_skm.massdelete' => 'Layanan SKM',

            'skm.list' => 'Manajemen SKM',
            'skm.show' => 'Manajemen SKM',
            'skm.edit' => 'Manajemen SKM',
            'skm.sync' => 'Manajemen SKM',
        ];

        foreach ($permissions as $name => $category) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => $guard],
                ['category' => $category]
            );
        }

        // Superadmin: seluruh permission
        $superadmin = Role::firstOrCreate(['name' => 'Superadmin', 'guard_name' => $guard]);
        $superadmin->syncPermissions(array_keys($permissions));

        // Admin: hanya lihat antrian
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => $guard]);
        $admin->syncPermissions(['antrian.list', 'antrian.show']);

        // Bersihkan cache permission spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
