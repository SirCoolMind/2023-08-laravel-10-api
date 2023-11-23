<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $roleAdmin = Role::firstOrCreate(['name' => 'admin'],['name' => 'admin']);
        $roleGuest = Role::firstOrCreate(['name' => 'guest'],['name' => 'guest']);

        // Admin User CRUD
        $userPermissions = [
            'view user',
            'add user',
            'edit user',
            'delete user',
        ];

        foreach ($userPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName],['name' => $permissionName]);
        }
        $permission = Permission::where('name',$userPermissions[0])->first();
        $roleGuest->givePermissionTo($permission);
        $permission = Permission::whereIn('name',$userPermissions)->get();
        $roleAdmin->syncPermissions($permission);



    }
}
