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
 * @package totara_core
 */

namespace totara_core\output;

/**
 * Renderable TUI component
 */
final class tui_component implements \renderable {
    /**
     * @var string Component name
     */
    protected $name;

    /**
     * @var array|null Component props
     */
    protected $props = [];

    /**
     * Create a new instance of tui_component
     *
     * @param string $name Module ID of the component to render.
     *     e.g. 'totara_core/components/Example`.
     * @param array|null $props Props to pass to the component.
     *     Props are not processed or escaped in any way, they are passed as-is
     *     to the component. Must be encodable as JSON.
     */
    public function __construct(string $name, array $props = null) {
        $this->name = $name;
        $this->props = $props;
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get component props
     *
     * @return array Mapping of prop keys to values
     */
    public function get_props(): ?array {
        return $this->props;
    }
}
