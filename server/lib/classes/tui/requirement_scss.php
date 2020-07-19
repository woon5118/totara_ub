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

namespace core\tui;

use moodle_url;

/**
 * Represents a SCSS bundle.
 */
class requirement_scss extends requirement {
    /**
     * {@inheritdoc}
     */
    public function get_type(): string {
        return self::TYPE_CSS;
    }

    /**
     * {@inheritdoc}
     */
    public function get_url(array $options = null): moodle_url {
        if ($this->name != 'tui_bundle.scss') {
            throw new \coding_exception('Unknown SCSS bundle');
        }
        if (empty($options['theme'])) {
            throw new \coding_exception('Theme not specified');
        }
        return $this->make_tui_scss_url($this->component, $options['theme']);
    }

    /**
     * Generate a URL to the the CSS mediator using tui_scss.
     *
     * @param string $component Totara component
     * @param string $theme
     * @return moodle_url
     */
    private function make_tui_scss_url(string $component, string $theme): moodle_url {
        global $CFG;

        $rev = theme_get_revision();
        $tui_scss_designer_mode = $rev <= -1 || !empty($CFG->tuidesignermode);
        $rtl = right_to_left();
        $legacy = \core_useragent::is_ie();

        if ($tui_scss_designer_mode) {
            $url = new moodle_url('/theme/styles_debug.php');
            $params = ['theme' => $theme, 'type' => 'tui_scss', 'subtype' => $component];
            if ($rtl) {
                $params['rtl'] = '1';
            }
            if ($legacy) {
                $params['legacy'] = '1';
            }
            $url->params($params);
        } else {
            $url = new moodle_url('/theme/styles.php');
            if (!empty($CFG->slasharguments)) {
                $slashargs = '/'.$theme.'/'.$rev.'/tui_scss/'.$component.($rtl ? '/rtl' : '').($legacy ? '/legacy' : '');
                $url->set_slashargument($slashargs, 'noparam', true);
            } else {
                $params = ['theme' => $theme, 'rev' => $rev, 'type' => 'tui_scss', 'subtype' => $component];
                if ($rtl) {
                    $params['rtl'] = '1';
                }
                if ($legacy) {
                    $params['legacy'] = '1';
                }
                $url->params($params);
            }
        }
        return $url;
    }
}
