<?php

namespace Database\Seeders\Test;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::create(['name' => 'administrador']);
        Role::create(['name' => 'promotor']);
    }
}