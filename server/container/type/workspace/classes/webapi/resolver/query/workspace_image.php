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
namespace container_workspace\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\query_resolver;
use core_container\factory;
use container_workspace\workspace;
use totara_core\advanced_feature;

/**
 * Class workspace_image
 * @package container_workspace\webapi\resolver\query
 */
final class workspace_image implements query_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return string
     */
    public static function resolve(array $args, execution_context $ec): string {
        global $CFG, $OUTPUT;
        require_login();
        advanced_feature::require('container_workspace');

        if (empty($args['workspace_id'])) {
            return $OUTPUT->image_url("default_space", 'container_workspace')->out();
        }

        require_once("{$CFG->dirroot}/lib/filelib.php");
        $workspace = factory::from_id($args['workspace_id']);

        if (!$workspace->is_typeof(workspace::get_type())) {
            throw new \coding_exception("Cannot fetch image of a container that is not a workspace");
        }

        $context = $workspace->get_context();
        $fs = get_file_storage();

        $files = $fs->get_area_files(
            $context->id,
            'container_workspace',
            workspace::IMAGE_AREA,
            0
        );

        $files = array_filter(
            $files,
            function (\stored_file $file): bool {
                return !$file->is_directory() && $file->is_valid_image();
            }
        );

        if (empty($files)) {
            return $OUTPUT->image_url("default_space", 'container_workspace')->out();
        }

        /** @var \stored_file $file */
        $file = reset($files);
        $url = \moodle_url::make_pluginfile_url(
            $context->id,
            'container_workspace',
            workspace::IMAGE_AREA,
            0,
            '/',
            $file->get_filename()
        );

        return $url->out();
    }
}