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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package core
 */

namespace totara_tui\local\scss\transforms;

use totara_tui\local\scss\transform_resource;

/**
 * SCSS code transform.
 *
 * Create a new instance and call
 * scss_compiler_implementation->register_transform()
 * to register it.
 */
abstract class transform {
    /**
     * Execute the transform on the specified resource
     *
     * @param transform_resource $resource
     */
    abstract public function execute(transform_resource $resource): void;
}
