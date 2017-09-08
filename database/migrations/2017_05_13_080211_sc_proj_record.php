<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScProjRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sc_proj_record', function (Blueprint $table) {
            $table->increments('rec_id');
            $table->unsignedInteger('proj_id')->default(0);
            $table->unsignedInteger('date')->default(0);
            $table->string('content', 500)->default('');
            $table->index('proj_id');
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
        Schema::dropIfExists('sc_proj_record');
    }
}
