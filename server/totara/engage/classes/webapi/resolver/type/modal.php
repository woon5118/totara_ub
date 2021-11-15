<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_engage
 */
namespace totara_engage\webapi\resolver\type;

use core\webapi\execution_context;
use core\webapi\type_resolver;
use totara_engage\modal\modal as actual;

final class modal implements type_resolver {
    /**
     * @param string            $field
     * @param actual            $source
     * @param array             $args
     * @param execution_context $ec
     *
     * @return mixed
     */
    public static function resolve(string $field, $source, array $args, execution_context $ec) {
        if (!($source instanceof actual)) {
            throw new \coding_exception("Invalid parameter \$source");
        }

        switch ($field) {
            case 'component':
                $component = $source->get_vue_component();
                return $component->get_name();

            case 'expandable':
                return $source->is_expandable();

            case 'label':
                return $source->get_label();

            case 'id':
                return $source-> get_id();

            default:
                debugging("Invalid field '{$field}' for modal type", DEBUG_DEVELOPER);
                return null;
        }
    }
}