<?php

namespace Tests\Browser;

use Tests\Browser\Pages\HomePage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LandingPageTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomePage())
                ->assertVisible('@banner')
                ->assertVisible('@buttons')
                ->assertVisible('@first-project');
        });
    }
}
