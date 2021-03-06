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
 * @author Ben Lobo <ben.lobo@kineo.com>
 * @package totara
 * @subpackage program
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot.'/totara/core/dialogs/dialog_content_hierarchy.class.php');
require_once($CFG->dirroot . '/totara/program/lib.php');

$PAGE->set_context(context_system::instance());
require_login();

///
/// Setup / loading data
///

// Get program id and check capabilities
$programid = required_param('programid', PARAM_INT);
require_capability('totara/program:configureassignments', context_program::instance($programid));

// Heirarchy type, e.g. position
$type = required_param('type', PARAM_ALPHA);

switch ($type) {
    case 'position':
        $table = 'pos';
        $assigntype = ASSIGNTYPE_POSITION;
        break;
    case 'organisation':
        $table = 'org';
        $assigntype = ASSIGNTYPE_ORGANISATION;
        break;
    default:
        throw new invalid_parameter_exception;
}

// Parent id
$parentid = optional_param('parentid', 0, PARAM_INT);

// Framework id
$frameworkid = optional_param('frameworkid', 0, PARAM_INT);

// Only return generated tree html
$treeonly = optional_param('treeonly', false, PARAM_BOOL);

// Already selected items
$selected = optional_param('selected', array(), PARAM_SEQUENCE);
$removed = optional_param('removed', array(), PARAM_SEQUENCE);

$selectedids = totara_prog_removed_selected_ids($programid, $selected, $removed, $assigntype);

$allselected = array();
if (!empty($selectedids)) {
    list($selectedsql, $selectedparams) = $DB->get_in_or_equal($selectedids);
    $allselected = $DB->get_records_select($table, "id {$selectedsql}", $selectedparams);
}

// Don't let them remove the currently selected ones
$unremovable = $allselected;


///
/// Setup dialog
///

// Load dialog content generator; skip access, since it's checked above
$dialog = new totara_dialog_content_hierarchy_multi($type, $frameworkid, false, $skipaccesschecks=true);

// Toggle treeview only display
$dialog->show_treeview_only = $treeonly;

// Load items to display
$dialog->load_items($parentid);

// Set disabled/selected items
$dialog->selected_items = $allselected;

// Set unremovable items
$dialog->unremovable_items = $unremovable;

// Set title
$dialog->selected_title = 'itemstoadd';

$dialog->select_title = '';

// Addition url parameters
$dialog->urlparams = array('programid' => $programid, 'type' => $type, 'table' => $table);

// Display
echo $dialog->generate_markup();
