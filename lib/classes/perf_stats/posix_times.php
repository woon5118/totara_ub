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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package core
 */

namespace core\perf_stats;

use stdClass;

class posix_times extends provider {

    /**
     * @inheritDoc
     */
    public function get_data() {
        global $PERF, $CFG, $PAGE;
        if (!empty($CFG->early_install_lang) or empty($PAGE)) {
            return null;
        }

        if (!function_exists('posix_times')) {
            return null;
        }

        $ptimes = posix_times();
        
        $data = new stdClass();

        if (is_array($ptimes)) {
            foreach ($ptimes as $key => $val) {
                $data->$key = $ptimes[$key] - $PERF->startposixtimes[$key];
            }
        }

        return $data;
    }

}