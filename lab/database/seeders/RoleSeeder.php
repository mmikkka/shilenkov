<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'code' => 'admin'],
            ['name' => 'User', 'code' => 'user'],
            ['name' => 'Guest', 'code' => 'guest'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['code' => $role['code']], $role);
        }
        $entities = ['user', 'role', 'permission'];
        $actions = [
            'get-list',
            'read',
            'create',
            'update',
            'delete',
            'restore'
        ];

        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                $code = "{$action}-{$entity}";
                $name = ucfirst($action) . ' ' . ucfirst($entity);

                Permission::firstOrCreate(
                    ['code' => "get-story-{$entity}"],
                    ['name' => "Get {$entity} change history"]
                );
            }
        }

        $adminRole = Role::where('code', 'admin')->first();
        $permissions = Permission::all();

        $adminRole->permissions()->sync($permissions->pluck('id'));

        $adminRole = Role::where('code', 'admin')->first();
        $userRole = Role::where('code', 'user')->first();
        $guestRole = Role::where('code', 'guest')->first();

        $userPermissions = Permission::whereIn('code', [
            'get-list-user',
            'read-user',
            'update-user'
        ])->pluck('id');
        $userRole->permissions()->sync($userPermissions);

        // c. Гость может:
        $guestPermissions = Permission::where('code', 'get-list-user')->pluck('id');
        $guestRole->permissions()->sync($guestPermissions);
    }
}
