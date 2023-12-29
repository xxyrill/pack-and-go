<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'vehicle_list_id',
        'user_id',
        'user_driver_id',
        'pick_up_longitude',
        'pick_up_latitude',
        'drop_off_longitude',
        'drop_off_latitude',
        'pickup_house_information',
        'pickup_helper_stairs',
        'pickup_helper_elivator',
        'pickup_ring_door',
        'pickup_adition_remarks',
        'drop_off_house_information',
        'booking_date_time_start',
        'booking_date_time_end',
        'need_helper',
        'alt_contact_number_one',
        'alt_contact_number_two',
        'alt_email',
        'price',
        'status',
        'order_number'
    ];
}
