<?php

namespace Database\Seeders;

use App\Models\ChatSchedule;
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
        //patient 1
        $user_patient = User::create([
            'email' => 'patient1@patient.com',
            'username' => 'patient1',
            'password' => Hash::make('patient'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $patient = Patient::create([
            'user_id' => $user_patient->id,
            'first_name' => 'Iwan 1',
            'last_name' => 'Gunawan',
            'datebirth' => "1999-04-07",
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'phone' => '0812345678',
            'is_dummy' => true,
        ]);

        $user_patient->assignRole('patient');

        //patient 2
        $user_patient = User::create([
            'email' => 'patient2@patient.com',
            'username' => 'patient2',
            'password' => Hash::make('patient'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $patient = Patient::create([
            'user_id' => $user_patient->id,
            'first_name' => 'Iwan 2',
            'last_name' => 'Gunawan',
            'datebirth' => "1999-04-07",
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'phone' => '0812345678',
            'is_dummy' => true,
        ]);

        $user_patient->assignRole('patient');

        //patient 3
        $user_patient = User::create([
            'email' => 'patient3@patient.com',
            'username' => 'patient3',
            'password' => Hash::make('patient'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $patient = Patient::create([
            'user_id' => $user_patient->id,
            'first_name' => 'Iwan 3',
            'last_name' => 'Gunawan',
            'datebirth' => "1999-04-07",
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'phone' => '0812345678',
            'is_dummy' => true,
        ]);

        $user_patient->assignRole('patient');

        //patient 4
        $user_patient = User::create([
            'email' => 'patient4@patient.com',
            'username' => 'patient4',
            'password' => Hash::make('patient'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $patient = Patient::create([
            'user_id' => $user_patient->id,
            'first_name' => 'Iwan 4',
            'last_name' => 'Gunawan',
            'datebirth' => "1999-04-07",
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'phone' => '0812345678',
            'is_dummy' => true,
        ]);

        $user_patient->assignRole('patient');

        //psikolog 1
        $user_psychologist = User::create([
            'email' => 'psikolog1@psikolog.com',
            'username' => 'psikolog1',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Norman Kamaru M.Psi, Psikolog',
            'speciality' => 'Psikolog Klinis',
            'datebirth' => "1999-04-07",
            'graduation_year' => '2020',
            'graduation_university' => 'Samudera Hindia',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'str_number' => '12345',
            'is_dummy' => true,
        ]);

        $user_psychologist->assignRole('psychologist');

        //psikolog 2
        $user_psychologist = User::create([
            'email' => 'psikolog2@psikolog.com',
            'username' => 'psikolog2',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Norman Kamaru M.Psi, Psikolog',
            'speciality' => 'Psikolog Klinis',
            'datebirth' => "1999-04-07",
            'graduation_year' => '2020',
            'graduation_university' => 'Samudera Hindia',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'str_number' => '12345',
            'is_dummy' => true,
        ]);

        $user_psychologist->assignRole('psychologist');

        $schedule = ChatSchedule::create([
            'psychologist_id' => $psychologist->id,
            'day' => 'Rabu',
            'time_start' => '01:00:00',
            'time_end' => '23:00:00',
        ]); 
        $schedule = ChatSchedule::create([
            'psychologist_id' => $psychologist->id,
            'day' => 'Jumat',
            'time_start' => '01:00:00',
            'time_end' => '23:00:00',
        ]); 

        //psikolog 3
        $user_psychologist = User::create([
            'email' => 'psikolog3@psikolog.com',
            'username' => 'psikolog3',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Jayde Mill M.Psi, Psikolog',
            'speciality' => 'Psikolog Klinis',
            'datebirth' => "1999-04-07",
            'graduation_year' => '2020',
            'graduation_university' => 'Samudera Hindia',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'str_number' => '12345',
            'is_dummy' => true,
        ]);

        $user_psychologist->assignRole('psychologist');

        $schedule = ChatSchedule::create([
            'psychologist_id' => $psychologist->id,
            'day' => 'Kamis',
            'time_start' => '18:00:00',
            'time_end' => '22:00:00',
        ]);
        $schedule = ChatSchedule::create([
            'psychologist_id' => $psychologist->id,
            'day' => 'Senin',
            'time_start' => '01:00:00',
            'time_end' => '23:00:00',
        ]); 

        //psikolog 4
        $user_psychologist = User::create([
            'email' => 'psikolog4@psikolog.com',
            'username' => 'psikolog4',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Adele M.Psi, Psikolog',
            'speciality' => 'Psikolog Klinis',
            'datebirth' => "1999-04-07",
            'graduation_year' => '2020',
            'graduation_university' => 'Samudera Hindia',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'str_number' => '12345',
            'is_dummy' => true,
        ]);

        $user_psychologist->assignRole('psychologist');

        $schedule = ChatSchedule::create([
            'psychologist_id' => $psychologist->id,
            'day' => 'Sabtu',
            'time_start' => '01:00:00',
            'time_end' => '23:00:00',
        ]);
        $schedule = ChatSchedule::create([
            'psychologist_id' => $psychologist->id,
            'day' => 'Selasa',
            'time_start' => '01:00:00',
            'time_end' => '23:00:00',
        ]); 

        //psikolog 5
        $user_psychologist = User::create([
            'email' => 'psikolog5@psikolog.com',
            'username' => 'psikolog5',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Bruno M.Psi, Psikolog',
            'speciality' => 'Psikolog Klinis',
            'datebirth' => "1999-04-07",
            'graduation_year' => '2020',
            'graduation_university' => 'Samudera Hindia',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'str_number' => '12345',
            'is_dummy' => true,
        ]);

        $user_psychologist->assignRole('psychologist');

        $schedule = ChatSchedule::create([
            'psychologist_id' => $psychologist->id,
            'day' => 'Kamis',
            'time_start' => '07:00:00',
            'time_end' => '18:00:00',
        ]); 
        $schedule = ChatSchedule::create([
            'psychologist_id' => $psychologist->id,
            'day' => 'Jumat',
            'time_start' => '01:00:00',
            'time_end' => '23:00:00',
        ]); 

        //psikolog 6
        $user_psychologist = User::create([
            'email' => 'psikolog6@psikolog.com',
            'username' => 'psikolog6',
            'password' => Hash::make('psikolog'),
            'email_verified_at' => '2021-12-10 02:42:41'
        ]);

        $psychologist = Psychologist::create([
            'user_id' => $user_psychologist->id,
            'full_name' => 'Alan Jayde M.Psi, Psikolog',
            'speciality' => 'Psikolog Klinis',
            'datebirth' => "1999-04-07",
            'graduation_year' => '2020',
            'graduation_university' => 'Samudera Hindia',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'str_number' => '12345',
            'is_dummy' => true,
        ]);

        $user_psychologist->assignRole('psychologist');

        $schedule = ChatSchedule::create([
            'psychologist_id' => $psychologist->id,
            'day' => 'Minggu',
            'time_start' => '01:00:00',
            'time_end' => '23:00:00',
        ]); 
        $schedule = ChatSchedule::create([
            'psychologist_id' => $psychologist->id,
            'day' => 'Sabtu',
            'time_start' => '01:00:00',
            'time_end' => '23:00:00',
        ]); 
    }
}
