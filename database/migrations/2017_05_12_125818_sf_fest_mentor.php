<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SfFestMentor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sf_fest_mentor', function (Blueprint $table) {
            $table->unsignedInteger('fest_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->primary(['user_id', 'fest_id']);
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
        Schema::dropIfExists('sf_fest_mentor');
    }
}
