<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Events',
    'version_from' => '11.0.0',
    'version_to' => '11.0.1',
    'vendor' => 'BoonEx',

    'compatible_with' => array(
        '11.0.0-RC1'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/events/updates/update_11.0.0_11.0.1/',
    'home_uri' => 'events_update_1100_1101',

    'module_dir' => 'boonex/events/',
    'module_uri' => 'events',

    'db_prefix' => 'bx_events_',
    'class_prefix' => 'BxEvents',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'execute_sql' => 1,
        'update_files' => 1,
        'update_languages' => 1,
        'clear_db_cache' => 1,
    ),

    /**
     * Category for language keys.
     */
    'language_category' => 'Events',

    /**
     * Files Section
     */
    'delete_files' => array(),
);
