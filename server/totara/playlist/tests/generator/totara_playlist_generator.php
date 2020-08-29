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

use totara_engage\rating\rating_manager;
use totara_engage\share\shareable;
use totara_playlist\playlist;
use totara_engage\access\access;
use totara_engage\generator\engage_generator;
use totara_engage\share\manager as share_manager;
use totara_engage\share\share as share_model;
use totara_engage\share\recipient\recipient;
use core_user\totara_engage\share\recipient\user as user_recipient;
use totara_topic\provider\topic_provider;

final class totara_playlist_generator extends component_generator_base implements engage_generator {
    /**
     * @param array|\stdClass $parameters
     * @return playlist
     */
    public function create_playlist($parameters = []): playlist {
        if ($parameters instanceof \stdClass) {
            $parameters = get_object_vars($parameters);
        }

        if (!is_array($parameters)) {
            throw new \coding_exception("Invalid argument \$parameters");
        }

        if (isset($parameters['name'])) {
            $name = $parameters['name'];
        } else {
            $name = $this->get_random_name();
        }

        $access = access::PRIVATE;
        if (isset($parameters['access'])) {
            $access = $parameters['access'];
        }

        $contextid = null;
        if (isset($parameters['contextid'])) {
            $contextid = $parameters['contextid'];
        }

        $userid = null;
        if (isset($parameters['userid'])) {
            $userid = $parameters['userid'];
        }

        $summary_format = $parameters['summaryformat'] ?? null;

        $summary = null;
        if (isset($parameters['summary'])) {
            if ($summary_format === null || $summary_format === FORMAT_JSON_EDITOR) {
                $summary_format = FORMAT_JSON_EDITOR;
                $summary = json_encode([
                    'type' => 'doc',
                    'content' => [\core\json_editor\node\paragraph::create_json_node_from_text($parameters['summary'])]
                ]);
            } else {
                $summary = $parameters['summary'];
            }
        }

        $playlist = playlist::create($name, $access, $contextid, $userid, $summary, $summary_format);

        if (isset($parameters['topics']) && !empty($parameters['topics'])) {
            $playlist->add_topics_by_ids($parameters['topics']);
        }

        return $playlist;
    }

    /**
     * @return void
     */
    public function generate_random(): void {
        $this->create_playlist();
    }

    /**
     * @return string
     */
    private function get_random_name(): string {
        $items = [
            'Security',
            'Kali linux',
            'How to Python - 102',
            'How to Python - 101',
            'How to deal with your HR',
            'How to cook Ruby ?',
            'How to programming with Python - 201',
            'Gaming industries ?',
            'How to avoid your HR ?',
            'How to REKT your machine ?',
            'God of War is awesome !'
        ];

        $nb = rand(0, (count($items) - 1));
        return $items[$nb];
    }

    /**
     * @param int $count
     * @return array
     */
    public function create_users(int $count): array {
        $users = [];
        for ($x = 1; $x <= $count; ++$x) {
            $user['firstname'] = "Some{$x}";
            $user['lastname'] = "Any{$x}";
            $users[] = $this->datagenerator->create_user($user);
        }

        return $users;
    }

    /**
     * @param array $users
     * @return array
     */
    public function create_user_recipients(array $users): array {
        $recipients = [];
        foreach ($users as $user) {
            $recipients[] = new user_recipient($user->id);
        }
        return $recipients;
    }

    /**
     * @param shareable $playlist
     * @param recipient[] $recipients
     * @return share_model[]
     */
    public function share_playlist(shareable $playlist, array $recipients): array {
        $context = $playlist->get_context();

        // Make the create method public so we can test it.
        $class = new ReflectionClass(share_manager::class);
        $method = $class->getMethod('create');
        $method->setAccessible(true);

        return $method->invokeArgs(null, [
            $playlist->get_id(),
            $playlist->get_userid(),
            playlist::get_resource_type(),
            $recipients,
            $context->id
        ]);
    }

    /**
     * @param int $permission
     * @param int $userid
     * @param context $context
     *
     * @return void
     */
    public function set_capabilities(int $permission, int $userid, context $context): void {
        $roles = get_archetype_roles('user');
        foreach ($roles as $role) {
            // Can view user full details.
            $user_context = context_user::instance($userid, MUST_EXIST);
            assign_capability('moodle/user:viewdetails', $permission, $role->id, $user_context, true);

            // Can share a playlist.
            role_assign($role->id, $userid, $context->id);
            assign_capability('totara/playlist:share', $permission, $role->id, $context, true);
        }
    }

    /**
     * Callback from behat data generator.
     *
     * @param array $parameters
     * @return playlist
     */
    public function create_playlist_from_params(array $parameters): playlist {
        global $DB;

        if (!isset($parameters['name']) || !isset($parameters['username'])) {
            throw new \coding_exception(
                "Cannot create playlist from parameters that does not have user name and the name itself"
            );
        }

        $user_id = $DB->get_field('user', 'id', ['username' => $parameters['username']]);
        $access = access::get_value($parameters['access']);

        if (access::is_public($access) && empty($parameters['topics'])) {
            throw new \coding_exception("Cannot create public playlist without the topics");
        }

        $data = [
            'userid' => $user_id,
            'access' => $access,
            'contextid' => \context_user::instance($user_id)->id,
            'name' => $parameters['name'],
            'summary' => isset($parameters['summary']) ? $parameters['summary'] : null,

            // List of topic's id.
            'topics' => []
        ];

        if (!empty($parameters['topics'])) {
            $topics = explode(",", $parameters['topics']);
            $topics = array_map('trim', $topics);

            foreach ($topics as $topic_name) {
                $topic = topic_provider::find_by_name($topic_name);
                if (null === $topic) {
                    debugging("Cannot find topic by name '{$topic_name}'", DEBUG_DEVELOPER);
                    continue;
                }

                $data['topics'][] = $topic->get_id();
            }
        }

        return $this->create_playlist($data);
    }

    /**
     * @param playlist $playlist
     * @param int $rating
     * @param int|null $user_id
     */
    public function add_rating(playlist $playlist, int $rating, ?int $user_id = null): void {
        global $USER;
        if (null == $user_id) {
            $user_id = $USER->id;
        }

        $manager = rating_manager::instance(
            $playlist->get_id(),
            'totara_playlist',
            $playlist::RATING_AREA
        );

        if (!$manager->can_rate($playlist->get_userid())) {
            throw new \coding_exception("Current user with id '{$playlist->get_userid()}' can not rate the playlist");
        }

        $manager->add($rating, $user_id);
    }
}
