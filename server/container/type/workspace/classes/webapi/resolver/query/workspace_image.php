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

use core\files\file_helper;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use core_container\factory;
use container_workspace\workspace;
use container_workspace\theme\file\workspace_image as workspace_image_file;
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
        require_login();
        advanced_feature::require('container_workspace');

        if (empty($args['workspace_id'])) {
            $workspace_image = new workspace_image_file();
            $url = $workspace_image->get_current_or_default_url();
        } else {
            $workspace = factory::from_id($args['workspace_id']);
            if (!$workspace->is_typeof(workspace::get_type())) {
                throw new \coding_exception("Cannot fetch image of a container that is not a workspace");
            }

            $file_helper = new file_helper(
                workspace::get_type(),
                workspace::IMAGE_AREA,
                $workspace->get_context()
            );
            $url = $file_helper->get_file_url();
        }

        return $url->out();
    }
}