<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScProjCard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sc_proj_card', function (Blueprint $table) {
            $table->increments('card_id');
            $table->unsignedInteger('grid_id')->default(0);
            $table->string('title', 32)->default('');
            $table->string('assumption', 500)->default('');
            $table->string('result', 500)->default('');
            $table->unsignedTinyInteger('status')->default(0);
            $table->index('grid_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sc_proj_card');
    }
}
