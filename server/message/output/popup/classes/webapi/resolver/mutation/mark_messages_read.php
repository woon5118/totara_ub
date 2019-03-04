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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package message_popup
 */

namespace message_popup\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\middleware\require_authenticated_user;
use core\webapi\mutation_resolver;
use core\webapi\resolver\has_middleware;

global $CFG;
require_once($CFG->dirroot . '/message/lib.php');

class mark_messages_read implements mutation_resolver, has_middleware {
    /**
     * This updates a list of messages as being read.
     *
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        global $USER, $DB;
        $message_ids = $args['input']['message_ids'];
        if (empty($message_ids)) {
            throw new \invalid_parameter_exception('empty message id list');
        }

        // Following logic from message\externallib::mark_message_read()
        $timeread = time();

        $message_read_ids = [];
        foreach ($message_ids as $message_id) {
            $message = $DB->get_record('message', array('id' => $message_id), '*', MUST_EXIST);
            if ($message->useridto != $USER->id) {
                throw new \invalid_parameter_exception('Invalid messageid, you don\'t have permissions to mark this message as read');
            }

            // This returns an updated message id, because the message has changed tables.
            $message_read_ids[] = message_mark_message_read($message, $timeread);
        }

        return ['read_message_ids' => $message_read_ids];
    }

    /**
     * {@inheritdoc}
     */
    public static function get_middleware(): array {
        return [
            require_authenticated_user::class
        ];
    }
}