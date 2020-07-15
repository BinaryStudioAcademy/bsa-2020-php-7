<?php

use Illuminate\Database\Seeder;
use App\Entity\Product;
use App\Entity\User;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Product::class, 30)->create();
    }
}
