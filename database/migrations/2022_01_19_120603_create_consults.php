<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsults extends Migration
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
            $table->datetime('next_date');
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
