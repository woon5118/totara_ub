<?php
/**
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
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
 * @author Andrew McGhie <andrew.mcghie@totaralearning.com>
 * @package block_totara_featured_links
 */

function xmldb_block_totara_featured_links_upgrade($oldversion, $block) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2017111600) {

        $sql = "SELECT {$DB->sql_length('btfl.type')} length, type
                  FROM {block_totara_featured_links_tiles} btfl
              ORDER BY {$DB->sql_length('btfl.type')} desc";
        $longest = $DB->get_record_sql($sql, null, IGNORE_MULTIPLE);

        if ($longest && $longest->length > 100) {
            throw new \upgrade_exception('block_totara_featured_links',
                2017111600,
                "The type \"{$longest->type}\" is longer than 100 characters. Please shorten the class name on the tile type to be smaller");
        }

        $table = new xmldb_table('block_totara_featured_links_tiles');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_type($table, $field);

        upgrade_plugin_savepoint(true, 2017111600, 'block', 'totara_featured_links');
    }

    return true;
}