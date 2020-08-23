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

use ml_recommender\local\environment;

/**
 * Export class for interaction table.
 */
class user_interactions_export extends export {

    /**
     * @param \csv_export_writer $writer
     * @return bool
     */
    public function export(\csv_export_writer $writer): bool {
        global $DB;

        // Get the minimum unix epoch timestamp for this export (after expressing weeks as seconds).
        $min_timestamp = time() - (environment::get_interactions_period() * 7 * 86400);

        // Build sql.
        $components = ['engage_article', 'totara_playlist', 'container_workspace'];
        list($componentinorequal, $params) = $DB->get_in_or_equal($components);

        // Interactions with Engage content.
        $sql = '
            SELECT user_id, item_id, component, MAX(time_created) AS mytimestamp, SUM(rating) AS myrating
            FROM {ml_recommender_interactions}';
        $sql .= ' WHERE component ' . $componentinorequal . ' AND time_created >= ' . $min_timestamp . ' ';
        $sql .= 'GROUP BY user_id, item_id, component';

        // Interactions with self-enrol courses.
        $self_enrol_positive_rating = 2;
        $sql .= "
            UNION ALL
            SELECT ue.userid AS user_id,
                e.courseid AS item_id,
                'container_course' AS component,
                ue.timecreated AS mytimestamp,
                " . $self_enrol_positive_rating . " AS myrating
            FROM {user_enrolments} ue
            JOIN {enrol} e ON (ue.enrolid = e.id)
            WHERE e.enrol = 'self' AND e.status = " . ENROL_USER_ACTIVE;

        // Set recordset cursor.
        $recordset = $DB->get_recordset_sql($sql, $params);
        if (!$recordset->valid()) {
            return false;
        }

        // Column headings for csv file.
        $writer->add_data([
            'user_id',
            'item_id',
            'rating',
            'timestamp'
        ]);

        foreach ($recordset as $interaction) {
            // Get the mapped item and user ids.
            $user_id = $interaction->user_id;
            $item_id = $interaction->component . $interaction->item_id;
            $timestamp = $interaction->mytimestamp;

            // Normalise "rating" - more than 1 interaction is positive - implicit feedback.  See:
            // Collaborative prediction and ranking with non-random missing data (Benjamin M. Marlin, Richard S. Zemel)
            // https://media.netflix.com/en/company-blog/goodbye-stars-hello-thumbs
            $rating = 0;
            if ($interaction->myrating > 1) {
                $rating = 1;
            }

            // Create CSV record.
            $writer->add_data([
                $user_id,
                $item_id,
                $rating,
                $timestamp
            ]);
        }
        $recordset->close();

        return true;
    }
}
