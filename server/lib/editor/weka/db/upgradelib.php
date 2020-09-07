<?php
/**
 * This file is part of Totara Learn
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package editor_weka
 */
defined('MOODLE_INTERNAL') || die();

function editor_weka_add_weka_to_texteditors(): void {
    global $CFG;

    // Adding weka editor to the list of text editors.
    if (!empty($CFG->texteditors)) {
        $current_editors = explode(',', $CFG->texteditors);

        if (!in_array('weka', $current_editors)) {
            $atto_editor_key = array_search('atto', $current_editors);
            $textarea_editor_key = array_search('textarea', $current_editors);

            if (false !== $atto_editor_key) {
                // This is including the atto editor as well.
                $before_atto_editors = array_slice($current_editors, 0, $atto_editor_key + 1);
                $after_atto_editors = array_slice($current_editors, $atto_editor_key + 1, count($current_editors));

                // Adding 'weka' after atto editor.
                $new_editors = array_merge($before_atto_editors, ['weka'], $after_atto_editors);
            } else if (false !== $textarea_editor_key) {
                // This is to exclude the text area, as we would want to add the weka editor before the text area.
                $before_textarea_editors = array_slice($current_editors, 0, $textarea_editor_key);
                $after_atto_editors = array_slice($current_editors, $textarea_editor_key, count($current_editors));

                // Adding 'weka' before the textarea.
                $new_editors = array_merge($before_textarea_editors, ['weka'], $after_atto_editors);
            } else {
                // Otherwise just add it to after all.
                $new_editors = $current_editors;
                $new_editors[] = 'weka';
            }

            set_config('texteditors', implode(',', $new_editors));
        }
    }
}