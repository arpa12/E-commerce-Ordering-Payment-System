<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'is_admin' => true,
        ]);

        // 2. Create Sample Customer User
        User::create([
            'name' => 'John Doe',
            'email' => 'customer@example.com',
            'password' => Hash::make('secret123'),
            'is_admin' => false,
        ]);

        // 3. Create Sample Categories
        $electronics = Category::create(['name' => 'Electronics']);
        $clothing = Category::create(['name' => 'Clothing']);
        $accessories = Category::create(['name' => 'Accessories']);

        // Create child categories
        $audio = Category::create(['name' => 'Audio & Speakers', 'parent_id' => $electronics->id]);
        $wearables = Category::create(['name' => 'Smart Wearables', 'parent_id' => $electronics->id]);

        // 4. Create Sample Products
        Product::create([
            'category_id' => $audio->id,
            'name' => 'Premium Wireless Earbuds',
            'sku' => 'PROD-EARBUDS',
            'description' => 'Noise-cancelling bluetooth earbuds with deep bass and 30-hour battery life.',
            'price' => 79.99,
            'stock' => 50,
            'status' => 'active',
        ]);

        Product::create([
            'category_id' => $wearables->id,
            'name' => 'Glow Smart Watch',
            'sku' => 'PROD-WATCH',
            'description' => 'Fitness smartwatch tracking heart rate, sleep, and sports activities with AMOLED screen.',
            'price' => 129.50,
            'stock' => 30,
            'status' => 'active',
        ]);

        Product::create([
            'category_id' => $clothing->id,
            'name' => 'Vintage Denim Jacket',
            'sku' => 'PROD-JACKET',
            'description' => 'Comfortable denim jacket with button closure and soft cotton fabric.',
            'price' => 59.00,
            'stock' => 20,
            'status' => 'active',
        ]);

        Product::create([
            'category_id' => $accessories->id,
            'name' => 'Leather Travel Wallet',
            'sku' => 'PROD-WALLET',
            'description' => 'Genuine leather wallet with multiple card slots and passport compartment.',
            'price' => 35.00,
            'stock' => 15,
            'status' => 'active',
        ]);

        Product::create([
            'category_id' => $audio->id,
            'name' => 'Glow Headset RGB',
            'sku' => 'PROD-HEADSET',
            'description' => 'RGB gaming headset with 7.1 surround sound and noise-isolating microphone.',
            'price' => 110.00,
            'stock' => 10,
            'status' => 'active',
        ]);
    }
}

