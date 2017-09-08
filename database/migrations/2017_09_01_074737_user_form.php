<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_form', function (Blueprint $table) {
            $table->increments('record_id');
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedTinyInteger('app_type')->default(0);
            $table->string('form_id', 128)->default('');
            $table->unsignedInteger('expire_time')->default(0);
            $table->unsignedTinyInteger('is_used')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_form');
    }
}
