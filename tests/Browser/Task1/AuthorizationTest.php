<?php

namespace Tests\Browser\Task1;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\TestDataFactory;

class AuthorizationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_unauthorized_dont_see_pages()
    {
        $this->browse(
            function (Browser $browser) {
                $user = TestDataFactory::createUser();
                $product = TestDataFactory::createProduct($user);
                $browser->visit('/products')
                    ->assertPathIs('/login');
                $browser->visit('/products/' . $product->id)
                    ->assertPathIs('/login');
                $browser->visit('/products/' . $product->id . '/edit')
                    ->assertPathIs('/login');
                $browser->visit('/products/add')
                    ->assertPathIs('/login');
            }
        );
    }

    public function test_authorized_see_pages()
    {
        $this->browse(
            function (Browser $browser) {
                $user = TestDataFactory::createUser();
                $product = TestDataFactory::createProduct($user);
                $browser->loginAs($user)
                    ->visit('/products')
                    ->assertPathIs('/products')
                    ->visit('/products/' . $product->id)
                    ->assertPathIs('/products/' . $product->id);
            }
        );
    }
}