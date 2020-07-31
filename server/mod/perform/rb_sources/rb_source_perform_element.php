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

use mod_perform\models\activity\element_plugin;
use mod_perform\rb\traits\activity_trait;
use mod_perform\rb\traits\element_trait;
use mod_perform\rb\traits\section_element_trait;
use mod_perform\rb\traits\section_trait;
use totara_core\advanced_feature;

defined('MOODLE_INTERNAL') || die();

/**
 * Performance response report.
 *
 * Class rb_source_perform_element
 */
class rb_source_perform_element extends rb_base_source {

    use section_element_trait;
    use element_trait;
    use section_trait;
    use activity_trait;

    /**
     * Constructor.
     *
     * @param mixed $groupid
     * @param rb_global_restriction_set|null $globalrestrictionset
     * @throws coding_exception
     */
    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        global $DB;

        $this->sourcetitle = get_string('sourcetitle', 'rb_source_perform_element');
        $this->sourcesummary = get_string('sourcesummary', 'rb_source_perform_element');
        $this->sourcelabel = get_string('sourcelabel', 'rb_source_perform_element');

        $this->usedcomponents[] = 'mod_perform';
        $this->base = '{perform_element}';
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();

        $this->add_element_to_base();

        $this->add_section_element(
            new rb_join(
                'section_element',
                'INNER',
                '{perform_section_element}',
                'base.id = section_element.element_id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            )
        );

        $this->add_section(
            new rb_join(
                'perform_section',
                'INNER',
                '{perform_section}',
                'section_element.section_id = perform_section.id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE,
                'section_element'
            )
        );

        $this->add_activity(
            new rb_join(
                'perform',
                'INNER',
                '{perform}',
                'perform_section.activity_id = perform.id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE,
                'perform_section'
            )
        );

        $this->contentoptions = $this->define_contentoptions();
        $this->paramoptions = $this->define_paramoptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->defaultfilters = $this->define_defaultfilters();

        $non_respondable_elements = array_keys(element_plugin::get_element_plugins(false));
        if (!empty($non_respondable_elements)) {
            $sql = $DB->sql_not_in($non_respondable_elements);
            $this->sourcewhere = 'base.plugin_name ' . $sql->get_sql();
            $this->sourceparams = $sql->get_params();
        }

        parent::__construct();
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
            // Column for sorting that combines activity name, section and element sorts to get sensible overall order for elements
            new rb_column_option(
                'element',
                'default_sort',
                get_string('default_sort', 'mod_perform'),
                // This will ensure elements are grouped by activity and order within but isn't perfect, particularly for
                // multiple identically named activities (which we don't prevent). Having an activity.sort_order would be better.
                $DB->sql_concat_join("' '", ['perform.name', 'perform_section.sort_order', 'section_element.sort_order']),
                [
                    'joins' => ['perform', 'perform_section', 'section_element'],
                    'hidden' => true,
                    'noexport' => true,
                    'selectable' => false,
                ]
            ),
            new rb_column_option(
                'element',
                'actions',
                get_string('actions', 'mod_perform'),
                'base.id',
                [
                    'dbdatatype' => 'integer',
                    'displayfunc' => 'element_actions',
                    'noexport' => true,
                    'nosort' => true,
                ]
            )
        ];

        return $columnoptions;
    }

    /**
     * Define the filter options available for this report.
     *
     * @return array
     */
    protected function define_filteroptions() {
        $filteroptions = [];

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
                'type' => 'element',
                'value' => 'title',
                'heading' => get_string('element_title', 'mod_perform'),
            ],
            [
                'type' => 'element',
                'value' => 'type',
                'heading' => get_string('element_type', 'mod_perform'),
            ],
            [
                'type' => 'element',
                'value' => 'identifier',
                'heading' => get_string('element_identifier', 'mod_perform'),
            ],
            [
                'type' => 'section',
                'value' => 'title',
                'heading' => get_string('section_title', 'mod_perform'),
            ],
            [
                'type' => 'activity',
                'value' => 'name',
                'heading' => get_string('activity_name', 'mod_perform'),
            ],
        ];
    }

    /**
     * The default filters for this and embedded reports.
     *
     * @return array
     */
    public static function get_default_filters() {
        return [
            [
                'type' => 'element',
                'value' => 'title',
            ],
            [
                'type' => 'element',
                'value' => 'type',
            ],
            [
                'type' => 'element',
                'value' => 'identifier',
            ],
            [
                'type' => 'section',
                'value' => 'title',
            ],
            [
                'type' => 'activity',
                'value' => 'name',
            ],
        ];
    }

    /**
     * Define the available content options for this report.
     *
     * @return array
     */
    protected function define_contentoptions() {
        $contentoptions = [];

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
                'element_id',
                'section_element.element_id',
                'section_element'
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
     * Global report restrictions are implemented in this source.
     *
     * @return boolean
     */
    public function global_restrictions_supported() {
        // Not relevant as elements aren't user data.
        return false;
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
        require_once(__DIR__ . '/../tests/generator/mod_perform_generator.class.php');

        // This creates a complete activity with two elements and related tables we need to test this source.
        $si = (new mod_perform_generator(new testing_data_generator()))->create_subject_instance([
            'activity_name' => 'Weekly catchup',
            'subject_is_participating' => true,
            'subject_user_id' => \core\entities\user::repository()->get()->last()->id,
            'include_questions' => true,
        ]);
    }

    /**
     * Adjust expected result of column test for columns in this source.
     *
     * @param rb_column_option $columnoption
     * @return int
     */
    public function phpunit_column_test_expected_count($columnoption) {
        // create_subject_instances() creates an activity with two elements, so should expect two results from this report.
        return 2;
    }
}
