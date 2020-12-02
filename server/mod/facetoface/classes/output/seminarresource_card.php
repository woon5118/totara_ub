<?php
/*
 * This file is part of Totara LMS
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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\output;

use \core\output\template;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * The seminar resource card on the seminar resource detail page.
 */
final class seminarresource_card extends template {
    /**
     * Create a template instance to display only the heading text.
     *
     * @param string $heading heading text
     * @param boolean $inactive set true to deactivate the card
     * @return self
     */
    public static function create_simple(string $heading, bool $inactive): self {
        return new static([
            'heading' => $heading,
            'simple' => true,
            'inactive' => $inactive,
        ]);
    }

    /**
     * Create a template instance to display only the heading text and details.
     *
     * @param string $heading
     * @param seminarevent_detail_section $details
     * @param boolean $inactive
     * @return self
     */
    public static function create_details(string $heading, seminarevent_detail_section $details, bool $inactive): self {
        return new static([
            'heading' => $heading,
            'detailsection' => $details->get_template_data(),
            'inactive' => $inactive,
        ]);
    }

    /**
     * Create a template instance to display the heading text, details and a command link.
     *
     * @param string $heading
     * @param string $buttonlabel
     * @param string|moodle_url $url
     * @param boolean $accent
     * @param seminarevent_detail_section|null $details
     * @param boolean $inactive
     * @param string|null $buttonhint
     * @param string|null $preview
     * @return self
     */
    public static function create(string $heading, string $buttonlabel, $url, bool $accent, ?seminarevent_detail_section $details, bool $inactive, ?string $buttonhint = null, ?string $preview = null): self {
        if ($url instanceof moodle_url) {
            $url = $url->out(false);
        }
        return new static([
            'heading' => $heading,
            'detailsection' => $details ? $details->get_template_data() : false,
            'simple' => !$details,
            'button' => [
                'text' => $buttonlabel,
                'url' => (string)$url,
                'accent' => $accent,
                'hint' => $buttonhint,
            ],
            'inactive' => $inactive,
            'preview' => $preview,
        ]);
    }
}
