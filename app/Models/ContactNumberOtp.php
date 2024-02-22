<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactNumberOtp extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'contact_number_otp',
        'contact_number'
    ];
}
