<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package core
 */

namespace core\hook;

/**
 * Course edit form definition complete hook.
 *
 * This hook is called at the end of the course edit form definition, prior to data being set.
 *
 * @package core_course\hook
 */
class renderer_standard_footer_html_complete extends \totara_core\hook\base {

    /**
     * Rendered HTML output
     * @var string
     */
    public $output;

    /**
     * Core renderer instance
     * @var \core_renderer
     */
    public $renderer;

    /**
     * Page that the renderer is assisting with
     * @var \moodle_page
     */
    public $page;

    /**
     * The edit_form_definition_complete constructor.
     *
     * @param string $output
     * @param \core_renderer $renderer
     * @param \moodle_page $page
     */
    public function __construct(string &$output, \core_renderer $renderer, \moodle_page $page) {
        $this->output = &$output;
        $this->renderer = $renderer;
        $this->page = $page;
    }
}