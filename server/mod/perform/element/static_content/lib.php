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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Johannes Cilliers <simon.coggins@totaralearning.com>
 * @package performelement_static_content
 */

/**
 * This is a callback from the file system. Use for serving the file to the user.
 * @see file_pluginfile
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context  $context
 * @param string    $filearea
 * @param array     $args
 * @param bool      $forcedownload
 * @param array     $options
 *
 * @return void
 */
function performelement_static_content_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options) {
    global $CFG;
    require_once("{$CFG->dirroot}/lib/filelib.php");

    // TODO: Is there further access control here?

    // Check multi-tenancy.
    if ($context->is_user_access_prevented()) {
        send_file_not_found();
    }

    // Whitelisted file areas.
    if (!in_array($filearea, ['content'])) {
        return;
    }

    $relativepath = implode("/", $args);
    $fullpath = "/{$context->id}/performelement_static_content/{$filearea}/{$relativepath}";

    $fs = get_file_storage();
    $file = $fs->get_file_by_hash(sha1($fullpath));

    if (!$file) {
        return;
    }

    send_stored_file($file, 360, 0, $forcedownload, $options);
}