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
 * @author Simon Coggins <simon.coggins@totaralms.com>
 * @package totara
 * @subpackage totara_hierarchy
 */

use totara_core\advanced_feature;

require_once(__DIR__ . '/../../../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/totara/core/dialogs/dialog_content_hierarchy.class.php');

require_once($CFG->dirroot.'/totara/hierarchy/prefix/competency/lib.php');
require_once($CFG->dirroot.'/totara/hierarchy/prefix/position/lib.php');
require_once($CFG->dirroot.'/totara/core/js/lib/setup.php');

position::check_feature_enabled();

// Page title
$pagetitle = 'linkcompetencies';

///
/// Params
///

// Assign to id
$assignto = required_param('assignto', PARAM_INT);

// Parent id
$parentid = optional_param('parentid', 0, PARAM_INT);

// Framework id
$frameworkid = optional_param('frameworkid', 0, PARAM_INT);

// Only return generated tree html
$treeonly = optional_param('treeonly', false, PARAM_BOOL);

// Check if Competencies are enabled.
if (advanced_feature::is_disabled('competencies')) {
    echo html_writer::tag('div', get_string('competenciesdisabled', 'totara_hierarchy'), array('class' => 'notifyproblem'));
    die();
}

///
/// Permissions checks
///

// Setup page
admin_externalpage_setup('positionmanage');


// Load currently assigned competencies
$position = new position();     // Used to determine the currently-assigned competencies
$currentlyassigned = $position->get_assigned_competencies($assignto, $frameworkid);
if (!is_array($currentlyassigned)) {
    $currentlyassigned = array();
}

///
/// Display page
///

// Load dialog content generator
$dialog = new totara_dialog_content_hierarchy_multi('competency', $frameworkid);

// Toggle treeview only display
$dialog->show_treeview_only = $treeonly;

// Load items to display
$dialog->load_items($parentid);

// Set disabled items
$dialog->disabled_items = $currentlyassigned;

// Set title
$dialog->selected_title = 'itemstoadd';
$dialog->selected_items = $currentlyassigned;

// Additional url parameters
$dialog->urlparams = array('assignto' => $assignto);

// Display
echo $dialog->generate_markup();

