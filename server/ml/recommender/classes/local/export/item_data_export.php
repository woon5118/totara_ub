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
 * @package ml_recommender
 */
namespace ml_recommender\local\export;


/**
 * Export class for item (i.e. articles, playlists) data.
 */
class item_data_export extends export {

    /**
     * @param \csv_export_writer $writer
     * @return bool
     */
    public function export(\csv_export_writer $writer): bool {
        global $DB;

        // Build sql.
        $unique_article_id = $DB->sql_concat("'engage_article'", 'er.id');
        $unique_playlist_id = $DB->sql_concat("'totara_playlist'", 'tp.id');
        $unique_workspace_id = $DB->sql_concat("'container_workspace'", 'cw.id');
        $unique_course_id = $DB->sql_concat("'container_course'", 'cc.id');
        $sql = "
        SELECT {$unique_article_id} AS uniqueid, er.id, er.name AS title, ea.content AS content, ea.format as summaryformat
        FROM {engage_resource} er
        JOIN {engage_article} ea ON er.instanceid = ea.id
        WHERE er.resourcetype = 'engage_article'
        UNION ALL
        SELECT {$unique_playlist_id} AS uniqueid, tp.id, tp.name AS title, tp.summary AS content, tp.summaryformat
        FROM {playlist} tp
        UNION ALL
        SELECT {$unique_workspace_id} AS uniqueid, cw.id, cw.fullname AS title, cw.summary AS content, cw.summaryformat
        FROM {course} cw WHERE cw.containertype = 'container_workspace'
        UNION ALL
        SELECT {$unique_course_id} AS uniqueid, cc.id, cc.fullname AS title, cc.summary AS content, cc.summaryformat
        FROM {course} cc
        JOIN {enrol} te on cc.id = te.courseid
        WHERE cc.containertype = 'container_course' AND te.enrol = 'self'
        ";

        // Set recordset cursor.
        $recordset = $DB->get_recordset_sql($sql);
        if (!$recordset->valid()) {
            return false;
        }

        // Components.
        $component_names = [
            'container_course',
            'container_workspace',
            'engage_article',
            'totara_playlist'
        ];
        $component_names = $this->one_hot_components($component_names);

        // Component names are not consistently applied in tags table - note differences here.
        $tag_component_names = [
            'container_course' => 'course'
        ];

        // List of topics.
        $topics = $this->get_topics();

        // Build headings -> id, [components], [topics], document.
        $headings = ['item_id'];
        foreach ($component_names as $component_name => $onehot) {
            $headings[] = $component_name;
        }
        foreach ($topics as $key => $topic) {
            $headings[] = $topic;
        }
        $headings[] = 'document';

        // Column headings for csv file.
        $writer->add_data($headings);

        // Write the feature data for each item.
        foreach ($recordset as $item) {
            $cells = [$item->uniqueid];

            foreach ($component_names as $component_name => $onehot) {
                if (strpos($item->uniqueid, $component_name) === 0) {
                    $component = $component_name;
                    $component_onehot = $onehot;
                    break;
                }
            }

            // One-hot encode component for this item.
            foreach ($component_onehot as $id => $onehot) {
                $cells[] = $onehot;
            }

            // Retrieve topics for this specific item.
            $resource_topics = [];
            if (isset($tag_component_names[$component])) {
                $select = 'itemid = ? and itemtype = ?';
                $this_item_topics = $DB->get_fieldset_select('tag_instance', 'tagid', $select, [$item->id, $tag_component_names[$component]]);
            } else {
                $select = 'itemid = ? and component = ?';
                $this_item_topics = $DB->get_fieldset_select('tag_instance', 'tagid', $select, [$item->id, $component]);
            }

            // Ensure integer index.
            foreach ($this_item_topics as $index => $id) {
                $id *= 1;
                $resource_topics[$id] = true;
            }

            // One-hot encode topics for item.
            foreach ($topics as $id => $topic) {
                if(isset($resource_topics[$id])) {
                    $cells[] = 1;
                } else {
                    $cells[] = 0;
                }
            }

            // Clean up text data after prepending title as an additional sentence.
            $item->content = html_to_text(format_text($item->content, $item->summaryformat));
            $cells[] = $this->scrubtext($item->title . ' ' . $item->content);

            // Create CSV record.
            $writer->add_data($cells);
        }
        $recordset->close();

        return true;
    }

    /**
     * Pre-clean text data for processing by content filtering recommender engine.
     *
     * @param string $text
     * @return string
     */
    private function scrubtext(string $text) :string {
        $text = str_replace(['"'],"'", $text);
        return trim(str_replace(['\n', '\r', '\t', ','],' ', $text));
    }

    /**
     * Get a scrubbed list of registered topics and tags to include as item metadata.
     *
     * We want only tags for those courses where self-enrolment is enabled, but all Engage topics.
     *
     * @throws \dml_exception
     */
    private function get_topics() {
        global $DB;

        // Set up database cursor and process records.
        $system_topics = $DB->get_records_sql("
            SELECT id, name FROM {tag}
                WHERE tagcollid = (SELECT id FROM {tag_coll} WHERE component = 'totara_topic')
            UNION ALL
            SELECT DISTINCT(ti.tagid), tg.name FROM {tag_instance} ti
            JOIN {enrol} te on ti.itemid = te.courseid
            JOIN {tag} tg on ti.tagid = tg.id
                WHERE ti.itemtype = 'course' AND ti.component = 'core' AND te.enrol = 'self'
        ");

        $topics = [];
        foreach ($system_topics as $id => $topic) {
            $topics[$id] = 'topic_' . str_replace(['"', "'", '-', ',', '.', '  ', ' '],'', $topic->name);
        }

        return $topics;
    }

    /**
     * Build one-hot encodings for
     * @param array $component_names
     * @return array
     */
    private function one_hot_components(array $component_names) {
        $hot = 0;
        $components = [];
        $default = array_fill(0, count($component_names), 0);

        foreach ($component_names as $component_name) {
            $components[$component_name] = $default;
            $components[$component_name][$hot] = 1;
            $hot += 1;
        }

        return $components;
    }
}
