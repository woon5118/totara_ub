<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_topic
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Creating a tag collection record. Then returning the id of that collection.
 * Also this function will try to set the config which is '$CFG->topic_collection_id'
 * @return int
 */
function totara_topic_add_tag_collection(): int {
    global $DB, $CFG;

    if (!empty($CFG->topic_collection_id)) {
        return $CFG->topic_collection_id;
    }

    $sql = 'SELECT MAX(sortorder) AS sortorder FROM "ttr_tag_coll"';
    $sortorder = $DB->get_field_sql($sql);

    if (null === $sortorder) {
        $sortorder = 0;
    }

    $record = new \stdClass();

    // Set the name as the string from language pack instead.
    $record->name = get_string('pluginname', 'totara_topic');
    $record->isdefault = 1;
    $record->component = 'totara_topic';
    $record->sortorder = $sortorder + 1;
    $record->searchable = 1;

    $id = (int) $DB->insert_record('tag_coll', $record);

    set_config('topic_collection_id', $id);
    return $id;
}