<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VmMeetInvor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vm_meet_invor', function (Blueprint $table) {
            $table->unsignedInteger('meet_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->primary(['user_id', 'meet_id']);
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
        Schema::dropIfExists('vm_meet_invor');
    }
}
