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
 * Export class for user data.
 */
class user_data_export extends export {

    /**
     * @param \csv_export_writer $writer
     * @return bool
     */
    public function export(\csv_export_writer $writer): bool {
        global $DB;

        // Build sql.
        $sql = 'SELECT u.id, u.lang FROM {user} u WHERE u.deleted = 0 AND u.suspended = 0';

        // Set recordset cursor.
        $recordset = $DB->get_recordset_sql($sql);
        if (!$recordset->valid()) {
            return false;
        }

        // Column headings for csv file.
        $writer->add_data([
            'user_id',
            'lang'
        ]);

        foreach ($recordset as $user) {
            // Create CSV record.
            $writer->add_data([
                $user->id,
                $user->lang
            ]);
        }
        $recordset->close();

        return true;
    }
}
