<?php

namespace mod_facetoface\rb\traits;

defined('MOODLE_INTERNAL') || die();

trait deprecated_events_source {

    /**
     * Display evet actions
     *
     * @deprecated Since Totara 12.0
     * @param $session
     * @param $row
     * @param bool $isexport
     * @return null|string
     */
    public function rb_display_actions($session, $row, $isexport = false) {
        debugging('rb_source_facetoface_events::rb_display_actions has been deprecated since Totara 12.0. Use mod_facetoface\rb\display\f2f_session_actions::display', DEBUG_DEVELOPER);
        global $OUTPUT;

        if ($isexport) {
            return null;
        }

        $cm = get_coursemodule_from_instance('facetoface', $row->facetofaceid);
        $context = context_module::instance($cm->id);
        if (!has_capability('mod/facetoface:viewattendees', $context)) {
            return null;
        }

        return html_writer::link(
            new moodle_url('/mod/facetoface/attendees/view.php', array('s' => $session)),
            $OUTPUT->pix_icon('t/cohort', get_string("attendees", "facetoface"))
        );
    }

    /**
     * Spaces left on session.
     *
     * @deprecated Since Totara 12.0
     * @param string $count Number of signups
     * @param object $row Report row
     * @return string Display html
     */
    public function rb_display_session_spaces($count, $row) {
        debugging('rb_source_facetoface_events::rb_display_session_spaces has been deprecated since Totara 12.0. Use mod_facetoface\rb\display\f2f_session_spaces::display', DEBUG_DEVELOPER);
        $spaces = $row->overall_capacity - $count;
        return ($spaces > 0 ? $spaces : 0);
    }

    /**
     * Show if manager's approval required
     *
     * @deprecated Since Totara 12.0
     * @param bool $required True when approval required
     * @param stdClass $row
     */
    public function rb_display_approver($required, $row) {
        debugging('rb_source_facetoface_events::rb_display_approver has been deprecated since Totara 12.0', DEBUG_DEVELOPER);
        if ($required) {
            return get_string('manager', 'core_role');
        } else {
            return get_string('noone', 'rb_source_facetoface_summary');
        }
    }
}