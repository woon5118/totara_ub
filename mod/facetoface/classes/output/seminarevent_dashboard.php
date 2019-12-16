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

namespace mod_facetoface\output;

use stdClass;
use context;
use html_writer;
use moodle_url;
use core\output\template;
use mod_facetoface\dashboard\filter_list;
use mod_facetoface\seminar;
use mod_facetoface_renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * The seminar dashboard.
 */
final class seminarevent_dashboard extends template {

    /**
     * Create an instance.
     *
     * @param seminar $seminar
     * @param context $context
     * @param stdClass $cm
     * @param stdClass $course
     * @return self
     */
    public static function create(seminar $seminar, context $context, stdClass $cm, stdClass $course): self {
        global $PAGE;

        /** @var mod_facetoface_renderer $f2f_renderer */
        $renderer = $PAGE->get_renderer('mod_facetoface');
        $renderer->setcontext($context);

        $data = array();

        $data['title'] = $seminar->get_name();
        $data['actions'] = self::get_action_bar_data($seminar, $context);
        $data['selfcompletionform']  = self_completion_form($cm, $course);

        if (!empty($seminar->get_intro())) {
            $data['intro'] = format_module_intro('facetoface', $seminar->get_properties(), $cm->id);
        }

        // Display a warning about previously mismatched self approval sessions.
        ob_start();
        $renderer->selfapproval_notice($seminar->get_id());
        $data['selfapprovalnotice'] = ob_get_contents();
        ob_end_clean();

        $hassessions = $seminar->has_events();

        $filters = (new filter_list(function (string $parname, $default, string $type) {
            return $default;
        }))->add_default_filters();

        // only print the filter bar if this seminar has events
        if ($hassessions) {
            $data['filters'] = self::get_filter_bar_data($seminar, $context, $filters);
        }

        // only print the export form if this seminar has events
        if ($hassessions) {
            ob_start();
            $renderer->attendees_export_form($seminar);
            $data['attendeesexportform'] = ob_get_contents();
            ob_end_clean();
        }

        ob_start();
        $renderer->declare_interest($seminar);
        $data['declareinterest'] = ob_get_contents();
        ob_end_clean();

        $data['tables'] = [
            [
                'heading' => get_string('upcomingsessions', 'mod_facetoface'),
                'type' => 'upcoming',
            ],
            [
                'heading' => get_string('previoussessions', 'mod_facetoface'),
                'type' => 'past',
            ]
        ];

        // Behat steps are not supposed to look at deprecated CSS class names.
        if (!defined('BEHAT_SITE_RUNNING')) {
            $data['tables'][0]['legacystateclass'] = 'upcomingsessionlist';
            $data['tables'][1]['legacystateclass'] = 'previoussessionlist';
        }

        return new self($data);
    }

    /**
     * Get the template data of the action bar.
     *
     * @param seminar $seminar
     * @param context $context
     * @return array
     */
    private static function get_action_bar_data(seminar $seminar, context $context): array {
        $editevents = has_capability('mod/facetoface:editevents', $context);

        if ($editevents) {
            $actionbar = seminarevent_actionbar::builder()
                ->set_align('far')
                ->set_class('dashboard')
                ->add_commandlink(
                    'addevent',
                    new moodle_url(
                        'events/add.php',
                        array('f' => $seminar->get_id(), 'backtoallsessions' => 1)
                    ),
                    get_string('addsession', 'mod_facetoface')
                );

            return $actionbar->build()->get_template_data();
        }

        return array();
    }

    /**
     * Get the template data of the filter bar.
     *
     * @param seminar $seminar
     * @param context $context
     * @param filter_list $filters
     * @param integer|null $user
     * @return array
     */
    private static function get_filter_bar_data(seminar $seminar, context $context, filter_list $filters, int $user = null): array {
        $id = html_writer::random_id('id-');
        $filterbar = $filters->to_filterbar_builder($seminar, $id, $context, $user)
            ->set_icon(new \core\output\flex_icon('mod_facetoface|filters'));

        // Always add the "Reset" link
        $filterbar->add_link('Reset', '?f=' . $seminar->get_id());
        // seminarevent_dashboard.js takes over seminarevent_filterbar.js
        $filterbar->set_noscript(true);
        return $filterbar->build()->get_template_data();
    }
}
