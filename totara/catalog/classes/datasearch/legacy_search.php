<?php
/*
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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_catalog
 */

namespace totara_catalog\datasearch;

defined('MOODLE_INTERNAL') || die();

class legacy_search extends like {

    /**
     * Legacy search is active if a value has been specified and admin setting is on.
     *
     * @return bool
     */
    public function is_active(): bool {
        global $CFG;
        if (is_null($this->currentdata) || !$CFG->cataloglegacysearch) {
            return false;
        }
        return true;
    }

    /**
     * Get SQL statement with parameters.
     *
     * @param \stdClass $source
     * @return array
     */
    protected function make_compare(\stdClass $source): array {
        global $DB;

        if (!$this->is_active()) {
            throw new \coding_exception('Tried to apply \'like\' filter with no value specified');
        }

        $uniqueparam = $DB->get_unique_param(substr($this->alias, 0, 20));
        $where = $DB->sql_like($source->filterfield, ':' . $uniqueparam, false, $DB->is_fts_accent_sensitive());
        $params = [$uniqueparam => $this->likeprefix . $this->currentdata . $this->likesuffix];

        return [$where, $params];
    }

    /**
     * We don't need the base class's encoding to be done here so just return data.
     *
     * @param $data
     * @return null|bool|string
     */
    protected function filter_json_encode($data) {
        return $data;
    }
}