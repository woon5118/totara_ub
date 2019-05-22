<?php
/*
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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package totara_core
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Various tests to confirm all db/install.xml files defined correctly.
 */
class totara_core_install_xmls_testcase extends advanced_testcase {
    public function test_key_index_duplicates() {
        global $DB;
        $dbmanager = $DB->get_manager();

        $schema = $dbmanager->get_install_xml_schema();

        $tables = $schema->getTables();
        foreach ($tables as $table) {
            $tablename = $table->getName();
            $keys = $table->getKeys();
            foreach ($keys as $key) {
                if ($key->getType() == XMLDB_KEY_PRIMARY) {
                    continue;
                }
                $keyname = $key->getName();
                $fields = $key->getFields();
                $indexes = $table->getIndexes();
                foreach ($indexes as $index) {
                    $indexname = $index->getName();
                    $this->assertNotSame($index->getFields(), $fields, "'$tablename' table key '$keyname' duplicates index '$indexname'");
                }
            }
        }
    }
}
