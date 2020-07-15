<?php

namespace Tests\Browser\Task3;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\TestDataFactory;

class AdminActionsTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_admin_see_own_controls()
    {
        $this->browse(
            function (Browser $browser) {
                $admin = TestDataFactory::createAdminUser();
                $product = TestDataFactory::createProduct($admin);

                $browser
                    ->loginAs($admin)
                    ->visit('/products')
                    ->assertSeeLink('Add')
                    ->visit('/products/' . $product->id)
                    ->assertSeeLink('Edit')
                    ->assertSee('Delete');
            }
        );
    }

    public function test_admin_see_other_user_controls()
    {
        $this->browse(
            function (Browser $browser) {
                $admin = TestDataFactory::createAdminUser();
                $user = TestDataFactory::createUser();
                $product = TestDataFactory::createProduct($user);

                $browser
                    ->loginAs($admin)
                    ->visit('/products/' . $product->id)
                    ->assertSeeLink('Edit')
                    ->assertSee('Delete');
            }
        );
    }

    public function test_admin_can_visit_add_product_page()
    {
        $this->browse(
            function (Browser $browser) {
                $admin = TestDataFactory::createAdminUser();

                $browser
                    ->loginAs($admin)
                    ->visit('/products/add')
                    ->assertPathIs('/products/add');
            }
        );
    }

    public function test_admin_can_visit_own_edit_product_page()
    {
        $this->browse(
            function (Browser $browser) {
                $admin = TestDataFactory::createAdminUser();
                $product = TestDataFactory::createProduct($admin);

                $browser
                    ->loginAs($admin)
                    ->visit('/products/' . $product->id . '/edit')
                    ->assertPathIs('/products/' . $product->id . '/edit');
            }
        );
    }

    public function test_admin_can_visit_other_user_edit_product_page()
    {
        $this->browse(
            function (Browser $browser) {
                $admin = TestDataFactory::createAdminUser();
                $user = TestDataFactory::createUser();
                $product = TestDataFactory::createProduct($user);

                $browser
                    ->loginAs($admin)
                    ->visit('/products/' . $product->id . '/edit')
                    ->assertPathIs('/products/' . $product->id . '/edit');
            }
        );
    }

    public function test_admin_can_add_product()
    {
        $this->browse(
            function (Browser $browser) {
                $admin = TestDataFactory::createAdminUser();

                $browser
                    ->loginAs($admin)
                    ->visit('/products/add')
                    ->value('input[name=name]', 'Admin Add Test Product')
                    ->value('input[name=price]', '49.99')
                    ->press('Save')
                    ->assertPathIs('/products');
            }
        );

        $this->assertDatabaseHas('products', [
            'name' => 'Admin Add Test Product',
            'price' => 49.99
        ]);
    }

    public function test_admin_can_update_product()
    {
        $this->browse(
            function (Browser $browser) {
                $admin = TestDataFactory::createAdminUser();
                $product = TestDataFactory::createProduct($admin);

                $browser
                    ->loginAs($admin)
                    ->visit('/products/' . $product->id . '/edit')
                    ->assertSee('Save')
                    ->value('input[name=name]', 'Admin Update Test Product')
                    ->value('input[name=price]', '39.99')
                    ->press('Save')
                    ->assertPathIs('/products/' . $product->id);
            }
        );

        $this->assertDatabaseHas('products', [
            'name' => 'Admin Update Test Product',
            'price' => 39.99
        ]);
    }

    public function test_admin_can_delete_product()
    {
        $admin = TestDataFactory::createAdminUser();
        $product = TestDataFactory::createProduct($admin);

        $this->browse(
            function (Browser $browser) use ($admin, $product) {
                $browser
                    ->loginAs($admin)
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