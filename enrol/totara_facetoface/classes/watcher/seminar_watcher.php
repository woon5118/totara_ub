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
            // redirect to totara_facetoface *unless* the user has already enrolled on the course
            $cm = $seminar->get_coursemodule();
            $context = \context_module::instance($cm->id);
            if (!is_enrolled($context)) {
                $hook->signuplink = '/enrol/totara_facetoface/signup.php';
            }
        }
    }
}
