<?php

namespace Database\Seeders;

use App\Models\TestAnswer;
use App\Models\TestPage;
use App\Models\TestQuestion;
use App\Models\TestType;
use Illuminate\Database\Seeder;

class PDS5Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $test_type = TestType::create([
            'type' => 'pds5',
            'name' => 'Posttraumatic Diagnostic Scale (PDS-5)',
            'total_score' => 80,
            'total_page' => 6,
            'delay_days' => 30,
            'description' => "Tes dengan 24 item yang menilai tingkat keparahan gejala PTSD dengan kriteria DSM-5"
        ]);

        $test_page = TestPage::create([
            'number' => 1,
            'test_type_id' => $test_type['id'],
            'title'=> 'Skrining Trauma',
            'description' => null,
        ]);

        // $test_question = TestQuestion::create([
        //     'test_page_id' => $test_page['id'],
        //     'text'=> 'Testing essay',
        //     'answer_type' => 'essay',
        // ]);

        // $test_answer = TestAnswer::create([
        //     'test_question_id' => $test_question['id'],
        //     'text'=> null,
        //     'weight' => 0,
        //     'is_essay' => true,
        // ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Pernahkah Anda mengalami, menyaksikan, atau dihadapkan berulang kali pada hal berikut ini:',
            'answer_type' => 'mc_multi',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Penyakit serius yang mengancam nyawa (serangan jantung, dll)',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Kekerasan fisik (diserang dengan senjata, luka parah akibat perkelahian, ditodongan senjata, dll)',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Kekerasan seksual (korban pemerkosaan, korban percobaan pemerkosaan, dll)',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Pertempuran militer atau tinggal di zona perang',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Kekerasan pada anak (dipukul berat, tindakan seksual dengan seseorang 5 tahun lebih tua, dll)',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Kecelakaan (luka serius atau kematian di kecelakaan mobil, saat bekerja, kebakaran rumah, dll)',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Bencana alam (badai parah, banjir, gempa bumi, dll)',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'description'=> 'Trauma lainnya (Harap dijelaskan dengan ringkas):',
            'weight' => 0,
            'is_essay' => true,
        ]);

        $test_page = TestPage::create([
            'number' => 2,
            'test_type_id' => $test_type['id'],
            'title'=> null,
            'description' => null,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Jika Anda menandai salah satu item sebelumnya, pengalaman traumatis mana yang ada di pikiran Anda dan saat ini sangat mengganggu Anda?',
            'answer_type' => 'mc_one',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Penyakit serius yang mengancam nyawa (serangan jantung, dll)',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Kekerasan fisik (diserang dengan senjata, luka parah akibat perkelahian, ditodongan senjata, dll)',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Kekerasan seksual (korban pemerkosaan, korban percobaan pemerkosaan, dll)',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Pertempuran militer atau tinggal di zona perang',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Kekerasan pada anak (dipukul berat, tindakan seksual dengan seseorang 5 tahun lebih tua, dll)',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Kecelakaan (luka serius atau kematian di kecelakaan mobil, saat bekerja, kebakaran rumah, dll)',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Bencana alam (badai parah, banjir, gempa bumi, dll)',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'description'=> 'Trauma lainnya (Harap dijelaskan dengan ringkas):',
            'weight' => 0,
            'is_essay' => true,
        ]);

        $test_page = TestPage::create([
            'number' => 3,
            'test_type_id' => $test_type['id'],
            'title'=> 'Skala Diagnostik PTSD untuk DSM-5 (PDS-5)',
            'description' => 'Harap baca setiap pernyataan dengan seksama dan pilih nomor yang paling menggambarkan seberapa sering masalah itu terjadi dan seberapa besar hal itu mengganggu Anda selama SEBULAN TERAKHIR. Beri nilai setiap masalah perihal dengan peristiwa traumatis yang Anda alami tulis di atas.',
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Kenangan menjengkelkan yang tidak diinginkan tentang trauma',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Mimpi buruk atau mengerikan yang berhubungan dengan trauma',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Mengenang kembali peristiwa atau perasaan traumatis seolah-olah kenangan itu benar-benar terjadi lagi',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Merasa sangat kesal secara EMOSIONAL ketika diingatkan akan traumanya',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Memiliki reaksi FISIK saat mengingat trauma (misalnya berkeringat, jantung berdebar kencang, dll)',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Mencoba menghindari pikiran atau perasaan yang berhubungan dengan trauma',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Berusaha menghindari aktivitas, situasi, atau tempat yang mengingatkan Anda akan trauma atau yang terasa lebih berbahaya sejak trauma itu terjadi',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Tidak dapat mengingat bagian penting dari trauma',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Melihat diri sendiri, orang lain, atau dunia dengan cara yang lebih negatif (misalnya "Saya tidak bisa mempercayai orang", "Saya orang yang lemah")',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Menyalahkan diri sendiri atau orang lain (selain orang yang menyakiti Anda) atas apa yang telah terjadi',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_page = TestPage::create([
            'number' => 4,
            'test_type_id' => $test_type['id'],
            'title'=> null,
            'description' => null,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Memiliki perasaan negatif yang intens seperti takut, ngeri, marah, bersalah, atau malu',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Kehilangan minat atau tidak berpartisipasi dalam aktivitas yang biasa Anda lakukan',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Merasa jauh atau terputus hubungannya dari orang lain',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Mengalami kesulitan mengalami perasaan positif',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Bertindak lebih mudah tersinggung atau agresif dengan orang lain',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Melakukan lebih banyak hal yang berisiko atau melakukan hal-hal yang dapat membahayakan Anda atau orang lain (misalnya mengemudi dengan sembrono, menggunakan narkoba, berhubungan seks tanpa pengaman)',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Menjadi terlalu waspada atau berjaga-jaga (misalnya memeriksa siapa yang ada di sekitar Anda, merasa tidak nyaman dengan membelakangi pintu)',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Menjadi gelisah atau lebih mudah terkejut (misalnya ketika seseorang berjalan di belakang Anda)',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Mengalami kesulitan berkonsentrasi',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Mengalami kesulitan tidur atau tetap tertidur',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_page = TestPage::create([
            'number' => 5,
            'test_type_id' => $test_type['id'],
            'title'=> 'Gangguan dan Interferensi',
            'description' => null,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Seberapa besar kesulitan-kesulitan tadi mengganggu Anda?',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Telah seberapa besar kesulitan-kesulitan tadi mengganggu kehidupan sehari-hari Anda (misalnya hubungan, pekerjaan, atau kegiatan penting lainnya)?',
            'answer_type' => 'score',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Tidak sama sekali',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Sekali seminggu atau kurang / sedikit',
            'weight' => 1,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '2 hingga 3 kali seminggu / agak banyak',
            'weight' => 2,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '4 hingga 5 kali seminggu / sangat banyak',
            'weight' => 3,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> '6 kali atau lebih dalam seminggu / parah',
            'weight' => 4,
            'is_essay' => false,
        ]);

        $test_page = TestPage::create([
            'number' => 6,
            'test_type_id' => $test_type['id'],
            'title'=> 'Awal Mula Gejala dan Durasinya',
            'description' => null,
        ]);

        $test_question = TestQuestion::create([
            'test_page_id' => $test_page['id'],
            'text'=> 'Berapa lama setelah trauma, kesulitan-kesulitan tadi dimulai?',
            'answer_type' => 'mc_one',
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Kurang dari 6 bulan',
            'weight' => 0,
            'is_essay' => false,
        ]);

        $test_answer = TestAnswer::create([
            'test_question_id' => $test_question['id'],
            'text'=> 'Lebih dari 6 bulan',
            'weight' => 0,
            'is_essay' => false,
        ]);
    }
}