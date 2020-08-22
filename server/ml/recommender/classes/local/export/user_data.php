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

use ml_recommender\local\csv\writer;

/**
 * Export class for user data.
 */
class user_data extends export {

    public function get_name(): string {
        return 'user_data';
    }

    /**
     * @param writer $writer
     * @return bool
     */
    public function export(writer $writer): bool {
        $recordset = $this->get_export_recordset();
        if (!$recordset->valid()) {
            return false;
        }

        // Column headings for csv file.
        $writer->add_headings([
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
        $writer->close();
        $recordset->close();

        return true;
    }

    /**
     * Prepare and run SQL query to database to get users
     * @return \moodle_recordset
     */
    private function get_export_recordset() {
        global $CFG, $DB;

        $params_sql = [];
        // Tenant restrictions.
        $tenant_join_sql = '';
        if ($this->tenant) {
            $tenant_join_sql = 'INNER JOIN {cohort_members} cm ON (cm.cohortid = :cohort_id AND u.id = cm.userid)';
            $params_sql['cohort_id'] = $this->tenant->cohortid;
        }

        $guest_id = $CFG->siteguest;
        $sql = "
            SELECT u.id, u.lang 
            FROM {user} u
            $tenant_join_sql 
            WHERE u.deleted = 0 AND u.suspended = 0 AND u.id <> $guest_id
        ";

        return $DB->get_recordset_sql($sql, $params_sql);
    }
}
