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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\services;


use context_system;
use core\format;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use totara_competency\entities;
use totara_core\advanced_feature;
use totara_core\formatter\field\string_field_formatter;
use totara_core\formatter\field\text_field_formatter;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/externallib.php');

class competency extends \external_api {

    /**
     * @return external_function_parameters
     */
    public static function index_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'filters' => new external_single_structure(
                    [
                        'text' => new external_value(PARAM_TEXT, 'text search field value', VALUE_OPTIONAL, null),
                        'assignment_type' => new external_multiple_structure(
                            new external_value(PARAM_TEXT, 'admin, self, etc', VALUE_OPTIONAL),
                            'one or more assignment types',
                            VALUE_OPTIONAL
                        ),
                        'framework' => new external_value(PARAM_INT, 'competency framework id', VALUE_OPTIONAL, null),
                        'assignment_status' => new external_multiple_structure(
                            new external_value(PARAM_INT, 'unassigned (0), assigned (1)', VALUE_OPTIONAL),
                            'filter for assigned/unassigned competencies',
                            VALUE_OPTIONAL
                        ),
                        'parent' => new external_value(PARAM_INT, 'Filter children (one level only)', VALUE_OPTIONAL, null),
                        'path' => new external_value(PARAM_INT, 'Filter children (all levels below)', VALUE_OPTIONAL, null),
                        'basket' => new external_value(PARAM_TEXT, 'Filter by selection in the basket', VALUE_OPTIONAL, null),
                        'visible' => new external_value(PARAM_BOOL, 'Filter by visible', VALUE_OPTIONAL, true),
                        'type' => new external_multiple_structure(
                            new external_value(PARAM_INT, 'id of the type', VALUE_OPTIONAL),
                            'one or more competency type ids',
                            VALUE_OPTIONAL
                        ),
                    ],
                    VALUE_OPTIONAL,
                    []
                ),
                'page' => new external_value(PARAM_INT, 'pagination: page to load', VALUE_REQUIRED, 0),
                'order' => new external_value(PARAM_ALPHANUMEXT, 'name of column to order by', VALUE_REQUIRED),
                'direction' => new external_value(PARAM_ALPHA, 'direction of ordering (either ASC or DESC)', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @param array $filters
     * @param int $page
     * @param string $order_by
     * @param string $order_dir
     * @return array
     */
    public static function index(array $filters = [], int $page = 0, string $order_by = '', string $order_dir = '') {
        advanced_feature::require('competency_assignment');

        require_capability('totara/competency:view', context_system::instance());

        if (!array_key_exists('visible', $filters)) {
            $filters['visible'] = true;
        }

        if (empty($order_by)) {
            $order_by = 'framework_hierarchy';
        }

        $order_dir = (strtolower($order_dir) == 'asc') ? 'ASC' : 'DESC';
        // TODO $order_by is not validated against list of columns,
        // TODO Shall we allow it to fail with an exception? Param alpha num text should remove any SQL nasty stuff

        $repository = entities\competency::repository();
        $competencies = $repository
            ->select('*')
            ->with_assignments_count()
            ->with_children_count()
            ->set_filters($filters)
            ->order_by($order_by, $order_dir)
            ->paginate($page);

        $competencies->transform(function (entities\competency $competency) {
            return self::prepare_competency_response($competency, false, false, true);
        });

        return $competencies->to_array();
    }

    /**
     * @return null
     */
    public static function index_returns() {
        return null;
    }

    /**
     * @return external_function_parameters
     */
    public static function show_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', VALUE_REQUIRED),
                'include' => new external_single_structure(
                    [
                        'crumbs' => new external_value(PARAM_INT, 'with_crumbtrail', VALUE_OPTIONAL, 0),
                        'usergroups' => new external_value(PARAM_INT, 'with_user_groups', VALUE_OPTIONAL, 0),
                    ],
                    'options'
                )
            ]
        );
    }

    /**
     * Returns one entity
     *
     * @param int $id
     * @param array $options
     * @return array
     */
    public static function show(int $id, array $options) {
        advanced_feature::require('competency_assignment');

        require_capability('totara/competency:view', context_system::instance());

        /** @var entities\competency $competency */
        $competency = entities\competency::repository()->find($id);
        if (empty($competency)) {
            return [];
        }

        if (!empty($options['crumbs'])) {
            $competency->with_crumbtrail();
        }
        if (!empty($options['usergroups'])) {
            $competency->with_assigned_user_groups();
        }

        return self::prepare_competency_response($competency, !empty($options['crumbs']), !empty($options['usergroups']), false);
    }

    /**
     * @return null
     */
    public static function show_returns() {
        return null;
    }

    /**
     * Prepare assignment response
     *
     * @param entities\competency $competency
     * @param bool|null $with_crumbs
     * @param bool|null $with_user_groups
     * @param bool|null $with_assignments
     * @return array
     */
    protected static function prepare_competency_response(entities\competency $competency,
                                                            ?bool $with_crumbs =  false,
                                                            ?bool $with_user_groups = false,
                                                            ?bool $with_assignments = false): array {
        global $PAGE;

        $context = context_system::instance();
        // As we use format_string make sure we have the page context set
        $PAGE->set_context($context);

        $string_formatter = new string_field_formatter(format::FORMAT_HTML, $context);
        $text_formatter = (new text_field_formatter(format::FORMAT_HTML, $context))
            ->set_pluginfile_url_options($context, 'totara_hierarchy', 'comp', $competency->id);

        $response = [
            'id' => $competency->id,
            'fullname' => $string_formatter->format($competency->fullname),
            'description' => $text_formatter->format($competency->description),
            'display_name' => $string_formatter->format($competency->display_name),
            'parentid' => $competency->parentid,
            'frameworkid' => $competency->frameworkid
        ];

        if ($with_user_groups) {
            $assigned_user_groups = array_map(function ($item) use ($string_formatter) {
                return ['user_group_name' => $string_formatter->format($item)];
            }, $competency->assigned_user_groups);

            $response['assigned_user_groups'] = array_values($assigned_user_groups);
        }

        if ($with_crumbs) {
            $response['crumbtrail'] = $competency->crumbtrail;
        }

        if ($with_assignments) {
            $response['children_count'] = (int)$competency->children_count;
            $response['assignments_count'] = (int)$competency->assignments_count;
        }

        return $response;
    }

}