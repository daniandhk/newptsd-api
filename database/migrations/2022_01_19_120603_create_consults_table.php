<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consults', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('relation_id', 26);
            $table->string('consult_index');
            $table->datetime('last_date')->nullable();
            $table->datetime('videocall_date');
            $table->string('videocall_link')->nullable();
            $table->boolean('is_finished')->default(0);
            $table->timestamps();

            $table->foreign('relation_id')
                ->references('id')
                ->on('relations')
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
        Schema::dropIfExists('consults');
    }
}
