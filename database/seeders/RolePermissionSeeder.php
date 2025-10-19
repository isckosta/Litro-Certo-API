<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Station permissions
            'view-stations',
            'create-stations',
            'update-stations',
            'delete-stations',
            'verify-stations',
            
            // Price permissions
            'view-prices',
            'report-prices',
            'approve-prices',
            'reject-prices',
            
            // Review permissions
            'view-reviews',
            'create-reviews',
            'update-reviews',
            'delete-reviews',
            'moderate-reviews',
            
            // User permissions
            'view-users',
            'create-users',
            'update-users',
            'delete-users',
            'manage-roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $moderatorRole = Role::firstOrCreate(['name' => 'moderator']);
        $moderatorRole->syncPermissions([
            'view-stations',
            'update-stations',
            'verify-stations',
            'view-prices',
            'approve-prices',
            'reject-prices',
            'view-reviews',
            'moderate-reviews',
        ]);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'view-stations',
            'view-prices',
            'report-prices',
            'view-reviews',
            'create-reviews',
            'update-reviews',
        ]);
    }
}
