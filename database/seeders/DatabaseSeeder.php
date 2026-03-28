<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->delete();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@nexium.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $goldenFork = Restaurant::create([
            'name' => 'The Golden Fork',
            'address' => '12 Main Street, Lahore',
            'phone' => '+92-300-1234567',
            'description' => 'Fine dining restaurant with a warm ambiance',
            'is_active' => true,
        ]);

        $spiceGarden = Restaurant::create([
            'name' => 'Spice Garden',
            'address' => '45 Liberty Market, Lahore',
            'phone' => '+92-321-9876543',
            'description' => 'Authentic desi cuisine in a cozy setting',
            'is_active' => true,
        ]);

        $goldenT1 = RestaurantTable::create([
            'restaurant_id' => $goldenFork->id,
            'table_number' => '1',
            'capacity' => 2,
            'is_active' => true,
        ]);

        $goldenT2 = RestaurantTable::create([
            'restaurant_id' => $goldenFork->id,
            'table_number' => '2',
            'capacity' => 4,
            'is_active' => true,
        ]);

        RestaurantTable::create([
            'restaurant_id' => $goldenFork->id,
            'table_number' => '3',
            'capacity' => 6,
            'is_active' => true,
        ]);

        $spiceT1 = RestaurantTable::create([
            'restaurant_id' => $spiceGarden->id,
            'table_number' => '1',
            'capacity' => 4,
            'is_active' => true,
        ]);

        $spiceT2 = RestaurantTable::create([
            'restaurant_id' => $spiceGarden->id,
            'table_number' => '2',
            'capacity' => 8,
            'is_active' => true,
        ]);

        $ali = Customer::create([
            'name' => 'Ali Hassan',
            'email' => 'ali@example.com',
            'phone' => '+92-311-1111111',
        ]);

        $sara = Customer::create([
            'name' => 'Sara Khan',
            'email' => 'sara@example.com',
            'phone' => '+92-322-2222222',
        ]);

        $usman = Customer::create([
            'name' => 'Usman Raza',
            'email' => 'usman@example.com',
            'phone' => '+92-333-3333333',
        ]);

        $tomorrow = Carbon::tomorrow()->setTime(19, 0);
        $dayAfterTomorrow = Carbon::tomorrow()->addDay()->setTime(20, 0);
        $yesterday = Carbon::yesterday()->setTime(18, 30);
        $threeDaysFromNow = Carbon::now()->addDays(3)->setTime(19, 0);

        Reservation::create([
            'customer_id' => $ali->id,
            'table_id' => $goldenT2->id,
            'party_size' => 3,
            'reservation_date' => $tomorrow,
            'status' => 'confirmed',
        ]);

        Reservation::create([
            'customer_id' => $sara->id,
            'table_id' => $spiceT2->id,
            'party_size' => 6,
            'reservation_date' => $dayAfterTomorrow,
            'status' => 'pending',
        ]);

        Reservation::create([
            'customer_id' => $usman->id,
            'table_id' => $goldenT1->id,
            'party_size' => 2,
            'reservation_date' => $yesterday,
            'status' => 'completed',
        ]);

        Reservation::create([
            'customer_id' => $ali->id,
            'table_id' => $spiceT1->id,
            'party_size' => 4,
            'reservation_date' => $threeDaysFromNow,
            'status' => 'cancelled',
        ]);
    }
}
