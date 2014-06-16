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
 * @author Ciaran Irvine <ciaran.irvine@totaralms.com>
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package totara
 * @subpackage totara_appraisal
 */

/**
 * Local db upgrades for Totara Core
 */

require_once($CFG->dirroot.'/totara/core/db/utils.php');
/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_totara_appraisal_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2013080501) {

        // Define field appraisalscalevalueid to be added to appraisal_review_data.
        $table = new xmldb_table('appraisal_review_data');
        $field = new xmldb_field('appraisalscalevalueid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0,
                'appraisalquestfieldid');

        // Conditionally launch add field appraisalscalevalueid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Appraisal savepoint reached.
        upgrade_plugin_savepoint(true, 2013080501, 'totara', 'appraisal');
    }

    if ($oldversion < 2014061600) {
        require_once($CFG->dirroot.'/totara/appraisal/lib.php');
        $usercount = $DB->count_records('user', array('deleted' => 1));
        if ($usercount > 0) {
            // This could take some time and use a lot of resources.
            set_time_limit(0);
            raise_memory_limit(MEMORY_EXTRA);
            $i = 0;
            $deletedusers = $DB->get_recordset('user', array('deleted' => 1), null, 'id, username, firstname, lastname, email, idnumber, picture, mnethostid');
            $context = context_system::instance();
            $pbar = new progress_bar('fixdeleteduserappraisal', 500, true);
            $pbar->update($i, $usercount, "Fixing Appraisals for deleted users - {$i}/{$usercount}.");
            foreach ($deletedusers as $user) {
                $event = \core\event\user_deleted::create(
                    array(
                        'objectid' => $user->id,
                        'context' => $context,
                        'other' => array(
                            'username' => $user->username,
                            'email' => $user->email,
                            'idnumber' => $user->idnumber,
                            'picture' => $user->picture,
                            'mnethostid' => $user->mnethostid
                        )
                ));
                appraisal_event_handler::appraisal_user_deleted($event);
                $i++;
                $pbar->update($i, $usercount, "Fixing Appraisals for deleted users - {$i}/{$usercount}.");
            }
            $deletedusers->close();
        }
        upgrade_plugin_savepoint(true, 2014061600, 'totara', 'appraisal');
    }
    return true;
}
