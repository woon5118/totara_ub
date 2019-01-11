<?php

/*
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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\output\builder;

defined('MOODLE_INTERNAL') || die();


class seminarevent_filterbar_builder {
    private $id;
    private $method;
    private $params = [];
    private $filters = [];

    /**
     * @param string $id part of form id
     * @param string $method get or post
     */
    public function __construct(string $id, string $method = 'get') {
        $this->id = $id;
        $this->method = $method;
    }

    /**
     * Add a query parameter.
     *
     * @param string $name
     * @param string|integer $value
     * @return \mod_facetoface\output\builder\seminarevent_filterbar_builder
     */
    public function add_param(string $name, $value) : seminarevent_filterbar_builder {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * Add a select menu.
     *
     * @param string $name the name attribute of an element
     * @param array $options key/value array of options
     * @param string $class part of css class
     * @param string $label label
     * @param boolean $disabled true to disable/hide an element
     * @return \mod_facetoface\output\builder\seminarevent_filterbar_builder
     */
    public function add_filter(string $name, array $options, $class = '', $label = '', $disabled = false) : seminarevent_filterbar_builder {
        $this->filters[$name] = [
            'options' => $options,
            'class' => $class,
            'label' => $label,
            'disabled' => $disabled
        ];
        return $this;
    }

    /**
     * Create a seminarevent_filterbar object.
     *
     * @return \mod_facetoface\output\seminarevent_filterbar
     */
    public function build() : \mod_facetoface\output\seminarevent_filterbar {
        $params = [];
        foreach ($this->params as $name => $value) {
            if (array_key_exists($name, $this->filters)) {
                continue;
            }
            $params[] = [
                'name' => $name,
                'value' => $value
            ];
        }

        $filters = [];
        foreach ($this->filters as $name => $opts) {
            $selectedvalue = $this->params[$name] ?? null;
            $options = [];
            foreach ($opts['options'] as $optvalue => $optname) {
                $options[] = [
                    'name' => $optname,
                    'value' => $optvalue,
                    'selected' => $optvalue === $selectedvalue
                ];
            }
            $filters[] = [
                'id' => '',
                'name' => $name,
                'class' => $opts['class'],
                'label' => $opts['label'],
                'disabled' => $opts['disabled'],
                'webkit_init_value' => $selectedvalue,
                'options' => $options
            ];
        }

        return new \mod_facetoface\output\seminarevent_filterbar(
            [
                'formid' => $this->id,
                'method' => $this->method,
                'params' => $params,
                'filters' => $filters
            ]
        );
    }
}
