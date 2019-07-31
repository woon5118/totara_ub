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
 * @package engage_article
 */

use engage_article\totara_engage\resource\article;
use totara_engage\generator\engage_generator;
use totara_engage\share\shareable;
use totara_engage\timeview\time_view;
use core_user\totara_engage\share\recipient\user as user_recipient;
use totara_engage\share\manager as share_manager;
use totara_engage\share\share as share_model;
use totara_engage\access\access;
use totara_topic\provider\topic_provider;

final class engage_article_generator extends component_generator_base implements engage_generator {
    /**
     * @var string[]
     */
    private static $items;

    /**
     * @param array|\stdClass $parameters
     * @return article
     */
    public function create_article($parameters = []): article {
        if ($parameters instanceof \stdClass) {
            $parameters = get_object_vars($parameters);
        }

        if (!is_array($parameters)) {
            throw new \coding_exception("Invalid parameter \$parameters");
        }

        if (!isset($parameters['name'])) {
            $parameters['name'] = $this->get_random_name();
        }

        $userid = null;

        if (isset($parameters['userid'])) {
            $userid = $parameters['userid'];
            unset($parameters['userid']);
        }

        if (!isset($parameters['content'])) {
            $parameters['content'] = "This is content";
        }

        if (!isset($args['timeview'])) {
            $parameters['timeview'] = time_view::LESS_THAN_FIVE;
        }

        /** @var article $article */
        $article = article::create($parameters, $userid);
        return $article;
    }

    /**
     * @return void
     */
    public function generate_random(): void {
        $this->create_article();
    }

    /**
     * @return string
     */
    private function get_random_name(): string {
        global $CFG;
        if (null === static::$items) {
            static::$items = require_once(
                "{$CFG->dirroot}/totara/engage/resources/article/tests/fixtures/article_names.php"
            );
        }

        $nb = rand(0, (count(static::$items) - 1));
        return static::$items[$nb];
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
     * @param shareable $article
     * @param array $recipients
     * @return share_model[]
     */
    public function share_article(shareable $article, array $recipients): array {
        $context = $article->get_context();

        // Make the create method public so we can test it.
        $class = new ReflectionClass(share_manager::class);
        $method = $class->getMethod('create');
        $method->setAccessible(true);

        return $method->invokeArgs(null, [
            $article->get_id(),
            $article->get_userid(),
            article::get_resource_type(),
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

            // Can share a article.
            role_assign($role->id, $userid, $context->id);
            assign_capability('engage/article:share', $permission, $role->id, $context, true);
        }
    }

    /**
     * This is a callback from behat data generators.
     *
     * @param array $parameters
     * @return article
     */
    public function create_article_from_params(array $parameters): article {
        global $DB;

        if (!isset($parameters['name']) || !isset($parameters['content'])) {
            throw new \coding_exception("Cannot create the article without name or content");
        } else if (!isset($parameters['username'])) {
            throw new \coding_exception("Cannot create article without user");
        }

        $userid = $DB->get_field('user', 'id', ['username' => $parameters['username']], MUST_EXIST);
        $format = FORMAT_PLAIN;

        if (isset($parameters['format'])) {
            switch ($parameters['format']) {
                case 'FORMAT_JSON_EDITOR':
                    $format = FORMAT_JSON_EDITOR;
                    break;

                case 'FORMAT_HTML':
                    $format = FORMAT_HTML;
                    break;

                case 'FORMAT_PLAIN':
                default:
                    $format = FORMAT_PLAIN;
                    break;
            }
        }

        $data = [
            'name' => $parameters['name'],
            'content' => $parameters['content'],
            'format' => $format,
            'userid' => $userid
        ];

        $access = access::PRIVATE;
        if (isset($parameters['access'])) {
            $access = access::get_value($parameters['access']);
            if (access::is_public($access) && (!isset($parameters['topics'])) || empty($parameters['topics']) ) {
                throw new \coding_exception("Cannot set the article to public without the topics");
            }
        }

        $data['access'] = $access;
        $data['topics'] = [];

        if (isset($parameters['topics']) && !empty($parameters['topics'])) {
            $topics = explode(",", $parameters['topics']);
            $topics = array_map('trim', $topics);

            foreach ($topics as $topic_name) {
                $topic = topic_provider::find_by_name($topic_name);
                $data['topics'][] = $topic->get_id();
            }
        }

        return $this->create_article($data);
    }
}