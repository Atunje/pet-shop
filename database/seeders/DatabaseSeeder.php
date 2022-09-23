<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderStatus;
use App\Models\Post;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //create the test admin
        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'email' => 'test_admin@example.com',
            'is_admin' => true,
            'password' => Hash::make('admin'), //admin
        ]);

        //promotion seeders
        Promotion::factory(20)->create();

        //blog seeders
        Post::factory(25)->create();

        //category seeders
        Category::factory(10)->create();

        //brands seeders
        Brand::factory(5)->create();

        //product seeders
        Product::factory(25)->create();

        //order status seeders
        $statuses = ['open', 'pending payment', 'paid', 'shipped', 'cancelled'];
        foreach ($statuses as $status) {
            OrderStatus::factory()->create(['title' => $status]);
        }

        User::factory()->count(10)->hasOrders(50)->create();
    }
}
