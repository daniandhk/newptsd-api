<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_answers', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('note_question_id', 26);
            $table->datetime('date');
            $table->string('answer_text');
            $table->timestamps();

            $table->foreign('note_question_id')
                ->references('id')
                ->on('note_questions')
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
        Schema::dropIfExists('note_answers');
    }
}
