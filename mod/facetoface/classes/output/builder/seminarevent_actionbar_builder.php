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


class seminarevent_actionbar_builder {
    private $id;
    private $align = '';
    private $commandlinks = [];

    /**
     * @param string $id part of element id
     */
    public function __construct(string $id) {
        $this->id = $id;
    }

    /**
     * Add a button.
     *
     * @param string $name
     * @param string|\moodle_url $url
     * @param string $text label text
     * @param bool $primary true to accent the element
     * @return \mod_facetoface\output\builder\seminarevent_actionbar_builder
     */
    public function add_commandlink(string $name, $url, string $text, $primary = false) : seminarevent_actionbar_builder {
        if ($url instanceof \moodle_url) {
            $url = $url->out();
        }
        $this->commandlinks[$name] = [
            'name' => $name,
            'href' => $url,
            'text' => $text,
            'primary' => $primary
        ];
        return $this;
    }

    /**
     * Set the alignment of buttons.
     *
     * @param string $align one of far, near or center
     * @return \mod_facetoface\output\builder\seminarevent_actionbar_builder
     */
    public function set_align(string $align) : seminarevent_actionbar_builder {
        $this->align = $align;
        return $this;
    }

    /**
     * Create a seminarevent_actionbar object.
     *
     * @return \mod_facetoface\output\seminarevent_actionbar
     */
    public function build() : \mod_facetoface\output\seminarevent_actionbar {
        return new \mod_facetoface\output\seminarevent_actionbar(
            [
                'id' => $this->id,
                'align' => $this->align ?: 'near',
                'commandlinks' => array_values($this->commandlinks)
            ]
        );
    }
}
