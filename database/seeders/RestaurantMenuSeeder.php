<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RestaurantMenu;

class RestaurantMenuSeeder extends Seeder
{
    public function run(): void
    {
        RestaurantMenu::create([
            'name'         => 'Nasi Goreng Spesial Neo',
            'category'     => 'food',
            'price'        => 45000,
            'description'  => 'Nasi goreng autentik dengan topping udang windu, telur mata sapi, ayam suwir, dan kerupuk udang renyah.',
            'foto_url'     => 'img/food-1.jpg',
            'is_available' => true,
            'prep_time'    => 15,
            'calories'     => 650,
            'allergens'    => 'Shrimp, Egg',
            'serving'      => '1 Person',
            'rating'       => 4.9
        ]);

        RestaurantMenu::create([
            'name'         => 'Iced Lychee Tea',
            'category'     => 'drink',
            'price'        => 25000,
            'description'  => 'Teh premium yang diseduh dingin dengan sirup leci dan buah leci utuh yang manis dan segar.',
            'foto_url'     => 'img/drink-1.jpg',
            'is_available' => true,
            'prep_time'    => 5,
            'calories'     => 120,
            'allergens'    => 'None',
            'serving'      => '1 Glass',
            'rating'       => 4.8
        ]);

        RestaurantMenu::create([
            'name'         => 'Chocolate Lava Cake',
            'category'     => 'dessert',
            'price'        => 35000,
            'description'  => 'Kue cokelat Belgia dengan bagian tengah yang lumer, disajikan hangat bersama es krim vanilla premium.',
            'foto_url'     => 'img/dessert-1.jpg',
            'is_available' => true,
            'prep_time'    => 20,
            'calories'     => 450,
            'allergens'    => 'Dairy, Gluten, Egg',
            'serving'      => '1 Portion',
            'rating'       => 5.0
        ]);

        RestaurantMenu::create([
            'name'         => 'Truffle French Fries',
            'category'     => 'snack',
            'price'        => 38000,
            'description'  => 'Kentang goreng potong tebal dengan aroma minyak truffle yang mewah dan taburan keju parmesan.',
            'foto_url'     => 'img/snack-1.jpg',
            'is_available' => true,
            'prep_time'    => 10,
            'calories'     => 320,
            'allergens'    => 'Cheese',
            'serving'      => 'Shared Plate',
            'rating'       => 4.7
        ]);
    }
}