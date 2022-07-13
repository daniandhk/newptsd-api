<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_types', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('type');
            $table->string('name');
            $table->integer('delay_days');
            $table->string('description');
            $table->integer('total_page');
            $table->integer('total_score')->default(0);
            $table->char('submitter_id', 26);
            $table->char('updater_id', 26)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('submitter_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

                $table->foreign('updater_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('test_types');
    }
}
