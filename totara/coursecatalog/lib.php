<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @package totara
 * @subpackage totara_coursecatalog
 */

// Require MOODLE_INTERNAL.
defined('MOODLE_INTERNAL') || die();

/**
 * Get the number of visible items in or below the selected categories
 *
 * This function counts the number of items within a set of categories, only including
 * items that are visible to the user.
 *
 * By default returns the course count, but will work for programs, certifications too.
 *
 * We need to jump through some hoops to do this efficiently:
 *
 * - To avoid having to do it recursively it relies on the context
 *   path to find courses within a category
 *
 * - To avoid having to check capabilities for every item it only
 *   checks hidden courses, and only if user isn't a siteadmin
 *
 * @param integer|array $categoryids ID or IDs of the category/categories to fetch
 * @param boolean $viewtype  - type of item to count: course,program,certification
 *
 * @return integer|array Associative array, where keys are the sub-category IDs and value is the count.
 * If $categoryids is a single integer, just returns the count as an integer
 */
function totara_get_category_item_count($categoryids, $viewtype = 'course') {
    global $USER, $DB;

    $single = false;
    $toload = $categoryids;
    if (!is_array($toload)) {
        $single = true;
        $toload = [$toload];
    }

    $cache = cache::make('totara_core', 'visible_content');
    $visible = \totara_core\visibility_controller::get($viewtype, $cache)->get_visible_counts_for_all_categories($USER->id);

    $results = [];
    foreach ($toload as $id) {
        // Ensure $id is database safe.
        $id = (int)$id;
        if (!$id) {
            debugging('Invalid category id passed to totara_get_category_item_count() ', DEBUG_DEVELOPER);
            continue;
        }

        $results[$id] = 0;
        if (isset($visible[$id])) {
            $results[$id] += $visible[$id];
        }
        $subcatids = $DB->get_fieldset_select('course_categories', 'id', 'path LIKE :path', ['path' => "%/{$id}/%"]);
        foreach ($subcatids as $subcatid) {
            if (isset($visible[$subcatid])) {
                $results[$id] += $visible[$subcatid];
            }
        }
    }

    if ($single) {
        return $results[$categoryids];
    }

    return $results;
}

/**
 * Sorts a pair of objects based on the itemcount property (high to low)
 *
 * @param object $a The first object
 * @param object $b The second object
 * @return integer Returns 1/0/-1 depending on the relative values of the objects itemcount property
 */
function totara_course_cmp_by_count($a, $b) {
    if ($a->itemcount < $b->itemcount) {
        return +1;
    } else if ($a->itemcount > $b->itemcount) {
        return -1;
    } else {
        return 0;
    }
}

/**
 * Returns the style css name for the component's visibility.
 *
 * @param stdClass $component Component (Course, Program, Certification) object
 * @param string $oldvisfield Old visibility field
 * @param string $audvisfield Audience visibility field
 * @return string $dimmed Css class name
 */
function totara_get_style_visibility($component, $oldvisfield = 'visible', $audvisfield = 'audiencevisible') {
    global $CFG;
    $dimmed = '';

    if (!is_object($component)) {
        return $dimmed;
    }

    if (empty($CFG->audiencevisibility)) {
        if (isset($component->{$oldvisfield}) && !$component->{$oldvisfield}) {
            $dimmed = 'dimmed';
        }
    } else {
        require_once($CFG->dirroot . '/totara/cohort/lib.php');
        if (isset($component->{$audvisfield}) && $component->{$audvisfield} == COHORT_VISIBLE_NOUSERS) {
            $dimmed = 'dimmed';
        }
    }

    return $dimmed;
}


/**
 * Get the where clause sql fragment and parameters needed to restrict an sql query to only those courses or
 * programs available to a user.
 *
 * sqlparams in return are SQL_PARAMS_NAMED, so queries built using this function must also use named params.
 *
 * !!! Your query must join to the context table, with alias "ctx" !!!
 *
 * Note that currently, if using normal visibility, hidden items will not show in the RoL for a learner, but
 * it will show in their Required Learning, is accessible, and they are processed for completion. All other
 * places which display learning items are limited to those that are not hidden. We may want to change this.
 * For example, f2f calendar items, appraisal questions, recent learning, user course completion report,
 * enrol_get_my_courses, ... Basically we should check every call to this function.
 *
 * @param int $userid The user that the results should be restricted for. Defaults to current user.
 * @param string $fieldbaseid The field in the base sql query which this query can link to.
 * @param string $fieldvisible The field in the base sql query which contains the visible property.
 * @param string $fieldaudvis The field in the base sql query which contains the audiencevisibile property.
 * @param string $tablealias The alias for the base table (This is used mainly for programs and cert which has available field)
 * @param string $type course, program or certification.
 * @param bool $iscached True if the fields passed comes from a report which data has been cached.
 * @param bool $showhidden If using normal visibility, show items even if they are hidden.
 * @return array(sqlstring, array(sqlparams))
 */
function totara_visibility_where($userid = null, $fieldbaseid = 'course.id', $fieldvisible = 'course.visible',
             $fieldaudvis = 'course.audiencevisible', $tablealias = 'course', $type = 'course', $iscached = false,
             $showhidden = false) {
    global $CFG, $DB, $USER;

    if ($userid === null) {
        $userid = $USER->id;
    }

    $usercontext = false;
    if ($userid) {
        $usercontext = context_user::instance($userid, IGNORE_MISSING);
        if (!$usercontext) {
            // Most likely deleted users - they cannot access anything!
            return array('1=0', array());
        }
    }

    $audiencebased = !empty($CFG->audiencevisibility);
    $separator = ($iscached) ? '_' : '.'; // When the report is caches its fields comes in type_value form.
    $systemcontext = context_system::instance();

    $quoted_separator = preg_quote($separator, '#');
    $regex = "#^([^{$quoted_separator}]+){$quoted_separator}.*\$#";
    $alias = preg_replace($regex, '$1', $fieldbaseid);

    switch ($type) {
        case 'course':
            $capability = 'moodle/course:viewhiddencourses';
            break;
        case 'program':
            $capability = 'totara/program:viewhiddenprograms';
            break;
        case 'certification':
            $capability = 'totara/certification:viewhiddencertifications';
            break;
        default:
            throw new \coding_exception('Unknown type', $type);
    }

    // Deal with the old showhidden argument, it was always half broken and only worked with traditional visibility.
    // So just hack it in here in the same way it used to be dealt with.
    if (!$audiencebased && ($showhidden || has_capability($capability, $systemcontext, $userid))) {
        return array('1=1', array());
    }

    $type = \totara_core\visibility_controller::get($type);
    $type->set_sql_separator($separator);
    $sql = $type->sql_where_visible($userid, $alias);
    if ($sql->is_empty()) {
        return ['1=1', []];
    }
    return [$sql->get_sql(), $sql->get_params()];
}

/**
 * Get the join clause sql fragment and parameters needed to get the isvisibletouser column only for those courses or
 * programs available to a user.
 *
 * Use in the following form:
 *
 * list($visibilityjoinsql, $visibilityjoinparams) = totara_visibility_join($userid, 'program', 'p');
 *
 * $sql = "SELECT p.*, visibilityjoin.isvisibletouser
 *           FROM {prog} p
 *                {$visibilityjoinsql}
 *          WHERE p.certifid IS NULL";
 *
 * @param mixed $userid The user that the results should be restricted for. Defaults to current user.
 * @param string $type course, program or certification.
 * @param string $mainalias Alias of the table that contains the data you are working with.
 * @param string $joinalias Alias to give the joined table, which will contain the isvisible field.
 * @param string $jointype 'LEFT JOIN' will not restrict the rows returned from you main table,
 *                         'JOIN' will remove rows from you main table (like using totara_visibility_where).
 * @return array list(string $joinsql, array $joinparams)
 */
function totara_visibility_join($userid = null, $type = 'course', $mainalias = 'course', $joinalias = 'visibilityjoin',
                                $jointype = 'LEFT JOIN') {
    global $USER, $DB;

    // Default user.
    if ($userid === null) {
        $userid = $USER->id;
    }

    // Figure out what type of data we're dealing with.
    if ($type === 'course') {
        $basetable = 'course';
        $restrictions = '';
        $contextlevel = CONTEXT_COURSE;
    } else {
        $basetable = 'prog';
        if ($type === 'program') {
            $restrictions = ' AND tvjoinsub.certifid IS NULL';
        } else {
            $restrictions = ' AND tvjoinsub.certifid IS NOT NULL';
        }
        $contextlevel = CONTEXT_PROGRAM;
    }

    // Get the totara_visibility_where sql, which the join will be based on.
    list($visibilitysql, $visibilityparams) = totara_visibility_where($userid, 'tvjoinsub.id', 'tvjoinsub.visible',
        'tvjoinsub.audiencevisible', 'tvjoinsub', $type);

    // Construct the result.
    $one = '1';
    if ($DB->get_dbfamily() === 'mysql') {
        // Workaround for broken MariaDB - see TL-7785.
        $one = '(CASE WHEN tvjoinsub.id > 0 THEN 1 ELSE 0 END)';
    }
    $sql = "{$jointype} (SELECT tvjoinsub.id, $one AS isvisibletouser
                           FROM {{$basetable}} tvjoinsub
                           JOIN {context} ctx
                             ON tvjoinsub.id = ctx.instanceid AND ctx.contextlevel = :tvjcontextlevel
                          WHERE {$visibilitysql} {$restrictions}
                         ) {$joinalias}
                ON {$mainalias}.id = {$joinalias}.id";

    $visibilityparams['tvjcontextlevel'] = $contextlevel;

    return array($sql, $visibilityparams);
}
