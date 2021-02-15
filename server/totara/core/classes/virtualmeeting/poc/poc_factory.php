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
 * @package core_virtualmeeting
 */

namespace totara_core\virtualmeeting\poc;

use admin_setting_configcheckbox;
use admin_setting_configselect;
use admin_setting_heading;
use admin_settingpage;
use coding_exception;
use totara_core\http\client;
use totara_core\virtualmeeting\exception\unsupported_exception;
use totara_core\virtualmeeting\plugin\factory\factory;
use totara_core\virtualmeeting\plugin\factory\feature_factory;
use totara_core\virtualmeeting\plugin\feature;
use totara_core\virtualmeeting\plugin\provider\provider;

/**
 * PoC plugin factory
 */
abstract class poc_factory implements factory, feature_factory {
    /** plugin name */
    protected const NAME = '';

    /** plugin description */
    protected const DESC = '';

    /** has user authentication */
    protected const USER_AUTH = false;

    /** throws unsupported_exception */
    public const FEATURE_UNKNOWN = -1;

    /** returns false */
    public const FEATURE_NO = 0;

    /** returns true */
    public const FEATURE_YES = 1;

    /**
     * Enable/disable plugin for testing
     *
     * @param string $plugin poc_xxx
     * @param boolean $state
     * @codeCoverageIgnore
     */
    public static function toggle(string $plugin, bool $state): void {
        if (strncmp($plugin, 'poc_', 4)) {
            throw new coding_exception('invalid plugin name');
        }
        set_config("virtualmeeting_{$plugin}_enabled", $state ? '1' : '0', 'totara_core');
    }

    /**
     * Enable/disable whether to return additional info for testing
     *
     * @param string $plugin poc_xxx
     * @param string $info provider::INFO_xxx
     * @param boolean $state
     * @codeCoverageIgnore
     */
    public static function toggle_info(string $plugin, string $info, bool $state): void {
        if (strncmp($plugin, 'poc_', 4)) {
            throw new coding_exception('invalid plugin name');
        }
        set_config("virtualmeeting_{$plugin}_{$info}", $state ? '1' : '0', 'totara_core');
    }

    /**
     * Enable/disable whether to have the particular characteristic testing
     *
     * @param string $plugin poc_xxx
     * @param string $feature feature::xxx
     * @param int $state poc_factory::FEATURE_xxx
     * @codeCoverageIgnore
     */
    public static function toggle_feature(string $plugin, string $feature, int $state): void {
        if (strncmp($plugin, 'poc_', 4)) {
            throw new coding_exception('invalid plugin name');
        }
        set_config("virtualmeeting_{$plugin}_{$feature}", $state, 'totara_core');
    }

    /**
     * @inheritDoc
     */
    public function is_available(): bool {
        $name = static::NAME;
        return get_config('totara_core', "virtualmeeting_poc_{$name}_enabled");
    }

    /**
     * @inheritDoc
     */
    public function create_service_provider(client $client): provider {
        return new poc_service_provider(static::NAME, static::USER_AUTH);
    }

    /**
     * @inheritDoc
     */
    public function create_setting_page(string $section, string $displayname, bool $fulltree, bool $hidden): ?admin_settingpage {
        $page = new admin_settingpage($section, $displayname, 'moodle/site:config', $hidden);
        if ($fulltree) {
            $name = static::NAME;
            $page->add(new admin_setting_heading("totara_core/virtualmeeting_poc_{$name}_header", 'Plugin settings', 'Configure ' . static::DESC));
            $page->add(new admin_setting_configcheckbox("totara_core/virtualmeeting_poc_{$name}_enabled", 'Enabled', '', '1'));
            $infos = [
                provider::INFO_HOST_URL => 'Hosting',
                provider::INFO_INVITATION => 'Invitation',
                provider::INFO_PREVIEW => 'Preview'
            ];
            foreach ($infos as $info => $label) {
                $page->add(new admin_setting_configcheckbox("totara_core/virtualmeeting_poc_{$name}_{$info}", $label, '', '1'));
            }
            $features = [
                feature::LOSSY_UPDATE => ['Lossy update', '1'],
            ];
            $choices = [
                self::FEATURE_UNKNOWN => 'N/A',
                self::FEATURE_NO => 'No',
                self::FEATURE_YES => 'Yes',
            ];
            foreach ($features as $feature => [$label, $default]) {
                $page->add(new admin_setting_configselect("totara_core/virtualmeeting_poc_{$name}_{$feature}", $label, '', $default, $choices));
            }
        }
        return $page;
    }

    /**
     * @inheritDoc
     */
    public function get_feature(string $feature): bool {
        $name = static::NAME;
        $value = (int)get_config('totara_core', "virtualmeeting_poc_{$name}_{$feature}");
        if ($value < 0) {
            throw unsupported_exception::feature($name);
        }
        return (bool)$value;
    }
}
