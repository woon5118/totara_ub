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
use admin_setting_heading;
use admin_settingpage;
use totara_core\http\client;
use totara_core\virtualmeeting\plugin\factory\factory;
use totara_core\virtualmeeting\plugin\provider\provider;

/**
 * PoC plugin factory
 */
abstract class poc_factory implements factory {
    /** plugin name */
    protected const NAME = '';

    /** has user authentication */
    protected const USER_AUTH = false;

    /**
     * Enable/disable plugin for testing
     *
     * @param string $plugin poc_xxx
     * @param boolean $state
     * @codeCoverageIgnore
     */
    public static function toggle(string $plugin, bool $state): void {
        set_config("virtualmeeting_{$plugin}_enabled", $state ? '1' : '0', 'totara_core');
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
        return new poc_service_provider(static::USER_AUTH);
    }

    /**
     * @inheritDoc
     */
    public function create_setting_page(string $section, string $displayname, bool $fulltree, bool $hidden): ?admin_settingpage {
        $page = new admin_settingpage($section, $displayname, 'moodle/site:config', $hidden);
        if ($fulltree) {
            $name = static::NAME;
            $page->add(new admin_setting_heading("totara_core/virtualmeeting_poc_{$name}_header", 'Plugin settings', ''));
            $page->add(new admin_setting_configcheckbox("totara_core/virtualmeeting_poc_{$name}_enabled", 'Enabled', '', '1'));
        }
        return $page;
    }
}
