<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScCampMentor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sc_camp_mentor', function (Blueprint $table) {
            $table->unsignedInteger('camp_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->primary(['user_id', 'camp_id']);
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
        Schema::dropIfExists('sc_camp_mentor');
    }
}
