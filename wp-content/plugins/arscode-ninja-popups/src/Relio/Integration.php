<?php

namespace Relio;

/**
 * Class Autooptimize
 * This class is responsible for running some tasks to integrate with other plugins, like auto excluding minified css and js files from caching plugins
 * @package Relio
 */
class Integration
{
    protected $tasks = [
        '\Relio\Integration\Autooptimize',
        '\Relio\Integration\Mailinglist',
    ];

    /**
     * Run integration jobs
     */
    public function run()
    {
        foreach ($this->tasks as $task) {
            if (class_exists($task)) {
                $class = new $task;
                if (method_exists($class, 'init')) {
                    $class->init();
                }
            }
        }
    }
}