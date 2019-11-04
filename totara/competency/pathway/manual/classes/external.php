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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package pathway_manual
 */

namespace pathway_manual;

use totara_competency\achievement_configuration;
use totara_competency\entities\competency;
use totara_competency\entities\configuration_change;
use totara_competency\scale_provider;
use totara_core\advanced_feature;

class external extends \external_api {

    /** get_detail */
    public static function get_detail_parameters() {
        return new \external_function_parameters(
            [
                'id' => new \external_value(PARAM_INT, 'Pathway id')
            ]
        );
    }

    public static function get_detail(int $id) {
        advanced_feature::require('competency_assignment');

        $pathway = manual::fetch($id);
        return $pathway->export_edit_detail();
    }

    public static function get_detail_returns() {
        return new \external_single_structure(
            [
                'roles' => new \external_multiple_structure(
                    new \external_single_structure(
                        [
                            'id' => new \external_value(PARAM_INT, 'Role index'),
                            'role' => new \external_value(PARAM_ALPHA, 'String identifier for role'),
                            'name' => new \external_value(PARAM_TEXT, 'Human readable name for role'),
                        ]
                    )
                ),
            ]
        );
    }


    /**
     * get_roles
     * Sorting and pagination is ignored. Only added for compatibility with picker
     */
    public static function get_roles_parameters() {
        return new \external_function_parameters(
            [
                'filters' => new \external_single_structure(
                    [
                        'ids' => new \external_multiple_structure(
                            new \external_value(PARAM_INT, 'id', VALUE_OPTIONAL),
                            'Role ids to filter by',
                            VALUE_OPTIONAL
                        ),
                        'name' => new \external_value(PARAM_TEXT, 'Filter by role name ', VALUE_OPTIONAL, null),
                        'pw_id' => new \external_value(PARAM_TEXT, 'Filter by pathway id ', VALUE_OPTIONAL, null),
                    ]
                ),
                'page' => new \external_value(PARAM_INT, 'Pagination: page to load'),
                'order' => new \external_value(PARAM_TEXT, 'Name of column to order by'),
                'direction' => new \external_value(PARAM_TEXT, 'Direction of ordering (either ASC or DESC)'),
            ]
        );
    }

    /**
     * Return list of roles
     *
     * @param array $filters
     * @param int $page
     * @param string $order
     * @param string $direction
     * @return array
     */
    public static function get_roles(array $filters, int $page, string $order, string $direction) {
        advanced_feature::require('competency_assignment');

        $roles = manual::get_all_valid_roles();

        if (!empty($filters['ids'])) {
            $ids = $filters['ids'];

            $roles = array_filter(
                $roles,
                function ($role, $id) use ($ids) {
                    return in_array($id, $ids);
                },
                ARRAY_FILTER_USE_BOTH
            );
        }

        if (!empty($filters['name'])) {
            $search = $filters['name'];

            $roles = array_filter(
                $roles,
                function ($role, $id) use ($search) {
                    return stripos($role, $search) !== false;
                },
                ARRAY_FILTER_USE_BOTH
            );
        }

        if (!empty($filters['pw_id'])) {
            $pw_id = $filters['pw_id'];
            $pw_roles = (new manual($pw_id))->get_configured_roles();

            $roles = array_filter(
                $roles,
                function ($role, $id) use ($pw_roles) {
                    return in_array($id, $pw_roles);
                },
                ARRAY_FILTER_USE_BOTH
            );
        }

        $results = [
            'page' => 1,
            'pages' => 1,
            'items_per_page' => count($roles),
            'total' => count($roles),
            'items' => [],
        ];

        foreach ($roles as $id => $role) {
            $results['items'][] = [
                'id' => $id,
                'role' => $role,
                'name' => ucfirst($role),
            ];
        }

        return  $results;
    }

    public static function get_roles_returns() {
        return new \external_single_structure(
            [
                'items' => new \external_multiple_structure(
                    new \external_single_structure(
                        [
                            'id' => new \external_value(PARAM_INT, 'Role id'),
                            'role' => new \external_value(PARAM_ALPHA, 'String identifier for role'),
                            'name' => new \external_value(PARAM_TEXT, 'Human readable name for role'),
                        ]
                    )
                ),
                'page' => new \external_value(PARAM_INT, 'Current page'),
                'pages' => new \external_value(PARAM_INT, 'Total number of pages'),
                'items_per_page' => new \external_value(PARAM_INT, 'Number of items per page'),
                'next' => new \external_value(PARAM_INT, 'Next page number', VALUE_OPTIONAL),
                'prev' => new \external_value(PARAM_INT, 'Previous page number', VALUE_OPTIONAL),
                'total' => new \external_value(PARAM_INT, 'Total number of items'),
            ]
        );
    }


    /** create */
    public static function create_parameters() {
        return new \external_function_parameters(
            [
                'comp_id' => new \external_value(PARAM_INT, 'Competency id'),
                'sortorder' => new \external_value(PARAM_INT, 'Sortorder'),
                'roles' => new \external_multiple_structure(
                    new \external_value(PARAM_ALPHA, 'Role name'),
                    'Roles that may assign a manual rating',
                    VALUE_OPTIONAL
                ),
                'actiontime' => new \external_value(PARAM_INT, 'Time user initiated the action. It is used to group changes done in single user action together'),
            ]
        );
    }

    public static function create(int $comp_id, int $sortorder, array $roles, string $action_time) {
        advanced_feature::require('competency_assignment');

        $competency = new competency($comp_id);
        $config = new achievement_configuration($competency);

        // Save history before making any changes - for now the action_time is used to ensure we do this only once per user 'Apply changes' action
        $config->save_configuration_history($action_time);

        $pathway = new manual();
        $pathway->set_competency($competency)
            ->set_sortorder($sortorder)
            ->set_roles($roles)
            ->save();

        // Log the configuration change- for now the action_time is used to ensure we do this only once per user 'Apply changes' action
        configuration_change::add_competency_entry(
            $config->get_competency()->id,
            configuration_change::CHANGED_CRITERIA,
            $action_time
        );

        return $pathway->get_id();
    }

    public static function create_returns() {
        return new \external_value(PARAM_INT, 'Pathway id');
    }


    /** update */
    public static function update_parameters() {
        return new \external_function_parameters(
            [
                'id' => new \external_value(PARAM_INT, 'Id of pathway'),
                'sortorder' => new \external_value(PARAM_INT, 'Sortorder'),
                'roles' => new \external_multiple_structure(
                    new \external_value(PARAM_ALPHA, 'Role name'),
                    'Roles that may assign a manual rating'
                ),
                'actiontime' => new \external_value(PARAM_INT, 'Time user initiated the action. It is used to group changes done in single user action together'),
            ]
        );
    }

    public static function update(int $id, int $sortorder, array $roles, string $action_time) {
        advanced_feature::require('competency_assignment');

        $pathway = manual::fetch($id);
        $config = new achievement_configuration($pathway->get_competency());

        // Save history before making any changes - for now the action_time is used to ensure we do this only once per user 'Apply changes' action
        $config->save_configuration_history($action_time);

        $pathway->set_sortorder($sortorder)
            ->set_roles($roles)
            ->save();

        // Log the configuration change- for now the action_time is used to ensure we do this only once per user 'Apply changes' action
        configuration_change::add_competency_entry(
            $config->get_competency()->id,
            configuration_change::CHANGED_CRITERIA,
            $action_time
        );

        return $pathway->get_id();
    }

    public static function update_returns() {
        return new \external_value(PARAM_INT, 'Pathway id');
    }
}
