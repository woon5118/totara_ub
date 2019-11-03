<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package tassign_competency
 */

namespace tassign_competency\webapi\resolver\type;

use core\webapi\execution_context;
use core\webapi\type_resolver;
use tassign_competency\formatter;
use tassign_competency\models\assignment as assignment_model;

/**
 * Note: It is the responsibility of the query to ensure the user is permitted to see an organisation.
 */
class assignment implements type_resolver {

    /**
     * @param string $field
     * @param assignment $assignment
     * @param array $args
     * @param execution_context $ec
     * @return mixed
     */
    public static function resolve(string $field, $assignment, array $args, execution_context $ec) {
        if (!$assignment instanceof assignment_model) {
            throw new \coding_exception('Accepting only assignment models.');
        }

        $formatter = new formatter\assignment($assignment, \context_system::instance());
        return $formatter->format($field, $args['format'] ?? null);
    }

}