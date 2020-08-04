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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../totara/reportbuilder/rb_sources/rb_source_user.php');

/**
 * Performance reporting user report.
 *
 * This is an extension of the rb_source_user source but with additional capability checks applied.
 *
 * Class rb_source_user_performance_reporting
 */
class rb_source_user_performance_reporting extends rb_source_user {

    /**
     * Constructor.
     *
     * @param mixed $groupid
     * @param rb_global_restriction_set|null $globalrestrictionset
     * @throws coding_exception
     */
    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        global $CFG;
        parent::__construct($groupid, $globalrestrictionset);

        // This source is not available for user selection - it is used by the embedded report only.
        $this->selectable = false;

        $this->sourcetitle = get_string('sourcetitle', 'rb_source_user_performance_reporting');
        $this->sourcesummary = get_string('sourcesummary', 'rb_source_user_performance_reporting');
        $this->sourcelabel = get_string('sourcelabel', 'rb_source_user_performance_reporting');

        $this->usedcomponents[] = 'mod_perform';

        // Remove guest user from this source.
        $guest_user_id = $CFG->siteguest;
        $this->sourcewhere = "base.id <> :guest_user_id";
        $this->sourceparams = ['guest_user_id' => $guest_user_id];
    }

    protected function define_columnoptions() {
        global $DB;
        $columnoptions = parent::define_columnoptions();

        $usednamefields = totara_get_all_user_name_fields_join('base', null, true);
        $allnamefields = totara_get_all_user_name_fields_join('base');

        $columnoptions[] = new rb_column_option(
            'user',
            'name_linked_to_performance_reporting',
            get_string('name_linked_to_performance_reporting', 'rb_source_user_performance_reporting'),
            $DB->sql_concat_join("' '", $usednamefields),
            array(
                'displayfunc' => 'name_linked_to_performance_reporting',
                'defaultheading' => get_string('userfullname', 'totara_reportbuilder'),
                'extrafields' => array_merge(array('id' => "base.id", 'deleted' => "base.deleted"), $allnamefields),
            )
        );
        $columnoptions[] = new rb_column_option(
            'user',
            'user_performance_reporting_actions',
            get_string('actions', 'mod_perform'),
            "base.id",
            [
                'displayfunc' => 'user_performance_reporting_actions',
                'noexport' => true,
                'nosort' => true,
            ]
        );
        $columnoptions[] = new \rb_column_option(
            'user',
            'user_performance_emailunobscured',
            get_string('user_email_unobscured_no_cap_checks', 'mod_perform'),
            'base.email',
            array(
                'displayfunc' => 'user_email_unobscured',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
            )
        );

        return $columnoptions;
    }

    protected function define_filteroptions() {
        $filteroptions = parent::define_filteroptions();

        $filteroptions[] = new \rb_filter_option(
            'user',
            'user_performance_emailunobscured',
            get_string('user_email_unobscured_no_cap_checks', 'mod_perform'),
            'text'
        );

        return $filteroptions;
    }

    public function post_config(reportbuilder $report) {
        $restrictions = \mod_perform\util::get_report_on_subjects_sql($report->reportfor, "base.id");
        $report->set_post_config_restrictions($restrictions);
    }
}
