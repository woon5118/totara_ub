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
use mod_perform\models\activity\participant_source;
use mod_perform\state\participant_instance\closed;
use mod_perform\state\participant_instance\complete;
use mod_perform\state\participant_instance\participant_instance_availability;
use mod_perform\state\participant_instance\participant_instance_progress;
use mod_perform\state\state_helper;
use rb_base_source;
use rb_column_option;
use rb_filter_option;
use rb_join;
use totara_core\relationship\relationship;
use totara_core\relationship\relationship_provider;

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

        $this->joinlist[] = new rb_join(
            'totara_core_relationship',
            'INNER',
            '{totara_core_relationship}',
            "{$join}.core_relationship_id = totara_core_relationship.id",
            REPORT_BUILDER_RELATION_MANY_TO_ONE,
            [$join]
        );

        /*
         * TODO we might need something like this to ensure subject_instance join is in joinlist
         *      in cases where a source uses this trait without using the subject instance one.
         *      BUT need to be careful this isn't similar but slightly different so it tries to
         *      add it twice! See TODO below in columnoptions.
        $subject_instance_join = new rb_join(
            'subject_instance',
            'INNER',
            '{perform_subject_instance}',
            "{$join}.subject_instance_id = subject_instance.id",
            REPORT_BUILDER_RELATION_MANY_TO_ONE
        );
        if (!in_array($subject_instance_join, $this->joinlist, true)) {
            $this->joinlist[] = $subject_instance_join;
        }
        */

        $this->add_core_user_tables(
            $this->joinlist,
            $join,
            "participant_id AND {$join}.participant_source = " . participant_source::INTERNAL,
            'participant_user'
        );

        $this->joinlist[] = new \rb_join(
            'external_participant',
            'LEFT',
            '{perform_participant_external}',
            "external_participant.id = $join.participant_id AND $join.participant_source = " . participant_source::EXTERNAL,
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            $join
        );
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
        //      See comment above in add_joins method.
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

        $this->columnoptions[] = new rb_column_option(
            'participant_instance',
            'relationship_name',
            get_string('relationship_name', 'mod_perform'),
            "totara_core_relationship.idnumber",
            [
                'joins' => [$join, 'totara_core_relationship'],
                'displayfunc' => 'relationship_name'
            ]
        );
        $this->columnoptions[] = new rb_column_option(
            'participant_instance',
            'relationship_id',
            get_string('relationship_id', 'mod_perform'),
            "totara_core_relationship.id",
            [
                'joins' => [$join, 'totara_core_relationship'],
                'displayfunc' => 'integer',
                'selectable' => false,
            ]
        );
        $this->columnoptions[] = new rb_column_option(
            'participant_instance',
            'relationship_sort_order',
            get_string('relationship_sort_order', 'mod_perform'),
            "totara_core_relationship.sort_order",
            [
                'joins' => [$join, 'totara_core_relationship'],
                'displayfunc' => 'integer',
            ]
        );

        // Add columns for participants (internal / external)
        $this->add_participant_columns($join);

        $this->add_core_user_columns($this->columnoptions, 'participant_user', 'participant_user', true);
    }

    private function add_participant_columns(string $join) {
        global $DB;

        $this->columnoptions[] = new rb_column_option(
            'participant_instance',
            'participant_source',
            get_string('participant_source', 'rb_source_perform_participant_instance'),
            "$join.participant_source",
            [
                'joins' => $join,
                'dbdatatype' => 'char',
                'displayfunc' => 'participant_source',
            ]
        );

        $usednamefields = totara_get_all_user_name_fields_join('participant_user', null, true);
        $allnamefields = totara_get_all_user_name_fields_join('participant_user');

        $this->columnoptions[] = new rb_column_option(
            'participant_instance',
            'participant_name',
            get_string('participant_name', 'rb_source_perform_participant_instance'),
            "CASE
                    WHEN
                        {$join}.participant_source = " . participant_source::EXTERNAL . "
                    THEN external_participant.name
                    ELSE ".$DB->sql_concat_join("' '", $usednamefields)."
                END",
            [
                'joins' => [$join, 'external_participant', 'participant_user'],
                'dbdatatype' => 'char',
                'displayfunc' => 'participant_link',
                'extrafields' => array_merge(
                    [
                        'participant_source' => "{$join}.participant_source",
                        'id' => "participant_user.id",
                        'deleted' => "participant_user.deleted"
                    ],
                    $allnamefields
                ),
            ]
        );

        $this->columnoptions[] = new rb_column_option(
            'participant_instance',
            'participant_email',
            get_string('participant_email', 'rb_source_perform_participant_instance'),
            // use CASE to include/exclude email in SQL
            // so search won't reveal hidden results
            "CASE
                    WHEN
                        {$join}.participant_source = " . participant_source::EXTERNAL . "
                    THEN external_participant.email
                    ELSE CASE WHEN participant_user.maildisplay <> 1 THEN '-' ELSE participant_user.email END
                END",
            [
                'joins' => [$join, 'external_participant', 'participant_user'],
                'displayfunc' => 'participant_email',
                'extrafields' => [
                    'participant_source' => "{$join}.participant_source",
                    'emailstop' => "participant_user.emailstop",
                    'maildisplay' => "participant_user.maildisplay",
                ],
                'dbdatatype' => 'char',
                'outputformat' => 'text',
            ]
        );

        // Only include this column if email is among fields allowed by showuseridentity setting or
        // if the current user has the 'moodle/site:config' capability.
        $canview = !empty($CFG->showuseridentity) && in_array('email', explode(',', $CFG->showuseridentity));
        $canview |= has_capability('moodle/site:config', \context_system::instance());
        if ($canview) {
            $this->columnoptions[] = new \rb_column_option(
                'participant_instance',
                'participant_email_unobscured',
                get_string('participant_email_unobscured', 'rb_source_perform_participant_instance'),
                "CASE
                    WHEN
                        {$join}.participant_source = " . participant_source::EXTERNAL . "
                    THEN external_participant.email
                    ELSE participant_user.email
                END",
                [
                    'joins' => [$join, 'external_participant', 'participant_user'],
                    'displayfunc' => 'participant_email_unobscured',
                    'extrafields' => [
                        'participant_source' => "{$join}.participant_source",
                    ],
                    // Users must have viewuseridentity to see the
                    // unobscured email address.
                    'capability' => 'moodle/site:viewuseridentity',
                    'dbdatatype' => 'char',
                    'outputformat' => 'text',
                ]
            );
        }
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

        $this->filteroptions[] = new rb_filter_option(
            'participant_instance',
            'relationship_id',
            get_string('relationship_name', 'mod_perform'),
            'select',
            [
                'selectchoices' => $this->get_relationship_type_options(),
                'simplemode' => true,
            ]
        );

        $this->filteroptions[] = new rb_filter_option(
            'participant_instance',
            'participant_source',
            get_string('participant_source', 'rb_source_perform_participant_instance'),
            'select',
            [
                'selectchoices' => [
                    participant_source::EXTERNAL => get_string('participant_source_external', 'rb_source_perform_participant_instance'),
                    participant_source::INTERNAL => get_string('participant_source_internal', 'rb_source_perform_participant_instance'),
                ]
            ]
        );

        $this->filteroptions[] = new rb_filter_option(
            'participant_instance',
            'participant_name',
            get_string('participant_name', 'rb_source_perform_participant_instance'),
            'text'
        );

        $this->filteroptions[] = new rb_filter_option(
            'participant_instance',
            'participant_email',
            get_string('participant_email', 'rb_source_perform_participant_instance'),
            'text'
        );

        // Only include this column if email is among fields allowed by showuseridentity setting or
        // if the current user has the 'moodle/site:config' capability.
        $canview = !empty($CFG->showuseridentity) && in_array('email', explode(',', $CFG->showuseridentity));
        $canview |= has_capability('moodle/site:config', \context_system::instance());
        if ($canview) {
            $this->filteroptions[] = new rb_filter_option(
                'participant_instance',
                'participant_email_unobscured',
                get_string('participant_email_unobscured', 'rb_source_perform_participant_instance'),
                'text'
            );
        }

        $this->add_core_user_filters($this->filteroptions, 'participant_user', true);
    }

    private function get_relationship_type_options() {
        $options = [];
        $relationships = (new relationship_provider())
            ->filter_by_component('mod_perform', true)
            ->get_compatible_relationships(['user_id']);
        /** @var relationship $relationship */
        foreach ($relationships as $relationship) {
            $options[$relationship->id] = $relationship->get_name();
        }
        return $options;
    }
}
