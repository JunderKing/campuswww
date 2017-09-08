<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SfFestProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sf_fest_project', function (Blueprint $table) {
            $table->unsignedInteger('fest_id')->default(0);
            $table->unsignedInteger('proj_id')->default(0);
            $table->primary(['fest_id', 'proj_id']);
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
        Schema::dropIfExists('sf_fest_project');
    }
}
