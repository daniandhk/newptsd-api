<?php

namespace Database\Seeders;

use App\Models\TestType;
use Illuminate\Database\Seeder;

class TestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TestType::create([
            'type' => 'pds5',
            'name' => 'Posttraumatic Diagnostic Scale (PDS-5)',
            'total_score' => 80,
            'delay_days' => 30,
            'description' => "Tes dengan 24 item yang menilai tingkat keparahan gejala PTSD dengan kriteria DSM-5"
        ]);
    }
}