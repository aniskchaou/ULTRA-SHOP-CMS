<?php

namespace Relio;

use Relio\OptionsController;

/**
 * Class BackendController
 * @package Relio
 */
class BackendController
{
    /**
     * Run integration jobs
     */
    public function run()
    {
        $this->createOptionsPage();
    }

    /**
     * Build options page
     */
    private function createOptionsPage()
    {
        add_action('csf_init', [OptionsController::class, 'init']);
    }
}