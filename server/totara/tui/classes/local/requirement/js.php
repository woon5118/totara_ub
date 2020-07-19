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

namespace totara_tui\local\requirement;

use totara_tui\local\locator\bundle;
use totara_tui\local\requirement;

/**
 * Represents a JS bundle
 */
final class js extends requirement {

    /**
     * Create a new instance of requirement_js
     *
     * @param string $component Totara component this requirement is part of, e.g. 'mod_example'
     * @param string $name Name of the bundle (e.g. tui_bundle.js)
     */
    public function __construct(string $component) {
        parent::__construct($component, 'tui_bundle.js');
    }

    /**
     * {@inheritdoc}
     */
    public function get_type(): string {
        return self::TYPE_JS;
    }

    /**
     * {@inheritdoc}
     */
    public function get_url(array $options = null): \moodle_url {
        global $CFG;

        $jsrev = bundle::get_js_rev();
        $suffix = bundle::get_js_suffix_for_url();

        if (empty($CFG->slasharguments)) {
            return new \moodle_url('/totara/tui/javascript.php', ['rev' => $jsrev, 'component' => $this->component, 'suffix' => $suffix]);
        } else {
            $returnurl = new \moodle_url('/totara/tui/javascript.php');
            $returnurl->set_slashargument('/' . $jsrev . '/' . $suffix . '/' . $this->component);
            return $returnurl;
        }
    }

    public function required(): bool {
        return !is_null(bundle::get_js_component_bundle($this->component));
    }
}
