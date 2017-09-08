<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VmUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vm_user', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedTinyInteger('schl_id')->default(0);
            $table->unsignedInteger('cur_meet_id')->default(0);
            $table->unsignedTinyInteger('cur_proj_id')->default(0);
            $table->primary('user_id');
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
        Schema::dropIfExists('vm_user');
    }
}
