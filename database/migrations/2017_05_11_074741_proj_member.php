<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProjMember extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proj_member', function (Blueprint $table) {
            $table->unsignedInteger('proj_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedTinyInteger('app_type')->default(0);
            $table->unsignedTinyInteger('is_leader')->default(0);
            $table->primary(['proj_id', 'user_id', 'app_type']);
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
        Schema::dropIfExists('proj_member');
    }
}
