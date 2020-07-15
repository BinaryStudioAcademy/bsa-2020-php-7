<?php

namespace Tests;

use App\Entity\User;
use App\Entity\Product;

class TestDataFactory
{
    public static function createUser(): User
    {
        return factory(User::class)->create();
    }

    public static function createProduct(User $user): Product
    {
        return factory(Product::class)
            ->create(['user_id' => $user->id]);
    }

    public static function createAdminUser(): User
    {
        return factory(User::class)->create([
            'is_admin' => true
        ]);
    }
}