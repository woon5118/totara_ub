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

use mod_facetoface\output\actions_in_dashboard;

/**
 * A builder class for actions_in_dashboard.
 */
final class actions_in_dashboard_builder {
    /**
     * @var array
     */
    private $viewevent = array();

    /**
     * @var array
     */
    private $icons = array();

    /**
     * @var array
     */
    private $links = array();

    /**
     * @var string
     */
    private $nonactionable = '';

    /**
     * Set the text and the url of a button.
     *
     * @param string             $text button label
     * @param string|\moodle_url $url
     * @return self
     */
    public function set_view_event_button(string $text, $url): self {
        if ($text == '') {
            $this->viewevent = [];
            return $this;
        }
        if ($url instanceof \moodle_url) {
            $url = $url->out(false);
        }
        $this->viewevent = [
            'text' => $text,
            'link' => $url
        ];
        return $this;
    }

    /**
     * Add an action icon.
     *
     * @param \pix_icon          $icon
     * @param string|\moodle_url $url
     * @return self
     */
    public function add_action_icon(\pix_icon $icon, $url): self {
        if ($url instanceof \moodle_url) {
            $url = $url->out(false);
        }
        $this->icons[] = [
            'icon' => $icon,
            'link' => $url,
        ];
        return $this;
    }

    /**
     * Add an action link.
     *
     * @param string             $text link label
     * @param string|\moodle_url $url
     * @return self
     */
    public function add_action_link(string $text, $url): self {
        if ($url instanceof \moodle_url) {
            $url = $url->out(false);
        }
        $this->links[] = [
            'text' => $text,
            'link' => $url
        ];
        return $this;
    }

    /**
     * Set the text that is visible if no buttons and no links are visible.
     *
     * @param string $text
     * @return self
     */
    public function set_non_actionable(string $text): self {
        $this->nonactionable = $text;
        return $this;
    }

    /**
     * Create an actions_in_dashboard object.
     *
     * @return actions_in_dashboard
     */
    public function build(): actions_in_dashboard {
        $data = [
            'noactions' => empty($this->viewevent) && empty($this->icons) && empty($this->links),
            'nonactionable' => $this->nonactionable,
            'viewevent' => $this->viewevent,
            'hasicons' => !empty($this->icons),
            'icons' => array_map(function ($icon) {
                global $OUTPUT;
                $iconattr = array(
                    'template' => $icon['icon']->get_template(),
                    'context' => $icon['icon']->export_for_template($OUTPUT),
                );
                return [
                    'link' => $icon['link'],
                    'icon' => $iconattr,
                ];
            }, $this->icons),
            'haslinks' => !empty($this->links),
            'links' => $this->links,
        ];
        return new actions_in_dashboard($data);
    }
}
