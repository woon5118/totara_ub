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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface;

use coding_exception;
use context_course;
use moodle_url;
use external_api;
use external_description;
use external_function_parameters;
use external_single_structure;
use external_value;
use mod_facetoface\dashboard\filter_list;
use mod_facetoface\dashboard\filters\filter;
use mod_facetoface\dashboard\render_session_option;
use mod_facetoface\output\session_list;
use mod_facetoface\output\show_previous_events;
use mod_facetoface\query\query_helper;
use mod_facetoface_renderer;
use moodle_exception;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');

/**
 * This is the external API for mod_facetoface.
 */
class external extends external_api {

    /**
     * Parameter definitions of render_session_list.
     *
     * @return external_function_parameters
     */
    public static function render_session_list_parameters(): external_function_parameters {
        $filterparams = [];
        (new filter_list())->add_default_filters()->walk(function (string $name, string $type, filter $filter) use (&$filterparams) {
            $filterparams[$name] = new external_value($type, $name, VALUE_OPTIONAL, $filter->get_default_value());
        });

        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'Course module id', VALUE_DEFAULT, 0),
            'f' => new external_value(PARAM_INT, 'Facetoface id', VALUE_DEFAULT, 0),
            'type' => new external_value(PARAM_ALPHA, 'List type'),
            'cookie' => new external_value(PARAM_INT, 'Unique value to distinguish requests'),
            'filterparams' => new external_single_structure($filterparams, 'Filter params'),
            'debug' => new external_value(PARAM_BOOL, 'Debug', VALUE_DEFAULT, false)
        ]);
    }

    /**
     * Render the session list table.
     *
     * @param integer|null $id course module id
     * @param integer|null $f facetoface (seminar) id
     * @param string $type 'upcoming' or 'past'
     * @param integer $cookie
     * @param array $filterparams filters as [name => value, ...]
     * @param boolean|null $debug
     * @return array
     * @throws moodle_exception $id or $f is wrong
     * @throws coding_exception $type is wrong
     */
    public static function render_session_list(?int $id, ?int $f, string $type, int $cookie, array $filterparams, ?bool $debug): array {
        global $PAGE;

        // Only admins can see debug information.
        $debug = ($debug ?? false) && is_siteadmin();

        if ($id) {
            if (!$cm = get_coursemodule_from_id('facetoface', $id)) {
                throw new moodle_exception('error:incorrectcoursemoduleid', 'facetoface');
            }
            $seminar = new seminar($cm->instance);
        } else if ($f) {
            $seminar = new seminar($f);
            $cm = $seminar->get_coursemodule();
        } else {
            throw new moodle_exception('error:mustspecifycoursemodulefacetoface', 'facetoface');
        }

        $courseid = $seminar->get_course();
        $context = context_course::instance($courseid);
        /** @var context_course $context */

        $PAGE->set_context($context);
        $PAGE->set_url(new moodle_url('/mod/facetoface/classes/external.php'));

        /** @var mod_facetoface_renderer $f2f_renderer */
        $f2f_renderer = $PAGE->get_renderer('mod_facetoface');
        $f2f_renderer->setcontext($context);

        $filters = new filter_list(function (string $parname, $default, string $type) use (&$filterparams) {
            return $filterparams[$parname] ?? $default;
        });
        $filters->add_default_filters();

        if ($type == 'upcoming') {
            // Upcoming events.
            $option = (new render_session_option())
                ->set_displayreservation(true)
                ->set_eventascendingorder(true)
                ->set_sessionascendingorder(true)
                ->set_eventtimes([ event_time::FUTURE, event_time::INPROGRESS, event_time::WAITLISTED ]);
            $data = session_list::create($seminar, $filters, $option, $context, 'mod_facetoface_upcoming_events_table')->export_for_template($f2f_renderer);
        } else if ($type == 'past') {
            // Past events.
            $option = (new render_session_option())
                ->set_displayreservation(false)
                ->set_displaysignupperiod(false)
                ->set_eventascendingorder(false)
                ->set_sessionascendingorder(false)
                ->set_eventtimes([ event_time::PAST, event_time::CANCELLED ]);
            $data = session_list::create($seminar, $filters, $option, $context, 'mod_facetoface_past_events_table')->export_for_template($f2f_renderer);
            if ($seminar->has_events()) {
                $data['pastlink'] = $f2f_renderer->render(show_previous_events::create($seminar, $filters, 'previoussessionheading'));
            }
        } else {
            throw new coding_exception('Unknown $type specified: '.$type);
        }

        $aredefault = $filters->are_default();
        // if ($aredefault) {
        //     $title = get_string('allsessionsin', 'mod_facetoface', $seminar->get_name());
        // } else {
            $title = get_string('allfilteredsessionsin', 'mod_facetoface', $seminar->get_name());
        // }

        $response = ['cookie' => $cookie, 'title' => $title, 'resetfilter' => $aredefault, 'data' => $data];

        if ($debug) {
            $response['debug'] = [];
            $stmt = $filters->to_query_with_option($seminar, $context, null, $option)->get_statement();
            $response['debug'] = [
                'option' => $option->to_object(),
                'query' => query_helper::highlight($stmt->get_sql(), $stmt->get_parameters()),
            ];
        }

        return $response;
    }

    /**
     * Returns an object that describes the structure of the return from render_session_list.
     *
     * @return external_description|null
     */
    public static function render_session_list_returns(): ?external_description {
        return null;
    }
}
