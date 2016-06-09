<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/totara/customfield/fieldlib.php');
require_once($CFG->dirroot . '/lib/filelib.php');
require_once($CFG->dirroot . '/totara/core/totara.php');
require_once($CFG->dirroot . '/mod/facetoface/asset/asset_form.php');

/**
 * Creates a new asset, or updates an existing one depending on whether $data->id is set
 * @stdClass $data
 * @dataobject $todb
 * @return bool true
 */
function create_or_update_asset($data, $todb) {
    global $DB, $TEXTAREA_OPTIONS;
    if ($data->id == 0) {
        $data->id = $DB->insert_record('facetoface_asset', $todb);
        $todb->id = $data->id;
    } else {
        $todb->id = $data->id;
        $DB->update_record('facetoface_asset', $todb);
    }

    customfield_save_data($data, 'facetofaceasset', 'facetoface_asset');

    // Update description.
    $descriptiondata = file_postupdate_standard_editor(
      $data,
      'description',
      $TEXTAREA_OPTIONS,
      $TEXTAREA_OPTIONS['context'],
      'mod_facetoface',
      'asset',
      $data->id
    );

    $DB->set_field('facetoface_asset', 'description', $descriptiondata->description, array('id' => $data->id));
}

/**
 * Delete asset and related information
 * @param int $id
 */
function asset_delete($id) {
    global $DB;

    $sqldelparam = "
        DELETE FROM {facetoface_asset_info_data_param}
        WHERE dataid IN
            (SELECT id FROM {facetoface_asset_info_data} WHERE facetofaceassetid = :id)
        ";
    $DB->execute($sqldelparam, array('id' => $id));
    $DB->delete_records('facetoface_asset_info_data', array('facetofaceassetid' => $id));
    $DB->delete_records('facetoface_asset_dates', array('assetid' => $id));
    $DB->delete_records('facetoface_asset', array('id' => $id));
}

/**
 * Process asset edit form and call related handlers
 * @param int $assetid
 * @param callable $successhandler function($asset) where $asset is saved instance
 * @param callable $cancelhandler
 * @param array $customdata additional form customdata
 * @return \mod_facetoface_asset_form
 */
function process_asset_form($assetid, callable $successhandler, callable $cancelhandler = null, array $customdata = array()) {
    global $DB, $TEXTAREA_OPTIONS, $USER;

    if (empty($customdata['userid'])) {
        $userid = $USER->id;
    } else {
        $userid = $customdata['userid'];
    }

    if ($assetid == 0) {
        $asset = new stdClass();
        $asset->id = 0;
        $asset->description = '';
        $asset->status = 1;
        $asset->custom=0;
        if (!empty($customdata['custom'])) {
            $asset->custom=1;
        }
    } else {
        $asset = $DB->get_record('facetoface_asset', array('id' => $assetid), '*', MUST_EXIST);
        customfield_load_data($asset, 'facetofaceasset', 'facetoface_asset');
    }

    $asset->descriptionformat = FORMAT_HTML;
    $asset = file_prepare_standard_editor($asset, 'description', $TEXTAREA_OPTIONS, $TEXTAREA_OPTIONS['context'], 'mod_facetoface', 'asset', $asset->id);

    $customdata['asset'] = $asset;
    $customdata['editoroptions'] = $TEXTAREA_OPTIONS;
    if (empty($asset->id)) {
        // This kills the auto-save for when creating new assets. We do this as the same description
        // will keep coming up if creating several assets in a row.
        $customdata['editorattributes'] = array('id' => rand());
    } else {
        $customdata['editorattributes'] = array('id' => $asset->id);
    }

    $form = new mod_facetoface_asset_form(null, $customdata, 'post', '', array('class' => 'dialog-nobind'));
    $form->set_data($asset);

    if ($form->is_cancelled()) {
        if (is_callable($cancelhandler)) {
            $cancelhandler();
        }
    }

    if ($data = $form->get_data()) {
        $todb = new stdClass();
        $todb->name = $data->name;
        $todb->allowconflicts = empty($data->allowconflicts) ? 0 : 1;
        if ($data->custom && empty($data->notcustom)) {
            $todb->custom = 1;
        } else {
            $todb->custom = 0;
        }
        $new = false;
        if (empty($data->id)) {
            $todb->timecreated = time();
            $todb->usercreated = $USER->id;
        } else {
            $todb->timemodified = time();
            $todb->usermodified = $USER->id;
            $new = true;
        }

        create_or_update_asset($data, $todb);
        $successhandler($todb);
    }
    return $form;
}