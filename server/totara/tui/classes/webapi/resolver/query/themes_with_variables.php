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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_tui
 */

namespace totara_tui\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use totara_tui\local\locator\bundle;
use totara_tui\local\mediation\helper;
use totara_tui\local\theme_config;

/**
 * Query to get themes that defines css variables.
 */
final class themes_with_variables implements query_resolver, has_middleware {

    /**
     * @inheritDoc
     */
    public static function resolve(array $args, execution_context $ec) {
        if (!isset($args['theme'])) {
            throw new \coding_exception('Missing required argument', 'theme');
        }

        if (!helper::validate_theme_name($args['theme'])) {
            throw new \coding_exception('Invalid theme', $args['theme']);
        }

        $themes = [];
        foreach (theme_config::load($args['theme'])->get_tui_theme_chain() as $theme) {
            if (bundle::get_bundle_css_json_variables_file('theme_' . $theme)) {
                $themes[] = $theme;
            }
        }
        return $themes;
    }

    /**
     * @inheritDoc
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
        ];
    }
}
