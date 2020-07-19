<?php
/**
 * This file is part of Totara Perform
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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\rb\traits;

use coding_exception;
use mod_perform\state\participant_instance\closed;
use mod_perform\state\participant_instance\complete;
use mod_perform\state\participant_instance\participant_instance_availability;
use mod_perform\state\participant_instance\participant_instance_progress;
use mod_perform\state\state_helper;
use rb_base_source;
use rb_column_option;
use rb_filter_option;
use rb_join;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/totara/reportbuilder/lib.php");

/**
 * Trait participant_instance_trait
 */
trait participant_instance_trait {
    /** @var string $participant_instance_join */
    protected $participant_instance_join = null;

    /**
     * Add participant instance info where participant_instance is the base table.
     *
     * @throws coding_exception
     */
    protected function add_participant_instance_to_base() {
        /** @var participant_instance_trait|rb_base_source $this */
        if (isset($this->participant_instance_join)) {
            throw new coding_exception('Participant instance info can be added only once!');
        }

        $this->participant_instance_join = 'base';

        // Add component for lookup of display functions and other stuff.
        if (!in_array('mod_perform', $this->usedcomponents, true)) {
            $this->usedcomponents[] = 'mod_perform';
        }

        $this->add_participant_instance_joins();
        $this->add_participant_instance_columns();
        $this->add_participant_instance_filters();
    }

    /**
     * Add participant instance info where participant_instance is a joined table.
     *
     * @param rb_join $join
     * @throws coding_exception
     */
    protected function add_participant_instance(rb_join $join) {
        /** @var participant_instance_trait|rb_base_source $this */
        if (isset($this->participant_instance_join)) {
            throw new coding_exception('Participant instance info can be added only once!');
        }

        if (!in_array($join, $this->joinlist, true)) {
            $this->joinlist[] = $join;
        }
        $this->participant_instance_join = $join->name;

        // Add component for lookup of display functions and other stuff.
        if (!in_array('mod_perform', $this->usedcomponents, true)) {
            $this->usedcomponents[] = 'mod_perform';
        }

        $this->add_participant_instance_joins();
        $this->add_participant_instance_columns();
        $this->add_participant_instance_filters();
    }

    /**
     * Add joins required for participant instance column and filter options to report.
     */
    protected function add_participant_instance_joins() {
        /** @var participant_instance_trait|rb_base_source $this */
        $join = $this->participant_instance_join;

        $this->add_core_user_tables($this->joinlist, $join, 'participant_id', 'participant_user');
    }

    /**
     * Add columnoptions for participant instances to report.
     */
    protected function add_participant_instance_columns() {
        /** @var participant_instance_trait|rb_base_source $this */
        $join = $this->participant_instance_join;

        $this->columnoptions[] = new rb_column_option(
            'participant_instance',
            'progress',
            get_string('progress', 'mod_perform'),
            "{$join}.progress",
            [
                'joins' => [$join],
                'dbdatatype' => 'integer',
                'displayfunc' => 'state_display_name',
                'extracontext' => [
                    'object_type' => 'participant_instance',
                    'state_type' => participant_instance_progress::get_type(),
                ],
            ]
        );

        $this->columnoptions[] = new rb_column_option(
            'participant_instance',
            'availability',
            get_string('availability', 'mod_perform'),
            "{$join}.availability",
            [
                'joins' => [$join],
                'dbdatatype' => 'integer',
                'displayfunc' => 'state_display_name',
                'extracontext' => [
                    'object_type' => 'participant_instance',
                    'state_type' => participant_instance_availability::get_type(),
                ],
            ]
        );

        $this->columnoptions[] = new rb_column_option(
            'participant_instance',
            'created_at',
            get_string('date_created', 'mod_perform'),
            "{$join}.created_at",
            [
                'joins' => [$join],
                'dbdatatype' => 'timestamp',
                'displayfunc' => 'nice_date'
            ]
        );

        $this->columnoptions[] = new rb_column_option(
            'participant_instance',
            'updated_at',
            get_string('date_updated', 'mod_perform'),
            "{$join}.updated_at",
            [
                'joins' => [$join],
                'dbdatatype' => 'timestamp',
                'displayfunc' => 'nice_date'
            ]
        );

        // TODO Check subject_instance join is added by this trait alone
        //      Do we need to conditionally add it to joinlist?
        $this->columnoptions[] = new rb_column_option(
            'participant_instance',
            'overdue',
            get_string('overdue', 'mod_perform'),
            "CASE
                    WHEN
                        subject_instance.due_date <= " . time() . "
                        AND NOT (
                            {$join}.progress = " . complete::get_code() . "
                            OR {$join}.availability = " . closed::get_code() . "
                        )
                    THEN 1
                    ELSE 0
                END",
            [
                'joins' => [$join, 'subject_instance'],
                'dbdatatype' => 'boolean',
                'displayfunc' => 'yes_or_no',
            ]
        );

        $this->add_core_user_columns($this->columnoptions, 'participant_user', 'participant_user', true);
    }

    /**
     * Add filteroptions for participant instances to report.
     */
    protected function add_participant_instance_filters() {
        $this->filteroptions[] = new rb_filter_option(
            'participant_instance',
            'progress',
            get_string('progress', 'mod_perform'),
            'select',
            [
                'selectchoices' => state_helper::get_all_display_names(
                    'participant_instance', participant_instance_progress::get_type()
                ),
            ]
        );

        $this->filteroptions[] = new rb_filter_option(
            'participant_instance',
            'availability',
            get_string('availability', 'mod_perform'),
            'select',
            [
                'selectchoices' => state_helper::get_all_display_names(
                    'participant_instance', participant_instance_availability::get_type()
                ),
                'simplemode' => true
            ]
        );

        $this->filteroptions[] = new rb_filter_option(
            'participant_instance',
            'overdue',
            get_string('overdue', 'mod_perform'),
            'multicheck',
            [
                'simplemode' => true,
                'selectfunc' => 'yesno_list',
            ]
        );

        $this->filteroptions[] = new rb_filter_option(
            'participant_instance',
            'created_at',
            get_string('date_created', 'mod_perform'),
            'date'
        );

        $this->filteroptions[] = new rb_filter_option(
            'participant_instance',
            'updated_at',
            get_string('date_updated', 'mod_perform'),
            'date'
        );

        $this->add_core_user_filters($this->filteroptions, 'participant_user', true);
    }
}
