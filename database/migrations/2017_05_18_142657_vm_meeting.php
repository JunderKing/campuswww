<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VmMeeting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vm_meeting', function (Blueprint $table) {
            $table->increments('meet_id');
            $table->unsignedInteger('orger_id')->default(0);
            $table->unsignedInteger('schl_id')->default(0);
            $table->string('name', 32)->default('');
            $table->string('intro', 500)->default('');
            $table->string('addr', 32)->default('');
            $table->string('sponsor', 32)->default('');
            $table->string('logo_url', 128)->default('');
            $table->unsignedInteger('start_time')->default(0);
            $table->unsignedInteger('end_time')->default(0);
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
        Schema::dropIfExists('vm_meeting');
    }
}
