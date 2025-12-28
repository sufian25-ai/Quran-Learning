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

        // Create Permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage users',

            // Course management
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'publish courses',

            // Batch management
            'view batches',
            'create batches',
            'edit batches',
            'delete batches',

            // Enrollment management
            'view enrollments',
            'create enrollments',
            'edit enrollments',
            'delete enrollments',
            'process refunds',

            // Class management
            'view classes',
            'create classes',
            'edit classes',
            'delete classes',
            'start classes',
            'mark attendance',

            // Resource management
            'view resources',
            'upload resources',
            'delete resources',

            // Analytics
            'view analytics',
            'view reports',
            'export data',

            // Settings
            'manage settings',
            'manage payments',
            'manage coupons',

            // Support
            'view tickets',
            'respond tickets',
            'manage tickets',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles with Permissions

        // Admin - Full access
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Teacher
        $teacherRole = Role::create(['name' => 'teacher']);
        $teacherRole->givePermissionTo([
            'view courses',
            'view batches',
            'edit batches',
            'view classes',
            'create classes',
            'edit classes',
            'start classes',
            'mark attendance',
            'view resources',
            'upload resources',
            'view enrollments',
        ]);

        // Student
        $studentRole = Role::create(['name' => 'student']);
        $studentRole->givePermissionTo([
            'view courses',
            'view classes',
            'view resources',
        ]);

        // Support Staff
        $supportRole = Role::create(['name' => 'support']);
        $supportRole->givePermissionTo([
            'view users',
            'view courses',
            'view enrollments',
            'view tickets',
            'respond tickets',
            'manage tickets',
        ]);
    }
}
