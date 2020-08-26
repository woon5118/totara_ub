<?php
/*
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
 * @author: Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package: mod_perform
 */

defined('MOODLE_INTERNAL') || die();

use core\entities\user;
use mod_perform\rb\traits\activity_trait;
use mod_perform\rb\traits\participant_instance_trait;
use mod_perform\rb\traits\participant_section_trait;
use mod_perform\rb\traits\section_trait;
use mod_perform\rb\traits\subject_instance_trait;
use totara_core\advanced_feature;
use totara_job\rb\source\report_trait;

/**
 * Performance participant section report.
 *
 * Class rb_source_perform_participant_section
 */
class rb_source_perform_participant_section extends rb_base_source {
    use activity_trait;
    use participant_instance_trait;
    use participant_section_trait;
    use report_trait;
    use section_trait;
    use subject_instance_trait;

    /**
     * Constructor.
     *
     * @param mixed $groupid
     * @param rb_global_restriction_set|null $globalrestrictionset
     */
    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        if ($groupid instanceof rb_global_restriction_set) {
            throw new coding_exception('Wrong parameter orders detected during report source instantiation.');
        }
        // Remember the active global restriction set.
        $this->globalrestrictionset = $globalrestrictionset;

        // Apply global user restrictions.
        $this->add_global_report_restriction_join('base', 'user_id');

        $this->sourcetitle = get_string('sourcetitle', 'rb_source_perform_participant_section');
        $this->sourcesummary = get_string('sourcesummary', 'rb_source_perform_participant_section');
        $this->sourcelabel = get_string('sourcelabel', 'rb_source_perform_participant_section');

        $this->base = '{perform_participant_section}';
        $this->joinlist = $this->define_joinlist();
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();

        $this->add_participant_section_to_base();

        $this->add_section(
            new rb_join(
                'section',
                'INNER',
                '{perform_section}',
                'base.section_id = section.id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            )
        );

        $this->add_participant_instance();
        $this->add_subject_instance();
        $this->add_activity();

        $this->contentoptions = $this->define_contentoptions();
        $this->paramoptions = $this->define_paramoptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->defaultfilters = $this->define_defaultfilters();

        $this->usedcomponents[] = 'mod_perform';

        parent::__construct();
    }

    /**
     * Global report restrictions are implemented in this source.
     *
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
        $columnoptions = [];

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
            // Participant name
            [
                'type' => 'participant_instance',
                'value' => 'participant_name',
                'heading' => get_string('participant_name', 'rb_source_perform_participant_instance'),
            ],
            // Participant section name
            [
                'type' => 'section',
                'value' => 'title',
                'heading' => get_string('section_title', 'mod_perform'),
            ],
            // Subject of the activity
            [
                'type' => 'subject_user',
                'value' => 'namelink',
                'heading' => get_string('subject_name', 'rb_source_perform_subject_instance')
            ],
            // Participant's relationship in activity
            [
                'type' => 'participant_instance',
                'value' => 'relationship_name',
                'heading' => get_string('relationship_name', 'mod_perform'),
            ],
            // Participant instance date created
            [
                'type' => 'participant_instance',
                'value' => 'created_at',
                'heading' => get_string('date_created', 'mod_perform'),
            ],
            // Progress of participant section
            [
                'type' => 'participant_section',
                'value' => 'progress',
                'heading' => get_string('progress', 'mod_perform'),
            ],
            // Availability of participant section
            [
                'type' => 'participant_section',
                'value' => 'availability',
                'heading' => get_string('availability', 'mod_perform'),
            ],
        ];
    }

    /**
     * The default filters for this and embedded reports.
     *
     * @return array
     */
    public static function get_default_filters() {
        $default_filters = [
            [
                'type' => 'participant_instance',
                'value' => 'participant_name',
            ],
            [
                'type' => 'section',
                'value' => 'title',
            ],
            [
                'type' => 'subject_user',
                'value' => 'fullname',
            ],
            [
                'type' => 'participant_section',
                'value' => 'progress',
            ],
            [
                'type' => 'participant_section',
                'value' => 'availability',
            ],
            [
                'type' => 'participant_instance',
                'value' => 'created_at',
            ],
            [
                'type' => 'participant_instance',
                'value' => 'relationship_id',
            ],
        ];
        return $default_filters;
    }

    /**
     * Define the available content options for this report.
     *
     * @return array
     */
    protected function define_contentoptions() {
        $contentoptions = [];

        // Add the manager/position/organisation content options.
        $this->add_basic_user_content_options($contentoptions);

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
                'track.activity_id',
                'track'
            ),
            new rb_param_option(
                'participant_instance_id',
                'base.participant_instance_id',
                'participant_instance'
            ),
        ];
        return $paramoptions;
    }

    /**
     * Disable participant reports if the performance activities feature is disabled.
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
        // TODO
        global $CFG;

        if (!PHPUNIT_TEST) {
            throw new coding_exception('phpunit_column_test_add_data() cannot be used outside of unit tests');
        }

        require_once($CFG->dirroot.'/lib/testing/generator/component_generator_base.php');
        require_once($CFG->dirroot.'/lib/testing/generator/data_generator.php');
        require_once(__DIR__ . '/../tests/generator/mod_perform_generator.class.php');

        $si = (new \mod_perform_generator(new testing_data_generator()))->create_subject_instance([
            'activity_name' => 'Weekly catchup',
            'subject_is_participating' => true,
            'subject_user_id' => user::repository()->get()->last()->id,
        ]);
    }
}