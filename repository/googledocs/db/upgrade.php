<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_repository_googledocs_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 10 branching line.

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017111300) {
        // Set default import formats from Google.
        if (get_config('googledocs', 'documentformat') === false) {
            set_config('documentformat', 'rtf', 'googledocs');
        }
        if (get_config('googledocs', 'drawingformat') === false) {
            set_config('drawingformat', 'pdf', 'googledocs');
        }
        if (get_config('googledocs', 'presentationformat') === false) {
            set_config('presentationformat', 'pptx', 'googledocs');
        }
        if (get_config('googledocs', 'spreadsheetformat') === false) {
            set_config('spreadsheetformat', 'xlsx', 'googledocs');
        }

        // Plugin savepoint reached.
        upgrade_plugin_savepoint(true, 2017111300, 'repository', 'googledocs');
    }

    return true;
}
