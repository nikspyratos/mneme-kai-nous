<?php

declare(strict_types=1);

use App\Models\Budget;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tallies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Budget::class);
            $table->string('name');
            $table->string('currency')->default('ZAR');
            $table->bigInteger('balance')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tallies');
    }
};
