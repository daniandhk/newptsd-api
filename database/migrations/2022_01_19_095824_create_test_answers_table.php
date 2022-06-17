<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_answers', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('test_question_id', 26);
            $table->string('text')->nullable();
            $table->string('description')->nullable();
            $table->integer('weight');
            $table->boolean('is_essay');
            $table->timestamps();

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
        Schema::dropIfExists('test_answers');
    }
}
