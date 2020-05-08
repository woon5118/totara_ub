<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTDvs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 * @author  Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\middleware;

use Closure;
use invalid_parameter_exception;
use core\webapi\middleware;
use core\webapi\resolver\payload;
use core\webapi\resolver\result;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\section;
use mod_perform\models\activity\track;

/**
 * Interceptor that uses activity related data in the incoming graphql payload
 * for authentication.
 */
class require_activity implements middleware {
    /**
     * @var Closure retriever payload->activity method that retrieves an activity
     *      using data in the specified payload.
     */
    private $retriever = null;

    /**
     * @var bool whether to populate the incoming graphql execution context's
     *      relevant context field with the activity context.
     */
    private $set_relevant_context = false;

    /**
     * Creates an object instance that validates based on an activity id in the
     * incoming payload.
     *
     * @param string $payload_keys the keys in the payload to use to extract the
     *        id from the payload. For example if the keys are "a.b.c", then the
     *        id is retrieved as $payload['a']['b']['c'].
     * @param bool $set_relevant_context if true, sets the graphql execution
     *        context's relevant context field with the activity context.
     *
     * @return require_activity the object instance.
     */
    public static function by_activity_id(
        string $payload_keys,
        bool $set_relevant_context = false
    ): require_activity {
        $retriever = function (payload $payload) use ($payload_keys): activity {
            $id = self::get_id($payload_keys, $payload);
            if (!$id) {
                throw new invalid_parameter_exception('invalid activity id');
            }

            return activity::load_by_id($id);
        };

        return new require_activity($retriever, $set_relevant_context);
    }

    /**
     * Creates an object instance that validates based on a section id in the
     * incoming payload.
     *
     * @param string $payload_keys the keys in the payload to use to extract the
     *        id from the payload. For example if the keys are "a.b.c", then the
     *        id is retrieved as $payload['a']['b']['c'].
     * @param bool $set_relevant_context if true, sets the graphql execution
     *        context's relevant context field with the activity context.
     *
     * @return require_activity the object instance.
     */
    public static function by_section_id(
        string $payload_keys,
        bool $set_relevant_context = false
    ): require_activity {
        $retriever = function (payload $payload) use ($payload_keys): activity {
            $id = self::get_id($payload_keys, $payload);
            if (!$id) {
                throw new invalid_parameter_exception('invalid section id');
            }

            return section::load_by_id($id)->activity;
        };

        return new require_activity($retriever, $set_relevant_context);
    }

    /**
     * Creates an object instance that validates based on an activity track id
     * in the incoming payload.
     *
     * @param string $payload_keys the keys in the payload to use to extract the
     *        id from the payload. For example if the keys are "a.b.c", then the
     *        id is retrieved as $payload['a']['b']['c'].
     * @param bool $set_relevant_context if true, sets the graphql execution
     *        context's relevant context field with the activity context.
     *
     * @return require_activity the object instance.
     */
    public static function by_track_id(
        string $payload_keys,
        bool $set_relevant_context = false
    ): require_activity {
        $retriever = function (payload $payload) use ($payload_keys): activity {
            $id = self::get_id($payload_keys, $payload);
            if (!$id) {
                throw new invalid_parameter_exception('invalid track id');
            }

            return track::load_by_id($id)->activity;
        };

        return new require_activity($retriever, $set_relevant_context);
    }

    /**
     * Returns an id extracted from the incoming payload.
     *
     * @param string $payload_keys the keys in the payload to use to extract the
     *        id from the payload. For example if the keys are "a.b.c", then the
     *        id is retrieved as $payload['a']['b']['c'].
     * @param payload $payload the incoming payload to parse.
     *
     * @return id the extracted ID.
     */
    private static function get_id(string $payload_keys, payload $payload): int {
        $keys = explode('.', $payload_keys);

        $initial = array_shift($keys);
        $result = $payload->get_variable($initial);

        if ($result) {
            foreach ($keys as $key) {
                $result = $result[$key] ?? null;
            }
        }

        return (int)$result;
    }

    /**
     * Default constructor.
     *
     * @param Closure $retriever payload->activity method that retrieves an activity
     *        using data in the specified payload.
     * @param bool $set_relevant_context if true, sets the graphql execution
     *        context's relevant context field with the activity context.
     */
    private function __construct(Closure $retriever, bool $set_relevant_context) {
        $this->retriever = $retriever;
        $this->set_relevant_context = $set_relevant_context;
    }

    /**
     * @inheritDoc
     */
    public function handle(payload $payload, Closure $next): result {
        $retrieve = $this->retriever;
        $activity = $retrieve($payload);

        [$course, $cm] = get_course_and_cm_from_instance($activity->id, 'perform');
        require_login($course, false, $cm, false, true);

        if ($this->set_relevant_context) {
            $context = $activity->get_context();
            $payload->get_execution_context()->set_relevant_context($context);
        }

        return $next($payload);
    }
}
