<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class schlAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schl_admin', function (Blueprint $table) {
            $table->unsignedInteger('schl_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->unique(['schl_id', 'user_id']);
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
        Schema::dropIfExists('schl_admin');
    }
}
