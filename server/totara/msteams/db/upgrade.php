<?php
/**
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package totara_msteams
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the Microsoft Teams
 *
 * @param int $oldversion The version that we are upgrading from
 */
function xmldb_totara_msteams_upgrade($oldversion) {
    global $DB, $CFG;
    /** @var moodle_database $DB */

    $dbman = $DB->get_manager();

    if ($oldversion < 2020040100) {

        // Just drop old tables and create new tables :P
        $xmldbfile = new xmldb_file($CFG->dirroot . '/totara/msteams/db/install.xml');
        $xmldbfile->setDTD($CFG->dirroot . '/lib/xmldb/xmldb.dtd');
        $xmldbfile->setSchema($CFG->dirroot . '/lib/xmldb/xmldb.xsd');
        $xmldbfile->loadXMLStructure();
        $structure = $xmldbfile->getStructure();

        $droptables = [];
        foreach ($structure->getTables() as $table) {
            $droptables[$table->getName()] = 1;
            foreach ($table->getKeys() as $key) {
                // Watch out foreign key references!
                if ($key->getType() == XMLDB_KEY_FOREIGN) {
                    $reftable = $key->getRefTable();
                    unset($droptables[$reftable]);
                    $droptables[$reftable] = 1;
                }
            }
        }
        foreach ($droptables as $tablename => $unused) {
            if (strpos($tablename, 'totara_msteams_') === 0) {
                $table = new xmldb_table($tablename);
                if ($dbman->table_exists($table)) {
                    $dbman->drop_table($table);
                }
            }
        }

        foreach ($structure->getTables() as $table) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2020040100, 'totara', 'msteams');
    }

    return true;
}
