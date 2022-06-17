<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_questions', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('consult_id', 26);
            $table->string('question_text');
            $table->timestamps();

            $table->foreign('consult_id')
                ->references('id')
                ->on('consults')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('note_questions');
    }
}
