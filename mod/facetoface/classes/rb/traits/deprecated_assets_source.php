<?php

namespace mod_facetoface\rb\traits;

defined('MOODLE_INTERNAL') || die();

trait deprecated_assets_source {

    /**
     * Asset actions
     *
     * @deprecated Since Totara 12.0
     * @param int $assetid
     * @param stdClass $row
     * @param bool $isexport
     */
    public function rb_display_actions($assetid, $row, $isexport = false) {
        debugging('rb_source_facetoface_asset::rb_display_actions has been deprecated since Totara 12.0. Use mod_facetoface\rb\display\f2f_asset_actions::display', DEBUG_DEVELOPER);
        global $OUTPUT;

        if ($isexport) {
            return null;
        }

        $output = array();

        $output[] = $OUTPUT->action_icon(
            new moodle_url('/mod/facetoface/reports/assets.php', array('assetid' => $assetid)),
            new pix_icon('t/calendar', get_string('details', 'mod_facetoface'))
        );

        $output[] = $OUTPUT->action_icon(
            new moodle_url('/mod/facetoface/asset/edit.php', array('id' => $assetid)),
            new pix_icon('t/edit', get_string('edit'))
        );

        if ($row->hidden && $this->embeddedurl) {
            $params = array_merge($this->urlparams, ['action' => 'show', 'id' => $assetid, 'sesskey' => sesskey()]);
            $output[] = $OUTPUT->action_icon(
                new moodle_url($this->embeddedurl, $params),
                new pix_icon('t/show', get_string('assetshow', 'mod_facetoface'))
            );
        } else if ($this->embeddedurl) {
            $params = array_merge($this->urlparams, ['action' => 'hide', 'id' => $assetid, 'sesskey' => sesskey()]);
            $output[] = $OUTPUT->action_icon(
                new moodle_url($this->embeddedurl, $params),
                new pix_icon('t/hide', get_string('assethide', 'mod_facetoface'))
            );

        }
        if ($row->cntdates) {
            $output[] = $OUTPUT->pix_icon('t/delete_gray', get_string('currentlyassigned', 'mod_facetoface'), 'moodle', array('class' => 'disabled iconsmall'));
        } else {
            $output[] = $OUTPUT->action_icon(
                new moodle_url('/mod/facetoface/asset/manage.php', ['action' => 'delete', 'id' => $assetid]),
                new pix_icon('t/delete', get_string('delete'))
            );
        }

        return implode('', $output);
    }
}