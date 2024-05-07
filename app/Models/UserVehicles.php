<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserVehicles extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'vehicle_list_id',
        'make',
        'year_model',
        'plate_number',
        'type',
        'or_path',
        'cr_path'
    ];
    public function vehicle() {
        return $this->hasOne(VehicleList::class, 'id', 'vehicle_list_id');
    }
    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
