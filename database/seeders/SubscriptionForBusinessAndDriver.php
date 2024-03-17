<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\SubscriptionInclusion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionForBusinessAndDriver extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            [
                "subscription_period" => 1,
                "price" => 3000.00,
                "type" => "business",
                "status" => "active",
                "title" => "Monthly Subscription",
                "description" => "Php 3,000 per month. Cancel anytime.",
                "vehicle_number" => 5,
                "inclusions" => [
                    [ "inclusion" => "Includes five vehicle"]
                ]
            ],[
                "subscription_period" => 6,
                "price" => 10000.00,
                "type" => "business",
                "status" => "active",
                "title" => "Semi Annual Subscription",
                "description" => "Php 10,000 per six month. Cancel anytime.",
                "vehicle_number" => 10,
                "inclusions" => [
                    [ "inclusion" => "Includes ten vehicle"]
                ]
            ],[
                "subscription_period" => 12,
                "price" => 20000.00,
                "type" => "business",
                "status" => "active",
                "title" => "Annual Subscription",
                "description" => "Php 20,000 per month. Cancel anytime.",
                "vehicle_number" => 20,
                "inclusions" => [
                    [ "inclusion" => "Includes twenty vehicle"]
                ]
            ],[
                "subscription_period" => 1,
                "price" => 500.00,
                "type" => "driver",
                "status" => "active",
                "title" => "Monthly Subscription",
                "description" => "Php 500 per month. Cancel anytime.",
                "vehicle_number" => 2,
                "inclusions" => [
                    [ "inclusion" => "Includes two vehicle"]
                ]
            ],[
                "subscription_period" => 6,
                "price" => 2000.00,
                "type" => "driver",
                "status" => "active",
                "title" => "Semi Annual Subscription",
                "description" => "Php 2,000 per six month. Cancel anytime.",
                "vehicle_number" => 5,
                "inclusions" => [
                    [ "inclusion" => "Includes five vehicle"]
                ]
            ],[
                "subscription_period" => 12,
                "price" => 5000.00,
                "type" => "driver",
                "status" => "active",
                "title" => "Annual Subscription",
                "description" => "Php 5,000 per month. Cancel anytime.",
                "vehicle_number" => 7,
                "inclusions" => [
                    [ "inclusion" => "Includes seven vehicle"]
                ]
            ],
        ];
        Subscription::truncate();
        foreach ($fields as $key) {
            $data = Subscription::create([
                "subscription_period" => $key['subscription_period'],
                "price" => $key['price'],
                "status" => $key['status'],
                "type" => $key['type'],
                "vehicle_number" => $key['vehicle_number'],
                "title" => $key['title'],
                "description" => $key['description'],
            ]);
            $data->subscriptionInclusion()->createMany($key['inclusions']);
        }
    }
}
