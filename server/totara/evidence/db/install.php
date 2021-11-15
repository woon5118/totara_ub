<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_evidence
 */

/**
 * Database install script
 *
 * @return boolean
 */
function xmldb_totara_evidence_install() {
    global $DB;
    require_once(__DIR__ . '/upgradelib.php');

    totara_evidence_create_completion_types();

    $dbman = $DB->get_manager();

    $old_tables = [
        new xmldb_table('dp_plan_evidence'),
        new xmldb_table('dp_plan_evidence_info_field'),
        new xmldb_table('dp_plan_evidence_info_data'),
        new xmldb_table('dp_plan_evidence_info_data_param'),
        new xmldb_table('dp_evidence_type'),
    ];

    $all_tables_exist = true;
    foreach ($old_tables as $table) {
        $all_tables_exist &= $dbman->table_exists($table);
    }

    if ($all_tables_exist) {
        totara_evidence_migrate();
    }

    return true;
}

/**
 * Retry the installation if it failed
 *
 * @return boolean
 */
function xmldb_totara_evidence_install_recovery() {
    return xmldb_totara_evidence_install();
}
