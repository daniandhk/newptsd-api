<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consult_infos', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('consult_id', 26);
            $table->string('videocall_link')->nullable();
            $table->datetime('videocall_date')->nullable();
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
        Schema::dropIfExists('consult_infos');
    }
}
