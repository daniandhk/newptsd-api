<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('test_id', 26);
            $table->char('test_answer_id', 26);
            $table->char('test_question_id', 26);
            $table->text('text');
            $table->timestamps();

            $table->foreign('test_id')
                ->references('id')
                ->on('tests')
                ->onDelete('cascade');

            $table->foreign('test_answer_id')
                ->references('id')
                ->on('test_answers')
                ->onDelete('cascade');

            $table->foreign('test_question_id')
                ->references('id')
                ->on('test_questions')
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
        Schema::dropIfExists('answers');
    }
}
