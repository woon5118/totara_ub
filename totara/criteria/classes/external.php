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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

namespace totara_criteria;

class external extends \external_api {

    /** get_definition_template */
    public static function get_definition_template_parameters() {
        return new \external_function_parameters(
            [
                'type' => new \external_value(PARAM_ALPHAEXT, 'Criterion type')
            ]
        );
    }

    public static function get_definition_template(string $type) {
        return criterion_factory::create($type)
            -> export_criterion_edit_template();
    }

    public static function get_definition_template_returns() {
        return new \external_single_structure([
            'type' => new \external_value(PARAM_ALPHAEXT, 'Criterion type'),
            'criterion_templatename' => new \external_value(PARAM_TEXT, 'Template to use to display and manage instances of this criterion'),
            'title' => new \external_value(PARAM_TEXT, 'Criterion title'),
            'singleuse' => new \external_value(PARAM_BOOL, 'Indication whether this is a single-use criterion type', VALUE_OPTIONAL),
        ]);
    }
}
