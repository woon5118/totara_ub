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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\formatter\response;

use core\webapi\formatter\field\base;
use mod_perform\models\activity\element;
use mod_perform\models\activity\element_plugin;

/**
 * Generic element response formatter.
 *
 * Create the formatter with ::for_element(); this ensures the incoming response
 * data is formatted properly for a given element.
 *
 * @see \mod_perform\formatter\response\section_element_response for an example
 * on how to use it
 */
class element_response_formatter extends base {
    /**
     * Returns the specified element's response formatter classname.
     *
     * @param element $element_model the element for which to get a response
     *        formatter.
     *
     * @return string the response formatter classname. Note this could be the
     *         generic formatter classname if the element does not have a
     *         response formatter.
     */
    public static function for_element(element $element_model) {
        return self::for_plugin($element_model->get_element_plugin());
    }

    /**
     * Returns the specified element plugin's response formatter classname.
     *
     * @param element_plugin $element_plugin plugin for which to get a response
     *        formatter.
     *
     * @return string the response formatter classname. Note this could be the
     *         generic formatter classname if the element plugin does not have a
     *         response formatter.
     */
    public static function for_plugin(element_plugin $element_plugin) {
        $plugin_name = $element_plugin->get_plugin_name();
        $formatter_class = "performelement_{$plugin_name}\\formatter\\response_formatter";

        // If the plugin does not implement it's own formatter use a blank one
        if (!class_exists($formatter_class)) {
            $formatter_class = __CLASS__;
        } else if (!is_subclass_of($formatter_class, base::class)) {
            throw new \coding_exception('The response formatter must extend the base field formatter class');
        }

        return $formatter_class;
    }

}