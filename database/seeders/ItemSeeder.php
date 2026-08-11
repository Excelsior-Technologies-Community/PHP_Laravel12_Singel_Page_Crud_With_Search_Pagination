<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Item;
use App\Models\ActivityLog;
use App\Models\User;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->warn('No user found. Please run UserSeeder first.');
            return;
        }

        $items = [
            ['name' => 'Wireless Headphones', 'description' => 'High-quality Bluetooth headphones with noise cancellation', 'category' => 'Electronics', 'status' => 'active', 'price' => 79.99],
            ['name' => 'Running Shoes', 'description' => 'Lightweight comfortable running shoes for daily training', 'category' => 'Sports', 'status' => 'active', 'price' => 129.99],
            ['name' => 'Coffee Maker', 'description' => 'Automatic drip coffee maker with programmable timer', 'category' => 'Home', 'status' => 'active', 'price' => 59.99],
            ['name' => 'JavaScript Book', 'description' => 'Comprehensive guide to modern JavaScript programming', 'category' => 'Books', 'status' => 'active', 'price' => 39.99],
            ['name' => 'Cotton T-Shirt', 'description' => 'Premium cotton t-shirt, comfortable fit', 'category' => 'Clothing', 'status' => 'inactive', 'price' => 24.99],
            ['name' => 'Smart Watch', 'description' => 'Fitness tracker with heart rate monitor and GPS', 'category' => 'Electronics', 'status' => 'active', 'price' => 199.99],
            ['name' => 'Yoga Mat', 'description' => 'Non-slip yoga mat with carrying strap', 'category' => 'Sports', 'status' => 'active', 'price' => 29.99],
            ['name' => 'Desk Lamp', 'description' => 'LED desk lamp with adjustable brightness', 'category' => 'Home', 'status' => 'active', 'price' => 34.99],
            ['name' => 'Python Programming', 'description' => 'Learn Python from beginner to advanced level', 'category' => 'Books', 'status' => 'active', 'price' => 44.99],
            ['name' => 'Denim Jacket', 'description' => 'Classic denim jacket for casual wear', 'category' => 'Clothing', 'status' => 'active', 'price' => 89.99],
            ['name' => 'Wireless Mouse', 'description' => 'Ergonomic wireless mouse with long battery life', 'category' => 'Electronics', 'status' => 'active', 'price' => 29.99],
            ['name' => 'Basketball', 'description' => 'Official size basketball for indoor/outdoor use', 'category' => 'Sports', 'status' => 'inactive', 'price' => 19.99],
            ['name' => 'Blender', 'description' => 'High-speed blender for smoothies and shakes', 'category' => 'Home', 'status' => 'active', 'price' => 69.99],
            ['name' => 'Fiction Novel', 'description' => 'Bestselling mystery novel with thrilling plot', 'category' => 'Books', 'status' => 'active', 'price' => 14.99],
            ['name' => 'Winter Jacket', 'description' => 'Warm winter jacket with waterproof coating', 'category' => 'Clothing', 'status' => 'active', 'price' => 149.99],
        ];

        foreach ($items as $index => $data) {
            $imageUrl = "https://picsum.photos/seed/item{$index}/400/300";
            
            try {
                $response = Http::timeout(30)->get($imageUrl);
                if ($response->successful()) {
                    $imageName = 'item_' . time() . '_' . $index . '.jpg';
                    file_put_contents(public_path('image/' . $imageName), $response->body());
                    $data['image'] = 'image/' . $imageName;
                }
            } catch (\Exception $e) {
                $data['image'] = null;
            }

            $data['user_id'] = $user->id;

            $item = Item::create($data);
            ActivityLog::log('created', $item, "Seeded item: {$item->name}", null, $item->toArray());
        }

        echo "15 items seeded successfully!\n";
    }
}
