<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScCamp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sc_camp', function (Blueprint $table) {
            $table->increments('camp_id');
            $table->unsignedInteger('orger_id')->default(0);
            $table->unsignedInteger('schl_id')->default(0);
            $table->string('name', 32)->default('');
            $table->string('intro', 500)->default('');
            $table->string('sponsor', 32)->default('');
            $table->string('logo_url', 128)->default('');
            $table->index('orger_id');
            $table->softDeletes();
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
        Schema::dropIfExists('sc_camp');
    }
}
