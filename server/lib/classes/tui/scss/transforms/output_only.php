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

namespace core\tui\scss\transforms;

use core\tui\scss\transform_resource;
use core\tui\scss\util;

/**
 * Filter to only emit code that results in output (inverse of {@see definitions_only}).
 * 
 * In:
 * $link-color: red; a { color: $link-color }
 *
 * Out:
 * a { color: $link-color }
 */
class output_only extends base_output_filter {
    /**
     * {@inheritdoc}
     */
    public function execute(transform_resource $resource): void {
        $tree = $resource->get_ast();
        $context = (object) ['only_output' => true, 'name' => 'output_only'];
        util::traverse($tree, $context, \Closure::fromCallable([$this, 'output_filter_visitor']));
        $resource->mark_ast_modified();
    }
}
