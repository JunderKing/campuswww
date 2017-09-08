<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScProjGrid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sc_proj_grid', function (Blueprint $table) {
            $table->increments('grid_id');
            $table->unsignedInteger('proj_id')->default(0);
            $table->unsignedInteger('grid_num')->default(0);
            $table->string('content', 500)->default('');
            $table->unique(['proj_id', 'grid_num']);
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
        Schema::dropIfExists('sc_proj_grid');
    }
}
