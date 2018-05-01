<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package tool_sitepolicy
 */


// TL-17142 - Include HTML editor for Site Policy statement
function tool_sitepolicy_upgrade_convert_policytext_to_html() {
    global $DB;

    $rows = $DB->get_records('tool_sitepolicy_localised_policy');
    foreach($rows as $row) {
        $updated = false;

        // Put text in <p> instead of <div> to be compatible with the editor element
        // Use a crude test for a starting <p< tag to avoid converting a record more than once
        if (!preg_match('/^<p>/', $row->policytext)) {
            $row->policytext = '<p>' . text_to_html($row->policytext, null, false, true) . '</p>';
            $updated = true;
        }
        if (!empty($row->whatsnew) && !preg_match('/^<p>/', $row->whatsnew)) {
            $row->whatsnew = '<p>' . text_to_html($row->whatsnew, null, false, true) . '</p>';
            $updated = true;
        }

        if ($updated) {
            $DB->update_record('tool_sitepolicy_localised_policy', $row);
        }
    }
}
