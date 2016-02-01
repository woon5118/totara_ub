<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/totara/reportbuilder/filters/f2f_available.php');
/**
 * Empty rooms during specified time search implementation
 */
class rb_filter_f2f_assetavailable extends rb_filter_f2f_available
{
    // TODO: This need to be covered by unit tests.
    public function get_sql_snippet($sessionstarts, $sessionends) {
        $paramstarts1 = rb_unique_param('timestart');
        $paramstarts2 = rb_unique_param('timestart');

        $paramends1 = rb_unique_param('timefinish');
        $paramends2 = rb_unique_param('timefinish');
        $field = $this->get_field();
        $sql = "$field NOT IN (
            SELECT fa.id
            FROM {facetoface_asset} fa
            INNER JOIN {facetoface_asset_dates} fad
                ON fad.assetid = fa.id
            INNER JOIN {facetoface_sessions_dates} fsd
                ON fsd.id = fad.sessionsdateid
            WHERE (fsd.timestart < :$paramstarts1 AND fsd.timefinish > :$paramends1)
              OR (fsd.timestart > :$paramstarts2 AND fsd.timestart < :$paramends2)
            )";

        $params = array();
        $params[$paramstarts1] = $sessionstarts;
        $params[$paramstarts2] = $sessionstarts;
        $params[$paramends1] = $sessionends;
        $params[$paramends2] = $sessionends;

        return array($sql, $params);
    }
}