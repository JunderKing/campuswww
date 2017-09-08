<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class School extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school', function (Blueprint $table) {
            $table->increments('schl_id');
            $table->string('name', 32)->default('');
            $table->string('intro', 512)->default('');
            $table->string('logo_url', 128)->default('');
            $table->unsignedTinyInteger('province')->default(0);
            $table->unsignedInteger('admin_id')->default(0);
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
        Schema::dropIfExists('school');
    }
}
