<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Account
 *
 * @property int $id
 * @property string $name
 * @property string|null $bank_name
 * @property string|null $account_number
 * @property string $currency
 * @property int|null $balance
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $debt
 * @property bool $has_overdraft
 * @property int|null $overdraft_amount
 * @property string|null $bank_identifier
 * @property array|null $data
 * @property bool $is_primary
 * @property bool|null $is_main
 * @property-read float|null $available_credit_percentage
 * @property-read float|null $debt_paid_off_percentage
 * @property-read string $formatted_balance
 * @property-read string $formatted_debt
 * @property-read string $formatted_debt_balance
 * @property-read string $formatted_overdraft_amount
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @method static \Database\Factories\AccountFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBankIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDebt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereHasOverdraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIsMain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereOverdraftAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedAt($value)
 */
	class Account extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Budget
 *
 * @property int $id
 * @property string $name
 * @property string $currency
 * @property int|null $amount
 * @property string $period_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Collection|null $identifier
 * @property string|null $identifier_transaction_type
 * @property int $enabled
 * @property-read string $formatted_amount
 * @property-read string $identifier_string
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tally> $tallies
 * @property-read int|null $tallies_count
 * @method static \Illuminate\Database\Eloquent\Builder|Budget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Budget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Budget query()
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereIdentifierTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget wherePeriodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Budget withCurrentTallies()
 */
	class Budget extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExpectedTransaction
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $group
 * @property string $currency
 * @property int|null $amount
 * @property string|null $due_period
 * @property int|null $due_day
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Collection|null $identifier
 * @property string|null $identifier_transaction_type
 * @property int $enabled
 * @property string $type
 * @property int $is_tax_relevant
 * @property \Illuminate\Support\Carbon|null $next_due_date
 * @property int|null $budget_id
 * @property bool $is_paid
 * @property-read \App\Models\Budget|null $budget
 * @property-read string $formatted_amount
 * @property-read string $identifier_string
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereBudgetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereDueDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereDuePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereIdentifierTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereIsTaxRelevant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereNextDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectedTransaction whereUpdatedAt($value)
 */
	class ExpectedTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LoadsheddingSchedule
 *
 * @property int $id
 * @property string $name
 * @property string $zone
 * @property string $api_id
 * @property string $region
 * @property array|null $today_times
 * @property array|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $is_home
 * @property-read string $today_times_formatted
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereIsHome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereTodayTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoadsheddingSchedule whereZone($value)
 */
	class LoadsheddingSchedule extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Perception
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Quote> $quotes
 * @property-read int|null $quotes_count
 * @method static \Database\Factories\PerceptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Perception newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Perception newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Perception query()
 * @method static \Illuminate\Database\Eloquent\Builder|Perception whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Perception whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Perception whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Perception whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Perception whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Perception whereUpdatedAt($value)
 */
	class Perception extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Quote
 *
 * @property int $id
 * @property int $perception_id
 * @property string $content
 * @property string|null $author
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Perception|null $perception
 * @method static \Database\Factories\QuoteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Quote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote query()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote shortContent()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote wherePerceptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereUpdatedAt($value)
 */
	class Quote extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Summary
 *
 * @property int $id
 * @property string $name
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Summary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Summary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Summary query()
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summary whereUpdatedAt($value)
 */
	class Summary extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Tally
 *
 * @property int $id
 * @property int $budget_id
 * @property string $name
 * @property string $currency
 * @property int|null $balance
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $limit
 * @property-read \App\Models\Budget|null $budget
 * @property-read string $formatted_balance
 * @property-read string $formatted_limit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Tally forCurrentBudgetMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|Tally forPeriod(\Illuminate\Support\Carbon $startDate, \Illuminate\Support\Carbon $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tally newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tally query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereBudgetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tally whereUpdatedAt($value)
 */
	class Tally extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property int $account_id
 * @property int|null $tally_id
 * @property \Illuminate\Support\Carbon $date
 * @property string|null $type
 * @property string|null $description
 * @property string|null $detail
 * @property string $currency
 * @property int|null $amount
 * @property int|null $fee
 * @property int|null $listed_balance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $category
 * @property array|null $data
 * @property int $is_tax_relevant
 * @property int|null $parent_id
 * @property-read \App\Models\Account|null $account
 * @property-read \App\Models\Budget|null $budget
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Transaction> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\ExpectedTransaction|null $expectedTransaction
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExpectedTransaction> $expectedTransactions
 * @property-read int|null $expected_transactions_count
 * @property-read string $formatted_amount
 * @property-read string $formatted_fee
 * @property-read string $formatted_listed_balance
 * @property-read Transaction|null $parent
 * @property-read \App\Models\Tally|null $tally
 * @method static \Database\Factories\TransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction inCurrentBudgetMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction taxRelevant(?\Illuminate\Support\Carbon $startDate = null, ?\Illuminate\Support\Carbon $endDate = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereIsTaxRelevant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereListedBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTallyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 */
	class Transaction extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $birthdate
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser {}
}

