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
        $components = ['engage_article', 'totara_playlist'];
        list($componentinorequal, $params) = $DB->get_in_or_equal($components);

        $sql = '
            SELECT user_id, item_id, component, MAX(time_created) AS mytimestamp, SUM(rating) AS myrating
            FROM {ml_recommender_interactions}';
        $sql .= ' WHERE component ' . $componentinorequal . ' AND time_created >= ' . $min_timestamp . ' ';
        $sql .= 'GROUP BY user_id, item_id, component ORDER BY user_id, item_id, component, mytimestamp, myrating ASC';

        // Set recordset cursor.
        $recordset = $DB->get_recordset_sql($sql, $params);
        if (!$recordset->valid()) {
            return false;
        }

        // Column headings for csv file.
        $writer->add_data([
            'user_id',
            'item_id',
            'rating'
        ]);

        foreach ($recordset as $interaction) {
            // Get the mapped item and user ids.
            $user_id = $interaction->user_id;
            $item_id = $interaction->component . $interaction->item_id;

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
                $rating
            ]);
        }
        $recordset->close();

        return true;
    }
}
