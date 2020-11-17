<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrunooctoSampleCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('brunoocto_sample')->create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps(3);
            $table->unsignedBigInteger('created_by');
            $table->string('other_type')->nullable();
            $table->unsignedBigInteger('other_id')->nullable();
            $table->text('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::connection('brunoocto_sample')->dropIfExists('comments');
    }
}
