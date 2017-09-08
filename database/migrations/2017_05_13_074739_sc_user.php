<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sc_user', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedTinyInteger('schl_id')->default(0);
            $table->unsignedInteger('cur_camp_id')->default(0);
            $table->unsignedTinyInteger('cur_proj_id')->default(0);
            $table->timestamps();
            $table->primary('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sc_user');
    }
}
