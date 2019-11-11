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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_assignment
 */

namespace totara_assignment\services;


use external_function_parameters;
use external_single_structure;
use external_value;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/externallib.php');

class organisation_framework extends \external_api {

    /**
     * @return external_function_parameters
     */
    public static function index_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'filters' => new external_single_structure(
                    [
                        'text' => new external_value(PARAM_TEXT, 'Text search field value', VALUE_OPTIONAL, null),
                        'visible' => new external_value(PARAM_BOOL, 'Filter by visible', VALUE_OPTIONAL, true),
                    ],
                    VALUE_REQUIRED
                ),
                'page' => new external_value(PARAM_INT, 'Pagination: page to load', VALUE_REQUIRED),
                'order' => new external_value(PARAM_ALPHANUMEXT, 'Name of column to order by - not used currently', VALUE_REQUIRED),
                'direction' => new external_value(PARAM_ALPHA, 'either ASC or DESC - not used currently', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @param array $filters
     * @param int $page
     * @param string $order
     * @param string $direction
     * @return array
     */
    public static function index(array $filters, int $page, string $order, string $direction) {
        require_capability('totara/hierarchy:vieworganisationframeworks', \context_system::instance());

        if (!array_key_exists('visible', $filters)) {
            $filters['visible'] = true;
        }

        return \hierarchy_organisation\entities\organisation_framework::repository()
            ->set_filters($filters)
            ->order_by($order, $direction)
            ->paginate($page)
            ->transform(function (\hierarchy_organisation\entities\organisation_framework $item) {
                $fullname = format_string($item->fullname);
                return [
                    'id' => $item->id,
                    'display_name' => $fullname,
                    'fullname' => $fullname
                ];
            })->to_array();
    }
    /**
     * @return null
     */
    public static function index_returns() {
        return null;
    }
}