<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrunooctoSampleSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('brunoocto_sample')->create('samples', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps(3);
            // $table->softDeletes(3); does not work properly, it set the column name at "3" instead of 'deleted_at', so I use dateTime
            $table->dateTime('deleted_at', 3)->nullable();
            $table->text('text')->comment('A comment');
            
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /**
         * HIGH RISK
         * If you want to allow dropIfExists method, please insert a back feature before to avoid loosing any data
         */
        // Schema::connection('brunoocto_sample')->dropIfExists('samples');
    }
}
