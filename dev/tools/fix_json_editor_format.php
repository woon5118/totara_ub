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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 */
// This script is to help internal development on converting all the field within database that store
// the editor format of JSON_EDITOR to current value - which is from 42 -> 5. Sadly that we cannot keep 42.

// This script HAVE TO BE REMOVED prior to the release.
define('CLI_SCRIPT', true);

require_once(__DIR__ . "/../../server/config.php");
global $CFG, $DB;

$db_manager = $DB->get_manager();
$structure = $db_manager->get_install_xml_schema();

$table_prefix = $CFG->prefix;
$tables = $structure->getTables();
echo "\nBegin the transaction:\n";

foreach ($tables as $table) {
    $table_name = $table->getName();
    echo "Check table: {$table_name}\n";

    $fields = $table->getFields();

    foreach ($fields as $field) {
        $field_name = $field->getName();
        echo "\tCheck field: {$field_name}";

        if (false !== stripos($field_name, "format")) {
            $is_valid = (
                (1 == $field->getLength() || 2 == $field->getLength()) &&
                (XMLDB_TYPE_CHAR == $field->getType() || XMLDB_TYPE_INTEGER == $field->getType())
            );

            if ($is_valid) {
                echo " ---- affected";

                $DB->execute(
                    "UPDATE {$table_prefix}{$table_name} SET {$field_name}=? WHERE {$field_name}=?",
                    [FORMAT_JSON_EDITOR, 42]
                );

            }
        }

        echo "\n";
    }
}