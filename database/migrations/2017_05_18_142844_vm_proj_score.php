<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VmProjScore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vm_proj_score', function (Blueprint $table) {
          $table->unsignedInteger('comnt_id')->default(0);
          $table->unsignedInteger('grader_id')->default(0);
          $table->unsignedTinyInteger('t_score')->default(0);
          $table->unsignedTinyInteger('a_score')->default(0);
          $table->unsignedTinyInteger('b_score')->default(0);
          $table->unsignedTinyInteger('c_score')->default(0);
          $table->primary(['grader_id', 'comnt_id']);
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
        Schema::dropIfExists('vm_proj_score');
    }
}
