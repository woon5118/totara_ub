<?php
/**
 *
 * This file is part of Totara LMS
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
 * @author David Curry <david.curry@totaralearning.com>
 * @package totara_core
 */

namespace totara_core\webapi\resolver\type;

use core\webapi\execution_context;
use totara_core\formatter\learning_item_formatter;
use totara_core\user_learning\item;
use core\format;
use core\webapi\type_resolver;
use coding_exception;

class learning_item implements type_resolver {

    /**
     * Resolve program fields
     *
     * @param string $field
     * @param mixed $program
     * @param array $args
     * @param execution_context $ec
     * @return mixed
     */
    public static function resolve(string $field, $item, array $args, execution_context $ec) {
        global $CFG;

        if (!$item instanceof item) {
             throw new \coding_exception('Only learning_item objects are accepted: ' . gettype($item));
        }

        if ($item->get_type() == 'course') {
            $classpath = 'core\webapi\resolver\type\course';
        } else {
            $classpath = $item->get_component() . '\webapi\resolver\type\\' . $item->get_type();
        }

        $format = $args['format'] ?? null;
        $context = self::get_item_context($item);
        $authfield = ($field == 'description') ? 'summary' : $field;
        if (!$classpath::authorize($authfield, $format, $context)) {
            return null;
        }

        if ($field == 'duedate') {
            if (empty($item->duedate) || $item->duedate == -1) {
                $item->duedate = null; // For consistency.
            }
        }

        if ($field == 'url_view') {
            return $item->url_view->out();
        }

        $formatter = new learning_item_formatter($item, $context);
        return $formatter->format($field, $format);
    }

    private static function get_item_context($item) {
        switch ($item->itemtype) {
            case 'course':
                return \context_course::instance($item->id);
                break;
            case 'program':
            case 'certification':
                return \context_program::instance($item->id);
                break;
        }
    }
}
