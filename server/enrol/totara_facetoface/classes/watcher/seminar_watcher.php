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
 * @package totara_facetoface
 */

namespace enrol_totara_facetoface\watcher;

/**
 * Hook watcher for a seminar.
 */
class seminar_watcher {
    /**
     * Rewrite a sign-up link if necessary.
     *
     * @param \mod_facetoface\hook\alternative_signup_link $hook
     * @return void
     */
    public static function alter_signup_link(\mod_facetoface\hook\alternative_signup_link $hook) {
        global $CFG;
        require_once($CFG->dirroot . '/enrol/totara_facetoface/lib.php');

        /** @var \enrol_totara_facetoface_plugin */
        $enrol = new \enrol_totara_facetoface_plugin();
        $seminar = $hook->seminarevent->get_seminar();
        // see if the current user can enrol on the course
        if (in_array($hook->seminarevent->get_id(), array_keys($enrol->get_enrolable_sessions($seminar->get_course())))) {
            $showsignuplink = true;
            if (!enrol_is_enabled('totara_facetoface') || $CFG->enableavailability) {
                $cm = get_coursemodule_from_instance('facetoface', $hook->seminarevent->get_facetoface());
                $modinfo = get_fast_modinfo($cm->course);
                $cm = $modinfo->get_cm($cm->id);

                // If Seminar enrolment plugin is not enabled check visibility of the activity.
                if (!enrol_is_enabled('totara_facetoface')) {
                    // Check visibility of activity (includes visible flag, conditional availability, etc) before adding Sign up link.
                    $showsignuplink = $cm->uservisible;
                }

                if ($CFG->enableavailability) {
                    // Check whether this activity is available for the user. However if it's available, but not visible
                    // for some reason we're still not displaying a link.
                    $showsignuplink &= $cm->available;
                }
            }
            if (!$showsignuplink) {
                $hook->signuplink = '';
                $hook->signuptsandcslink = '';
                return;
            }

            // redirect to totara_facetoface *unless* the user has already enrolled on the course
            $cm = $seminar->get_coursemodule();
            $context = \context_module::instance($cm->id);
            if (!is_enrolled($context)) {
                $hook->signuplink = '/enrol/totara_facetoface/signup.php';
                $hook->signuptsandcslink = '/enrol/totara_facetoface/ajax/signup_tsandcs.php';
            }
        }
    }
}
