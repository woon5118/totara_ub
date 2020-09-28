<?php
/**
 *
 * This file is part of Totara Perform
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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package mod_perform
 *
 */

use container_perform\perform as perform_container;
use mod_perform\models\activity\element_plugin;
use mod_perform\models\activity\participant_source;
use mod_perform\rb\traits\activity_trait;
use mod_perform\rb\traits\element_trait;
use mod_perform\rb\traits\participant_instance_trait;
use mod_perform\rb\traits\section_element_trait;
use mod_perform\rb\traits\section_trait;
use mod_perform\rb\traits\subject_instance_trait;
use totara_core\advanced_feature;
use mod_perform\state\participant_section\complete;

defined('MOODLE_INTERNAL') || die();

/**
 * Performance response report.
 *
 * Class rb_source_perform_response
 */
class rb_source_perform_response_base extends rb_base_source {

    use participant_instance_trait;
    use subject_instance_trait;
    use section_element_trait;
    use element_trait;
    use section_trait;
    use activity_trait;

    private $default_context;

    /**
     * Constructor.
     *
     * @param mixed $groupid
     * @param rb_global_restriction_set|null $globalrestrictionset
     * @throws coding_exception
     */
    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        global $DB;

        if ($groupid instanceof rb_global_restriction_set) {
            throw new coding_exception('Wrong parameter orders detected during report source instantiation.');
        }

        // Remember the active global restriction set.
        $this->globalrestrictionset = $globalrestrictionset;

        $this->default_category_context = context_coursecat::instance(perform_container::get_default_category_id());

        // Apply global user restrictions.
        $this->add_global_report_restriction_join('subject_instance', 'subject_user_id', 'subject_instance');

        // This source is not available for user selection - it is used by the embedded report only.
        $this->selectable = false;

        $this->sourcetitle = get_string('sourcetitle', 'rb_source_perform_response_base');
        $this->sourcesummary = get_string('sourcesummary', 'rb_source_perform_response_base');
        $this->sourcelabel = get_string('sourcelabel', 'rb_source_perform_response_base');

        $this->usedcomponents[] = 'mod_perform';
        $this->base = "(
            SELECT es.*
            FROM {perform_element_response} es
            JOIN {perform_section_element} se ON es.section_element_id = se.id
            JOIN {perform_participant_section} ps
                ON ps.section_id = se.section_id AND ps.participant_instance_id = es.participant_instance_id
                    AND ps.progress = ".complete::get_code()."
            JOIN {perform_participant_instance} ppi ON es.participant_instance_id = ppi.id
            LEFT JOIN {user} u ON ppi.participant_id = u.id 
                AND ppi.participant_source = " . participant_source::INTERNAL . "
            WHERE ppi.participant_source = " . participant_source::EXTERNAL . " 
                OR u.deleted = 0
        )";
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();

        $this->add_section_element(
            new rb_join(
                'section_element',
                'INNER',
                '{perform_section_element}',
                'base.section_element_id = section_element.id',
                REPORT_BUILDER_RELATION_MANY_TO_ONE
            )
        );

        $this->add_element(
            new rb_join(
                'perform_element',
                'INNER',
                '{perform_element}',
                'section_element.element_id = perform_element.id',
                REPORT_BUILDER_RELATION_MANY_TO_ONE,
                'section_element'
            )
        );

        $this->add_section(
            new rb_join(
                'perform_section',
                'INNER',
                '{perform_section}',
                'section_element.section_id = perform_section.id',
                REPORT_BUILDER_RELATION_MANY_TO_ONE,
                'section_element'
            )
        );

        $this->add_participant_instance(
            new rb_join(
                'participant_instance',
                'INNER',
                '{perform_participant_instance}',
                'base.participant_instance_id = participant_instance.id',
                REPORT_BUILDER_RELATION_MANY_TO_ONE
            )
        );

        $this->add_subject_instance();
        $this->add_activity();

        $this->contentoptions = $this->define_contentoptions();
        $this->paramoptions = $this->define_paramoptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->defaultfilters = $this->define_defaultfilters();

        // TODO remove this from this source once non-respondable elements are no longer generating response records.
        //      still used in element source.
        $non_respondable_elements = array_keys(element_plugin::get_element_plugins(false));
        if (!empty($non_respondable_elements)) {
            $sql = $DB->sql_not_in($non_respondable_elements);
            $this->sourcewhere = 'perform_element.plugin_name ' . $sql->get_sql();
            $this->sourceparams = $sql->get_params();
            $this->sourcejoins = ['perform_element'];
        }

        parent::__construct();
    }

    /**
     * Global report restrictions are implemented in this source.
     * @return boolean
     */
    public function global_restrictions_supported() {
        return true;
    }

    /**
     * Define join table list.
     *
     * @return array
     */
    protected function define_joinlist() {
        $joinlist = [];

        return $joinlist;
    }

    /**
     * Define the column options available for this report.
     *
     * @return array
     */
    protected function define_columnoptions() {
        global $DB;
        $columnoptions = [
            new rb_column_option(
                'response',
                'response_data',
                get_string('response_data', 'rb_source_perform_response_base'),
                'base.response_data',
                [
                    'joins' => ['perform_element'],
                    'displayfunc' => 'element_response',
                    'extrafields' => [
                        'element_type' => "perform_element.plugin_name",
                        'element_data' => "perform_element.data",
                    ],
                    'extracontext' => [
                        'default_category_context' => $this->default_category_context,
                    ]
                ]
            ),
            // Column for sorting that combines activity name, section and element sorts, relationship and participant
            // to get sensible overall order for responses
            new rb_column_option(
                'response',
                'default_sort',
                get_string('default_sort', 'mod_perform'),
                // This will ensure elements are grouped by activity and order within but isn't perfect, particularly for
                // multiple identically named activities (which we don't prevent). Having an activity.sort_order would be better.
                $DB->sql_concat_join(
                    "' '",
                    [
                        'perform.name',
                        'perform_section.sort_order',
                        'section_element.sort_order',
                        'totara_core_relationship.sort_order',
                        'participant_instance.participant_source',
                        'participant_instance.participant_id'
                    ]
                ),
                [
                    'joins' => ['perform', 'perform_section', 'section_element', 'participant_instance', 'totara_core_relationship'],
                    'hidden' => true,
                    'noexport' => true,
                    'selectable' => false,
                ]
            ),
        ];

        return $columnoptions;
    }

    /**
     * Define the filter options available for this report.
     *
     * @return array
     */
    protected function define_filteroptions() {
        $filteroptions = [
            new rb_filter_option(
                'response',
                'response_data',
                get_string('response_data', 'rb_source_perform_response_base'),
                'text'
            ),
        ];

        return $filteroptions;
    }

    /**
     * Define the default columns for this report.
     *
     * @return array
     */
    protected function define_defaultcolumns() {
        return self::get_default_columns();
    }

    /**
     * Define the default filters for this report.
     *
     * @return array
     */
    protected function define_defaultfilters() {
        return self::get_default_filters();
    }

    /**
     * The default columns for this and embedded reports.
     *
     * @return array
     */
    public static function get_default_columns() {
        return [
            [
                'type' => 'activity',
                'value' => 'name',
                'heading' => get_string('activity_name', 'mod_perform'),
            ],
            [
                'type' => 'activity',
                'value' => 'type',
                'heading' => get_string('activity_type', 'mod_perform'),
            ],
            [
                'type' => 'section',
                'value' => 'title',
                'heading' => get_string('section_title', 'mod_perform'),
            ],
            [
                'type' => 'element',
                'value' => 'title',
                'heading' => get_string('element_title', 'mod_perform'),
            ],
            [
                'type' => 'element',
                'value' => 'identifier',
                'heading' => get_string('element_identifier', 'mod_perform'),
            ],
            [
                'type' => 'subject_user',
                'value' => 'namelink',
                'heading' => get_string('subject_user', 'mod_perform'),
            ],
            [
                'type' => 'participant_instance',
                'value' => 'participant_name',
                'heading' => get_string('participant_name', 'rb_source_perform_participation_participant_instance'),
            ],
            [
                'type' => 'response',
                'value' => 'response_data',
                'heading' => get_string('response_data', 'rb_source_perform_response_base'),
            ],
            [
                'type' => 'response',
                'value' => 'default_sort',
                'heading' => get_string('default_sort', 'mod_perform'),
            ],
        ];
    }

    /**
     * The default filters for this and embedded reports.
     *
     * @return array
     */
    public static function get_default_filters() {
        return [];
    }

    /**
     * Define the available content options for this report.
     *
     * @return array
     */
    protected function define_contentoptions() {
        $contentoptions = [];

        // Add the manager/position/organisation content options.
        $this->add_basic_user_content_options($contentoptions, 'subject_user');

        return $contentoptions;
    }

    /**
     * Define the available param options for this report.
     *
     * @return array
     */
    protected function define_paramoptions() {
        $paramoptions = [
            new rb_param_option(
                'activity_id',
                'perform_section.activity_id',
                'perform_section'
            ),
            new rb_param_option(
                'subject_user_id',
                'subject_instance.subject_user_id',
                'subject_instance'
            ),
            new rb_param_option(
                'subject_instance_id',
                'participant_instance.subject_instance_id',
                'participant_instance'
            ),
            new rb_param_option(
                'element_id',
                'section_element.element_id',
                'section_element'
            ),
            new rb_param_option(
                'element_identifier',
                'perform_element.identifier_id',
                'perform_element'
            ),
        ];

        return $paramoptions;
    }

    /**
     * Disable subject reports if the performance activities feature is disabled.
     *
     * @return bool
     */
    public static function is_source_ignored() {
        return advanced_feature::is_disabled('performance_activities');
    }

    /**
     * Inject column_test data into database.
     *
     * @param totara_reportbuilder_column_testcase $testcase
     */
    public function phpunit_column_test_add_data(totara_reportbuilder_column_testcase $testcase) {
        global $CFG;

        if (!PHPUNIT_TEST) {
            throw new coding_exception('phpunit_column_test_add_data() cannot be used outside of unit tests');
        }

        require_once($CFG->dirroot.'/lib/testing/generator/component_generator_base.php');
        require_once($CFG->dirroot.'/lib/testing/generator/data_generator.php');
        require_once($CFG->dirroot.'/mod/perform/tests/generator/mod_perform_generator.class.php');

        $si = (new mod_perform_generator(new testing_data_generator()))->create_subject_instance([
            'activity_name' => 'Weekly catchup',
            'subject_is_participating' => true,
            'subject_user_id' => \core\entities\user::repository()->get()->last()->id,
            'include_questions' => true,
            'update_participant_sections_status' => 'complete',
        ]);
        (new mod_perform_generator(new testing_data_generator()))->create_responses($si, 1);
    }
}
