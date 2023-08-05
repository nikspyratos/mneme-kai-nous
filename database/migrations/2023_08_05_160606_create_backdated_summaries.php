<?php

declare(strict_types=1);

use App\Models\Summary;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        $dateRanges = [
            ['start' => '24 Feb', 'end' => '25 Mar'],
            ['start' => '25 Mar', 'end' => '25 Apr'],
            ['start' => '25 Apr', 'end' => '25 May'],
            ['start' => '25 May', 'end' => '3 Jun'],
            ['start' => '3 Jun', 'end' => '23 Jun'],
            ['start' => '23 Jun', 'end' => '25 Jul'],
        ];
        foreach ($dateRanges as $dateRange) {
            Summary::createForPeriod(Carbon::parse($dateRange['start']), Carbon::parse($dateRange['end']));
        }
    }

    public function down(): void
    {
    }
};
