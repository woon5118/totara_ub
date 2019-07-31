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
 * @author Vernon Denny <vernon.denny@totaralearning.com>
 * @package ml_recommender
 */

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_ml_recommender_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Recreate tables with correct names.
    if ($oldversion < 2020062202) {
        // Drop tables to be recreated.
        $rename_tables = [
            'recommender_interactions',
            'recommender_users',
            'recommender_items',
            'recommender_trending'
        ];

        foreach ($rename_tables as $rename_table) {
            $table = new xmldb_table($rename_table);
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        }

        // New table: ml_recommender_interactions.
        $table = new xmldb_table('ml_recommender_interactions');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('item_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('area', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('interaction', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('rating', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Add keys to table ml_recommender_interactions.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Add indexes to table ml_recommender_interactions.
        $table->add_index('component_idx', XMLDB_INDEX_NOTUNIQUE, ['component']);
        $table->add_index('area_idx', XMLDB_INDEX_NOTUNIQUE, ['area']);
        $table->add_index('useriditemidcomponentinteractiontime', XMLDB_INDEX_UNIQUE, ['user_id', 'item_id', 'component', 'interaction', 'time_created']);

        // Create table ml_recommender_interactions.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // New table: ml_recommender_users.
        $table = new xmldb_table('ml_recommender_users');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('unique_id', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('item_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('area', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('score', XMLDB_TYPE_NUMBER, '20,12', null, XMLDB_NOTNULL, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Add keys to table ml_recommender_users.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('user_fk', XMLDB_KEY_FOREIGN, ['user_id'], 'user', ['id'], 'cascade');

        // Add indexes to table ml_recommender_users.
        $table->add_index('component_idx', XMLDB_INDEX_NOTUNIQUE, ['component']);
        $table->add_index('area_idx', XMLDB_INDEX_NOTUNIQUE, ['area']);
        $table->add_index('score_idx', XMLDB_INDEX_NOTUNIQUE, ['score']);

        // Create table ml_recommender_users.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // New table: ml_recommender_items.
        $table = new xmldb_table('ml_recommender_items');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('unique_id', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('target_item_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('target_component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('target_area', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('item_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('area', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('score', XMLDB_TYPE_NUMBER, '20,12', null, XMLDB_NOTNULL, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Add keys to table ml_recommender_items.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Add indexes to table ml_recommender_items.
        $table->add_index('item_component_idx', XMLDB_INDEX_NOTUNIQUE, ['item_id', 'component']);
        $table->add_index('target_component_idx', XMLDB_INDEX_NOTUNIQUE, ['target_item_id', 'target_component']);
        $table->add_index('target_area_idx', XMLDB_INDEX_NOTUNIQUE, ['target_area']);
        $table->add_index('score_idx', XMLDB_INDEX_NOTUNIQUE, ['score']);

        // Create table ml_recommender_items.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // New table: ml_recommender_trending.
        $table = new xmldb_table('ml_recommender_trending');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('unique_id', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('item_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('area', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('counter', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Add keys to table ml_recommender_trending.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Add indexes to table ml_recommender_trending.
        $table->add_index('trendingitem', XMLDB_INDEX_NOTUNIQUE, ['item_id', 'component', 'area']);
        $table->add_index('resourcetypeall', XMLDB_INDEX_NOTUNIQUE, ['time_created', 'counter', 'item_id', 'component']);
        $table->add_index('resourcetype', XMLDB_INDEX_NOTUNIQUE, ['time_created', 'component', 'counter', 'item_id']);

        // Create table ml_recommender_trending.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2020062202, 'ml', 'recommender');
    }

    return true;
}