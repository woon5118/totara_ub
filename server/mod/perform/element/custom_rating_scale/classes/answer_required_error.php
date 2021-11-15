<?php
/*
 * This file is part of Totara Perform
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
 * @author Angela Kuznetsova <Angela.Kuznetsova@totaralearning.com>
 * @package performelement_custom_rating_scale
 */

namespace performelement_custom_rating_scale;

use mod_perform\models\response\element_validation_error;

class answer_required_error extends element_validation_error {

    public const ANSWER_REQUIRED = 'ANSWER_REQUIRED';

    public function __construct() {
        $error_code = self::ANSWER_REQUIRED;
        $error_message = get_string('error:question_required', 'performelement_custom_rating_scale');

        parent::__construct($error_code, $error_message);
    }
}