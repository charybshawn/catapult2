<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Administrator
        $admin = User::firstOrCreate(
            ['email' => 'admin@catapult.com'],
            [
            'name' => 'System Administrator',
            'email' => 'admin@catapult.com',
            'password' => Hash::make('password123'),
            'phone' => '+1-555-0100',
            'timezone' => 'America/New_York',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Administrator');

        // Create Shawn's Admin account
        $shawnAdmin = User::firstOrCreate(
            ['email' => 'charybshawn@gmail.com'],
            [
            'name' => 'Shawn',
            'email' => 'charybshawn@gmail.com',
            'password' => Hash::make('kngfqp57'),
            'phone' => '+1-555-0099',
            'timezone' => 'America/New_York',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $shawnAdmin->assignRole('Administrator');

        // Create Farm Manager
        $farmManager = User::firstOrCreate(
            ['email' => 'farm.manager@catapult.com'],
            [
            'name' => 'Sarah Johnson',
            'email' => 'farm.manager@catapult.com',
            'password' => Hash::make('password123'),
            'phone' => '+1-555-0101',
            'timezone' => 'America/New_York',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $farmManager->assignRole('Farm Manager');

        // Create Grower/Production Staff
        $grower1 = User::firstOrCreate(
            ['email' => 'grower1@catapult.com'],
            [
            'name' => 'Mike Rodriguez',
            'email' => 'grower1@catapult.com',
            'password' => Hash::make('password123'),
            'phone' => '+1-555-0102',
            'timezone' => 'America/New_York',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $grower1->assignRole('Grower/Production Staff');

        $grower2 = User::firstOrCreate(
            ['email' => 'grower2@catapult.com'],
            [
            'name' => 'Emily Chen',
            'email' => 'grower2@catapult.com',
            'password' => Hash::make('password123'),
            'phone' => '+1-555-0103',
            'timezone' => 'America/New_York',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $grower2->assignRole('Grower/Production Staff');

        // Create Sales Staff
        $sales1 = User::firstOrCreate(
            ['email' => 'sales1@catapult.com'],
            [
            'name' => 'David Thompson',
            'email' => 'sales1@catapult.com',
            'password' => Hash::make('password123'),
            'phone' => '+1-555-0104',
            'timezone' => 'America/New_York',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $sales1->assignRole('Sales Staff');

        $sales2 = User::firstOrCreate(
            ['email' => 'sales2@catapult.com'],
            [
            'name' => 'Jessica Williams',
            'email' => 'sales2@catapult.com',
            'password' => Hash::make('password123'),
            'phone' => '+1-555-0105',
            'timezone' => 'America/New_York',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $sales2->assignRole('Sales Staff');

        // Create Warehouse Staff
        $warehouse1 = User::firstOrCreate(
            ['email' => 'warehouse1@catapult.com'],
            [
            'name' => 'Robert Martinez',
            'email' => 'warehouse1@catapult.com',
            'password' => Hash::make('password123'),
            'phone' => '+1-555-0106',
            'timezone' => 'America/New_York',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $warehouse1->assignRole('Warehouse Staff');

        $warehouse2 = User::firstOrCreate(
            ['email' => 'warehouse2@catapult.com'],
            [
            'name' => 'Lisa Anderson',
            'email' => 'warehouse2@catapult.com',
            'password' => Hash::make('password123'),
            'phone' => '+1-555-0107',
            'timezone' => 'America/New_York',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $warehouse2->assignRole('Warehouse Staff');
    }
}
