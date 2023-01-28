<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tally extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'categories',
        'currency',
        'amount',
    ];
}