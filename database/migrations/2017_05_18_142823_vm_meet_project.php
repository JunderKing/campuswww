<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VmMeetProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vm_meet_project', function (Blueprint $table) {
            $table->unsignedInteger('meet_id')->default(0);
            $table->unsignedInteger('proj_id')->default(0);
            $table->primary(['proj_id', 'meet_id']);
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
        Schema::dropIfExists('vm_meet_project');
    }
}
