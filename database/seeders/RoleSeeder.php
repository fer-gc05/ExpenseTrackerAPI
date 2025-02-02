<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin', 'description' => 'Administrador del sistema, con capacidad de gestionar usuarios y categorías']);
        Role::create(['name' => 'user', 'description' => 'Usuario estándar']);
    }
}
