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
namespace container_workspace\webapi\resolver\mutation;

use container_workspace\exception\workspace_exception;
use container_workspace\interactor\workspace\interactor;
use container_workspace\workspace;
use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use core_container\factory;
use totara_core\advanced_feature;

/**
 * Resolver for updating
 */
final class update implements mutation_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return workspace
     */
    public static function resolve(array $args, execution_context $ec): workspace {
        global $USER;
        require_login();
        advanced_feature::require('container_workspace');

        /** @var workspace $workspace */
        $workspace = factory::from_id($args['id']);
        $type = workspace::get_type();

        if (!$workspace->is_typeof($type)) {
            throw new \coding_exception("Cannot update different container within workspace resolver");
        }

        $owner_id = $workspace->get_user_id();
        $context = $workspace->get_context();

        $interactor = new interactor($workspace, $USER->id);

        if ($USER->id != $owner_id && !$interactor->can_update()) {
            // Not a same owner and also not a site admin, therefore we skip it.
            throw workspace_exception::on_update($workspace->fullname);
        }

        $is_private = $args['private'] ?? $workspace->is_private();
        $is_hidden = $args['hidden'] ?? $workspace->is_hidden();

        // Check for the access settings config, to find out if update is possible or not.
        if (!$is_private && $is_hidden) {
            throw new \coding_exception("Cannot have a hidden public workspace");
        }

        if ($is_hidden && !$workspace->can_move_to_hidden()) {
            throw new \coding_exception("Cannot go down to hidden workspace");
        }

        if ($is_private && !$workspace->can_move_to_private()) {
            throw new \coding_exception("Cannot update to private workspace");
        }

        if (!$is_private && !$workspace->can_move_to_public()) {
            throw new \coding_exception("Cannot update to public workspace");
        }

        $record = new \stdClass();
        if (!isset($args['name'])) {
            // Set it back to the original if it is not being set.
            $record->fullname = $workspace->fullname;
        } else {
            $record->fullname = $args['name'];
        }

        if (isset($args['description'])) {
            $record->summary = $args['description'];
        }

        if (!isset($args['description_format'])) {
            $record->summaryformat = $workspace->summaryformat;
        } else {
            $record->summaryformat = $args['description_format'];
        }

        $record->visibleold = $workspace->visibleold;
        $record->visible = $is_hidden ? 0 : 1;
        $record->workspace_private = $is_private;

        // Update the record, then update the topics if there are any.
        $workspace->update($record);

        if (!empty($args['draft_id'])) {
            $workspace->save_image((int) $args['draft_id']);
        }

        return $workspace;
    }
}
