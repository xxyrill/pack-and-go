<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDriverDetails extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'vehicle_list_id',
        'driver_license_number',
        'front_license_path',
        'back_license_path',
        'make',
        'year_model',
        'plate_number',
        'helper',
        'license_expiry_date',
        'secondary_id_path'
    ];
}
