<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//TODO Remove?
class Summary extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'currency',
        'amount',
    ];
}
