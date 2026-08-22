<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
     {
          $products = [
              [
                  'name' => 'Signature Caramel Macchiato',
                  'category' => 'Coffee',
                  'price' => 4.99,
                  'stock' => 99,
                  'description' => 'Rich espresso combined with milk and sweet caramel syrup, served hot or iced with caramel drizzle.',
                  'image_url' => 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?auto=format&fit=crop&w=400&q=80',
                  'is_featured' => true,
              ],
              [
                  'name' => 'Classic Caffè Latte',
                  'category' => 'Coffee',
                  'price' => 3.75,
                  'stock' => 150,
                  'description' => 'Perfectly pulled double shot of espresso topped with velvety steamed milk and a thin layer of micro-foam.',
                  'image_url' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=400&q=80',
                  'is_featured' => false,
              ],
              [
                  'name' => 'Vanilla Cream Cold Brew',
                  'category' => 'Tea & Beverages',
                  'price' => 4.50,
                  'stock' => 80,
                  'description' => 'Slow-steeped cold brew coffee sweetened with vanilla syrup and topped with house-made sweet cream.',
                  'image_url' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=400&q=80',
                  'is_featured' => true,
              ],
              [
                  'name' => 'Iced Matcha Oat Latte',
                  'category' => 'Tea & Beverages',
                  'price' => 5.25,
                  'stock' => 60,
                  'description' => 'Ceremonial grade stone-ground Japanese matcha whisked with creamy oat milk and a touch of organic honey.',
                  'image_url' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?auto=format&fit=crop&w=400&q=80',
                  'is_featured' => false,
              ],
              [
                  'name' => 'Butter Croissant',
                  'category' => 'Pastries',
                  'price' => 3.25,
                  'stock' => 25,
                  'description' => 'Traditional French laminated pastry, baked to a golden brown with flaky, buttery layers.',
                  'image_url' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=400&q=80',
                  'is_featured' => true,
              ],
              [
                  'name' => 'Double Chocolate Muffin',
                  'category' => 'Pastries',
                  'price' => 3.50,
                  'stock' => 30,
                  'description' => 'Decadent chocolate muffin filled with premium dark chocolate chunks, served warm.',
                  'image_url' => 'https://images.unsplash.com/photo-1607958996333-41aef7caefaa?auto=format&fit=crop&w=400&q=80',
                  'is_featured' => false,
              ],
              [
                  'name' => 'Ethiopian Yirgacheffe Beans',
                  'category' => 'Coffee Beans',
                  'price' => 18.00,
                  'stock' => 40,
                  'description' => 'Single-origin whole bean coffee with delicate floral, bright citrus notes, and a tea-like body. Medium roast.',
                  'image_url' => 'https://images.unsplash.com/photo-1587917468748-02c5f11b22e0?auto=format&fit=crop&w=400&q=80',
                  'is_featured' => false,
              ]
          ];

          foreach ($products as $product) {
              Product::create($product);
          }
     }
}
