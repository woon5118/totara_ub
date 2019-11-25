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
 * @package editor_weka
 */
namespace editor_weka\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\query_resolver;
use editor_weka\local\file_helper;

/**
 * A query resolver for fetching the repository data.
 */
final class repository_data implements query_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     * @return array
     */
    public static function resolve(array $args, execution_context $ec): array {
        require_login();

        $context_id = \context_system::instance()->id;
        if (isset($args['context_id'])) {
            $context = \context::instance_by_id($args['context_id']);

            if (!$ec->has_relevant_context()) {
                $ec->set_relevant_context($context);
            }
        }

        return file_helper::get_upload_repository($context_id);
    }
}