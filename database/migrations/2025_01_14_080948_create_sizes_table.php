<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('size');
            $table->enum('material', ['Polyester', 'Cotton', 'Blended', 'Stretchable']);
            $table->enum('style', ['slim_fit', 'oversize', 'regular']);
            $table->float('chest_circumference_min');
            $table->float('chest_circumference_max');
            $table->float('shoulder_width_min');
            $table->float('shoulder_width_max');
            $table->float('sleeve_length_min');
            $table->float('sleeve_length_max');
            $table->float('shirt_length_min');
            $table->float('shirt_length_max');
            $table->float('waist_circumference_min');
            $table->float('waist_circumference_max');
            $table->float('neck_circumference_min');
            $table->float('neck_circumference_max');
            $table->integer('tolerance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};
