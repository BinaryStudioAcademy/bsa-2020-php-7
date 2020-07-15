<?php

namespace Tests\Browser\Task1;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MainPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testMainPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Marketplace');
        });
    }
}
