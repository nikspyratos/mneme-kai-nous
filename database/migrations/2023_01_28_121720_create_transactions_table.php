<?php

use App\Models\Account;
use App\Models\Budget;
use App\Models\ExpectedTransaction;
use App\Models\Tally;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Account::class);
            $table->foreignIdFor(ExpectedTransaction::class)->nullable();
            $table->foreignIdFor(Budget::class)->nullable();
            $table->foreignIdFor(Tally::class)->nullable();
            $table->dateTime('date');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->text('detail')->nullable();
            $table->string('currency')->default('ZAR');
            $table->bigInteger('amount')->nullable();
            $table->integer('fee')->nullable();
            $table->bigInteger('listed_balance')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
