<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtpCodes extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'contact_number',
        'otp_code',
        'timer'
    ];
}
