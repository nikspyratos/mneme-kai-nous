<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'bank_name',
        'account_number',
        'currency',
        'balance',
        'type',
    ];
}
