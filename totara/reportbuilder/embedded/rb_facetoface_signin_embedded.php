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
 * @author Lee Campbell <lee@learningpool.com>
 * @package totara_reportbuilder
 */

class rb_facetoface_signin_embedded extends rb_base_embedded {

    public $url, $source, $fullname, $filters, $columns;
    public $contentmode, $contentsettings, $embeddedparams;
    public $hidden, $accessmode, $accesssettings, $shortname;

    public function __construct($data) {
        $this->url = '/mod/facetoface/signinsheet.php';
        $this->source = 'facetoface_signin';
        $this->shortname = 'facetoface_signin';
        $this->fullname = get_string('signinsheetreport', 'mod_facetoface');
        $this->columns = array(
            array(
                'type' => 'user',
                'value' => 'namelink',
                'heading' => get_string('name', 'rb_source_user'),
            ),
            array(
                'type' => 'facetoface_signup',
                'value' => 'note',
                'heading' => get_string('usernote', 'mod_facetoface')
            ),
            array(
                'type' => 'user_signups',
                'value' => 'signature',
                'heading' => get_string('signature', 'mod_facetoface')
            )
        );

        $this->filters = array();

        // No restrictions.
        $this->contentmode = REPORT_BUILDER_CONTENT_MODE_NONE;

        $sessionid = array_key_exists('sessionid', $data) ? $data['sessionid'] : null;
        if ($sessionid != null) {
            $this->embeddedparams['sessionid'] = $sessionid;
        }

        if (isset($data['hasbooked'])) {
            $this->embeddedparams['hasbooked'] = $data['hasbooked'];
        }

        parent::__construct();
    }

    /**
     * Clarify if current embedded report support global report restrictions.
     * Override to true for reports that support GRR
     * @return boolean
     */
    public function embedded_global_restrictions_supported() {
        return true;
    }

    /**
     * Check if the user is capable of accessing this report.
     * We use $reportfor instead of $USER->id and $report->get_param_value() instead of getting params
     * some other way so that the embedded report will be compatible with the scheduler (in the future).
     *
     * @param int $reportfor userid of the user that this report is being generated for
     * @param reportbuilder $report the report object - can use get_param_value to get params
     * @return boolean true if the user can access this report
     */
    public function is_capable($reportfor, $report) {
        $facetofaceid = $report->get_param_value('facetofaceid');

        if ($facetofaceid) {
            $cm = get_coursemodule_from_instance('facetoface', $facetofaceid);

            // Users can only view this report if they have the viewinterestreport capability for this context.
            return (has_capability('mod/facetoface:exportsessionsigninsheet', context_module::instance($cm->id), $reportfor));
        } else {
            return true;
        }
    }
}
