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

namespace mod_facetoface\output;

defined('MOODLE_INTERNAL') || die();

class attendance_tracking_table_cell extends \html_table_cell {
    const CSS_CLASS = 'mod_facetoface__sessionlist__attendance';

    protected $class = '';
    protected $html = '';
    protected $icon = null;
    protected $url = null;
    protected $linkhtml = null;

    public function __construct() {
        $this->set_state();
    }

    /**
     * Set text of this cell.
     * Note that the text can contain html tags.
     *
     * @param string $html
     * @return \mod_facetoface\output\attendance_tracking_table_cell
     */
    public function set_text(string $html) : attendance_tracking_table_cell {
        $this->html = $html;
        $this->url = null;
        return $this;
    }

    /**
     * Set localized text of this cell.
     * Note that the text can contain html tags.
     *
     * @param string $id The key identifier for the localized string
     * @return \mod_facetoface\output\attendance_tracking_table_cell
     */
    public function set_text_l10n(string $id) : attendance_tracking_table_cell {
        $this->html = get_string($id, 'facetoface');
        $this->url = null;
        return $this;
    }

    /**
     * Set an icon of this cell.
     *
     * @param string $icon The flex icon identifier
     * @return \mod_facetoface\output\attendance_tracking_table_cell
     */
    public function set_icon(string $icon) : attendance_tracking_table_cell {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Set a localized link of this cell.
     *
     * @param string|moodle_url $url The URL
     * @param string $linkid The key identifier for the localized string
     * @return \mod_facetoface\output\attendance_tracking_table_cell
     */
    public function set_link_l10n($url, string $linkid) : attendance_tracking_table_cell {
        $this->url = $url;
        $this->linkhtml = get_string($linkid, 'facetoface');
        return $this;
    }

    /**
     * Set the state of attendance tracking.
     *
     * @param string $state The state name
     * @return \mod_facetoface\output\attendance_tracking_table_cell
     */
    public function set_state(string $state = '') : attendance_tracking_table_cell {
        if ($state) {
            $this->class = self::CSS_CLASS . '--' . $state;
        } else {
            $this->class = self::CSS_CLASS;
        }
        return $this;
    }

    /**
     * Export this cell as a data object for a template.
     *
     * @param core_renderer $output
     * @return stdClass
     */
    public function export_for_template($output) {
        global $OUTPUT;
        $this->attributes['class'] = $this->class;

        $html = '';
        if ($this->icon) {
            $html .= $OUTPUT->flex_icon(
                $this->icon, [ 'classes' => $this->class . '__icon' ]
            );
        }
        if ($this->url) {
            $linkattr = [ 'class' => $this->class . '__link' ];
            $html .= \html_writer::link($this->url, $this->linkhtml, $linkattr);
        } else {
            $html .= $this->html;
        }
        $this->text = $html;
        return parent::export_for_template($output);
    }
}