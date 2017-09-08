<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VmInvor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vm_invor', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->default(0);
            $table->string('real_name', 16)->default('');
            $table->string('company', 32)->default('');
            $table->string('position', 32)->default('');
            $table->string('intro', 500)->default('');
            $table->primary('user_id');
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
        Schema::dropIfExists('vm_invor');
    }
}
