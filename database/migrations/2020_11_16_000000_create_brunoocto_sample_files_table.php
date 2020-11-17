<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrunooctoSampleFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('brunoocto_sample')->create('files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps(3);
            $table->dateTime('deleted_at', 3)->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->string('title', 1000);
            $table->string('mime', 100);
            $table->unsignedInteger('bytes');
            $table->string('path', 1000);
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::connection('brunoocto_sample')->dropIfExists('files');
    }
}
