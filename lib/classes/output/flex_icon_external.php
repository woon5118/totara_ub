<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
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
 * @copyright 2017 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package   core
 */

namespace core\output;

/**
 * This class is used to bypass validation on the get_flex_icons return value, which is a dynamic array.
 *
 * @author     Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      10
 */
class flex_icon_external extends external {

    public static function clean_returnvalue(\external_description $description, $response) {
        // We only want to bypass for the get_flex_icons function, we can't tell what it is from here
        // but we can check it is PARAM_RAW and that the response is an array.
        if ($description->type === 'raw' && is_array($response)) {
            // Do not validate.
            return $response;
        }
        return parent::clean_returnvalue($description, $response);
    }

}