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
use totara_core\advanced_feature;
use container_workspace\workspace;
use core_message\api;
use container_workspace\interactor\workspace\interactor;

/**
 * Class search_users
 * @package container_workspace\webapi\resolver\query
 */
final class search_users implements query_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return \stdClass[]
     */
    public static function resolve(array $args, execution_context $ec): array {
        global $USER;

        require_login();
        advanced_feature::require('container_workspace');

        $workspace_id = $args['workspace_id'];

        /** @var workspace $workspace */
        $workspace = factory::from_id($workspace_id);

        if (!$workspace->is_typeof(workspace::get_type())) {
            throw new \coding_exception("Cannot find workspace by id '{$workspace_id}'");
        }

        if ($workspace->is_private()) {
            // Workspace is a private one, hence we will have to check if the actor is a member of this workspace.
            $actor_interactor = new interactor($workspace, $USER->id);
            if (!$actor_interactor->is_joined()) {
                throw new \coding_exception("Actor is not a member of the workspace");
            }
        }

        if (!$ec->has_relevant_context()) {
            $context = $workspace->get_context();
            $ec->set_relevant_context($context);
        }

        $pattern = $args['pattern'] ?? '';

        [$contacts, $courses, $non_contacts] = api::search_users($USER->id, $pattern, 20);
        $user_contacts = array_merge($contacts, $non_contacts);

        $user_name_fields = get_all_user_name_fields();

        // Reset on keys.
        $user_name_fields = array_values($user_name_fields);

        // Adding more additional fields in order to make resolver work.
        $user_name_fields[] = 'picture';
        $user_name_fields[] = 'imagealt';
        $user_name_fields[] = 'email';

        $user_name_fields = implode(", ", $user_name_fields);

        $result_records = [];
        $current_owner_id = $workspace->get_user_id();

        foreach ($user_contacts as $user_contact) {
            if ($user_contact->userid == $current_owner_id) {
                // We will skip the current owner for now.
                continue;
            }

            // The contact record does not have the information we need.
            $user_record = clone $user_contact;
            $user_record->id = $user_contact->userid;

            $field_record = \core_user::get_user($user_record->id, $user_name_fields, MUST_EXIST);
            $field_attributes = get_object_vars($field_record);

            foreach ($field_attributes as $field_attribute => $value) {
                $user_record->{$field_attribute} = $value;
            }

            $result_records[] = $user_record;
        }

        if (empty($pattern) && $current_owner_id != $USER->id) {
            // Actor is not an owner and we are not looking for specific users.
            // Hence add the current user as an option. This is happening because
            // we want to make this actor's record available for the list of options.
            $actor = \core_user::get_user($USER->id, $user_name_fields, MUST_EXIST);
            $actor->id = $USER->id;

            $result_records = array_merge([$actor], $result_records);
        }

        return $result_records;
    }
}