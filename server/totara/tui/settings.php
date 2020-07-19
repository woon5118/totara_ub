<?php

defined('MOODLE_INTERNAL') || die();
/* @var admin_root $ADMIN */

if ($ADMIN->fulltree) {
    $settings = new admin_settingpage('totara_tui_settings', new lang_string('pluginname', 'totara_tui'));
    $settings->add(
        new admin_setting_configcheckbox(
            'totara_tui/cache_js',
            new lang_string('setting_cache_js', 'totara_tui'),
            new lang_string('setting_cache_js_desc', 'totara_tui'),
            '1'
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            'totara_tui/cache_scss',
            new lang_string('setting_cache_scss', 'totara_tui'),
            new lang_string('setting_cache_scss_desc', 'totara_tui'),
            '1'
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            'totara_tui/development_mode',
            new lang_string('setting_development_mode', 'totara_tui'),
            new lang_string('setting_development_mode_desc', 'totara_tui'),
            '0'
        )
    );
    $ADMIN->add('appearance', $settings);
}