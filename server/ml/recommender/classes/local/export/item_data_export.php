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
        $sql = "
        SELECT {$unique_article_id} AS uniqueid, er.id, er.name AS title, ea.content AS content 
        FROM {engage_resource} er 
        JOIN {engage_article} ea ON er.instanceid = ea.id
        UNION ALL
        SELECT {$unique_playlist_id} AS uniqueid, tp.id, tp.name AS title, tp.summary AS content 
        FROM {playlist} tp
        ";

        // Set recordset cursor.
        $recordset = $DB->get_recordset_sql($sql);
        if (!$recordset->valid()) {
            return false;
        }

        // List of topics.
        $this->get_topics();

        // Build headings -> id, [topics], document.
        $headings = ['item_id'];
        foreach ($this->topics as $key => $topic) {
            $headings[] = $topic;
        }
        $headings[] = 'document';

        // Column headings for csv file.
        $writer->add_data($headings);

        foreach ($recordset as $item) {
            $cells = [$item->uniqueid];

            // Reformat item content & set component name.
            $component = 'totara_playlist';
            if (strpos($item->uniqueid, 'engage_article') === 0) {
                $component = 'engage_article';
                $item->content = $this->gettext(json_decode($item->content));
            }

            // Retrive topics for this item.
            $resource_topics = [];
            $select = 'itemid = ? and component = ?';
            $this_item_topics = $DB->get_fieldset_select('tag_instance', 'tagid', $select, [$item->id, $component]);
            foreach ($this_item_topics as $index => $id) {
                $id *= 1;
                $resource_topics[$id] = true;
            }

            // One-hot encode topics for item.
            foreach ($this->topics as $id => $topic) {
                if(isset($resource_topics[$id])) {
                    $cells[] = 1;
                } else {
                    $cells[] = 0;
                }
            }

            // Clean up text data after prepending title as an additional sentence.
            $cells[] = $this->scrubtext($item->title . ' ' . $item->content);

            // Create CSV record.
            $writer->add_data($cells);
        }
        $recordset->close();

        return true;
    }

    /**
     * Parse article json data tree to extract text content.
     *
     * @param object $object    A decoded JSON object.
     * @return string           The extracted text.
     */
    private function gettext(object $object) :string {
        $text = '';

        if ($object->type == 'text') {
            $text .= $object->text . ' ';
        } else {
            if (isset($object->content)) {
                foreach($object->content as $content) {
                    $text .= $this->gettext($content);
                }
            }
        }

        return $text;
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
     * Get a scrubbed list of registered topics to include as item metadata.
     *
     * @throws \dml_exception
     */
    private function get_topics() {
        global $DB;

        // Set up database cursor and process records.
        $topics = $DB->get_records_sql("
            SELECT id, name FROM {tag}
                WHERE tagcollid = (SELECT id FROM {tag_coll} WHERE component = 'totara_topic')
                ORDER BY id
        ");

        $this->topics = [];
        foreach ($topics as $id => $topic) {
            $this->topics[$id] = 'topic_' . str_replace(['"', "'", '-', ',', '.', '  ', ' '],'', $topic->name);
        }
    }
}
