<?php
/*
 * This file is part of Totara LMS
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
 * @package mod_facetoface
 */

namespace mod_facetoface\query\event\sortorder;

defined('MOODLE_INTERNAL') || die();

/**
 * Sort events in order:
 * + cancelled (cancelled event with recently timestart first -> furthiest then waitlisted cancel)
 * + past (far past over to recently over event)
 * + current (in progress event)
 * + upcoming (nearest first, then far away future)
 * + waitlisted
 */
final class default_sortorder extends sortorder {
    /**
     * @return string
     * @inheritdoc
     */
    public function get_sort_sql(): string {
        // PostgreSQL and MySQL sort NULL in a different order. We need wait-listed events to be the furthest future
        // events, meaning NULL needs to act as a positive maximum value. So we use PHP_INT_MAX as the
        // timestart/timefinish for events that are waitlisted (whose actual timestart/finish is NULL)
        $max = PHP_INT_MAX;
        return
            "ORDER BY s.cancelledstatus DESC, coalesce(m.mintimestart, {$max}), " .
            "coalesce(m.maxtimefinish, {$max}), s.id";
    }
}