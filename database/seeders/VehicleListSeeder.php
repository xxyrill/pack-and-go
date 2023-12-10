<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\VehicleList::truncate();
        \App\Models\VehicleList::insert([
            ['type'    => 'Motorcycle'],
            ['type'    => 'Jeepney Standard'],
            ['type'    => 'Jeepney Extended'],
            ['type'    => 'Taxi Sedan'],
            ['type'    => 'Taxi MPV'],
            ['type'    => '6-wheel Box Truck'],
            ['type'    => '10-wheel Box Truck'],
            ['type'    => 'Small Flatbed Truck'],
            ['type'    => 'Medium Flatbed Truck'],
            ['type'    => 'Large Flatbed Truck'],
        ]);
    }
}
