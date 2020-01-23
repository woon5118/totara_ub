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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package core
 */

/**
 * Transforms Moodle installation to Moodle 3.4.9 data format.
 */
function xmldb_core_premigrate() {
    global $CFG, $DB;
    $dbman = $DB->get_manager();

    $version = $CFG->version;

    if ($version >= 2019102500.04) {
        $table = new xmldb_table('h5p_libraries');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('h5p_library_dependencies');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('h5p');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('h5p_contents_libraries');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('h5p_libraries_cachedassets');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_main_savepoint(2019102500.03);
    }

    if ($version >= 2019072200.00) {
        $table = new xmldb_table('course');
        $field = new xmldb_field('relativedatesmode', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'enddate');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_main_savepoint(2019072100.00);
    }

    // Moodle 3.8 pre-migration line.

    if ($version >= 2019050600.00) {
        $table = new xmldb_table('badge_backpack');
        $field = new xmldb_field('apiversion', XMLDB_TYPE_CHAR, '12', null, XMLDB_NOTNULL, null, '1.0');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('badge_external_backpack');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('badge_external');
        $field = new xmldb_field('entityid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('badge_external_identifier');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('badge_backpack');
        $key = new xmldb_key('externalbackpack', XMLDB_KEY_FOREIGN, ['externalbackpackid'], 'badge_external_backpack', ['id']);
        $dbman->drop_key($table, $key);

        $table = new xmldb_table('badge_backpack');
        $field = new xmldb_field('externalbackpackid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'password');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('badge_backpack');
        $field = new xmldb_field('backpackurl', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $version = premigrate_main_savepoint(2019050500.00);
    }

    if ($version >= 2019042300.03) {
        $table = new xmldb_table('message');
        $field = new xmldb_field('customdata', XMLDB_TYPE_TEXT, null, null, null, null, null, 'eventtype');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_main_savepoint(2019042300.02);
    }

    if ($version >= 2019041000.02) {
        $table = new xmldb_table('messages');
        $field = new xmldb_field('fullmessagetrust', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'timecreated');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_main_savepoint(2019041000.01);
    }

    if ($version >= 2019040600.04) {
        $table = new xmldb_table('backup_controllers');
        $field = new xmldb_field('progress', XMLDB_TYPE_NUMBER, '15, 14', null, XMLDB_NOTNULL, null, '0', 'timemodified');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_main_savepoint(2019040600.03);
    }

    if ($version >= 2019032900.00) {
        $table = new xmldb_table('badge_alignment');
        if ($dbman->table_exists($table)) {
            if ($dbman->table_exists('badge_competencies')) {
                $dbman->drop_table($table);
            } else {
                $dbman->rename_table($table, 'badge_competencies');
            }
        }

        $version = premigrate_main_savepoint(2019032800.00);
    }

    if ($version >= 2019030800.00) {
        $table = new xmldb_table('message_conversation_actions');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_main_savepoint(2019030700.00);
    }

    if ($version >= 2019011801.00) {
        $table = new xmldb_table('customfield_category');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('customfield_field');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('customfield_data');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_main_savepoint(2019011800.00);
    }

    if ($version >= 2019011500.00) {
        $table = new xmldb_table('task_log');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_main_savepoint(2019011400.00);
    }

    // Moodle 3.7 pre-migration line.

    if ($version >= 2018111301.00) {
        $table = new xmldb_table('context');
        $field = new xmldb_field('locked', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'depth');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_main_savepoint(2018111300.00);
    }

    if ($version >= 2018110500.01) {
        $tablebadge = new xmldb_table('badge');
        $fieldversion = new xmldb_field('version', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'nextcron');
        $fieldlanguage = new xmldb_field('language', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'version');
        $fieldimageauthorname = new xmldb_field('imageauthorname', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'language');
        $fieldimageauthoremail = new xmldb_field('imageauthoremail', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'imageauthorname');
        $fieldimageauthorurl = new xmldb_field('imageauthorurl', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'imageauthoremail');
        $fieldimagecaption = new xmldb_field('imagecaption', XMLDB_TYPE_TEXT, null, null, null, null, null, 'imageauthorurl');
        if ($dbman->field_exists($tablebadge, $fieldversion)) {
            $dbman->drop_field($tablebadge, $fieldversion);
        }
        if ($dbman->field_exists($tablebadge, $fieldlanguage)) {
            $dbman->drop_field($tablebadge, $fieldlanguage);
        }
        if ($dbman->field_exists($tablebadge, $fieldimageauthorname)) {
            $dbman->drop_field($tablebadge, $fieldimageauthorname);
        }
        if ($dbman->field_exists($tablebadge, $fieldimageauthoremail)) {
            $dbman->drop_field($tablebadge, $fieldimageauthoremail);
        }
        if ($dbman->field_exists($tablebadge, $fieldimageauthorurl)) {
            $dbman->drop_field($tablebadge, $fieldimageauthorurl);
        }
        if ($dbman->field_exists($tablebadge, $fieldimagecaption)) {
            $dbman->drop_field($tablebadge, $fieldimagecaption);
        }

        $table = new xmldb_table('badge_endorsement');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('badge_related');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('badge_competencies');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_main_savepoint(2018110500.00);
    }

    if ($version >= 2018101800.00) {
        $table = new xmldb_table('favourite');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_main_savepoint(2018101700.00);
    }

    if ($version >= 2018092800.01) {
        $table = new xmldb_table('message_contacts');
        $field = new xmldb_field('blocked', XMLDB_TYPE_INTEGER, 1, null, XMLDB_NOTNULL, null, 0);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $version = premigrate_main_savepoint(2018092800.00);
    }

    if ($version >= 2018092800.00) {
        $table = new xmldb_table('message_contacts');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('message_contacts');
        $index = new xmldb_index('userid-contactid', XMLDB_INDEX_UNIQUE, ['userid', 'contactid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $table = new xmldb_table('message_contacts');
        $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $dbman->drop_key($table, $key);

        $table = new xmldb_table('message_contacts');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');
        $dbman->change_field_default($table, $field);

        $key = new xmldb_key('contactid', XMLDB_KEY_FOREIGN, ['contactid'], 'user', ['id']);
        $dbman->drop_key($table, $key);

        $table = new xmldb_table('message_contacts');
        $field = new xmldb_field('contactid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'userid');
        $dbman->change_field_default($table, $field);

        $table = new xmldb_table('message_contacts');
        $index = new xmldb_index('userid-contactid', XMLDB_INDEX_UNIQUE, ['userid', 'contactid']);
        $dbman->add_index($table, $index);

        $table = new xmldb_table('message_contact_requests');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('message_users_blocked');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_main_savepoint(2018092700.00);
    }

    if ($version >= 2018092100.04) {
        $table = new xmldb_table('question_categories');
        $index = new xmldb_index('contextididnumber', XMLDB_INDEX_UNIQUE, array('contextid', 'idnumber'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $table = new xmldb_table('question_categories');
        $field = new xmldb_field('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('question');
        $index = new xmldb_index('categoryidnumber', XMLDB_INDEX_UNIQUE, array('category', 'idnumber'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $table = new xmldb_table('question');
        $field = new xmldb_field('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_main_savepoint(2018092100.00);
    }

    if ($version >= 2018062800.03) {
        $table = new xmldb_table('event');
        $field = new xmldb_field('location', XMLDB_TYPE_TEXT, null, null, null, null, null, 'priority');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        $version = premigrate_main_savepoint(2018062800.02);
    }

    // Moodle 3.6 pre-migration line.

    if ($version >= 2018040500.01) {
        $table = new xmldb_table('cohort');
        $field = new xmldb_field('theme', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'timemodified');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_main_savepoint(2018040500.00);
    }

    if ($version >= 2018032200.05) {
        $table = new xmldb_table('message_working');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('unreadmessageid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('processorid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('unreadmessageid_idx', XMLDB_INDEX_NOTUNIQUE, array('unreadmessageid'));
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $version = premigrate_main_savepoint(2018032200.04);
    }

    if ($version >= 2018032200.01) {
        $table = new xmldb_table('messages');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('message_conversations');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('message_conversation_members');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('message_user_actions');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('notifications');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_main_savepoint(2018032200.00);
    }

    if ($version >= 2018022800.03) {
        $table = new xmldb_table('tag_area');
        $field = new xmldb_field('multiplecontexts', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'showstandard');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_main_savepoint(2018022800.02);
    }

    if ($version >= 2018022800.02) {
        $table = new xmldb_table('tag_instance');
        $index = new xmldb_index('taggeditem', XMLDB_INDEX_UNIQUE, array('component', 'itemtype', 'itemid', 'contextid', 'tiuserid', 'tagid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Get rid of multiple tags.
        $sql = "SELECT MIN(id) AS id, COUNT(id) AS duplicates, component, itemtype, itemid, tiuserid, tagid
                  FROM {tag_instance}
              GROUP BY component, itemtype, itemid, tiuserid, tagid
                HAVING COUNT(id) > 1";
        $instances = $DB->get_records_sql($sql);
        foreach ($instances as $instance) {
            $select = "id <> :id AND duplicates = :duplicates AMD component = :component
                       AND itemtype = :itemtype AND itemid = :itemid AND tiuserid = :tiuserid AND tagid = :tagid";
            $DB->delete_records_select('tag_instance', $select, (array)$instance);
        }

        $table = new xmldb_table('tag_instance');
        $index = new xmldb_index('taggeditem', XMLDB_INDEX_UNIQUE, array('component', 'itemtype', 'itemid', 'tiuserid', 'tagid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $version = premigrate_main_savepoint(2018022800.01);
    }

    if ($version >= 2017122200.01) {
        $table = new xmldb_table('search_index_requests');
        $index = new xmldb_index('indexprioritytimerequested', XMLDB_INDEX_NOTUNIQUE, array('indexpriority', 'timerequested'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $table = new xmldb_table('search_index_requests');
        $field = new xmldb_field('indexpriority', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'partialtime');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_main_savepoint(2017122200.00);
    }

    if ($version >= 2017121900.00) {
        $table = new xmldb_table('role_allow_view');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_main_savepoint(2017121800.00);
    }

    // This site is ready for migration from Moodle 3.4.9 to Totara 13.
    if ($version > 2017111309.00) {
        $version = premigrate_main_savepoint(2017111309.00);
    }
    set_config('release', '3.4.9 (Build: 20190513)');
}
