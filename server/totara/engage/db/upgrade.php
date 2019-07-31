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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_engage
 */

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_totara_engage_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020042200) {
        // Define table engage_share to be created.
        $table = new xmldb_table('engage_share');

        // Adding fields to table engage_share.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ownerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table engage_share.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('ownerid', XMLDB_KEY_FOREIGN, array('ownerid'), 'user', array('id'), 'cascade');

        // Adding indexes to table engage_share.
        $table->add_index('share_unique', XMLDB_INDEX_UNIQUE, array('itemid', 'component'));
        $table->add_index('item_idx', XMLDB_INDEX_NOTUNIQUE, array('itemid'));
        $table->add_index('component_idx', XMLDB_INDEX_NOTUNIQUE, array('component'));
        $table->add_index('contextid_idx', XMLDB_INDEX_NOTUNIQUE, array('contextid'));
        $table->add_index('timecreated_idx', XMLDB_INDEX_NOTUNIQUE, array('timecreated'));

        // Conditionally launch create table for engage_share.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table engage_share_recipient to be created.
        $table = new xmldb_table('engage_share_recipient');

        // Adding fields to table engage_share_recipient.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shareid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sharerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('area', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('visibility', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('notified', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table engage_share_recipient.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('shareid', XMLDB_KEY_FOREIGN, array('shareid'), 'engage_share', array('id'), 'cascade');
        $table->add_key('sharerid', XMLDB_KEY_FOREIGN, array('sharerid'), 'user', array('id'), 'cascade');

        // Adding indexes to table engage_share_recipient.
        $table->add_index('recipient_unique', XMLDB_INDEX_UNIQUE, array('shareid', 'sharerid', 'instanceid', 'area', 'component'));
        $table->add_index('recipient_idx', XMLDB_INDEX_NOTUNIQUE, array('instanceid', 'area', 'component'));
        $table->add_index('timecreated_idx', XMLDB_INDEX_NOTUNIQUE, array('timecreated'));

        // Conditionally launch create table for engage_share_recipient.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table engage_bookmark to be created.
        $table = new xmldb_table('engage_bookmark');

        // Adding fields to table engage_bookmark.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table engage_bookmark.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'), 'cascade');

        // Adding indexes to table engage_bookmark.
        $table->add_index('bookmark_unique', XMLDB_INDEX_UNIQUE, array('userid', 'itemid', 'component'));
        $table->add_index('timecreated_idx', XMLDB_INDEX_NOTUNIQUE, array('timecreated'));

        // Conditionally launch create table for engage_bookmark.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Engage savepoint reached.
        upgrade_plugin_savepoint(true, 2020042200, 'totara', 'engage');
    }

    if ($oldversion < 2020050700) {
        // Define table workspace_discussion to be created.
        $table = new xmldb_table('workspace_discussion');

        // Adding fields to table workspace_discussion.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('content_format', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('pinned', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('content_text', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_modified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table workspace_discussion.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('course_fk', XMLDB_KEY_FOREIGN, array('course_id'), 'course', array('id'));
        $table->add_key('user_fk', XMLDB_KEY_FOREIGN, array('user_id'), 'user', array('id'));

        // Conditionally launch create table for workspace_discussion.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2020050700, 'totara', 'engage');
    }

    if ($oldversion < 2020051200) {
        // Define field timestamp to be added to workspace_discussion.
        $table = new xmldb_table('workspace_discussion');
        $field = new xmldb_field('timestamp', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'time_modified');

        // Conditionally launch add field timestamp.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $pinned_field = new xmldb_field('pinned');

        // Conditionally launch drop field pinned.
        if ($dbman->field_exists($table, $pinned_field)) {
            $dbman->drop_field($table, $pinned_field);
        }

        $time_pinned_field = new xmldb_field('time_pinned', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'content_text');

        // Conditionally launch add field time_pinned.
        if (!$dbman->field_exists($table, $time_pinned_field)) {
            $dbman->add_field($table, $time_pinned_field);
        }

        // Workspace savepoint reached.
        upgrade_plugin_savepoint(true, 2020051200, 'totara', 'engage');
    }

    if ($oldversion < 2020052100) {
        // Changing the unique recommender interaction index
        $table = new xmldb_table('ml_recommender_interactions');
        $index = new xmldb_index('useriditemidcomponenttime', XMLDB_INDEX_UNIQUE, [
            'user_id', 'item_id', 'component', 'time_created'
        ]);

        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $new_index = new xmldb_index('useriditemidcomponentinteractiontime', XMLDB_INDEX_UNIQUE, [
            'user_id', 'item_id', 'component', 'interaction', 'time_created'
        ]);

        if (!$dbman->index_exists($table, $new_index)) {
            $dbman->add_index($table, $new_index);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2020052100, 'totara', 'engage');
    }

    if ($oldversion < 2020061600) {
        require_once("{$CFG->dirroot}/lib/filelib.php");
        $fs = get_file_storage();

        $update_json_fn = function (string $json_data) use ($fs): string {
            $json_content = json_decode($json_data, true);
            foreach ($json_content['content'] as &$node) {
                switch ($node['type']) {
                    case 'image':
                    case 'video':
                    case 'audio':

                        $attrs = $node['attrs'];
                        $filename = $attrs['filename'];

                        $file = $fs->get_file(
                            $attrs['contextid'],
                            $attrs['component'],
                            $attrs['filearea'],
                            $attrs['itemid'],
                            '/',
                            $filename
                        );

                        if (!$file) {
                            $identifier = "{$attrs['component']}-{$attrs['filearea']}-{$attrs['itemid']}";
                            debugging("Unable to find file for identifier '{$identifier}'", DEBUG_DEVELOPER);

                            break;
                        }

                        if ('image' === $node['type']) {
                            $node['attrs'] = [
                                'filename' => $filename,
                                'url' => "@@PLUGINFILE@@/{$filename}",
                                'alttext' => $attrs['alttext'] ?? null
                            ];
                        } else if ('video' === $node['type']) {
                            $node['attrs'] = [
                                'filename' => $filename,
                                'url' => "@@PLUGINFILE@@/{$filename}",
                                'mime_type' => $file->get_mimetype()
                            ];
                        } else {
                            // Audio
                            $node['attrs'] = [
                                'filename' => $filename,
                                'url' => "@@PLUGINFILE@@/{$filename}",
                                'mime_type' => $file->get_mimetype()
                            ];
                        }

                        break;

                    case 'attachments':
                        $attachments = $node['content'];
                        foreach ($attachments as &$attachment) {
                            $attrs = $attachment['attrs'];
                            $filename = $attrs['filename'];

                            $file = $fs->get_file(
                                $attrs['contextid'],
                                $attrs['component'],
                                $attrs['filearea'],
                                $attrs['itemid'],
                                '/',
                                $filename
                            );

                            if (!$file) {
                                $identifier = "{$attrs['component']}-{$attrs['filearea']}-{$attrs['itemid']}";
                                debugging("Unable to find file for identifier '{$identifier}'", DEBUG_DEVELOPER);

                                break;
                            }

                            $attachment['attrs'] = [
                                'filename' => $filename,
                                'size' => $file->get_filesize(),
                                'url' => "@@PLUGINFILE@@/{$filename}"
                            ];
                        }
                    break;
                }
            }

            return json_encode($json_content);
        };

        // Updating engage articles content.
        $articles = $DB->get_records('engage_article', ['format' => FORMAT_JSON_EDITOR]);
        if (is_array($articles) && !empty($articles)) {
            foreach ($articles as $article) {
                $article->content = $update_json_fn($article->content);
                $article->content = stripslashes($article->content);
                $DB->update_record('engage_article', $article);
            }
        }

        // Updating comments conmtent.
        $comments = $DB->get_records('totara_comment', ['format' => FORMAT_JSON_EDITOR]);
        if (is_array($comments) && !empty($comments)) {
            foreach ($comments as $comment) {
                $comment->content = $update_json_fn($comment->content);
                $comment->content = stripslashes($comment->content);
                $DB->update_record('totara_comment', $comment);
            }
        }

        // Updating workspace discussion content
        $discussions = $DB->get_records('workspace_discussion', ['content_format' => FORMAT_JSON_EDITOR]);
        if (is_array($discussions) && !empty($discussions)) {
            foreach ($discussions as $discussion) {
                $discussion->content = $update_json_fn($discussion->content);
                $discussion->content = stripslashes($discussion->content);
                $discussion->content_text = content_to_text($discussion->content, FORMAT_JSON_EDITOR);

                $DB->update_record('workspace_discussion', $discussion);
            }
        }

        // Editor save point
        upgrade_plugin_savepoint(true, 2020061600, 'totara', 'engage');
    }

    if ($oldversion < 2020061800) {

        // Define field summaryformat to be added to playlist.
        $table = new xmldb_table('playlist');
        $field = new xmldb_field('summaryformat', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'summary');

        // Conditionally launch add field summaryformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Set default to FORMAT_PLAIN for playlist summary format
        $records = $DB->get_records('playlist', null, '', 'id, summaryformat');
        foreach ($records as $record) {
            $record->summaryformat = FORMAT_PLAIN;
        }

        // Then make the field not null after data is populated.
        $field->setNotNull(XMLDB_NOTNULL);
        $dbman->change_field_notnull($table, $field);

        // Playlist savepoint reached.
        upgrade_plugin_savepoint(true, 2020061800, 'totara', 'engage');
    }

    if ($oldversion < 2020062200) {
        // Rename the recommended actions table if it exists
        $table = new xmldb_table('totara_recommendations_trending');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'ml_recommender_trending');
        } else {
            // If it isn't there to rename, recreate it (if it doesn't exist)
            $table = new xmldb_table('ml_recommender_trending');
            if (!$dbman->table_exists($table)) {
                $table->setComment('Trending recommendation items');
                $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
                $table->add_field('unique_id', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL);
                $table->add_field('item_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
                $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL);
                $table->add_field('area', XMLDB_TYPE_CHAR, '100');
                $table->add_field('counter', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
                $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
                $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
                $table->add_index('trendingitem', XMLDB_INDEX_NOTUNIQUE, ['item_id', 'component', 'area']);
                $table->add_index('resourcetypeall', XMLDB_INDEX_NOTUNIQUE, ['time_created', 'counter', 'item_id', 'component']);
                $table->add_index('resourcetype', XMLDB_INDEX_NOTUNIQUE, ['time_created', 'component', 'counter', 'item_id']);

                $dbman->create_table($table);
            }
        }

        // No more suggestions
        $table = new xmldb_table('ml_recommender_suggestions');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Alter the interactions table
        $table = new xmldb_table('ml_recommender_interactions');

        // Drop the indexes
        $indexes = [];
        $indexes[] = new xmldb_index('component_idx', XMLDB_INDEX_NOTUNIQUE, ['component']);
        $indexes[] = new xmldb_index('area_idx', XMLDB_INDEX_NOTUNIQUE, ['area']);
        $indexes[] = new xmldb_index('useriditemidcomponentinteractiontime', XMLDB_INDEX_UNIQUE, ['user_id', 'item_id', 'component', 'interaction', 'time_created']);

        foreach ($indexes as $index) {
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }
        }

        $col_component = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'component');
        $dbman->change_field_precision($table, $col_component);

        $col_component = new xmldb_field('area', XMLDB_TYPE_CHAR, '100', null, false, null, null, 'area');
        $col_component->setLength(100);
        $dbman->change_field_precision($table, $col_component);

        foreach ($indexes as $index) {
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        // Add the users recommendations table
        $table = new xmldb_table('ml_recommender_users');
        $table->setComment('Suggested content for users');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('unique_id', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL);
        $table->add_field('item_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL);
        $table->add_field('area', XMLDB_TYPE_CHAR, '100');
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('score', XMLDB_TYPE_NUMBER, '20,12', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('user_fk', XMLDB_KEY_FOREIGN, array('user_id'), 'user', array('id'), 'cascade');
        $table->add_index('component_idx', XMLDB_INDEX_NOTUNIQUE, ['component']);
        $table->add_index('area_idx', XMLDB_INDEX_NOTUNIQUE, ['area']);
        $table->add_index('score_idx', XMLDB_INDEX_NOTUNIQUE, ['score']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Add the items recommendations table
        $table = new xmldb_table('ml_recommender_items');
        $table->setComment('Related content');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('unique_id', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL);
        $table->add_field('target_item_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('target_component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL);
        $table->add_field('target_area', XMLDB_TYPE_CHAR, '100');
        $table->add_field('item_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL);
        $table->add_field('area', XMLDB_TYPE_CHAR, '100');
        $table->add_field('score', XMLDB_TYPE_NUMBER, '20,12', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('item_component_idx', XMLDB_INDEX_NOTUNIQUE, ['item_id', 'component']);
        $table->add_index('area_idx', XMLDB_INDEX_NOTUNIQUE, ['area']);
        $table->add_index('target_item_component_idx', XMLDB_INDEX_NOTUNIQUE, ['target_item_id', 'target_component']);
        $table->add_index('target_area_idx', XMLDB_INDEX_NOTUNIQUE, ['target_area']);
        $table->add_index('score_idx', XMLDB_INDEX_NOTUNIQUE, ['score']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2020062200, 'totara', 'engage');
    }

    if ($oldversion < 2020071300) {
        $items = [
            [
                'component' => 'engage_article',
                'itemtype' => 'engage_article'
            ],
            [
                'component' => 'engage_survey',
                'itemtype' => 'engage_survey'
            ]
        ];

        foreach ($items as $item) {
            $tag_area = $DB->get_record(
                'tag_area',
                [
                    'component' => $item['component'],
                    'itemtype' => $item['itemtype'],
                    'tagcollid' => $CFG->topic_collection_id
                ]
            );

            $tag_area->itemtype = 'engage_resource';
            $DB->update_record('tag_area', $tag_area);
        }

        upgrade_plugin_savepoint(true, 2020071300, 'totara', 'engage');
    }

    if ($oldversion < 2020071500) {
        $bot_table = new xmldb_table('bot');
        if ($dbman->table_exists($bot_table)) {
            $dbman->drop_table($bot_table);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2020071500, 'totara', 'engage');
    }

    if ($oldversion < 2020073000) {
        $reportedcontent_table = new xmldb_table('totara_reportedcontent');
        $field = new xmldb_field('complainer_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $dbman->change_field_notnull($reportedcontent_table, $field);

        $index = new xmldb_index('complain_uniq_idx', XMLDB_INDEX_NOTUNIQUE, array('complainer_id', 'item_id', 'context_id', 'component', 'area'));
        if ($dbman->index_exists($reportedcontent_table, $index)) {
            $dbman->drop_index($reportedcontent_table, $index);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2020073000, 'totara', 'engage');
    }

    if ($oldversion < 2020080300) {
        // Renaming table workspace owner.
        $workspace = new xmldb_table('workspace_owner');
        if ($dbman->table_exists($workspace)) {
            $dbman->rename_table($workspace, 'workspace');

            $workspace_table = new xmldb_table('workspace');
            $mode_field = new xmldb_field('private', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'user_id');

            // Conditionally launch add field mode.
            if (!$dbman->field_exists($workspace_table, $mode_field)) {
                $dbman->add_field($workspace_table, $mode_field);
            }
        }

        upgrade_plugin_savepoint(true, 2020080300, 'totara', 'engage');
    }

    return true;
}