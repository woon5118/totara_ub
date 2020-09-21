<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package container_workspace
 */
use container_workspace\discussion\discussion;
use container_workspace\totara\menu\your_spaces;
use totara_core\advanced_feature;
use container_workspace\interactor\workspace\interactor;
use totara_tui\output\component;

require_once(__DIR__ . "/../../../config.php");
global $OUTPUT, $PAGE;

require_login();

// Requiring discussion's id
$discussion_id = required_param('id', PARAM_INT);
advanced_feature::require('container_workspace');

$discussion = discussion::from_id($discussion_id);
$workspace = $discussion->get_workspace();
$context = $workspace->get_context();

$PAGE->set_url("/container/type/workspace/discussion.php", ['id' => $discussion_id]);
$PAGE->set_context($context);
$PAGE->set_totara_menu_selected(your_spaces::class);

$PAGE->set_title(format_string($workspace->fullname));
$PAGE->set_pagelayout('legacynolayout');

$interactor = new interactor($workspace);
$tui = new component('container_workspace/pages/WorkspaceEmptyPage');

if ($interactor->can_view_discussions()) {
    $tui = new component(
        'container_workspace/pages/WorkspaceDiscussionPage',
        ['discussion-id' => $discussion_id]
    );
}

$tui->register($PAGE);

echo $OUTPUT->header();
echo $OUTPUT->render($tui);
echo $OUTPUT->footer();