<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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

global $CFG;

require_once($CFG->dirroot . '/mod/facetoface/rb_sources/rb_facetoface_base_source.php');
require_once($CFG->dirroot . '/totara/customfield/field/location/define.class.php');

/**
 * Seminar Rooms
 */
class rb_source_facetoface_rooms extends rb_facetoface_base_source {

    use \mod_facetoface\rb\traits\rooms;
    use \mod_facetoface\rb\traits\deprecated_rooms_source;

    /**
     * Url of embedded report required for certain actions
     * @var string
     */
    protected $embeddedurl = '';

    /**
     * Report url params to pass through during actions
     * @var array
     */
    protected $urlparams = array();

    public function __construct(rb_global_restriction_set $globalrestrictionset = null) {

        $this->base = '{facetoface_room}';
        $this->sourcetitle = get_string('sourcetitle', 'rb_source_facetoface_rooms');
        $this->sourcesummary = get_string('sourcesummary', 'rb_source_facetoface_rooms');
        $this->sourcelabel = get_string('sourcelabel', 'rb_source_facetoface_rooms');
        $this->joinlist = $this->define_joinlist();
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->requiredcolumns = $this->define_requiredcolumns();
        $this->defaultfilters = $this->define_defaultfilters();
        $this->paramoptions = $this->define_paramoptions();
        $this->add_customfields();

        parent::__construct();
    }

    protected function define_joinlist() {
        $joinlist = array();

        $joinlist[] = new rb_join(
            'roomdates',
            'LEFT',
            '{facetoface_room_dates}',
            'roomdates.roomid = base.id',
            REPORT_BUILDER_RELATION_ONE_TO_MANY
        );

        $joinlist[] = new rb_join(
            'sessiondates',
            'LEFT',
            '{facetoface_sessions_dates}',
            'sessiondate.id = roomdates.sessionsdateid',
            REPORT_BUILDER_RELATION_ONE_TO_MANY
        );

        $joinlist[] = new rb_join(
            'assigned',
            'LEFT',
            '(SELECT roomid, COUNT(*) AS cntdates
              FROM {facetoface_room_dates} frd
              INNER JOIN {facetoface_sessions_dates} fsd ON (frd.sessionsdateid = fsd.id)
              GROUP BY roomid)',
            'assigned.roomid = base.id',
            REPORT_BUILDER_RELATION_ONE_TO_MANY
        );
        return $joinlist;
    }

    protected function define_columnoptions() {
        $columnoptions = array();

        $this->add_rooms_fields_to_columns($columnoptions, 'base', true);

        $columnoptions[] = new rb_column_option(
            'room',
            'actions',
            get_string('actions'),
            'base.id',
            array(
                'noexport' => true,
                'nosort' => true,
                'joins' => 'assigned',
                'capability' => 'mod/facetoface:managesitewiderooms',
                'displayfunc' => 'f2f_room_actions',
                'hidden' => false,
                'extrafields' => array(
                    'hidden' => 'base.hidden',
                    'cntdates' => 'assigned.cntdates',
                    'custom' => 'base.custom',
                ),
            )
        );
        return $columnoptions;
    }

    protected function define_filteroptions() {
        $filteroptions = array();

        $this->add_rooms_fields_to_filters($filteroptions, true);

        $filteroptions[] = new rb_filter_option(
            'room',
            'roomavailable',
            get_string('available', 'rb_source_facetoface_rooms'),
            'f2f_roomavailable',
            array(),
            'base.id'
        );
        return $filteroptions;
    }

    protected function define_defaultcolumns() {
        $defaultcolumns = array(
            array(
                'type' => 'room',
                'value' => 'namelink'
            ),
            array(
                'type' => 'room',
                'value' => 'description'
            ),
            array(
                'type' => 'room',
                'value' => 'published'
            ),
            array(
                'type' => 'room',
                'value' => 'visible'
            ),
            array(
                'type' => 'room',
                'value' => 'allowconflicts'
            )
        );
        return $defaultcolumns;
    }

    protected function define_defaultfilters() {
        $defaultfilters = array(
            array(
                'type' => 'room',
                'value' => 'name'
            ),
            array(
                'type' => 'room',
                'value' => 'roomavailable'
            ),
        );
        return $defaultfilters;
    }

    protected function define_paramoptions() {
        $paramoptions = array(
            new rb_param_option(
                'published',
                'base.custom'
            )
        );
        return $paramoptions;
    }

    protected function add_customfields() {
        $this->add_totara_customfield_component(
            'facetoface_room',
            'base',
            'facetofaceroomid',
            $this->joinlist,
            $this->columnoptions,
            $this->filteroptions
        );
    }

    public function post_params(reportbuilder $report) {
        $this->embeddedurl = $report->embeddedurl;
        $this->urlparams = $report->get_current_url_params();
    }

    /**
     * Get the embeddedurl
     *
     * @return string
     */
    public function get_embeddedurl() {
        return $this->embeddedurl;
    }

    /**
     * Get the url params
     *
     * @return mixed
     */
    public function get_urlparams() {
        return $this->urlparams;
    }

    public function global_restrictions_supported() {
        return true;
    }
}
