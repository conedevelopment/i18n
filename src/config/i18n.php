<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filtering
    |--------------------------------------------------------------------------
    |
    | Select which filtering mode will be enabled. Set to null to have no
    | filtering at all.
    |
    | The available filtering options are 'blacklist' and 'whitelist'.
    |
    */
    'filtering' => null,

    /*
    |--------------------------------------------------------------------------
    | Blacklist
    |--------------------------------------------------------------------------
    |
    | A list of all the translation files that are are going to be available
    | in the frontend. An empty array would return all of the available
    | translations.
    |
    */
    'blacklist' => [],

    /*
    |--------------------------------------------------------------------------
    | Whitelist
    |--------------------------------------------------------------------------
    |
    | A list of all the translation files that are are going to be excluded
    | in the frontend. This means an empty array would disable all frontend
    | translations.
    |
    */
    'whitelist' => [],
];
