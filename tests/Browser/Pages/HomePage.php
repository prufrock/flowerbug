<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class HomePage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@banner' => '#banner',
            '@buttons' => '#buttons',
            '@first-project' => 'div.project:nth-child(2)'
        ];
    }
}
