<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('patient_id', 26);
            $table->char('test_type_id', 26);
            $table->dateTime('next_date');
            $table->integer('score');
            $table->string('videocall_link')->nullable();
            $table->string('videocall_date')->nullable();
            $table->boolean('is_finished')->default(0);
            $table->integer('index');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade');

            $table->foreign('test_type_id')
                ->references('id')
                ->on('test_types')
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
        Schema::dropIfExists('tests');
    }
}
