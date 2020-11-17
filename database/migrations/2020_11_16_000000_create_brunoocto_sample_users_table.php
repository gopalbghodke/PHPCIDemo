<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrunooctoSampleUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::connection('brunoocto_sample')->hasTable('users')) {
            Schema::connection('brunoocto_sample')->create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps(3);
                $table->string('email', 190);
                $table->string('name');
                // password field exists only because Authentication feature needs it
                $table->string('password');
            });
        } else {
            if (!Schema::connection('brunoocto_sample')->hasColumn('users', 'email')) {
                Schema::connection('brunoocto_sample')->table('users', function (Blueprint $table) {
                    $table->string('email', 190);
                });
            }
            if (!Schema::connection('brunoocto_sample')->hasColumn('users', 'name')) {
                Schema::connection('brunoocto_sample')->table('users', function (Blueprint $table) {
                    $table->string('name');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::connection('brunoocto_sample')->dropIfExists('users');
    }
}
