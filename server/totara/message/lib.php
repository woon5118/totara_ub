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
 * @author Piers Harding <piers@catalyst.net.nz>
 * @package totara
 * @subpackage message
 */

defined('MOODLE_INTERNAL') || die();

// block display limits
define('TOTARA_MSG_ALERT_LIMIT', 5);
define('TOTARA_MSG_TASK_LIMIT', 5);

require_once('messagelib.php');

/**
 * Get the language string  and icon for the message status
 *
 * @param $msgstatus int message status
 * @return array('text' => , 'icon' => '')
 */
function totara_message_msgstatus_text($msgstatus) {
    global $CFG;

    if ($msgstatus == TOTARA_MSG_STATUS_OK) {
        $status = 'go';
        $text = get_string('statusok', 'block_totara_alerts');
    }
    else if ($msgstatus == TOTARA_MSG_STATUS_NOTOK) {
        $status = 'stop';
        $text = get_string('statusnotok', 'block_totara_alerts');
    }
    else {
        $status = 'grey_undecided';
        $text = get_string('statusundecided', 'block_totara_alerts');
    }
    return array('text' => $text, 'icon' => 't/'.$status);
}

/**
 * Get the language string  and icon for the message urgency
 *
 * @param $urgency int message urgency
 * @return array('text' => , 'icon' => '')
 */
function totara_message_urgency_text($urgency) {
    global $CFG;

    if ($urgency == TOTARA_MSG_URGENCY_URGENT) {
        $level = 'stop';
        $text = get_string('urgent', 'block_totara_alerts');
    }
    else {
        $level = 'go';
        $text = get_string('normal', 'block_totara_alerts');
    }
    return array('text' => $text, 'icon' => 't/'.$level);
}

/**
 * Get the short name of the message type
 *
 * @param $urgency int message urgency
 * @return array('text' => , 'icon' => '')
 */
function totara_message_cssclass($msgtype) {
    global $TOTARA_MESSAGE_TYPES;

    return $TOTARA_MESSAGE_TYPES[$msgtype];
}

/**
 * Get the language string  and icon for the message type
 *
 * @param $msgtype int message type
 * @return array('text' => '', 'icon' => '')
 */
function totara_message_msgtype_text($msgtype) {
    global $CFG, $TOTARA_MESSAGE_TYPES;

    if (array_key_exists($msgtype, $TOTARA_MESSAGE_TYPES)) {
        $text = get_string($TOTARA_MESSAGE_TYPES[$msgtype], 'totara_message');
    } else {
        $text = get_string('unknown', 'totara_message');
    }
    return array('text' => $text, 'icon' => '');
}


/**
 * Get the eventdata for a given event type
 * @param $id - message id
 * @param $event - event type
 * @param $metadata - allready read metadata record
 */
function totara_message_eventdata($id, $event, $metadata=null) {
    global $DB;

    if (empty($metadata)) {
        $metadata = $DB->get_record('message_metadata', array('messageid' => $id), '*', MUST_EXIST);
    }
    if ($event == 'onaccept') {
        $eventdata = unserialize($metadata->onaccept);
    } elseif ($event == 'oninfo') {
        if (isset($metadata->oninfo)) {
            $eventdata = unserialize($metadata->oninfo);
        } else {
            $eventdata = new stdClass();
        }
    } else {
        $eventdata = unserialize($metadata->onreject);
    }
    return $eventdata;
}


/**
 * construct the dismiss action in a new dialog
 *
 * @param int $id message Id
 * @return string HTML of dismiss button
 */
function totara_message_dismiss_action($id) {
    global $CFG, $FULLME, $PAGE, $OUTPUT;

    $clean_fullme = clean_param($FULLME, PARAM_LOCALURL);
    // Button Lang Strings
    $PAGE->requires->string_for_js('cancel', 'moodle');
    $PAGE->requires->string_for_js('dismiss', 'totara_message');
    $PAGE->requires->string_for_js('dismiss', 'block_totara_alerts');

    // Include JS for generic dismiss dialog
    $args = array('id'=>$id, 'selector'=>'dismissmsg', 'clean_fullme'=>$clean_fullme, 'sesskey'=>sesskey());
    $PAGE->requires->js_init_call('M.totara_message.create_dialog', $args);

    // TODO SCANMSG: Check this still outputs required markup in no/script render
    // Construct HTML for dismiss button
    $str = get_string('dismiss', 'block_totara_alerts');
    $deleteicon = $OUTPUT->flex_icon('delete-disabled', ['alt' => $str, 'classes' => 'ft-size-300']);
    $out = html_writer::tag('a', $deleteicon, array('href' => '#', 'id' => 'dismissmsg'.$id.'-dialog', 'name' => 'tm_dismiss_msg'));

    $out .= html_writer::tag('noscript',
        html_writer::tag('form',
            html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'id', 'value' => $id)) .
            html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey())) .
            html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'returnto', 'value' => $clean_fullme)) .
            html_writer::empty_tag('input', array('type' => 'image', 'class' => 'iconsmall', 'src' => $OUTPUT->image_url('t/delete_grey', 'totara_core'), 'title' => $str, 'alt' => $str)),
        array('action' => $CFG->wwwroot . '/totara/message/dismiss.php?id=' . $id, 'method' => 'post'))
    );
    return $out;
}


/**
 * construct the dismiss action in a new dialog
 *
 * @param int $id message Id
 * @param array $extrabuttons extra dialog buttons
 * @param string $messagetype message type
 */
function totara_message_alert_popup($id, $extrabuttons=array(), $messagetype) {
    global $CFG, $FULLME, $PAGE, $DB;

    $clean_fullme = clean_param($FULLME, PARAM_LOCALURL);

    // Button Lang Strings
    $PAGE->requires->string_for_js('cancel', 'moodle');
    $PAGE->requires->string_for_js('dismiss', 'totara_message');
    $PAGE->requires->string_for_js('reviewitems', 'block_totara_alerts');

    $metadata = $DB->get_record('message_metadata', array('messageid' => $id));
    $eventdata = totara_message_eventdata($id, 'onaccept', $metadata);
    if ($eventdata && isset($eventdata->action)) {
        switch ($eventdata->action) {
            case 'facetoface':
                // Note that seminarevent may not exist if it has been deleted in the meantime.
                $seminarevent = \mod_facetoface\seminar_event::seek($eventdata->data['session']->id);
                $canbook = ($seminarevent->get_id() && ($seminarevent->has_capacity() || $seminarevent->get_allowoverbook()));
                if (!$canbook) {
                    // Remove accept / reject buttons
                    $extrabuttons = array();
                }
                break;
            case 'prog_extension':
                require_once($CFG->dirroot . '/totara/program/lib.php');
                if (empty($CFG->enableprogramextensionrequests) || !totara_prog_extension_allowed($eventdata->data['programid'])) {
                    // Remove Grant/Deny/Manage extension requests buttons.
                    $extrabuttons = array();
                }
                break;
        }
    }

    $extrabuttonjson = '';
    if ($extrabuttons) {
        $extrabuttonjson .= '{';
        $count = sizeof($extrabuttons);
        for ($i = 0; $i < $count; $i++) {
            $clean_redirect = clean_param($extrabuttons[$i]->redirect, PARAM_LOCALURL);
            $extrabuttonjson .= '"'.$extrabuttons[$i]->text.'":{"action":"'.$extrabuttons[$i]->action.'&sesskey='.sesskey().'", "clean_redirect":"'.$clean_redirect.'"}';
            $extrabuttonjson .= ( $i < $count-1 )? ',' : '';
        }
        $extrabuttonjson .= '}';
    }

    $args = array('id'=>$id, 'selector'=>$messagetype, 'clean_fullme'=>$clean_fullme, 'sesskey'=>sesskey(), 'extrabuttonjson'=>$extrabuttonjson);
    $PAGE->requires->js_init_call('M.totara_message.create_dialog', $args);
}


/**
 * checkbox all/none script
 */
function totara_message_checkbox_all_none() {
    global $PAGE;
    $PAGE->requires->strings_for_js(array('all','none'), 'moodle');
    $PAGE->requires->js_init_call('M.totara_message.select_all_none_checkbox');
}


/**
 * include action buttons in a new dialog
 *
 * @param string $action action to perform
 */
function totara_message_action_button($action) {
    global $CFG, $FULLME, $PAGE;

    $clean_fullme = clean_param($FULLME, PARAM_LOCALURL);
    // Button Lang Strings
    $str = get_string($action, 'totara_message');
    $PAGE->requires->string_for_js('cancel', 'moodle');

    $args = array('action'=>$action, 'action_str' => $str, 'clean_fullme'=>$clean_fullme, 'sesskey'=>sesskey());
    $PAGE->requires->js_init_call('M.totara_message.create_action_dialog', $args);
}
