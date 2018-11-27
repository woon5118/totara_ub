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
 * Totara navigation deleting page.
 *
 * @package    totara_core
 * @subpackage navigation
 * @author     Oleg Demeshev <oleg.demeshev@totaralms.com>
 */

use \totara_core\totara\menu\helper;
use \totara_core\totara\menu\item;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/lib/adminlib.php');

// Menu item id.
$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

admin_externalpage_setup('totaranavigation');
// Double check capability, the settings file is too far away.
require_capability('totara/core:editmainmenu', context_system::instance());

$PAGE->set_url('/totara/core/menu/delete.php', array('id' => $id));

$returnurl = \totara_core\totara\menu\helper::get_admin_edit_return_url($id);

$record = $DB->get_record('totara_navigation', array('id' => $id));
if (!$record) {
    // Most likely result of concurrent editing, just go back.
    redirect($returnurl, get_string('error:findingmenuitem', 'totara_core'), 0, \core\output\notification::NOTIFY_ERROR);
}

$node = item::create_instance($record);
if ($node) {
    $itemtitle = $node->get_title();
} else {
    $itemtitle = $record->classname;
}

if (!helper::is_item_deletable($record->id)) {
    redirect($returnurl, get_string('error:menuitemcannotremove', 'totara_core', $itemtitle), 0, core\output\notification::NOTIFY_ERROR);
}

if ($confirm) {
    require_sesskey();
    ignore_user_abort(true);
    if (helper::delete_item($record->id)) {
        $returnurl = \totara_core\totara\menu\helper::get_admin_edit_return_url(0);
        redirect($returnurl, get_string('menuitem:deletesuccess', 'totara_core', $itemtitle), 0, core\output\notification::NOTIFY_SUCCESS);
    } else {
        redirect($returnurl, get_string('error:menuitemcannotremove', 'totara_core', $itemtitle), 0, core\output\notification::NOTIFY_ERROR);
    }
}

$PAGE->set_title($itemtitle);
$PAGE->navbar->add($itemtitle);
$PAGE->set_heading($itemtitle);

// Display page header.
echo $OUTPUT->header();

$url = new moodle_url('/totara/core/menu/delete.php', array('id' => $id, 'confirm' => 'true'));
$continue = new single_button($url, get_string('continue'), 'post');
$cancel = new single_button($returnurl, get_string('cancel'), 'get');

echo $OUTPUT->box_start('notifynotice');
echo html_writer::tag('p', get_string('menuitem:delete', 'totara_core', $itemtitle));
echo $OUTPUT->box_end();

echo html_writer::tag('div', $OUTPUT->render($continue) . $OUTPUT->render($cancel), array('class' => 'buttons'));

echo $OUTPUT->footer();
