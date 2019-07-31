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
namespace container_workspace\webapi\resolver\type;

use core\webapi\execution_context;
use core\webapi\type_resolver;
use container_workspace\member\member_request as model;
use totara_engage\formatter\field\date_field_formatter;
use container_workspace\interactor\workspace\interactor;

/**
 * Type resolver for member request.
 */
final class member_request implements type_resolver {
    /**
     * @param string            $field
     * @param model             $source
     * @param array             $args
     * @param execution_context $ec
     *
     * @return mixed
     */
    public static function resolve(string $field, $source, array $args, execution_context $ec) {
        if (!($source instanceof model)) {
            throw new \coding_exception("Invalid instance of source");
        }

        switch ($field) {
            case 'id':
                return $source->get_id();

            case 'user':
                return $source->get_user();

            case 'is_accepted':
                return $source->is_accepted();

            case 'is_declined':
                return $source->is_declined();

            case 'time_description':
                $format = $args['format'] ?? null;

                if ($ec->has_relevant_context()) {
                    $context = $ec->get_relevant_context();
                } else {
                    $workspace_id = $source->get_workspace_id();
                    $context = \context_course::instance($workspace_id);
                }

                $formatter = new date_field_formatter($format, $context);
                $time_created = $source->get_time_created();

                return $formatter->format($time_created);

            case 'workspace_id':
                return $source->get_workspace_id();

            case 'workspace_interactor':
                $workspace_id = $source->get_workspace_id();
                $user_id = $source->get_user_id();

                return interactor::from_workspace_id($workspace_id, $user_id);

            default:
                debugging("Field '{$field}' is not supported yet", DEBUG_DEVELOPER);
                return null;
        }
    }
}