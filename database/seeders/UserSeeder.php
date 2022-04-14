<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Psychologist;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'password' => Hash::make('admin'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $admin->assignRole('admin');

        $user_patient = User::create([
            'email' => 'daniandhika01@gmail.com',
            'password' => Hash::make('patient'),
            'email_verified_at' => null
        ]);

        $patient = Patient::create([
            'user_id' => $user_patient->id,
            'first_name' => 'Iwan',
            'last_name' => 'Gunawan',
            'datebirth' => "07/07/1999",
            'city' => 'Sexcity',
            'province' => 'CA',
            'phone' => '0812345678'
        ]);

        $user_patient->assignRole('patient');

        $user_psychologist = User::create([
            'email' => 'psikolog@psikolog.com',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Norman Kamaru',
            'speciality' => 'Psikolog Klinis',
            'datebirth' => "07/07/1999",
            'graduation_year' => '2020',
            'graduation_university' => 'Samudera Hindia',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'str_number' => '12345'
        ]);

        $user_psychologist->assignRole('psychologist');
    }
}
