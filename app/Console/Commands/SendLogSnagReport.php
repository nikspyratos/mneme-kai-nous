<?php

namespace App\Console\Commands;

use App\Enums\AccountTypes;
use App\Models\Account;
use App\Models\LoadsheddingSchedule;
use App\Models\Quote;
use App\Models\User;
use App\Services\LogSnag;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

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
            $message = sprintf('**%s:** %s', $account->name, $account->formattedBalance);
            if ($account->type == AccountTypes::DEBT->value) {
                $message .= sprintf(' | %s', $account->debt_paid_off_percentage . '%');
            }
            $data[] = $message;
        }

        $loadsheddingSchedule = LoadsheddingSchedule::whereIsHome(true)->first();
        if ($loadsheddingSchedule) {
            $data[] = '**Loadshedding:** ' . $loadsheddingSchedule->todayTimesFormatted;
        }

        [$percentageLeft, $percentageComplete] = $user->getDeathPercentage();
        $data[] = "**Life:** {$percentageComplete}%";

        $quote = Quote::inRandomOrder()->first();
        if ($quote) {
            $content = '*' . $quote->content;
            if ($quote->author) {
                $content .= ' - ' . $quote->author;
            }

            $data[] = $content . '*';
        }

        (new LogSnag)->log('Daily', Arr::join($data, "\n"), true);
    }
}
