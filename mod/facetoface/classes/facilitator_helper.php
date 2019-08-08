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
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_facetoface
*/

namespace mod_facetoface;

defined('MOODLE_INTERNAL') || die();

use mod_facetoface\customfield_area\facetofacefacilitator as facilitatorcustomfield;

/**
 * Class facilitator_helper
 */
final class facilitator_helper {
    /**
     * facilitator data
     * @param \stdClass $data to be saved includes:
     *      - int {facetoface_facilitator}.id
     *      - string {facetoface_facilitator}.name
     *      - int {facetoface_facilitator}.allowconflicts
     *      - string {facetoface_facilitator}.description
     *      - int {facetoface_facilitator}.custom
     *      - int {facetoface_facilitator}.hidden
     * @return facilitator
     */
    public static function save(\stdClass $data): facilitator {
        global $TEXTAREA_OPTIONS;

        $data->custom = $data->notcustom ? 0 : 1;

        if ($data->id) {
            $facilitator = new facilitator($data->id);
        } else {
            if (isset($data->custom) && $data->custom == 1) {
                $facilitator = facilitator::create_custom_facilitator();
            } else {
                $facilitator = new facilitator();
            }
        }
        $facilitator->set_name($data->name);
        $facilitator->set_userid($data->userid);
        $facilitator->set_allowconflicts($data->allowconflicts);
        if (empty($data->custom)) {
            $facilitator->publish();
        }

        // We need to make sure the facilitator exists before formatting the customfields and description.
        if (!$facilitator->exists()) {
            $facilitator->save();
        }

        // Export data to store in customfields and description.
        $data->id = $facilitator->get_id();

        $context = facilitatorcustomfield::get_context();
        $tblprefix = facilitatorcustomfield::get_prefix();
        $component = facilitatorcustomfield::get_component();
        $prefix = $filearea = facilitatorcustomfield::get_area_name();

        customfield_save_data($data, $prefix, $tblprefix);

        // Update description.
        $data = file_postupdate_standard_editor(
            $data,
            'description',
            $TEXTAREA_OPTIONS,
            $context,
            $component,
            $filearea,
            $facilitator->get_id()
        );
        $facilitator->set_description($data->description);
        $facilitator->save();
        // Return new/updated facilitator.
        return $facilitator;
    }

    /**
     * Sync the list of facilitators for a given seminar event date
     * @param integer $date Seminar date Id
     * @param array $facilitators List of facilitator Ids
     * @return bool
     */
    public static function sync(int $date, array $facilitators = []): bool {
        global $DB;

        if (empty($facilitators)) {
            return $DB->delete_records('facetoface_facilitator_dates', ['sessionsdateid' => $date]);
        }

        $oldfacilitators = $DB->get_fieldset_select('facetoface_facilitator_dates', 'facilitatorid', 'sessionsdateid = :date_id', ['date_id' => $date]);

        // WIPE THEM AND RECREATE if certain conditions have been met.
        if ((count($facilitators) == count($oldfacilitators)) && empty(array_diff($facilitators, $oldfacilitators))) {
            return true;
        }

        $res = $DB->delete_records('facetoface_facilitator_dates', ['sessionsdateid' => $date]);

        foreach ($facilitators as $facilitator) {
            $res &= $DB->insert_record('facetoface_facilitator_dates', (object)[
                'sessionsdateid' => (int)$date,
                'facilitatorid'  => (int)$facilitator
            ], false);
        }
        return !!$res;
    }

    /**
     * Get facilitators for specific session.
     * @param int $sessionid
     * @return string
     */
    public static function get_session_facilitatorids(int $sessionid): string {
        global $DB;

        $facilitatorid = $DB->sql_group_concat($DB->sql_cast_2char('ffd.facilitatorid'), ',');
        $sql = "
        SELECT {$facilitatorid} AS facilitatorids
          FROM {facetoface_sessions_dates} fsd
     LEFT JOIN {facetoface_facilitator_dates} ffd ON ffd.sessionsdateid = fsd.id
         WHERE fsd.id = :id";
        $ret = $DB->get_field_sql($sql, array('id' => $sessionid));
        return $ret ? $ret : '';
    }
}