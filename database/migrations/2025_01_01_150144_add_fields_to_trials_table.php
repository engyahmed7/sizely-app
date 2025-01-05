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
        Schema::table('trials', function (Blueprint $table) {
            $table->json('rightElbow')->nullable()->after('leftshoulder');
            $table->json('leftElbow')->nullable()->after('rightElbow');
            $table->json('rightWrist')->nullable()->after('leftElbow');
            $table->json('leftWrist')->nullable()->after('rightWrist');
            $table->json('rightHip')->nullable()->after('leftWrist');
            $table->json('leftHip')->nullable()->after('rightHip');
            $table->json('rightAnkle')->nullable()->after('leftHip');
            $table->json('leftAnkle')->nullable()->after('rightAnkle');
            $table->json('rightKnee')->nullable()->after('leftAnkle');
            $table->json('leftKnee')->nullable()->after('rightKnee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trials', function (Blueprint $table) {
            $table->dropColumn('rightElbow');
            $table->dropColumn('leftElbow');
            $table->dropColumn('rightWrist');
            $table->dropColumn('leftWrist');
            $table->dropColumn('rightHip');
            $table->dropColumn('leftHip');
            $table->dropColumn('rightAnkle');
            $table->dropColumn('leftAnkle');
            $table->dropColumn('rightKnee');
            $table->dropColumn('leftKnee');
        });
    }
};
