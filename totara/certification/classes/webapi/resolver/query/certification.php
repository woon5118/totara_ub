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
 * @author David Curry <david.curry@totaralearning.com>
 * @package totara_certification
 */

namespace totara_certification\webapi\resolver\query;

use core\webapi\execution_context;

/**
 * Query to return a program
 */
class certification implements \core\webapi\query_resolver {

    /**
     * Returns a single certification as specified by the id.
     *
     * @param array $args
     * @param execution_context $ec
     */
    public static function resolve(array $args, execution_context $ec) {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/totara/program/program.class.php');

        // TL-21305 will find a better, encapsulated solution for require_login calls.
        require_login(null, false, null, false, true);

        if (totara_feature_disabled('certifications')) {
            throw new \coding_exception('Certifications have been disabled.');
        }

        // Note: Certifications are bit more complicated than programs, might need to add more stuff later.
        $prog_rec = $DB->get_record('prog', ['id' => $args['certificationid']]);
        // Don't allow certification query to return programs.
        if (empty($prog_rec->certifid)) {
            throw new \coding_exception('Invalid certification.');
        }

        $program = new \program($prog_rec);
        // Don't allow users to view certifications they don't have access to.
        if (!$program->is_viewable($USER)) {
            throw new \coding_exception('Current user can not access this certification.');
        }

        return $program;
    }
}
