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

namespace mod_facetoface\query\event;

use mod_facetoface\query\event\filter\filter;
use mod_facetoface\query\event\sortorder\sortorder;
use mod_facetoface\query\statement;
use mod_facetoface\seminar;

defined('MOODLE_INTERNAL') || die();

/**
 * Class query
 * @package mod_facetoface\query\event
 */
final class query implements event_query {
    /**
     * @var filter[]
     */
    private $filters;

    /**
     * @var seminar
     */
    private $seminar;

    /**
     * @var sortorder
     */
    private $sortorder;

    /**
     * By default, seminar must always be injected into the query, because the query need to narrow down retrieving.
     *
     * query constructor.
     *
     * @param seminar $seminar
     */
    public function __construct(seminar $seminar) {
        $this->seminar = $seminar;
        $this->filters = [];
        $this->sortorder = null;
    }

    /**
     * Injecting the filter object to build WHERE clause sql statement. If the filter is already existing in this
     * object, just overriding the existing one.
     *
     * @param filter $filter
     *
     * @return query
     */
    public function with_filter(filter $filter): query {
        $this->filters[$filter->get_name()] = $filter;
        return $this;
    }

    /**
     * Add multiple filter object to build WHERE clause sql statement in one go.
     *
     * @param filter ...$filters
     *
     * @return query
     */
    public function with_filters(filter ...$filters): query {
        foreach ($filters as $filter) {
            $this->with_filter($filter);
        }

        return $this;
    }

    /**
     * @param sortorder $sortorder
     *
     * @return query
     */
    public function with_sortorder(sortorder $sortorder): query {
        $this->sortorder = $sortorder;
        return $this;
    }

    /**
     * This is the final call from query, to build up the SQL statement of retrieving seminar_events,
     * base on filters and sortorder injected into this object.
     *
     * @return statement
     * @inheritdoc
     */
    public function get_statement(): statement {
        $sql = "
          SELECT s.* 
          FROM {facetoface_sessions} s

          LEFT JOIN (
            SELECT fsd.sessionid,
            COUNT(fsd.id) AS cntdates,
            MIN(fsd.timestart) AS mintimestart,
            MAX(fsd.timefinish) AS maxtimefinish
            FROM {facetoface_sessions_dates} fsd
            GROUP BY fsd.sessionid
          ) m ON m.sessionid = s.id
                 
          WHERE s.facetoface = :facetoface
        ";

        $params = [
            'facetoface' => $this->seminar->get_id()
        ];

        if (!empty($this->filters)) {
            // Start building our where clause here, if there are any filters provided for this query object.
            $wheresqls = [];

            foreach ($this->filters as $filter) {
                [$wheresql, $whereparams] = $filter->get_where_and_params();

                $wheresqls[] = $wheresql;
                $params = array_merge($params, $whereparams);
            }

            $sql .= " AND " . implode(" AND ", $wheresqls);
        }

        if (null !== $this->sortorder) {
            $sql .= " " . $this->sortorder->get_sort_sql();
        }

        return new statement($sql, $params);
    }
}