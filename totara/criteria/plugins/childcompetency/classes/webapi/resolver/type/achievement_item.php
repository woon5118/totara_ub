<?php
/*
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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_hierarchy
 */

namespace criteria_childcompetency\webapi\resolver\type;

use context_system;
use core\format;
use core\webapi\execution_context;
use core\webapi\type_resolver;
use tassign_competency\entities\competency as competency_entity;
use totara_competency\entities\competency;
use totara_competency\formatter\competency_formatter;

/**
 * Organisation hierarchy type.
 *
 * Note: It is the responsibility of the query to ensure the user is permitted to see an organisation.
 */
class achievement_item implements type_resolver {

    /**
     * Resolves fields for an organisation
     *
     * @param string $field
     * @param competency_entity $competency
     * @param array $args
     * @param execution_context $ec
     * @return mixed
     */
    public static function resolve(string $field, $competency, array $args, execution_context $ec) {
        if (!$competency instanceof competency || !$competency->relation_loaded('achievement')) {
            throw new \coding_exception('You must supply competency with achievement loaded');
        }

        switch ($field) {
            case 'competency':
                return $competency;

            case 'value':
                return $competency->achievement->value ?? null;
        }
    }
}
