<?php

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

    public function canAccessFilament(): bool
    {
        return true;
    }

    public function getDeathPercentage(): ?array
    {
        if ($this->birthdate) {
            $currentDate = Carbon::today();
            $weeksPassed = $currentDate->diffInWeeks($this->birthdate);
            $percentageComplete = round(($weeksPassed / 3900) * 100, 2);
            $percentageLeft = round(((3900 - $weeksPassed) / 3900) * 100, 2);

            return [$percentageLeft, $percentageComplete];
        }

        return null;
    }
}
