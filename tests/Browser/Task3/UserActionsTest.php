<?php

namespace Tests\Browser\Task3;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\TestDataFactory;

class UserActionsTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_see_own_controls()
    {
        $this->browse(
            function (Browser $browser) {
                $user = TestDataFactory::createUser();
                $product = TestDataFactory::createProduct($user);

                $browser
                    ->loginAs($user)
                    ->visit('/products')
                    ->assertSeeLink('Add')
                    ->visit('/products/' . $product->id)
                    ->assertSeeLink('Edit')
                    ->assertSee('Delete');
            }
        );
    }

    public function test_user_dont_see_other_user_controls()
    {
        $this->browse(
            function (Browser $browser) {
                $user = TestDataFactory::createUser();
                $anotherUser = TestDataFactory::createUser();
                $product = TestDataFactory::createProduct($anotherUser);

                $browser
                    ->loginAs($user)
                    ->visit('/products/' . $product->id)
                    ->assertDontSee('Edit')
                    ->assertDontSee('Delete');
            }
        );
    }

    public function test_user_can_visit_add_product_page()
    {
        $this->browse(
            function (Browser $browser) {
                $user = TestDataFactory::createUser();

                $browser
                    ->loginAs($user)
                    ->visit('/products/add')
                    ->assertPathIs('/products/add');
            }
        );
    }

    public function test_user_can_visit_own_edit_product_page()
    {
        $this->browse(
            function (Browser $browser) {
                $user = TestDataFactory::createUser();
                $product = TestDataFactory::createProduct($user);

                $browser
                    ->loginAs($user)
                    ->visit('/products/' . $product->id . '/edit')
                    ->assertPathIs('/products/' . $product->id . '/edit');
            }
        );
    }

    public function test_user_cant_visit_other_user_edit_product_page()
    {
        $this->browse(
            function (Browser $browser) {
                $user = TestDataFactory::createUser();
                $anotherUser = TestDataFactory::createUser();
                $product = TestDataFactory::createProduct($anotherUser);

                $browser
                    ->loginAs($user)
                    ->visit('/products/' . $product->id . '/edit')
                    ->assertPathIsNot('/products/' . $product->id . '/edit');
            }
        );
    }

    public function test_user_can_add_product()
    {
        $this->browse(
            function (Browser $browser) {
                $user = TestDataFactory::createUser();

                $browser
                    ->loginAs($user)
                    ->visit('/products/add')
                    ->value('input[name=name]', 'User Add Test Product')
                    ->value('input[name=price]', '49.99')
                    ->press('Save')
                    ->assertPathIs('/products');
            }
        );

        $this->assertDatabaseHas('products', [
            'name' => 'User Add Test Product',
            'price' => 49.99
        ]);
    }

    public function test_user_can_update_product()
    {
        $this->browse(
            function (Browser $browser) {
                $user = TestDataFactory::createAdminUser();
                $product = TestDataFactory::createProduct($user);

                $browser
                    ->loginAs($user)
                    ->visit('/products/' . $product->id . '/edit')
                    ->assertSee('Save')
                    ->value('input[name=name]', 'User Update Test Product')
                    ->value('input[name=price]', '39.99')
                    ->press('Save')
                    ->assertPathIs('/products/' . $product->id);
            }
        );

        $this->assertDatabaseHas('products', [
            'name' => 'User Update Test Product',
            'price' => 39.99
        ]);
    }

    public function test_user_can_delete_product()
    {
        $user = TestDataFactory::createAdminUser();
        $product = TestDataFactory::createProduct($user);

        $this->browse(
            function (Browser $browser) use ($user, $product) {
                $browser
                    ->loginAs($user)
                    ->visit('/products/' . $product->id)
                    ->assertSee('Delete')
                    ->press('Delete')
                    ->assertPathIs('/products');
            }
        );

        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }
}