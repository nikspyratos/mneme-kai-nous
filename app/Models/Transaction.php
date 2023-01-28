<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public $fillable = [
        'account_id',
        'date',
        'description',
        'detail',
        'currency',
        'amount',
        'listed_balance',
    ];
}
