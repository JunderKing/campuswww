<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Project extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project', function (Blueprint $table) {
            $table->increments('proj_id');
            $table->unsignedInteger('leader_id')->default(0);
            $table->string('name', 32)->default('');
            $table->string('intro', 512)->default('');
            $table->string('logo_url', 128)->default('');
            $table->unsignedTinyInteger('province')->default(0);
            $table->string('tag', 32)->default('');
            $table->unsignedTinyInteger('origin')->default(0);
            $table->index('leader_id');
            $table->timestamps();
            $table->softDeletes();
            //$table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project');
    }
}
