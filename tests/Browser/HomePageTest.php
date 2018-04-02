<?php

namespace Tests\Browser;

use Tests\Browser\Pages\HomePage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LandingPageTest extends DuskTestCase
{
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
