<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AccountAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@hotelconsuelo.com',
                'password' => Hash::make('superadmin123'),
                'phone' => '09120000000',
                'address' => 'HQ',
                'gender' => 'Male',
                'birthdate' => '1985-01-01',
                'role' => 'Super Admin',
                'is_active' => true,
            ],
            [
                'name' => 'System Administrator',
                'email' => 'adminhotelconsuelo01@hotelconsuelo.com',
                'password' => Hash::make('admin123'),
                'phone' => '09123456781',
                'address' => 'Main Office',
                'gender' => 'Male',
                'birthdate' => '1990-01-01',
                'role' => 'Administrator',
                'is_active' => true,
            ],
            [
                'name' => 'Restaurant Manager',
                'email' => 'restaurant.manager01@hotelconsuelo.com',
                'password' => Hash::make('resto123'),
                'phone' => '09123456782',
                'address' => 'Consuelo Restaurant, Main Branch',
                'gender' => 'Female',
                'birthdate' => '1992-05-10',
                'role' => 'Restaurant Manager',
                'is_active' => true,
            ],
            [
                'name' => 'Hotel Manager',
                'email' => 'hotel.manager01@hotelconsuelo.com',
                'password' => Hash::make('hotel123'),
                'phone' => '09123456783',
                'address' => 'Consuelo Hotel, Main Branch',
                'gender' => 'Male',
                'birthdate' => '1988-07-22',
                'role' => 'Hotel Manager',
                'is_active' => true,
            ],
            [
                'name' => 'Restaurant Cashier',
                'email' => 'restaurant.cashier01@hotelconsuelo.com',
                'password' => Hash::make('cashier123'),
                'phone' => '09123456784',
                'address' => 'Consuelo Restaurant, Main Branch',
                'gender' => 'Female',
                'birthdate' => '1995-02-14',
                'role' => 'Restaurant Cashier',
                'is_active' => true,
            ],
            [
                'name' => 'Hotel Frontdesk',
                'email' => 'hotel.frontdesk01@hotelconsuelo.com',
                'password' => Hash::make('frontdesk123'),
                'phone' => '09123456785',
                'address' => 'Consuelo Hotel, Main Branch',
                'gender' => 'Female',
                'birthdate' => '1996-09-09',
                'role' => 'Hotel Frontdesk',
                'is_active' => true,
            ],
        ];

        foreach ($admins as $admin) {
            DB::table('account_admins')->insert([
                ...$admin,
                'last_login_at' => now(),
                'last_login_ip' => '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
