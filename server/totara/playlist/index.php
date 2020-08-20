<?php
/**
 * This file is part of Totara Learn
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_playlist
 */

use totara_core\advanced_feature;
use totara_playlist\playlist;
use totara_playlist\event\playlist_viewed;
use totara_engage\access\access_manager;
use core\notification;
use totara_playlist\totara_engage\link\nav_helper;
use totara_tui\output\component;

require_once(__DIR__ . "/../../config.php");
global $OUTPUT, $PAGE, $USER;

require_login();
advanced_feature::require('engage_resources');

$context = \context_user::instance($USER->id);
require_capability('totara/engage:viewlibrary', $context, $USER->id);

$id = required_param('id', PARAM_INT);
$library_view = optional_param('libraryView', false, PARAM_BOOL);
$source = optional_param('source', null, PARAM_TEXT);

$playlist = playlist::from_id($id);

$PAGE->set_context($playlist->get_context());
$PAGE->set_url("/totara/playlist/index.php", ['id' => $playlist->get_id()]);
$PAGE->set_pagelayout('legacynolayout');
$PAGE->set_title($playlist->get_name());

$back_button = nav_helper::build_back_button($playlist->get_userid(), $source);

if (access_manager::can_access($playlist)) {
    $event = playlist_viewed::from_playlist($playlist);
    $event->trigger();

    $tui = null;
    if ($library_view) {
        $tui = new component('totara_engage/pages/LibraryView', [
            'id' => "playlist_{$id}",
            'title' => $playlist->get_name(),
            'content' => [
                'component' => 'PlaylistResourcesContent',
                'tuicomponent' => 'totara_playlist/components/contribution/PlaylistResources',
            ],
            'sidePanel' => [
                'component' => 'PlaylistResourcesSidePanelContent',
                'tuicomponent' => 'totara_playlist/components/sidepanel/content/Playlist',
            ],
            'page-props' => [
                'playlistId' => $playlist->get_id(),
                'back-button' => $back_button ?? null,
            ],
        ]);

    } else {
        $tui = new component(
            'totara_playlist/pages/PlaylistView',
            [
                'playlist-id' => $playlist->get_id(),
                'back-button' => $back_button ?? null,
            ]
        );
    }

    $tui->register($PAGE);
    echo $OUTPUT->header();
    echo $OUTPUT->render($tui);

} else {

    echo $OUTPUT->header();
    notification::error(get_string('cannotviewplaylist', 'totara_playlist'));
}

echo $OUTPUT->footer();