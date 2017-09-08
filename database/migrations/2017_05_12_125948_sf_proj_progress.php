<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SfProjProgress extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('sf_proj_progress', function (Blueprint $table) {
      $table->unsignedInteger('proj_id')->default(0);
      $table->unsignedTinyInteger('step_num')->default(0);
      $table->string('image_url', 128)->default('');
      $table->string('content', 500)->default('');
      $table->primary(['proj_id', 'step_num']);
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
    Schema::dropIfExists('sf_proj_progress');
  }
}
