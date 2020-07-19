<?php
/**
 * This file is part of Totara Perform
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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\rb\traits;

use coding_exception;
use mod_perform\models\activity\element_plugin;
use rb_base_source;
use rb_column_option;
use rb_filter_option;
use rb_join;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/totara/reportbuilder/lib.php");

/**
 * Trait element_trait
 */
trait element_trait {
    /** @var string $element_join */
    protected $element_join = null;

    /**
     * Add element info where element is the base table.
     *
     * @throws coding_exception
     */
    protected function add_element_to_base() {
        /** @var element_trait|rb_base_source $this */
        if (isset($this->element_join)) {
            throw new coding_exception('Element info can be added only once!');
        }

        $this->element_join = 'base';

        // Add component for lookup of display functions and other stuff.
        if (!in_array('mod_perform', $this->usedcomponents, true)) {
            $this->usedcomponents[] = 'mod_perform';
        }

        $this->add_element_joins();
        $this->add_element_columns();
        $this->add_element_filters();
    }

    /**
     * Add element info where element is a joined table.
     *
     * @param rb_join $join
     * @throws coding_exception
     */
    protected function add_element(rb_join $join) {
        /** @var element_trait|rb_base_source $this */
        if (isset($this->element_join)) {
            throw new coding_exception('Element info can be added only once!');
        }

        if (!in_array($join, $this->joinlist, true)) {
            $this->joinlist[] = $join;
        }
        $this->element_join = $join->name;

        // Add component for lookup of display functions and other stuff.
        if (!in_array('mod_perform', $this->usedcomponents, true)) {
            $this->usedcomponents[] = 'mod_perform';
        }

        $this->add_element_joins();
        $this->add_element_columns();
        $this->add_element_filters();
    }

    /**
     * Add joins required for element column and filter options to report.
     */
    protected function add_element_joins() {
        /** @var element_trait|rb_base_source $this */
        $join = $this->element_join;

        // None at present.
    }

    /**
     * Add columnoptions for element to report.
     */
    protected function add_element_columns() {
        /** @var element_trait|rb_base_source $this */
        $join = $this->element_join;

        $this->columnoptions[] = new rb_column_option(
            'element',
            'title',
            get_string('element_title', 'mod_perform'),
            "{$join}.title",
            [
                'joins' => [$join],
                'dbdatatype' => 'text',
                'outputformat' => 'text',
                'displayfunc' => 'format_string'
            ]
        );

        // Element identifier is known to end users as Reporting ID.
        $this->columnoptions[] = new rb_column_option(
            'element',
            'identifier',
            get_string('element_identifier', 'mod_perform'),
            "{$join}.identifier",
            [
                'joins' => [$join],
                'dbdatatype' => 'text',
                'outputformat' => 'text',
                // TODO is this right? Not a multi-lang string but could contain chars to escape.
                'displayfunc' => 'format_string'
            ]
        );

        $this->columnoptions[] = new rb_column_option(
            'element',
            'is_required',
            get_string('element_is_required', 'mod_perform'),
            "{$join}.is_required",
            [
                'displayfunc' => 'yes_or_no',
                'dbdatatype' => 'boolean',
                'joins' => [$join],
            ]
        );
        // TODO remove or label as raw data
        $this->columnoptions[] = new rb_column_option(
            'element',
            'data',
            get_string('element_data', 'mod_perform'),
            "{$join}.data",
            [
                'joins' => [$join],
            ]
        );

        $this->columnoptions[] = new rb_column_option(
            'element',
            'type',
            get_string('element_type', 'mod_perform'),
            "{$join}.plugin_name",
            [
                'joins' => [$join],
                'displayfunc' => 'element_type',
            ]
        );
    }

    /**
     * Add filteroptions for elements to report.
     */
    protected function add_element_filters() {
        $this->filteroptions[] = new rb_filter_option(
            'element',
            'title',
            get_string('element_title', 'mod_perform'),
            'text'
        );

        $this->filteroptions[] = new rb_filter_option(
            'element',
            'identifier',
            get_string('element_identifier', 'mod_perform'),
            'text'
        );

        $this->filteroptions[] = new rb_filter_option(
            'element',
            'type',
            get_string('element_type', 'mod_perform'),
            'select',
            ['selectchoices' => $this->get_element_type_options()]
        );
    }

    /**
     * Get an array of element type options to use for filtering.
     *
     * @return string[] of [plugin_name => Display Name]
     */
    protected function get_element_type_options(): array {
        $respondable_elements = element_plugin::get_element_plugins(true, false);

        return array_map(function($element) {
            return $element->get_name();
        }, $respondable_elements);
    }
}
