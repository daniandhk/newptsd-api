<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        Role::create([
            'name' => 'patient',
            'guard_name' => 'web'
        ]);

        Role::create([
            'name' => 'psychologist',
            'guard_name' => 'web'
        ]);
    }
}