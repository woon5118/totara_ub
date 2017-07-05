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

    return true;
}
