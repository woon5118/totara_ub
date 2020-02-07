<?php

namespace mod_facetoface\rb\traits;

defined('MOODLE_INTERNAL') || die();

trait deprecated_rooms_source {

    /**
     * Room name
     *
     * @deprecated Since Totara 12.0
     * @param int $roomid
     * @param stdClass $row
     * @param bool $isexport
     */
    public function rb_display_actions($roomid, $row, $isexport = false) {
        debugging('rb_source_facetoface_rooms::rb_display_actions has been deprecated since Totara 12.0. Use mod_facetoface\rb\display\f2f_room_actions::display', DEBUG_DEVELOPER);
        global $OUTPUT;

        if ($isexport) {
            return null;
        }

        $output = array();

        $output[] = $OUTPUT->action_icon(
            new moodle_url('/mod/facetoface/reports/rooms.php', array('roomid' => $roomid)),
            new pix_icon('t/calendar', get_string('details', 'mod_facetoface'))
        );

        if ($row->custom) {
            $output[] = $OUTPUT->pix_icon('t/edit', get_string('nocustomroomedit', 'mod_facetoface'), 'moodle', array('class' => 'disabled iconsmall'));
        }
        else {
            $output[] = $OUTPUT->action_icon(
                new moodle_url('/mod/facetoface/room/edit.php', array('id' => $roomid)),
                new pix_icon('t/edit', get_string('edit'))
            );
        }

        if ($row->hidden && $this->embeddedurl) {
            $params = array_merge($this->urlparams, array('action' => 'show', 'id' => $roomid, 'sesskey' => sesskey()));
            $output[] = $OUTPUT->action_icon(
                new moodle_url($this->embeddedurl, $params),
                new pix_icon('t/show', get_string('roomshow', 'mod_facetoface'))
            );
        } else if ($this->embeddedurl) {
            $params = array_merge($this->urlparams, array('action' => 'hide', 'id' => $roomid, 'sesskey' => sesskey()));
            $output[] = $OUTPUT->action_icon(
                new moodle_url($this->embeddedurl, $params),
                new pix_icon('t/hide', get_string('roomhide', 'mod_facetoface'))
            );

        }
        if ($row->cntdates) {
            $output[] = $OUTPUT->pix_icon('t/delete_gray', get_string('currentlyassigned', 'mod_facetoface'), 'moodle', array('class' => 'disabled iconsmall'));
        } else {
            $output[] = $OUTPUT->action_icon(
                new moodle_url('/mod/facetoface/room/manage.php', array('action' => 'delete', 'id' => $roomid)),
                new pix_icon('t/delete', get_string('delete'))
            );
        }

        return implode('', $output);
    }
}