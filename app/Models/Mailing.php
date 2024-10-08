<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mailing extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'email',
        'type',
        'booking_id',
        'user_id',
        'link',
    ];
}
