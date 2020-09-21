<?php
/*
 * This file is part of Totara Learn
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

/**
 * Database upgrade script
 *
 * @param integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return bool
 *
 */

use mod_perform\entities\activity\element;
use mod_perform\models\activity\element_identifier;

defined('MOODLE_INTERNAL') || die();

function xmldb_perform_upgrade($oldversion) {
    global $DB, $CFG;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020090103) {

        // Define field notified_at to be added to perform_manual_relation_selector.
        $table = new xmldb_table('perform_manual_relation_selector');
        $field = new xmldb_field('notified_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'user_id');

        // Conditionally launch add field notified_at.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020090103, 'perform');
    }


    return true;
}
