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
namespace container_workspace\local;

use container_workspace\exception\workspace_exception;
use container_workspace\interactor\workspace\category_interactor;
use container_workspace\interactor\workspace\interactor;
use container_workspace\loader\member\loader;
use container_workspace\member\member;
use container_workspace\query\member\query;
use container_workspace\tracker\tracker;
use container_workspace\workspace;

/**
 * Class workspace_helper
 * @package container_workspace\local
 */
final class workspace_helper {
    /**
     * Preventing this class form being constructed.
     * workspace_helper constructor.
     */
    private function __construct() {
    }

    /**
     * @param string        $name
     * @param int           $actor_id
     * @param int|null      $category_id
     * @param string|null   $summary
     * @param int|null      $summary_format
     * @param int|null      $draft_id           This is for default image of a workspace
     * @param bool          $is_private         To tell whether the workspace is private or not.
     * @param bool          $is_hidden          Only works if the workspace is private - if not and hidden is set to
     *                                          true, then exception will be thrown.
     *
     * @return workspace
     */
    public static function create_workspace(string $name, int $actor_id,
                                            ?int $category_id = null, ?string $summary = null,
                                            ?int $summary_format = null,
                                            ?int $draft_id = null, bool $is_private = false,
                                            bool $is_hidden = false): workspace {
        if (empty($name)) {
            throw new \coding_exception("Cannot create a workspace with empty name");
        }

        if (null === $category_id) {
            $category_id = workspace::get_default_category_id();
        }

        $category_interactor = category_interactor::from_category_id($category_id);
        // The three types of workspaces (private, public & hidden) have individual capabilities
        // to work with. If you're trying to create a specific type of workspace, the matching
        // capability must be assigned.

        // Create hidden check
        if ($is_hidden && !$category_interactor->can_create_hidden()) {
            throw workspace_exception::on_create();
        }

        // Create private check
        if ($is_private && !$category_interactor->can_create_private()) {
            throw workspace_exception::on_create();
        }

        // Finally public check. Public is assumed if we're not private (since hidden is a child of private)
        if (!$is_private && !$category_interactor->can_create_public()) {
            throw workspace_exception::on_create();
        }

        // Final sanity check - you can't make a hidden public workspace.
        if (!$is_private && $is_hidden) {
            throw new \coding_exception("Cannot create a hidden public workspace");
        }

        $record = new \stdClass();
        $record->fullname = $name;
        $record->summary = '';
        $record->summaryformat = FORMAT_JSON_EDITOR;
        $record->user_id = $actor_id;
        $record->category = $category_id;
        $record->visible = 1;
        $record->workspace_private = $is_private;

        if ($is_private && $is_hidden) {
            $record->visible = 0;
        }

        if (null !== $summary) {
            $record->summary = $summary;
        }

        if (null !== $summary_format) {
            $record->summaryformat = $summary_format;
        }

        /** @var workspace $workspace */
        $workspace = workspace::create($record);
        $manager = $workspace->get_enrolment_manager();

        // After create a workspace, we might need to enable the self enrol plugin
        // here for this specific public workspace.
        $manager->enable_self_enrolment();
        $manager->enable_manual_enrolment();

        // Then enrol this very user as an owner for the workspace.
        member::join_workspace($workspace, $actor_id);

        if (null !== $draft_id && 0 !== $draft_id) {
            $workspace->save_image($draft_id, $actor_id);
        }

        return $workspace;
    }

    /**
     * @param workspace $workspace
     * @param int|null $actor_id
     *
     * @return void
     */
    public static function delete_workspace(workspace $workspace, ?int $actor_id = null): void {
        global $USER;

        if (null === $actor_id || 0 === $actor_id) {
            $actor_id = $USER->id;
        }

        $interactor = new interactor($workspace, $actor_id);
        if (!$interactor->can_delete()) {
            throw new \coding_exception("The actor cannot delete the workspace");
        }

        $query = new query($workspace->get_id());
        $cursor = $query->get_cursor();

        while (null !== $cursor) {
            $paginator = loader::get_members($query);
            $members = $paginator->get_items()->all();

            /** @var member $member */
            foreach ($members as $member) {
                $member->delete($actor_id);
            }

            $cursor = $paginator->get_next_cursor();
            if (null !== $cursor) {
                $query->set_cursor($cursor);
            }
        }

        // After deleting the member, we now need to delete all the instances.
        $manager = $workspace->get_enrolment_manager();
        $manager->delete_enrol_instances($actor_id);

        // Then finally, deleted the workspace itself.
        $workspace->delete();
    }

    /**
     * To check whether the given workspace is able to be updated to hidden status or not.
     *
     * @param workspace $workspace
     * @return bool
     */
    public static function can_workspace_update_to_hidden(workspace $workspace): bool {
        if (!static::can_workspace_update_to_private($workspace)) {
            return false;
        }

        if ($workspace->is_private()) {
            return false;
        }

        // Probably this is the last thing - which is when the workspace is hidden - meaning that user is able to
        // update to hidden.
        return true;
    }

    /**
     * To check whether the given workspace is able to be updated to private status or not.
     *
     * @param workspace $workspace
     * @return bool
     */
    public static function can_workspace_update_to_private(workspace $workspace): bool {
        // As long as workspace is a public workspace, then from either private or hidden can go up to
        // this status of access.
        return !$workspace->is_public();
    }

    /**
     * Function to update the workspace time stamp and also update the user's tracker.
     * We have to update user's tracker because:
     * + user visit workspace can happen before he/she add any content to the workspace. This means that when
     *   the workspace was updated with timestamp then timestamp will be higher than the user last access time.
     *   Meaning that it can cause the error calculation.
     *
     * @param workspace $workspace
     * @param int|null $actor_id
     * @param int|null $time
     *
     * @return void
     */
    public static function update_workspace_timestamp(workspace $workspace, ?int $actor_id = null,
                                                       ?int $time = null): void {
        global $USER;
        if (empty($actor_id)) {
            $actor_id = $USER->id;
        }

        if (empty($time)) {
            $time = time();
        }

        $workspace->touch($time);

        $interactor = new interactor($workspace, $actor_id);
        if (!$interactor->is_joined() && !$interactor->can_update()) {
            // This is to prevent the tracker being updated if user is not either any.
            debugging("User is not a member nor someone who has capability to update the workspace", DEBUG_DEVELOPER);
            return;
        }

        $tracker = new tracker($actor_id);
        $tracker->visit_workspace($workspace, $time);
    }
}