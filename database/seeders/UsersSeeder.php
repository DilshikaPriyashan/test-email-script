<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = new User;
        $admin->name = 'Admin User';
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('admin');
        $admin->email_verified_at = now();
        $admin->save();

        $client = new User;
        $client->name = 'Test Client';
        $client->email = 'client@example.com';
        $client->password = Hash::make('client');
        $admin->email_verified_at = now();
        $client->save();

        $admin->assignRole(Roles::Admin->value);
        $client->assignRole(Roles::Client->value);

    }
}
