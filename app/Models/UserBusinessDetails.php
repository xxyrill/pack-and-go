<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBusinessDetails extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'business_name',
        'business_address',
        'business_complete_address',
        'business_permit_number',
        'business_tourism_number',
        'business_contact_person',
        'business_contact_person_number'
    ];
}
