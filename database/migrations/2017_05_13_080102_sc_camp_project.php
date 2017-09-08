<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScCampProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sc_camp_project', function (Blueprint $table) {
            $table->unsignedInteger('camp_id')->default(0);
            $table->unsignedInteger('proj_id')->default(0);
            $table->primary(['camp_id', 'proj_id']);
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
        Schema::dropIfExists('sc_camp_project');
    }
}
