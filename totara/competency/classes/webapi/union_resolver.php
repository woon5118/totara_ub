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
 * @package totara_webapi
 */

// TODO: WARNING: THIS IS FROM A PR TO GO INTO INTEGRATION. IT MAY BE MERGED, IT MAY BE  CHANGED ETC.
namespace core\webapi;

interface union_resolver {

    /**
     * Union type resolver
     *
     * @param mixed $value The data for the type we want to resolve.
     * @return string The type name from *.graphqls that it resolves to.
     */
    public static function resolve_type($value);
}
