<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Simon Coggins <simon.coggins@totaralms.com>
 * @package totara
 * @subpackage reportbuilder
 */

/**
 * Local database upgrade script
 *
 * @param   int $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean always true
 */
function xmldb_totara_reportbuilder_upgrade($oldversion) {
    global $CFG, $DB;
    require_once(__DIR__ .'/upgradelib.php');

    $dbman = $DB->get_manager();

    // Totara 10 branching line.

    if ($oldversion < 2016110200) {

        totara_reportbuilder_delete_scheduled_reports();

        // Reportbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2016110200, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2017050400) {
        $oldtype = 'user';
        $filters = array('title'                         => 'alltitlenames',
                         'posstartdate'                  => 'allstartdates',
                         'posenddate'                    => 'allenddates',
                         'managername'                   => 'allmanagernames',
                         'managerfirstname'              => 'allmanagerfirstnames',
                         'managerlastname'               => 'allmanagerlastnames',
                         'managerid'                     => 'allmanagerids',
                         'manageridnumber'               => 'allmanageridnumbers',
                         'manageremail'                  => 'allmanagerobsemails',
                         'manageremailunobscured'        => 'allmanagerunobsemails',
                         'position'                      => 'allpositionnames',
                         'positionidnumber'              => 'allpositionidnumbers',
                         'pos_type'                      => 'allpositiontypes',
                         'positionframework'             => 'allposframenames',
                         'positionframeworkid'           => 'allposframeids',
                         'positionframeworkidnumber'     => 'allposframeidnumbers',
                         'organisation'                  => 'allorganisationnames',
                         'organisationidnumber'          => 'allorganisationidnumbers',
                         'org_type'                      => 'allorganisationtypes',
                         'organisationframework'         => 'allorgframenames',
                         'organisationframeworkid'       => 'allorgframeids',
                         'organisationframeworkidnumber' => 'allorgframeidnumbers',
                         'positionpath'                  => 'allpositions',
                         'positionid'                    => 'allpositions',
                         'positionid2'                   => 'allpositions',
                         'pos_type_id'                   => 'allpostypes',
                         'organisationpath'              => 'allorganisations',
                         'organisationid'                => 'allorganisations',
                         'organisationid2'               => 'allorganisations',
                         'org_type_id'                   => 'allorgtypes'
        );

        // Re-run saved searched migration for the job assignments update.
        // NOTE: this function contains code specific to the migration
        // from 2.9 to 9.0 for multiple jobs. DO NOT USE this function
        // for generic saved search migrations, use
        // {@link totara_reportbuilder_migrate_saved_searches()} instead.
        totara_reportbuilder_migrate_saved_search_filters($filters, $oldtype, 'job_assignment');

        // Reportbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2017050400, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2017052200) {
        // Update filter to match updated code.
        reportbuilder_rename_data('filters', '*', 'cohort', 'usercohortids', 'user', 'usercohortids');

        // Update filter names in saved searches.
        // NOTE: this function contains code specific to the migration
        // from 2.9 to 9.0 for multiple jobs. DO NOT USE this function
        // for generic saved search migrations, use
        // {@link totara_reportbuilder_migrate_saved_searches()} instead.
        totara_reportbuilder_migrate_saved_search_filters(array('usercohortids' => 'usercohortids'), 'cohort', 'user');

        upgrade_plugin_savepoint(true, 2017052200, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2017063000) {

        // Define field showtotalcount to be added to report_builder.
        $table = new xmldb_table('report_builder');
        $field = new xmldb_field('showtotalcount', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'timemodified');

        // Conditionally launch add field showtotalcount.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Reportbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2017063000, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2017070500) {

        // Update filter to match updated code.
        reportbuilder_rename_data('filters', '*', 'job_assignment', 'allstartdates', 'job_assignment', 'allstartdatesfilter');
        reportbuilder_rename_data('filters', '*', 'job_assignment', 'allenddates', 'job_assignment', 'allenddatesfilter');
        // Fix saved searches.
        totara_reportbuilder_migrate_saved_searches('*', 'job_assignment', 'allstartdates', 'job_assignment', 'allstartdatesfilter');
        totara_reportbuilder_migrate_saved_searches('*', 'job_assignment', 'allenddates', 'job_assignment', 'allenddatesfilter');

        // Update active datetime job custom fields.
        $posfields = $DB->get_records('pos_type_info_field', array('hidden' => '0', 'datatype' => 'datetime'));
        foreach ($posfields as $field) {
            $oldname = "pos_custom_{$field->id}";
            // Update filter to match updated code.
            reportbuilder_rename_data('filters', '*', 'job_assignment', $oldname, 'job_assignment', $oldname.'filter');
            // Fix saved searches.
            totara_reportbuilder_migrate_saved_searches('*', 'job_assignment', $oldname, 'job_assignment', $oldname.'filter');
        }
        $orgfields = $DB->get_records('org_type_info_field', array('hidden' => '0', 'datatype' => 'datetime'));
        foreach ($orgfields as $field) {
            $oldname = "org_custom_{$field->id}";
            // Update filter to match updated code.
            reportbuilder_rename_data('filters', '*', 'job_assignment', $oldname, 'job_assignment', $oldname.'filter');
            // Fix saved searches.
            totara_reportbuilder_migrate_saved_searches('*', 'job_assignment', $oldname, 'job_assignment', $oldname.'filter');
        }

        // Reportbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2017070500, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2017070600) {

        // Define field useclonedb to be added to report_builder.
        $table = new xmldb_table('report_builder');
        $field = new xmldb_field('useclonedb', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'showtotalcount');

        // Conditionally launch add field useclonedb.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Migrate existing setting from the POC patch.
        $useclonedb = get_config('totara_reportbuilder', 'useclonedb');
        if ($useclonedb) {
            $useclonedb = explode(',', $useclonedb);
            foreach ($useclonedb as $rid) {
                $DB->set_field('report_builder', 'useclonedb', 1, array('id' => $rid));
            }
        }
        unset_config('useclonedb', 'totara_reportbuilder');

        // Reportbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2017070600, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2017071300) {
        // Converting select filter operators to text filter operators for search of standard log report.
        $searches = $DB->get_recordset('report_builder_saved', null, '', 'id, search');
        foreach ($searches as $search) {
            $todb = new \stdClass();
            $todb->id = $search->id;
            $filter = unserialize($search->search);
            if (array_key_exists('logstore_standard_log-eventname', $filter)) {
                $newfilter = $filter;
                // Test the operator for 'is equal to'
                if ($filter['logstore_standard_log-eventname']['operator'] == 1) {
                    $newfilter['logstore_standard_log-eventname']['operator'] = 2;
                    $todb->search = serialize($newfilter);
                    $DB->update_record('report_builder_saved', $todb);
                }
                // Test the operator for 'isn't equal to'
                if ($filter['logstore_standard_log-eventname']['operator'] == 2) {
                    $newfilter['logstore_standard_log-eventname']['operator'] = 1;
                    $todb->search = serialize($newfilter);
                    $DB->update_record('report_builder_saved', $todb);
                }
            }
        }
        $searches->close();
        upgrade_plugin_savepoint(true, 2017071300, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2017072700) {
        // Rename competency timecompleted columns to timemodified since that's what they are displaying.
        totara_reportbuilder_migrate_column_names(array('completeddate' => 'timemodified'), 'competency_evidence');
        totara_reportbuilder_migrate_filter_names(array('completeddate' => 'timemodified'), 'competency_evidence');

        upgrade_plugin_savepoint(true, 2017072700, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2017090500) {
        $filters = array(
            'allpositionidnumbers', 'allpositiontypes', 'allposframeids', 'allposframenames', 'allposframeidnumbers',
            'allorganisationidnumbers', 'allorganisationtypes', 'allorgframeids', 'allorgframenames', 'allorgframeidnumbers',
            'allmanageridnumbers', 'allmanagerunobsemails', 'allmanagerobsemails',
        );

        foreach($filters as $fname) {
            // Update filter to match updated code.
            reportbuilder_rename_data('filters', '*', 'job_assignment', $fname, 'job_assignment', $fname . 'filter');
            // Fix saved searches.
            totara_reportbuilder_migrate_saved_searches('*', 'job_assignment', $fname, 'job_assignment', $fname . 'filter');
        }

        // Reportbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2017090500, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2017103000) {

        // Define field defaultvalue to be added to report_builder_filters.
        $table = new xmldb_table('report_builder_filters');
        $field = new xmldb_field('defaultvalue', XMLDB_TYPE_TEXT, null, null, false, null, null, 'region');

        // Conditionally launch add field defaultvalue.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Reportbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2017103000, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2017111400) {

        // Increase max length of report fullname field to 1333 characters.
        $table = new xmldb_table('report_builder');
        $field = new xmldb_field('fullname', XMLDB_TYPE_CHAR, 1333, null,XMLDB_NOTNULL, null);
        $dbman->change_field_precision($table, $field);

        // Reportbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2017111400, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2017120800) {

        // Define field defaultvalue to be added to report_builder_filters.
        $table = new xmldb_table('report_builder_schedule');
        $field1 = new xmldb_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, true, null, 0, 'nextreport');
        $field2 = new xmldb_field('lastmodified', XMLDB_TYPE_INTEGER, '10', null, true, null, 0, 'usermodified');

        // Conditionally launch add field defaultvalue.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Populate new columns.
        totara_reportbuilder_populate_scheduled_reports_usermodified();

        // Reportbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2017120800, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2018041100) {

        // Deprecated content option "hide currently unavailable content" has been removed in the UI via TL-16893.
        // This upgrade deals with removing redundant database entries.
        $sql = "DELETE FROM {report_builder_settings}
                      WHERE type = 'prog_availability_content'
                        AND name = 'enable'";
        $DB->execute($sql, array());

        // Get reports that use the program or certification source that still have content options.
        // This should only be custom content options that have been added.
        $sql = "SELECT DISTINCT rb.id
                  FROM {report_builder_settings} rbs
                  JOIN {report_builder} rb
                    ON rb.id = rbs.reportid
                 WHERE (rb.source = 'program' OR rb.source = 'certification')
                   AND rbs.name = 'enable'
                   AND rbs.value = '1'";
        $reportstoexclude = $DB->get_fieldset_sql($sql);

        if ($reportstoexclude) {
            list($sql, $reportstoexclude_params) = $DB->get_in_or_equal($reportstoexclude, SQL_PARAMS_QM, 'param', false);
            $reportstoexclude_sql = 'AND id ' . $sql;
        } else {
            $reportstoexclude_sql = '';
            $reportstoexclude_params = array();
        }

        // Update all reports using the program or certification source to not use any content options as there should
        // be none. (other than reports with custom content options detected via $reportstoexclude, so let's exclude those).
        $sql = "UPDATE {report_builder}
                   SET contentmode = 0
                 WHERE (source = 'program' OR source = 'certification')
                 $reportstoexclude_sql";
        $DB->execute($sql, $reportstoexclude_params);

        // Reportbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2018041100, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2018051700) {
        // Dropping unused preprocessor tables.
        $tables = ['report_builder_group', 'report_builder_group_assign', 'report_builder_preproc_track'];
        foreach ($tables as $table) {
            $table = new xmldb_table($table);
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        }

        // Reportbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2018051700, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2018111900) {
        // Fully separate certification reports from programs.

        $sources = array('certification', 'certification_completion', 'certification_membership', 'certification_overview');
        $types = array('prog' => 'certif', 'program_completion' => 'certif_completion', 'progcompletion' => 'certcompletion');

        foreach ($sources as $source) {
            foreach ($types as $oldtype => $newtype) {
                // Fix columns.
                $sql = "SELECT DISTINCT c.value
                          FROM {report_builder_columns} c
                          JOIN {report_builder} r ON r.id = c.reportid
                         WHERE r.source = :source AND c.type = :type";
                $values = $DB->get_recordset_sql($sql, array('source' => $source, 'type' => $oldtype));
                foreach ($values as $value) {
                    $value = $value->value;
                    reportbuilder_rename_data('columns', $source, $oldtype, $value, $newtype, $value);
                }
                // Fix filters.
                $sql = "SELECT DISTINCT f.value
                          FROM {report_builder_filters} f
                          JOIN {report_builder} r ON r.id = f.reportid
                         WHERE r.source = :source AND f.type = :type";
                $values = $DB->get_recordset_sql($sql, array('source' => $source, 'type' => $oldtype));
                foreach ($values as $value) {
                    $value = $value->value;
                    reportbuilder_rename_data('filters', $source, $oldtype, $value, $newtype, $value);
                    totara_reportbuilder_migrate_saved_searches($source, $oldtype, $value, $newtype, $value);
                }
            }
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2018111900, 'totara', 'reportbuilder');
    }

    if ($oldversion < 2019021300) {
        // Separate certification enrolled cohort columns/filters certification from programs,
        // and migrate cohort columns/filters into new types.
        $certif_sources = ['certification', 'certification_completion'];
        $prog_value = 'enrolledprogramcohortids';
        $certif_value = 'enrolledcertificationcohortids'; // Certifications also need their value migrated.
        foreach ($certif_sources as $source) {
            reportbuilder_rename_data('columns', $source, 'cohort', $prog_value, 'certif', $certif_value);
            reportbuilder_rename_data('filters', $source, 'cohort', $prog_value, 'certif', $certif_value);
            totara_reportbuilder_migrate_saved_searches($source, 'cohort', $prog_value, 'certif', $certif_value);
        }

        $prog_sources = ['program', 'program_completion', 'dp_program'];
        $value = 'enrolledprogramcohortids';
        foreach ($prog_sources as $source) {
            reportbuilder_rename_data('columns', $source, 'cohort', $value, 'prog', $value);
            reportbuilder_rename_data('filters', $source, 'cohort', $value, 'prog', $value);
            totara_reportbuilder_migrate_saved_searches($source, 'cohort', $value, 'prog', $value);
        }

        $course_sources = ['site_logstore', 'facetoface_events', 'facetoface_sessions', 'facetoface_signin', 'scorm',
                           'dp_course', 'badge_issued', 'courses', 'course_completion', 'site_logs'];
        $value = 'enrolledcoursecohortids';
        foreach ($course_sources as $source) {
            reportbuilder_rename_data('columns', $source, 'cohort', $value, 'course', $value);
            reportbuilder_rename_data('filters', $source, 'cohort', $value, 'course', $value);
            totara_reportbuilder_migrate_saved_searches($source, 'cohort', $value, 'course', $value);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2019021300, 'totara', 'reportbuilder');
    }

    return true;
}
