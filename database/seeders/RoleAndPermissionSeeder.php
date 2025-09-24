<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage user roles',

            // Crop Management
            'view crops',
            'create crops',
            'edit crops',
            'delete crops',
            'update crop stages',
            'harvest crops',
            'view crop recipes',
            'create crop recipes',
            'edit crop recipes',
            'delete crop recipes',

            // Inventory Management
            'view inventory',
            'create inventory items',
            'edit inventory items',
            'delete inventory items',
            'inventory transactions',
            'inventory counts',
            'view seed lots',
            'manage seed lots',

            // Customer Management
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'customer communication',

            // Order Management
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'approve orders',
            'fulfill orders',
            'generate invoices',
            'process payments',

            // Delivery & Packaging
            'view deliveries',
            'create deliveries',
            'edit deliveries',
            'schedule deliveries',
            'packaging operations',

            // Production Planning
            'view production plans',
            'create production plans',
            'edit production plans',
            'delete production plans',

            // Financial Reports
            'view financial reports',
            'view sales reports',
            'view production reports',
            'view cost reports',
            'export reports',

            // System Administration
            'system configuration',
            'view activity logs',
            'export data',
            'import data',

            // Time Tracking
            'clock in/out',
            'view timesheets',
            'approve timesheets',
            'edit timesheets',

            // Task Management
            'view tasks',
            'create tasks',
            'edit tasks',
            'assign tasks',
            'complete tasks',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $this->createAdministratorRole();
        $this->createFarmManagerRole();
        $this->createGrowerRole();
        $this->createSalesStaffRole();
        $this->createWarehouseStaffRole();
    }

    private function createAdministratorRole()
    {
        $role = Role::create(['name' => 'Administrator']);

        // Administrators have all permissions
        $role->givePermissionTo(Permission::all());
    }

    private function createFarmManagerRole()
    {
        $role = Role::create(['name' => 'Farm Manager']);

        $permissions = [
            // Crop and Recipe Management
            'view crops', 'create crops', 'edit crops', 'delete crops',
            'view crop recipes', 'create crop recipes', 'edit crop recipes', 'delete crop recipes',

            // Production Planning
            'view production plans', 'create production plans', 'edit production plans', 'delete production plans',

            // Inventory Oversight
            'view inventory', 'create inventory items', 'edit inventory items',
            'inventory transactions', 'inventory counts', 'view seed lots', 'manage seed lots',

            // Order Approval
            'view orders', 'approve orders',

            // Reports
            'view financial reports', 'view sales reports', 'view production reports', 'view cost reports', 'export reports',

            // Task Management
            'view tasks', 'create tasks', 'edit tasks', 'assign tasks',

            // Time Management
            'view timesheets', 'approve timesheets', 'edit timesheets',
        ];

        $role->givePermissionTo($permissions);
    }

    private function createGrowerRole()
    {
        $role = Role::create(['name' => 'Grower/Production Staff']);

        $permissions = [
            // Crop Operations
            'view crops', 'update crop stages', 'harvest crops',
            'view crop recipes',

            // Basic Task Management
            'view tasks', 'complete tasks',

            // Time Tracking
            'clock in/out', 'view timesheets',

            // Basic Inventory
            'view inventory',
        ];

        $role->givePermissionTo($permissions);
    }

    private function createSalesStaffRole()
    {
        $role = Role::create(['name' => 'Sales Staff']);

        $permissions = [
            // Customer Management
            'view customers', 'create customers', 'edit customers', 'customer communication',

            // Order Management
            'view orders', 'create orders', 'edit orders',
            'generate invoices', 'process payments',

            // Delivery Scheduling
            'view deliveries', 'create deliveries', 'edit deliveries', 'schedule deliveries',

            // Basic Reports
            'view sales reports',

            // Time Tracking
            'clock in/out', 'view timesheets',
        ];

        $role->givePermissionTo($permissions);
    }

    private function createWarehouseStaffRole()
    {
        $role = Role::create(['name' => 'Warehouse Staff']);

        $permissions = [
            // Inventory Management
            'view inventory', 'inventory transactions', 'inventory counts',
            'view seed lots',

            // Packaging and Fulfillment
            'packaging operations', 'fulfill orders',

            // Basic Order Viewing
            'view orders',

            // Task Management
            'view tasks', 'complete tasks',

            // Time Tracking
            'clock in/out', 'view timesheets',
        ];

        $role->givePermissionTo($permissions);
    }
}
