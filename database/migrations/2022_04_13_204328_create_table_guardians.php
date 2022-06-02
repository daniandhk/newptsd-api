<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGuardians extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->string('full_name');
            $table->string('status');
            $table->string('phone');
            $table->boolean('is_available');
            $table->timestamps();

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
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
        Schema::dropIfExists('guardians');
    }
}