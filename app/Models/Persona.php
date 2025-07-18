<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'title',
        'company',
        'email',
        'phone',
        'background',
        'goals',
        'challenges',
        'buying_motivation',
        'objections',
        'additional_notes',
    ];

}
