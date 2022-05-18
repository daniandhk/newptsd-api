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

        //patient 1
        $user_patient = User::create([
            'email' => 'daniandhika01@gmail.com',
            'password' => Hash::make('patient'),
            'email_verified_at' => null
        ]);

        $patient = Patient::create([
            'user_id' => $user_patient->id,
            'first_name' => 'Iwan 1',
            'last_name' => 'Gunawan',
            'datebirth' => "07/07/1999",
            'city' => 'Sexcity',
            'province' => 'CA',
            'phone' => '0812345678'
        ]);

        $user_patient->assignRole('patient');

        //patient 2
        $user_patient = User::create([
            'email' => 'daniandhika02@gmail.com',
            'password' => Hash::make('patient'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $patient = Patient::create([
            'user_id' => $user_patient->id,
            'first_name' => 'Iwan 2',
            'last_name' => 'Gunawan',
            'datebirth' => "07/07/1999",
            'city' => 'Sexcity',
            'province' => 'CA',
            'phone' => '0812345678'
        ]);

        $user_patient->assignRole('patient');

        //patient 3
        $user_patient = User::create([
            'email' => 'daniandhika03@gmail.com',
            'password' => Hash::make('patient'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $patient = Patient::create([
            'user_id' => $user_patient->id,
            'first_name' => 'Iwan 3',
            'last_name' => 'Gunawan',
            'datebirth' => "07/07/1999",
            'city' => 'Sexcity',
            'province' => 'CA',
            'phone' => '0812345678'
        ]);

        $user_patient->assignRole('patient');

        //patient 4
        $user_patient = User::create([
            'email' => 'daniandhika04@gmail.com',
            'password' => Hash::make('patient'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $patient = Patient::create([
            'user_id' => $user_patient->id,
            'first_name' => 'Iwan 4',
            'last_name' => 'Gunawan',
            'datebirth' => "07/07/1999",
            'city' => 'Sexcity',
            'province' => 'CA',
            'phone' => '0812345678'
        ]);

        $user_patient->assignRole('patient');

        //psikolog 1
        $user_psychologist = User::create([
            'email' => 'psikolog1@psikolog.com',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Norman Kamaru 1',
            'speciality' => 'Psikolog Klinis',
            'datebirth' => "07/07/1999",
            'graduation_year' => '2020',
            'graduation_university' => 'Samudera Hindia',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'str_number' => '12345'
        ]);

        $user_psychologist->assignRole('psychologist');

        //psikolog 2
        $user_psychologist = User::create([
            'email' => 'psikolog2@psikolog.com',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Norman Kamaru 2',
            'speciality' => 'Psikolog Klinis',
            'datebirth' => "07/07/1999",
            'graduation_year' => '2020',
            'graduation_university' => 'Samudera Hindia',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'str_number' => '12345'
        ]);

        $user_psychologist->assignRole('psychologist');

        //psikolog 3
        $user_psychologist = User::create([
            'email' => 'psikolog3@psikolog.com',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Norman Kamaru 3',
            'speciality' => 'Psikolog Klinis',
            'datebirth' => "07/07/1999",
            'graduation_year' => '2020',
            'graduation_university' => 'Samudera Hindia',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'str_number' => '12345'
        ]);

        $user_psychologist->assignRole('psychologist');

        //psikolog 4
        $user_psychologist = User::create([
            'email' => 'psikolog4@psikolog.com',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Norman Kamaru 4',
            'speciality' => 'Psikolog Klinis',
            'datebirth' => "07/07/1999",
            'graduation_year' => '2020',
            'graduation_university' => 'Samudera Hindia',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'str_number' => '12345'
        ]);

        $user_psychologist->assignRole('psychologist');

        //psikolog 5
        $user_psychologist = User::create([
            'email' => 'psikolog5@psikolog.com',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Norman Kamaru 5',
            'speciality' => 'Psikolog Klinis',
            'datebirth' => "07/07/1999",
            'graduation_year' => '2020',
            'graduation_university' => 'Samudera Hindia',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'str_number' => '12345'
        ]);

        $user_psychologist->assignRole('psychologist');

        //psikolog 6
        $user_psychologist = User::create([
            'email' => 'psikolog6@psikolog.com',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Norman Kamaru 6',
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
