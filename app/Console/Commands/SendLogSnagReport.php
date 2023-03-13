<?php

namespace App\Console\Commands;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\LoadsheddingSchedule;
use App\Models\User;
use App\Services\LogSnag;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use App\Models\Quote;

class SendLogSnagReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-logsnag-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a LogSnag report.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $user = User::first();
        $data = [];

        $accounts = Account::whereIsPrimary(true)->get();
        foreach ($accounts as $account) {
            $message = sprintf('**%s:** %s', $account->name, $account->formatted_balance);
            if ($account->type == AccountType::DEBT->value) {
                $message .= sprintf(' | %s', $account->debt_paid_off_percentage . '%');
            }
            $data[] = $message;
        }

        $loadsheddingSchedule = LoadsheddingSchedule::whereIsHome(true)->first();
        if ($loadsheddingSchedule) {
            $data[] = '**Loadshedding:** ' . $loadsheddingSchedule->todayTimesFormatted;
        }

        [$percentageLeft, $percentageComplete] = $user->getDeathPercentage();
        $data[] = "**Death:** $percentageComplete%";

        $quote = Quote::inRandomOrder()->first();
        if ($quote) {
            $data[] = sprintf('*%s - %s*', $quote->content, $quote->author);
        }

        (new LogSnag())->log('Daily', Arr::join($data, "\n"), true);
    }
}
