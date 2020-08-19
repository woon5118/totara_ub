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
use container_workspace\workspace;
use container_workspace\local\workspace_helper;
use totara_engage\generator\engage_generator;
use container_workspace\discussion\discussion;
use core\json_editor\node\paragraph;
use container_workspace\discussion\discussion_helper;
use container_workspace\interactor\workspace\interactor as workspace_interactor;
use container_workspace\member\member;
use container_workspace\totara_engage\share\recipient\library as library_recipient;

/**
 * Generator for container workspace
 */
final class container_workspace_generator extends component_generator_base implements engage_generator {
    /**
     * @var array
     */
    private static $names;

    /**
     * @var array
     */
    private static $discussions;

    /**
     * @return void
     */
    public function generate_random(): void {
        $this->create_workspace();
    }

    /**
     * @param string|null   $name
     * @param string|null   $summary
     * @param int|null      $summary_format
     * @param int|null      $owner
     * @param bool          $is_private
     * @param bool          $is_hidden
     * @return workspace
     */
    public function create_workspace(?string $name = null, ?string $summary = null,
                                     ?int $summary_format = null, ?int $owner = null,
                                     bool $is_private = false, bool $is_hidden = false): workspace {
        global $USER;

        if (null === $name || '' === $name) {
            $name = $this->random_name();
        }

        if (null === $summary_format) {
            $summary_format = FORMAT_PLAIN;
        }

        if (null === $owner || 0 === $owner) {
            $owner = $USER->id;
        }

        return workspace_helper::create_workspace(
            $name,
            $owner,
            null,
            $summary,
            $summary_format,
            null,
            $is_private,
            $is_hidden
        );
    }

    /**
     * @param string|null   $name
     * @param string|null   $summary
     * @param string|null   $summary_format
     * @param int|null      $owner
     *
     * @return workspace
     */
    public function create_private_workspace(?string $name = null, ?string $summary = null,
                                             ?string $summary_format = null, ?int $owner = null): workspace {
        return $this->create_workspace(
            $name,
            $summary,
            $summary_format,
            $owner,
            true
        );
    }

    /**
     * @param string|null   $name
     * @param string|null   $summary
     * @param string|null   $summary_format
     * @param int|null      $owner
     *
     * @return workspace
     */
    public function create_hidden_workspace(?string $name = null, ?string $summary = null,
                                            ?string $summary_format = null, ?int $owner = null): workspace {
        return $this->create_workspace(
            $name,
            $summary,
            $summary_format,
            $owner,
            true,
            true
        );
    }

    /**
     * @return string
     */
    private function random_name(): string {
        global $CFG;

        if (!isset(static::$names)) {
            static::$names = [];
            static::$names = require(
                "{$CFG->dirroot}/container/type/workspace/tests/fixtures/workspace_names.php"
            );
        }

        $nb = rand(0, (count(static::$names) - 1));
        return static::$names[$nb];
    }

    /**
     * @return string
     */
    private function random_discussion_content(): string {
        global $CFG;

        if (!isset(static::$discussions)) {
            static::$discussions = [];
            static::$discussions = require(
                "{$CFG->dirroot}/container/type/workspace/tests/fixtures/discussions.php"
            );
        }

        $nb = rand(0, (count(static::$discussions) - 1));
        return static::$discussions[$nb];
    }

    /**
     * @param int $workspace_id
     * @param string|null $content
     * @param int|null $draft_id
     * @param int|null $content_format
     * @param int|null $actor_id
     *
     * @return discussion
     */
    public function create_discussion(int $workspace_id, ?string $content = null, ?int $draft_id = null,
                                      ?int $content_format = null, ?int $actor_id = null): discussion {
        global $USER;

        if (null === $actor_id || 0 === $actor_id) {
            $actor_id = $USER->id;
        }

        $workspace = workspace::from_id($workspace_id);
        $interactor = new workspace_interactor($workspace, $actor_id);

        if (!$interactor->is_joined()) {
            // The actor has not yet joined the workspace yet, time to join the workspace automatically so that
            // we can generate the discussion easily.
            member::join_workspace($workspace, $actor_id);
        }

        if (null === $content_format) {
            $content_format = FORMAT_PLAIN;
        }

        if (null === $content || "" === $content) {
            $content = $this->random_discussion_content();
        }

        if (FORMAT_JSON_EDITOR == $content_format) {
            // Checking if the content is an actually json content.
            json_decode($content, true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                // $content is not a json document content.
                $content = json_encode([
                    'type' => 'doc',
                    'content' => [
                        paragraph::create_json_node_from_text($content)
                    ],
                ]);
            }
        }

        return discussion_helper::create_discussion(
          $workspace,
          $content,
          $draft_id,
          $content_format,
          $actor_id
        );
    }

    /**
     * Callback from behat data generator.
     *
     * @param array $parameters
     * @return workspace
     */
    public function create_workspace_from_params(array $parameters): workspace {
        if (!isset($parameters['name']) || empty($parameters['owner'])) {
            throw new \coding_exception(
                "Workspace name, topics and owner are required"
            );
        }

        $user = core_user::get_user_by_username($parameters['owner']);

        return self::create_workspace(
            $parameters['name'],
            $parameters['summary'] ?? null,
            null,
            $user->id,
            $parameters['private'] ?? false,
            $parameters['hidden'] ?? false
        );
    }

    /**
     * @param workspace[] $workspaces
     * @return array
     */
    public function create_workspace_recipients(array $workspaces): array {
        $recipients = [];
        foreach ($workspaces as $workspace) {
            $recipients[] = new library_recipient($workspace->id);
        }
        return $recipients;
    }

    /**
     * Behat helper to add a user to a workspace
     *
     * @param array $parameters
     */
    public function create_workspace_owners(array $parameters): void {
        global $DB;

        if (empty($parameters['username']) || empty($parameters['workspace'])) {
            throw new \coding_exception('`workspace` and `username` are required');
        }

        $user = core_user::get_user_by_username($parameters['username']);
        $workspace_id = $DB->get_field('course', 'id', ['shortname' => strtolower($parameters['workspace'])]);
        $workspace = workspace::from_id($workspace_id);

        $roles = get_archetype_roles('workspaceowner');
        if (empty($roles)) {
            throw new \coding_exception("No role for archetype 'workspaceowner'");
        }
        $role = reset($roles);

        $manager = $workspace->get_enrolment_manager();
        $manager->self_enrol_user($user->id, $role->id);
    }

    /**
     * @param int $permission
     * @param int $userid
     */
    public function set_capabilities(int $permission, int $userid) {
        $roles = get_archetype_roles('user');
        foreach ($roles as $role) {
            // Can create workspace.
            $user_context = context_user::instance($userid, MUST_EXIST);
            assign_capability('container/workspace:create', $permission, $role->id, $user_context, true);
        }
    }
}