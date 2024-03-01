<?php

namespace Database\Seeders\Test;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $promotorRoleId = Role::where('name', 'promotor')->first()->id;

        User::create([
            'name' => 'promotor1',
            'email' => 'promotor1@test.com',
            'password' => Hash::make('p12345678'),
            'role_id' => $promotorRoleId
        ]);

        User::create([
            'name' => 'promotor2',
            'email' => 'promotor2@test.com',
            'password' => Hash::make('p2345678'),
            'role_id' => $promotorRoleId
        ]);
    }
}