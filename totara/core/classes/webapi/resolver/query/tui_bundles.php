<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_core
 */

namespace totara_core\webapi\resolver\query;

use core\webapi\execution_context;

final class tui_bundles implements \core\webapi\query_resolver {
    public static function resolve(array $args, execution_context $ec) {
        $components = $args['components'];

        $reqs = new \core\tui\requirements();
        foreach ($components as $component) {
            $reqs->require_component($component);
        }

        $options = ['theme' => $args['theme']];

        return array_map(function ($x) use ($options) {
            return $x->get_api_data($options);
        }, $reqs->get_bundles());
    }
}
