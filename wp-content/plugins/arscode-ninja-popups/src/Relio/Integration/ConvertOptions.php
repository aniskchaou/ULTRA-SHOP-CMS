<?php

namespace Relio\Integration;

/**
 * Class ConvertOptions
 * @package Relio\Integration
 */
class ConvertOptions
{
    public function init()
    {
        $migrationStatus = get_option(S);

        //if () {
            $oldOptions = get_option(SNP_OPTIONS_MIGRATE_FROM);
            $newOptions = [];

            foreach ($oldOptions as $key => $value) {

            }

            //update_option(SNP_OPTIONS_MIGRATE_TO, $newOptions);
        //}
    }
}