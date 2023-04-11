<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loadshedding_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('zone');
            $table->string('api_id');
            $table->string('region');
            $table->json('today_times')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loadshedding_schedules');
    }
};
