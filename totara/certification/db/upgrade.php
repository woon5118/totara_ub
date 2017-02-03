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
 * @author Jon Sharp <jon.sharp@catalyst-eu.net>
 * @package totara
 * @subpackage certification
 */

// Certification db upgrades.

require_once($CFG->dirroot.'/totara/certification/db/upgradelib.php');

/**
 * Certification database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade)
 * @return  boolean $result
 */
function xmldb_totara_certification_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 10 branching line.

    // TL-12606 Recalculate non-zero course set group completion records.
    if ($oldversion < 2017020700) {

        totara_certification_upgrade_non_zero_prog_completions();

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2017020700, 'totara', 'certification');
    }

    return true;
}
