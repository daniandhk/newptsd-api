<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Psychologist;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'email' => 'admin@admin.com',
            'password' => 'admin',
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $admin->assignRole('admin');

        $user_patient = User::create([
            'email' => 'patient01@patient.com',
            'password' => 'patient',
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $patient = Patient::create([
            'user_id' => $user_patient->id,
            'first_name' => 'Iwan',
            'last_name' => 'Gunawan',
            'datebirth' => "07/07/1999",
            'city' => 'Sexcity',
            'province' => 'CA',
            'have_relation' => false,
        ]);

        $user_patient->assignRole('patient');

        $user_psychologist = User::create([
            'email' => 'psy@psy.com',
            'password' => 'psy 01',
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'first_name' => 'Norman',
            'last_name' => 'Kamaru',
            'degree' => '71',
            'datebirth' => "07/07/1999",
            'years_experience' => 3,
            'workplace' => 'Samudera Hindia',
        ]);

        $user_psychologist->assignRole('psychologist');
    }
}
