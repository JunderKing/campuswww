<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class User extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('union_id', 32)->default('');
            $table->string('open_id', 32)->default('');
            $table->string('avatar_url', 128)->default('');
            $table->string('nick_name', 32)->default('');
            $table->unsignedTinyInteger('role')->default(0);
            $table->unsignedTinyInteger('stage')->default(0);
            $table->unsignedTinyInteger('festStage')->default(0);
            $table->unsignedTinyInteger('campStage')->default(0);
            $table->unsignedTinyInteger('meetStage')->default(0);
            $table->unique('union_id');
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
        Schema::dropIfExists('user');
    }
}
