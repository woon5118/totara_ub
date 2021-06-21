<?php
/*
* This file is part of Totara Learn
*
* Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
* @author David Curry <david.curry@totaralearning.com>
* @package totara_mobile
*/

namespace totara_mobile\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\middleware\require_login;
use core\webapi\mutation_resolver;
use core\webapi\resolver\has_middleware;

final class program_view implements mutation_resolver, has_middleware {

    /**
     * @inheritDoc
     */
    public static function resolve(array $args, execution_context $ec) {
        global $DB;

        // Get program module and program (provided by middleware)
        $programid = $args['program_id'] ?? null;

        if (!empty($programid)) {
            // Load program.
            $program = $DB->get_record('prog', array('id' => $programid), 'id', IGNORE_MISSING);
        }

        if (empty($programid) || empty($program)) {
            throw new \invalid_parameter_exception('programid');
        }

        $context = \context_program::instance($program->id);
        $ec->set_relevant_context($context, 0);

        // Trigger event.
        $data = array('id' => $program->id, 'other' => array('section' => 'general'));
        $event = \totara_program\event\program_viewed::create_from_data($data)->trigger();

        return true;
    }

    public static function get_middleware(): array {
        return [
            require_login::class
        ];
    }
}
