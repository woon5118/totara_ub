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
 * @package mod_facetoface
 */

require_once($CFG->dirroot.'/mod/facetoface/db/upgradelib.php');

// This file keeps track of upgrades to
// the facetoface module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

/**
 * Local database upgrade script
 *
 * @param   int $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean always true
 */
function xmldb_facetoface_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 10 branching line.

    if ($oldversion < 2016110200) {

        // Remove seminar notifications for removed seminars.
        // Regression T-14050.
        $sql = "DELETE FROM {facetoface_notification} WHERE facetofaceid NOT IN (SELECT id FROM {facetoface})";
        $DB->execute($sql);

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2016110200, 'facetoface');
    }

    if ($oldversion < 2016110900) {

        $table = new xmldb_table('facetoface_notification_tpl');
        $field = new xmldb_field('ccmanager', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'body');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $templates = $DB->get_records('facetoface_notification_tpl', null, '', 'id, reference');
        $transaction = $DB->start_delegated_transaction();
        $references = array('confirmation', 'cancellation', 'reminder', 'request', 'adminrequest', 'registrationclosure');
        foreach ($templates as $template) {
            $todb = new stdClass();
            $todb->id = $template->id;
            $todb->ccmanager = (in_array($template->reference, $references) ? 1 : 0);
            $DB->update_record('facetoface_notification_tpl', $todb);
        }
        $transaction->allow_commit();

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2016110900, 'facetoface');
    }

    if ($oldversion < 2016122101) {
        // Adding "Below is the message that was sent to learner:" to the end of prefix text for existing notifications.
        // This will upgrade only non-changed text in comparison to original v9 manager prefix.
        facetoface_upgradelib_managerprefix_clarification();

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2016122101, 'facetoface');
    }

    return true;

}
