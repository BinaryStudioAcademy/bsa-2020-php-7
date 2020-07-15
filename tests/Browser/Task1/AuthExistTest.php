<?php

namespace Tests\Browser\Task1;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestDataFactory;

class AuthExistTest extends DuskTestCase
{
    use DatabaseMigrations;

    const USER_PASSWORD = 'password';

    public function testMainPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSeeLink('Login')
                ->assertSeeLink('Register')
                ->clickLink('Login')
                ->assertPathIs('/login')
                ->back()
                ->clickLink('Register')
                ->assertPathIs('/register');
        });
    }

    public function test_login()
    {
        $this->browse(
            function (Browser $browser) {
                $user = TestDataFactory::createUser();
                $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', self::USER_PASSWORD)
                    ->press('Login')
                    ->assertPathIs('/products')
                    ->assertDontSeeLink('Login');
            }
        );
    }

    public function test_registration()
    {
        $this->browse(
            function (Browser $browser) {
                $browser->visit('/register')
                    ->assertSee('Register')
                    ->type('name', 'Will Smith')
                    ->type('email', 'will@example.com')
                    ->value('input[name=password]', self::USER_PASSWORD)
                    ->value('input[name=password_confirmation]', self::USER_PASSWORD)
                    ->press('Register')
                    ->assertPathIs('/products')
                    ->assertDontSeeLink('Register');
            }
        );
        $this->assertDatabaseHas('users', [
            'name' => 'Will Smith',
            'email' => 'will@example.com',
        ]);
    }
}
