<?php

namespace Relio\Integration;

/**
 * Class Autooptimize
 * @package Relio\Integration
 */
class Autooptimize
{
    public function init()
    {
        add_filter('autoptimize_filter_js_exclude', [$this, 'excludeJavascript'], 10, 1);
        add_filter('autoptimize_filter_css_exclude', [$this, 'excludeCss'], 10, 1);
    }

    /**
     * @param $exclude
     * @return string
     */
    protected function excludeJavascript($exclude)
    {
        $exclusion = [
            'wp-content/plugins/arscode-ninja-popups/cookie.min.js',
            'wp-content/plugins/arscode-ninja-popups/jquery.material.form.min.js',
            'wp-content/plugins/arscode-ninja-popups/jquery.mb.YTPlayer.min.js',
            'wp-content/plugins/arscode-ninja-popups/ninjapopups.drip.min.js',
            'wp-content/plugins/arscode-ninja-popups/ninjapopups.learnq.min.js',
            'wp-content/plugins/arscode-ninja-popups/ninjapopups.metrilo.min.js',
            'wp-content/plugins/arscode-ninja-popups/ninjapopups-2.0.min.js',
            'wp-content/plugins/arscode-ninja-popups/tooltipster.bundle.min.js',
        ];

        return trim($exclude . ', ' . implode(', ', $exclusion), ', ');
    }

    /**
     * @param $exclude
     * @return string
     */
    protected function excludeCss($exclude)
    {
        $exclusion = [

        ];

        return trim($exclude . ', ' . implode(', ', $exclusion), ', ');
    }
}