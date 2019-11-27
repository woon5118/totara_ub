<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Marco Song <marco.song@totaralearning.com>
 * @package totara_criteria
 */

namespace criteria_othercompetency;

use totara_core\advanced_feature;

defined('MOODLE_INTERNAL') || die;

class external extends \external_api {

    /**
     * get_detail
     */
    public static function get_detail_parameters() {
        return new \external_function_parameters(
            [
                'id' => new \external_value(PARAM_INT, 'Criterion id')
            ]
        );
    }

    public static function get_detail(int $id) {
        advanced_feature::require('competency_assignment');

        return othercompetency::fetch($id)
            -> export_edit_detail();
    }

    public static function get_detail_returns() {
        return new \external_single_structure(
            [
                'items' => new \external_multiple_structure(
                    new \external_single_structure(
                        [
                            'type' => new \external_value(PARAM_TEXT, 'Item type'),
                            'id' => new \external_value(PARAM_INT, 'Item id'),
                            'name' => new \external_value(PARAM_TEXT, 'Item name'),
                        ]
                    )
                ),
                'aggregation' => new \external_single_structure(
                    [
                        'method' => new \external_value(PARAM_INT, 'Aggregation method'),
                        'reqitems' => new \external_value(PARAM_INT, 'Number or items required for fulfillment'),
                    ]
                ),
            ]
        );
    }

}
