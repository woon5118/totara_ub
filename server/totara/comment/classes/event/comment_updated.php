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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralearning.com>
 * @package totara_comment
 */
namespace totara_comment\event;

use core\event\base;
use totara_comment\comment;
use totara_comment\entity\comment as entity;

/**
 * Class comment_updated
 * @package totara_comment\event
 */
final class comment_updated extends base {
    /**
     * @param comment   $comment
     * @param \context  $context
     * @param int|null  $user_id    The user who is responsible to trigger this event.
     *
     * @return comment_updated
     */
    public static function from_comment(comment $comment, \context $context, ?int $user_id = null): comment_updated {
        if (!$comment->exists()) {
            throw new \coding_exception("Cannot create an event from a comment that does not exist in the system");
        }

        $component = $comment->get_component();
        $area = $comment->get_area();

        $data = [
            'objectid' => $comment->get_id(),
            'userid' => $user_id,
            'context' => $context,
            'relateduserid' => $comment->get_userid(),
            'other' => [
                'area' => $area,
                'component' => $component,
                'instanceid' => $comment->get_instanceid()
            ]
        ];

        if (CONTEXT_COURSE == $context->contextlevel) {
            $data['courseid'] = $context->instanceid;
        }

        /** @var comment_updated $event */
        $event = static::create($data);
        return $event;
    }

    /**
     * @return void
     */
    protected function init(): void {
        $this->data['objecttable'] = entity::TABLE;
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['crud'] = 'u';
    }
}
