<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package totara_msteams
 */

namespace totara_msteams;

defined('MOODLE_INTERNAL') || die;

use admin_category;
use admin_externalpage;
use admin_root;
use admin_setting_configcheckbox;
use admin_setting_configcolourpicker;
use admin_setting_configpasswordunmask;
use admin_setting_configstoredfile;
use admin_setting_configtext;
use admin_setting_configtext_with_maxlength;
use admin_setting_configtextarea;
use admin_setting_heading;
use admin_settingpage;
use core_message\api as message_api;
use core_plugin_manager;
use lang_string;
use moodle_url;
use totara_core\advanced_feature;
use totara_msteams\botfw\message;

/**
 * Generate admin settings pages.
 */
final class settings_helper {
    public const NS = 'msteams';

    /**
     * @return bool
     */
    private static function get_enable_msteams(): bool {
        return advanced_feature::is_enabled('totara_msteams');
    }

    /**
     * Triggered by the advanced features settings page when the
     * MS Teams feature is toggled on/off.
     */
    public static function advanced_features_callback(): void {
        // Purge any reports
        totara_rb_purge_ignored_reports();

        if (self::get_enable_msteams()) {
            // Enabled, so we need to do something?
            if (!message_api::is_processor_enabled('msteams')) {
                // Enable the message output provider
                $provider = message_api::get_message_processor('msteams');
                message_api::update_processor_status($provider, true);
            }
        } else {
            // Disabled, so clean up anything
            if (message_api::is_processor_enabled('msteams')) {
                // Disable the message output provider
                $provider = message_api::get_message_processor('msteams');
                message_api::update_processor_status($provider, false);
            }
        }

        core_plugin_manager::reset_caches();
    }

    /**
     * Add the admin settings for the totara_msteams plugin.
     *
     * @param admin_root $admin
     */
    public static function load_settings(admin_root $admin): void {
        global $CFG;

        $hideteams = !self::get_enable_msteams();

        $admin->add('root', new admin_category(self::NS, new lang_string('pluginname', 'totara_msteams'), $hideteams));

        $pages = self::load_msteams_settings_pages($admin->fulltree);
        foreach ($pages as $page) {
            $page->hidden = $page->hidden || $hideteams;
            $admin->add('msteams', $page);
        }
    }

    /**
     * Return an array of all admin settings pages for the MS Teams app.
     *
     * @param boolean $withsetting
     * @return admin_settingpage[]|admin_externalpage[]
     */
    private static function load_msteams_settings_pages(bool $withsetting): array {
        return [
            self::load_msteams_settings_teams($withsetting),
            self::load_msteams_settings_app($withsetting),
        ];
    }

    /**
     * Set up page.
     *
     * @param boolean $withsetting
     * @return admin_settingpage
     */
    private static function load_msteams_settings_teams(bool $withsetting): admin_settingpage {
        $page = new admin_settingpage(self::NS.'setup', new lang_string('settings:page_setup', 'totara_msteams'));

        if ($withsetting) {
            // Totara app setup.
            $page->add(new admin_setting_heading('totara_msteams/header_app',
                new lang_string('settings:header_app', 'totara_msteams'),
                new lang_string('settings:header_app_help', 'totara_msteams')));

            // App ID.
            $page->add(new admin_setting_configtext('totara_msteams/manifest_app_id',
                new lang_string('settings:manifest_app_id', 'totara_msteams'),
                new lang_string('settings:manifest_app_id_help', 'totara_msteams'),
                manifest_helper::GUID_NULL));

            // Package name.
            $page->add(new admin_setting_configtext('totara_msteams/manifest_app_package_name',
                new lang_string('settings:manifest_package_name', 'totara_msteams'),
                new lang_string('settings:manifest_package_name_help', 'totara_msteams'),
                new lang_string('settings:manifest_package_name_default', 'totara_msteams'),
                PARAM_TEXT));

            // Single Sign On.
            $page->add(new admin_setting_heading('totara_msteams/header_sso',
                new lang_string('settings:header_sso', 'totara_msteams'),
                new lang_string('settings:header_sso_help', 'totara_msteams')));

            // OAuth2 service.
            $page->add(new admin_setting_oauth2_issuer_select('totara_msteams/oauth2_issuer',
                new lang_string('settings:oauth2_issuer', 'totara_msteams'),
                new lang_string('settings:oauth2_issuer_help', 'totara_msteams', [
                    'authurl' => (new moodle_url('/admin/settings.php?section=manageauths'))->out(),
                    'issuerurl' => (new moodle_url('/admin/tool/oauth2/issuers.php'))->out()
                ]),
                0,
                []
            ));

            // App ID.
            $page->add(new admin_setting_configtext('totara_msteams/sso_app_id',
                new lang_string('settings:sso_app_id', 'totara_msteams'),
                new lang_string('settings:sso_app_id_help', 'totara_msteams'),
                manifest_helper::GUID_NULL, PARAM_TEXT));

            // Scope.
            $page->add(new admin_setting_configtext('totara_msteams/sso_scope',
                new lang_string('settings:sso_scope', 'totara_msteams'),
                new lang_string('settings:sso_scope_help', 'totara_msteams'),
                '', PARAM_TEXT));

            // Teams Bot settings.
            $page->add(new admin_setting_heading('totara_msteams/header_bot',
                new lang_string('settings:header_bot', 'totara_msteams'),
                new lang_string('settings:header_bot_help', 'totara_msteams')));

            // Enable bot feature.
            $page->add(new admin_setting_configcheckbox('totara_msteams/bot_feature_enabled',
                new lang_string('settings:bot_feature_enabled', 'totara_msteams'),
                new lang_string('settings:bot_feature_enabled_help', 'totara_msteams'),
                '0'));

            // Enable messaging extension.
            $page->add(new admin_setting_configcheckbox('totara_msteams/messaging_extension_enabled',
                new lang_string('settings:messaging_extension_enabled', 'totara_msteams'),
                new lang_string('settings:messaging_extension_enabled_help', 'totara_msteams'),
                '0'));

            // App ID.
            $page->add(new admin_setting_configtext('totara_msteams/bot_app_id',
                new lang_string('settings:bot_app_id', 'totara_msteams'),
                new lang_string('settings:bot_app_id_help', 'totara_msteams'),
                manifest_helper::GUID_NULL, PARAM_TEXT));

            // Client secret.
            $page->add(new admin_setting_configpasswordunmask('totara_msteams/bot_app_secret',
                new lang_string('settings:bot_app_secret', 'totara_msteams'),
                new lang_string('settings:bot_app_secret_help', 'totara_msteams'),
                ''));

            // Customisation.
            $page->add(new admin_setting_heading('totara_msteams/header_branding',
                new lang_string('settings:header_branding', 'totara_msteams'),
                new lang_string('settings:header_branding_help', 'totara_msteams')));

            // App version.
            $page->add(new admin_setting_configtext('totara_msteams/manifest_app_version',
                new lang_string('settings:manifest_app_version', 'totara_msteams'),
                new lang_string('settings:manifest_app_version_help', 'totara_msteams', [
                    'semverurl' => 'https://semver.org/'
                ]),
                new lang_string('settings:manifest_app_version_default', 'totara_msteams')));

            // App name.
            $page->add(new admin_setting_configtext_with_maxlength('totara_msteams/manifest_app_name',
                new lang_string('settings:manifest_app_name', 'totara_msteams'),
                new lang_string('settings:manifest_app_name_help', 'totara_msteams'),
                new lang_string('settings:manifest_app_name_default', 'totara_msteams'),
                PARAM_TEXT, null, \totara_msteams\check\checks\mf_name::MAX_LENGTH));

            // App full name.
            $page->add(new admin_setting_configtext_with_maxlength('totara_msteams/manifest_app_fullname',
                new lang_string('settings:manifest_app_full_name', 'totara_msteams'),
                new lang_string('settings:manifest_app_full_name_help', 'totara_msteams'),
                '',
                PARAM_TEXT, null, \totara_msteams\check\checks\mf_namefull::MAX_LENGTH));

            // App description.
            $page->add(new admin_setting_configtextarea('totara_msteams/manifest_app_description',
                new lang_string('settings:manifest_app_desc', 'totara_msteams'),
                new lang_string('settings:manifest_app_desc_help', 'totara_msteams'),
                new lang_string('settings:manifest_app_desc_default', 'totara_msteams')));

            // App full description.
            $page->add(new admin_setting_configtextarea('totara_msteams/manifest_app_fulldescription',
                new lang_string('settings:manifest_app_fulldesc', 'totara_msteams'),
                new lang_string('settings:manifest_app_fulldesc_help', 'totara_msteams'),
                new lang_string('settings:manifest_app_fulldesc_default', 'totara_msteams')));

            // Colour icon.
            $page->add(new admin_setting_configstoredfile('totara_msteams/manifest_app_icon_color',
                new lang_string('settings:manifest_app_icon_colour', 'totara_msteams'),
                new lang_string('settings:manifest_app_icon_colour_help', 'totara_msteams'),
                'manifest_app_icon_color',
                0,
                ['accepted_types' => ['.png']]));

            // Outline icon.
            $page->add(new admin_setting_configstoredfile('totara_msteams/manifest_app_icon_outline',
                new lang_string('settings:manifest_app_icon_outline', 'totara_msteams'),
                new lang_string('settings:manifest_app_icon_outline_help', 'totara_msteams'),
                'manifest_app_icon_outline',
                0,
                ['accepted_types' => ['.png']]));

            // Accent colour.
            $page->add(new admin_setting_configcolourpicker('totara_msteams/manifest_app_accent_color',
                new lang_string('settings:manifest_accent_colour', 'totara_msteams'),
                new lang_string('settings:manifest_accent_colour_help', 'totara_msteams'),
                new lang_string('settings:manifest_accent_colour_default', 'totara_msteams'),
                null,
                false));
        }

        return $page;
    }

    /**
     * Teams app page.
     *
     * @param boolean $withsetting
     * @return admin_externalpage
     */
    private static function load_msteams_settings_app(bool $withsetting): admin_externalpage {
        return new admin_externalpage(self::NS.'downloadmanifest', new lang_string('settings:page_totara_app', 'totara_msteams'),
            new moodle_url('/totara/msteams/download_manifest.php'));
    }
}
