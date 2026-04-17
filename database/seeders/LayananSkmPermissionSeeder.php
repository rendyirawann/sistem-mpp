<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LayananSkmPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'layanan_skm.list',
            'layanan_skm.show',
            'layanan_skm.create',
            'layanan_skm.edit',
            'layanan_skm.delete',
            'layanan_skm.massdelete',
            'skm.list',
            'skm.show',
            'skm.edit',
            'skm.sync',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superadmin = Role::where('name', 'Superadmin')->first();
        if ($superadmin) {
            $superadmin->givePermissionTo($permissions);
        }
    }
}
