<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialsTable extends Migration
{
    public function up()
    {
        Schema::create('trials', function (Blueprint $table) {
            $table->id();
            $table->string('trial_name');
            $table->json('righteye')->nullable();
            $table->json('lefteye')->nullable();
            $table->json('rightshoulder')->nullable();
            $table->json('leftshoulder')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trials');
    }
}
