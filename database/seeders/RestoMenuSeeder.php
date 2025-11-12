<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RestoMenu;
use Illuminate\Support\Str;

class RestoMenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            // =====================
            // 🍛 MAIN COURSE (6)
            // =====================
            [
                'name' => 'Adobo',
                'description' => 'Classic pork or chicken braised in soy sauce, vinegar, garlic, and bay leaves.',
                'price' => 180.00,
                'category' => 'Main Course',
                'main_ingredients' => ['pork', 'soy sauce', 'vinegar', 'garlic', 'bay leaves'],
                'allergens' => ['soy'],
                'cost_price' => 90.00,
                'production_cost' => ['labor' => 20, 'ingredients' => 70],
            ],
            [
                'name' => 'Kare-Kare',
                'description' => 'Rich peanut stew with oxtail, tripe, and vegetables, served with bagoong.',
                'price' => 260.00,
                'category' => 'Main Course',
                'main_ingredients' => ['oxtail', 'peanut sauce', 'string beans', 'banana heart'],
                'allergens' => ['peanuts', 'soy'],
                'cost_price' => 130.00,
                'production_cost' => ['labor' => 25, 'ingredients' => 105],
            ],
            [
                'name' => 'Sinigang na Baboy',
                'description' => 'Tangy pork belly soup in tamarind broth with assorted vegetables.',
                'price' => 190.00,
                'category' => 'Main Course',
                'main_ingredients' => ['pork belly', 'tamarind', 'kangkong', 'radish', 'tomato'],
                'allergens' => [],
                'cost_price' => 85.00,
                'production_cost' => ['labor' => 18, 'ingredients' => 67],
            ],
            [
                'name' => 'Bistek Tagalog',
                'description' => 'Filipino-style beef steak marinated in soy sauce and calamansi, topped with onions.',
                'price' => 210.00,
                'category' => 'Main Course',
                'main_ingredients' => ['beef sirloin', 'soy sauce', 'calamansi', 'onion'],
                'allergens' => ['soy'],
                'cost_price' => 95.00,
                'production_cost' => ['labor' => 22, 'ingredients' => 73],
            ],
            [
                'name' => 'Lechon Kawali',
                'description' => 'Crispy deep-fried pork belly served with liver sauce.',
                'price' => 230.00,
                'category' => 'Main Course',
                'main_ingredients' => ['pork belly', 'garlic', 'salt', 'pepper'],
                'allergens' => [],
                'cost_price' => 100.00,
                'production_cost' => ['labor' => 20, 'ingredients' => 80],
            ],
            [
                'name' => 'Chicken Inasal',
                'description' => 'Grilled chicken marinated in calamansi, lemongrass, garlic, and annatto oil.',
                'price' => 175.00,
                'category' => 'Main Course',
                'main_ingredients' => ['chicken', 'lemongrass', 'calamansi', 'garlic', 'annatto'],
                'allergens' => [],
                'cost_price' => 80.00,
                'production_cost' => ['labor' => 18, 'ingredients' => 62],
            ],

            // =====================
            // 🍚 RICE (3)
            // =====================
            [
                'name' => 'Garlic Fried Rice',
                'description' => 'Classic sinangag rice sautéed with garlic bits.',
                'price' => 40.00,
                'category' => 'Rice',
                'main_ingredients' => ['rice', 'garlic', 'oil'],
                'allergens' => [],
                'cost_price' => 15.00,
                'production_cost' => ['labor' => 5, 'ingredients' => 10],
            ],
            [
                'name' => 'Plain Steamed Rice',
                'description' => 'Freshly steamed white rice.',
                'price' => 25.00,
                'category' => 'Rice',
                'main_ingredients' => ['rice', 'water'],
                'allergens' => [],
                'cost_price' => 10.00,
                'production_cost' => ['labor' => 3, 'ingredients' => 7],
            ],
            [
                'name' => 'Java Rice',
                'description' => 'Yellow rice infused with annatto and butter.',
                'price' => 50.00,
                'category' => 'Rice',
                'main_ingredients' => ['rice', 'annatto', 'butter', 'garlic'],
                'allergens' => ['dairy'],
                'cost_price' => 18.00,
                'production_cost' => ['labor' => 5, 'ingredients' => 13],
            ],

            // =====================
            // 🍰 DESSERTS (4)
            // =====================
            [
                'name' => 'Leche Flan',
                'description' => 'Creamy caramel custard dessert made with egg yolks and milk.',
                'price' => 90.00,
                'category' => 'Dessert',
                'main_ingredients' => ['egg yolk', 'condensed milk', 'caramelized sugar'],
                'allergens' => ['egg', 'dairy'],
                'cost_price' => 35.00,
                'production_cost' => ['labor' => 10, 'ingredients' => 25],
            ],
            [
                'name' => 'Halo-Halo',
                'description' => 'Shaved ice dessert with sweetened fruits, jellies, beans, and ice cream.',
                'price' => 120.00,
                'category' => 'Dessert',
                'main_ingredients' => ['milk', 'ube', 'sweetened beans', 'nata de coco', 'leche flan'],
                'allergens' => ['dairy'],
                'cost_price' => 55.00,
                'production_cost' => ['labor' => 15, 'ingredients' => 40],
            ],
            [
                'name' => 'Turon',
                'description' => 'Banana and jackfruit wrapped in lumpia wrapper, fried with caramelized sugar.',
                'price' => 65.00,
                'category' => 'Dessert',
                'main_ingredients' => ['banana', 'jackfruit', 'sugar', 'wrapper'],
                'allergens' => ['gluten'],
                'cost_price' => 25.00,
                'production_cost' => ['labor' => 8, 'ingredients' => 17],
            ],
            [
                'name' => 'Bibingka',
                'description' => 'Baked rice cake topped with salted egg and cheese, served warm.',
                'price' => 100.00,
                'category' => 'Dessert',
                'main_ingredients' => ['rice flour', 'egg', 'cheese', 'salted egg'],
                'allergens' => ['egg', 'dairy'],
                'cost_price' => 45.00,
                'production_cost' => ['labor' => 10, 'ingredients' => 35],
            ],

            // =====================
            // 🍹 DRINKS (4)
            // =====================
            [
                'name' => 'Iced Tea',
                'description' => 'Refreshing sweet iced tea with lemon flavor.',
                'price' => 45.00,
                'category' => 'Drinks',
                'main_ingredients' => ['tea', 'lemon', 'sugar'],
                'allergens' => [],
                'cost_price' => 10.00,
                'production_cost' => ['labor' => 2, 'ingredients' => 8],
            ],
            [
                'name' => 'Buko Juice',
                'description' => 'Fresh coconut water served chilled.',
                'price' => 50.00,
                'category' => 'Drinks',
                'main_ingredients' => ['coconut water', 'coconut meat'],
                'allergens' => [],
                'cost_price' => 15.00,
                'production_cost' => ['labor' => 3, 'ingredients' => 12],
            ],
            [
                'name' => 'Calamansi Juice',
                'description' => 'Sweet and tangy local lemon drink, served cold.',
                'price' => 55.00,
                'category' => 'Drinks',
                'main_ingredients' => ['calamansi', 'water', 'sugar'],
                'allergens' => [],
                'cost_price' => 20.00,
                'production_cost' => ['labor' => 5, 'ingredients' => 15],
            ],
            [
                'name' => 'Sago’t Gulaman',
                'description' => 'Classic Filipino drink with tapioca pearls and jelly in caramel syrup.',
                'price' => 60.00,
                'category' => 'Drinks',
                'main_ingredients' => ['sago', 'gulaman', 'brown sugar', 'vanilla'],
                'allergens' => [],
                'cost_price' => 22.00,
                'production_cost' => ['labor' => 6, 'ingredients' => 16],
            ],
        ];

        foreach ($menus as $menu) {
            RestoMenu::create([
                'menu_id' => 'MENU-' . strtoupper(Str::random(8)),
                'name' => $menu['name'],
                'description' => $menu['description'],
                'price' => $menu['price'],
                'category' => $menu['category'],
                'stock_quantity' => rand(20, 100),
                'is_available' => true,
                'main_ingredients' => json_encode($menu['main_ingredients']),
                'allergens' => json_encode($menu['allergens']),
                'cost_price' => $menu['cost_price'],
                'production_cost' => json_encode($menu['production_cost']),
                'number_of_orders' => json_encode([]),
                'image_url' => null,
            ]);
        }
    }
}
