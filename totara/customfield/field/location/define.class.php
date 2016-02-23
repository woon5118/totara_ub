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
 * @package totara_customfield
 */

defined('MOODLE_INTERNAL') || die();

define('GMAP_SIZE_SMALL', 'small');
define('GMAP_SIZE_MEDIUM', 'medium');
define('GMAP_SIZE_LARGE', 'large');

define('GMAP_VIEW_MAP', 'map');
define('GMAP_VIEW_SATELLITE', 'satellite');
define('GMAP_VIEW_HYBRID', 'hybrid');

define('GMAP_DISPLAY_MAP_AND_ADDRESS', 'both');
define('GMAP_DISPLAY_MAP_ONLY', 'map');
define('GMAP_DISPLAY_ADDRESS_ONLY', 'address');

require_once($CFG->dirroot . '/totara/customfield/definelib.php');
require_once($CFG->dirroot . '/totara/customfield/field/location/field.class.php');

class customfield_define_location extends customfield_define_base {

    public static function set_location_field_form_element_defaults(&$form, $defaultlocationdata, $storeddata) {
        $datatouse = $storeddata;

        if (empty($storeddata)) {
            $datatouse = $defaultlocationdata;

            if (empty($defaultlocationdata)) {
                return;
            }
        }

        $defaults = self::convert_location_json_to_object($datatouse);

        if (empty($defaults)) {
            return;
        }

        $form->setDefault($form->_customlocationfieldname . 'address', $defaults->address);
        $form->setDefault($form->_customlocationfieldname . 'latitude', $defaults->latitude);
        $form->setDefault($form->_customlocationfieldname . 'longitude', $defaults->longitude);
        $form->setDefault($form->_customlocationfieldname . 'size', $defaults->size);
        $form->setDefault($form->_customlocationfieldname . 'view', $defaults->view);
        $form->setDefault($form->_customlocationfieldname . 'display', $defaults->display);
    }

    public function define_form_specific(&$form) {
        $args = new stdClass();
        $args->fordisplay = false;

        self::define_add_js($args);
        self::add_location_field_form_elements($form);
    }

    public static function add_radio_group($groupfieldname, $grouparray, $options, $form) {
        if (empty($options)) {
            return false;
        }

        // This will not be set when creating the custom field.
        $fieldprefix = isset($form->_customlocationfieldname) ? $form->_customlocationfieldname : '';
        foreach ($options as $size => $name) {
            $grouparray[] =& $form->createElement(
                'radio',
                $fieldprefix . $groupfieldname,
                '',
                get_string('customfieldtypelocation_' . $groupfieldname . $name, 'totara_customfield'),
                $name, // Value.
                array(
                    'class' => 'radio_' . $groupfieldname
                )
            );
        }

        if (!empty($grouparray)) {
            return $grouparray;
        }
    }

    /**
     * @param $form
     * @param $fielddefinition bool This flag is used to determine if this function is being used for field or instance creation
     * @throws coding_exception
     */
    public static function add_location_field_form_elements($form, $fieldname = 'Location', $fielddefinition = true) {

        // These fields shouldn't be required during field definition, just in field instantiation.
        $stringsuffix = $fielddefinition ? 'default' : '';

        $formprefix = (!$fielddefinition) ? $form->_customlocationfieldname : '';

        $form->addElement(
            'html',
            html_writer::tag('h3', $fieldname)
        );

        /*
         * Address element
         */
        $form->addElement(
            'textarea',
            $formprefix . 'address',
            get_string('customfieldtypelocation_address'.$stringsuffix, 'totara_customfield'),
            array(
                "cols" => "50",
                "rows" => "10"
            )
        );
        $form->setType($formprefix . 'address', PARAM_TEXT);
        if (!$fielddefinition) {
            // Default address is not required, but the address is required when the field is used.
            $form->addRule($formprefix . 'address', null, 'required', null, 'client');
        }

        /*
         * Size element
         */
        $radiogroupmapsize = array();
        $sizeoptions = [GMAP_SIZE_SMALL, GMAP_SIZE_MEDIUM, GMAP_SIZE_LARGE];
        $mapsizeoptions = self::add_radio_group('size', $radiogroupmapsize, $sizeoptions, $form);
        $form->addGroup(
            $mapsizeoptions,
            $formprefix . 'size',
            get_string('customfieldtypelocation_mapsize'.$stringsuffix, 'totara_customfield'),
            '<br />',
            false
        );
        if (!$fielddefinition) {
            $form->addRule($formprefix . 'size', null, 'required', null, 'client');
        }

        /*
         * View element
         */
        $radiogroupmapview = array();
        $viewoptions = [GMAP_VIEW_MAP, GMAP_VIEW_SATELLITE, GMAP_VIEW_HYBRID];
        $mapviewoptions = self::add_radio_group('view', $radiogroupmapview, $viewoptions, $form);
        $form->addGroup(
            $mapviewoptions,
            $formprefix . 'view',
            get_string('customfieldtypelocation_mapview'.$stringsuffix, 'totara_customfield'),
            '<br />',
            false
        );
        if (!$fielddefinition) {
            $form->addRule($formprefix . 'view', null, 'required', null, 'client');
        }

        /*
         * Display element
         */
        $radiogroupdisplay = array();
        $displayoptions = [GMAP_DISPLAY_MAP_AND_ADDRESS, GMAP_DISPLAY_MAP_ONLY, GMAP_DISPLAY_ADDRESS_ONLY];
        $radiogroupdisplayoptions = self::add_radio_group('display', $radiogroupdisplay, $displayoptions, $form);
        $form->addGroup(
            $radiogroupdisplayoptions,
            $formprefix . 'display',
            get_string('customfieldtypelocation_display'.$stringsuffix, 'totara_customfield'),
            '<br />',
            false
        );
        if (!$fielddefinition) {
            $form->addRule($formprefix . 'display', null, 'required', null, 'client');
        }

        $form->addElement('html', html_writer::start_div('mapaddresslookup'));

        $mapelements = array();
        $mapelements[] = $form->createElement(
            'button',
            $formprefix . 'useaddress_btn',
            get_string('customfieldtypelocation_useaddress', 'totara_customfield'),
            array(
                'class' => 'btn_useaddress'
            )
        );

        $mapelements[] = $form->createElement('static', 'selectpersonalheader', null,
            html_writer::tag('b', get_string('customfieldtypelocation_or', 'totara_customfield')));

        $mapelements[] = $form->createElement(
            'text',
            $formprefix . 'addresslookup',
            get_string('customfieldtypelocation_addresslookup'.$stringsuffix, 'totara_customfield')
        );
        $form->setType($formprefix . 'addresslookup', PARAM_TEXT);
        $form->setDefault($formprefix . 'addresslookup', '');

        $mapelements[] = $form->createElement(
            'button',
            $formprefix . 'searchaddress_btn',
            get_string('customfieldtypelocation_searchbutton', 'totara_customfield'),
            array(
                'class' => 'btn_search'
            )
        );

        $form->addGroup($mapelements, $formprefix . 'mapelements', get_string('customfieldtypelocation_setmap', 'totara_customfield'), ' ', false);

        // Google Map element

        $form->addElement(
            'html',
            html_writer::tag('h3', 'Preview (drag marker for more specificity)')
        );

        $form->addElement('html', '<div id="' . $formprefix . 'location_map" class="location_map" ></div>');

        /*
         * Latitude element
         */
        $form->addElement(
            'hidden',
            $formprefix . 'latitude',
            "",
            array(
                'id' => $formprefix . 'latitude'
            )
        );
        $form->setType($formprefix . 'latitude', PARAM_FLOAT);

        /*
         * Longitude element
         */
        $form->addElement(
            'hidden',
            $formprefix . 'longitude',
            "",
            array(
                'id' => $formprefix . 'longitude'
            )
        );
        $form->setType($formprefix . 'longitude', PARAM_FLOAT);

        $form->addElement('html', html_writer::end_div());
    }

    public static function define_add_js($args = null) {
        global $PAGE, $CFG;

        $mapparams = '';

        if (isset($CFG->gmapsforcemaplanguage)) {
            $mapparams .= "language=" . $CFG->gmapsforcemaplanguage;
        }

        if (is_null($args)) {
            $args = new stdClass();
            $args->fordisplay = true;
        }

        $args->regionbias = $CFG->gmapsregionbias;
        $args->defaultzoomlevel = $CFG->gmapsdefaultzoomlevel;
        $args->mapparams = $mapparams;

        $PAGE->requires->js(new moodle_url('https://maps.googleapis.com/maps/api/js?' . $mapparams));
        $PAGE->requires->js_call_amd('totara_customfield/field_location', 'init', array($args));
    }

    public function define_save_preprocess($data, $old = null) {
        $data->param2 = self::prepare_location_data($data);

        return $data;
    }

    public function define_load_preprocess($data) {
        if (isset($data->param2) && !empty($data->param2)) {
            $locationdata = self::convert_location_json_to_object($data->param2);
            foreach ($locationdata as $index => $value) {
                $data->$index = $value;
            }
        }
        return $data;
    }

    public static function prepare_location_data($data, $fieldname = '') {
        $locationdata = new stdClass();

        $latitude = $fieldname . 'latitude';
        $longitude = $fieldname . 'longitude';
        $address = $fieldname . 'address';
        $size = $fieldname . 'size';
        $view = $fieldname . 'view';
        $display = $fieldname . 'display';

        $locationdata->latitude = (!empty($data->$latitude)) ? $data->$latitude : "0";
        $locationdata->longitude = (!empty($data->$longitude)) ? $data->$longitude : "0";

        $newdata = new stdClass();
        $newdata->address = (!empty($data->$address)) ? $data->$address : "";
        $newdata->size = (!empty($data->$size)) ? $data->$size : "";
        $newdata->view = (!empty($data->$view)) ? $data->$view : "";
        $newdata->display = (!empty($data->$display)) ? $data->$display : "";
        $newdata->location = $locationdata;

        return json_encode($newdata);
    }

    public static function prepare_form_location_data_for_db(&$data, $fieldname) {
        $data->{$fieldname} = self::prepare_location_data($data, $fieldname);

        unset($data->latitude);
        unset($data->longitude);
        unset($data->address);
        unset($data->size);
        unset($data->view);
        unset($data->display);
    }

    public static function prepare_db_location_data_for_form($data) {

        if (is_null($data)) {
            return null;
        }
        $data = json_decode($data);

        $newdata = new stdClass();

        $newdata->address = (isset($data->address) && !empty($data->address)) ? $data->address : "";
        $newdata->size = (isset($data->size) && !empty($data->size)) ? $data->size : "";
        $newdata->view = (isset($data->view) && !empty($data->view)) ? $data->view : "";
        $newdata->display = (isset($data->display) && !empty($data->display)) ? $data->display : "";

        $newdata->location = new stdClass();
        $newdata->location->latitude = (isset($data->location->latitude) && !empty($data->location->latitude)) ? $data->location->latitude : "0";
        $newdata->location->longitude = (isset($data->location->longitude) && !empty($data->location->longitude)) ? $data->location->longitude : "0";

        return $newdata;
    }

    public static function convert_location_json_to_object($json) {
        if (!empty($json)) {
            $newdata = new stdClass();

            $locationdata = json_decode($json);

            foreach ($locationdata as $index => $value) {
                if ($index == 'location') {
                    $newdata->latitude = $value->latitude;
                    $newdata->longitude = $value->longitude;
                } else {
                    $newdata->$index = $value;
                }
            }

            return $newdata;
        } else {
            return null;
        }

    }

    public static function render($fielddata, $extradata) {
        $output = array();

        if (empty($fielddata)) {
            return "";
        }


        $fielddata = self::prepare_db_location_data_for_form($fielddata);
        // Enable extended display (Maps etc.).
        // Disabled by default, since custom fields are typically rendered in tables or within <dd> elements.
        if (!isset($extradata['extended']) || $extradata['extended'] == false) {
            $options = new stdClass();
            $options->para = false;
            return format_text($fielddata->address, FORMAT_MOODLE, $options);
        }

        switch ($fielddata->view) {
            case GMAP_VIEW_MAP:
                $view = GMAP_VIEW_MAP;
                break;
            case GMAP_VIEW_SATELLITE:
                $view = GMAP_VIEW_SATELLITE;
                break;
            default:
                $view = GMAP_VIEW_HYBRID;
                break;
        }

        switch ($fielddata->display) {
            case GMAP_DISPLAY_MAP_ONLY:
                $displaytype = GMAP_DISPLAY_MAP_ONLY;
                break;
            case GMAP_DISPLAY_ADDRESS_ONLY:
                $displaytype = GMAP_DISPLAY_ADDRESS_ONLY;
                break;
            default:
                $displaytype = GMAP_DISPLAY_MAP_AND_ADDRESS;
                break;
        }

        if ($displaytype === GMAP_DISPLAY_ADDRESS_ONLY || $displaytype === GMAP_DISPLAY_MAP_AND_ADDRESS) {
            $output[] = html_writer::tag('span', $fielddata->address);

            if ($displaytype === GMAP_DISPLAY_ADDRESS_ONLY) {
                return implode("", $output);
            }
        }

        if ($displaytype === GMAP_DISPLAY_MAP_ONLY || $displaytype === GMAP_DISPLAY_MAP_AND_ADDRESS) {
            $output[] = html_writer::div(
                '',
                '',
                array(
                    'id' => 'location_' . $extradata['itemid'] . '_location_map',
                    'class' => 'map_' . $fielddata->size
                )
            );
        }

        $output[] = html_writer::empty_tag('input',
            ['type' => 'hidden', 'id' => 'location_' . $extradata['itemid'] . '_address', 'value' => $fielddata->address]
        );
        $output[] = html_writer::empty_tag('input',
            ['type' => 'hidden', 'id' => 'location_' . $extradata['itemid'] . '_latitude', 'value' => $fielddata->location->latitude]
        );
        $output[] = html_writer::empty_tag('input',
            ['type' => 'hidden', 'id' => 'location_' . $extradata['itemid'] . '_longitude', 'value' => $fielddata->location->longitude]
        );
        $output[] = html_writer::empty_tag('input',
            ['type' => 'hidden', 'id' => 'location_' . $extradata['itemid'] . '_room-location-view', 'value' => $view]
        );

        $output = implode("", $output);

        return $output;
    }
}
