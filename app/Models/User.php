<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'birthdate',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthdate' => 'datetime',
    ];

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return true;
    }

    public function getDeathPercentage(int $year = 75): ?array
    {
        if ($this->birthdate) {
            $totalWeeks = $year * 52;
            $currentDate = Carbon::today();
            $weeksPassed = $currentDate->diffInWeeks($this->birthdate);
            $percentageComplete = round(($weeksPassed / $totalWeeks) * 100, 2);
            $percentageLeft = round((($totalWeeks - $weeksPassed) / $totalWeeks) * 100, 2);

            return [$percentageLeft, $percentageComplete];
        }

        return null;
    }
}
