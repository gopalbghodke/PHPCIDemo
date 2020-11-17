<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrunooctoSampleMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('brunoocto_sample')->create('milestones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps(3);
            $table->dateTime('deleted_at', 3)->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->string('title', 1000);
            $table->dateTime('deadline', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::connection('brunoocto_sample')->dropIfExists('milestones');
    }
}
