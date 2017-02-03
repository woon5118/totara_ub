<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2014 onwards Totara Learning Solutions LTD
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
 * @author Nathan Lewis <nathan.lewis@totaralms.com>
 * @package totara_certification
 */

// TL-12606 Recalculate non-zero course set group completion records.
function totara_certification_upgrade_non_zero_prog_completions() {
    global $CFG, $DB;

    require_once($CFG->dirroot . '/totara/program/lib.php');

    // Magic number 2 is STATUS_COURSESET_INCOMPLETE.
    $sql = "DELETE FROM {prog_completion}
             WHERE status = 2
               AND timestarted = 0
               AND timedue = 0
               AND timecompleted = 0
               AND coursesetid <> 0
               AND programid IN (SELECT id
                                   FROM {prog}
                                  WHERE certifid IS NOT NULL)";
    $DB->execute($sql);
}
