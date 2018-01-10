<?php
/*
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package totara_userdata
 */

namespace totara_userdata\form;

defined('MOODLE_INTERNAL') || die();

class export_type_edit extends \totara_form\form {
    public function definition() {
        global $DB, $OUTPUT;

        $exporttype = (object)$this->model->get_current_data(null);

        if ($exporttype->id) {
            $saveditems = $DB->get_records_menu('totara_userdata_export_type_item',
                array('exporttypeid' => $exporttype->id), '', $DB->sql_concat_join("'-'", array('component', 'name')) . ',exportdata');
        } else {
            $saveditems = array();
        }

        $fullname = new \totara_form\form\element\text('fullname', get_string('fullname', 'totara_userdata'), PARAM_TEXT);
        $fullname->set_attributes(array('required'=> 1, 'maxlength' => 1333, 'size' => 100));
        $this->model->add($fullname);

        $idnumber = new \totara_form\form\element\text('idnumber', get_string('idnumber'), PARAM_NOTAGS);
        $idnumber->set_attributes(array('required'=> 1, 'maxlength' => 255));
        $this->model->add($idnumber);


        $description = new \totara_form\form\element\editor('description', get_string('description'));
        $description->set_attributes(array('rows'=> 4));
        $this->model->add($description);

        $options = array(
            'allowself' => get_string('exportoriginself', 'totara_userdata'),
        );
        $availablefor = new \totara_form\form\element\checkboxes('availablefor', get_string('exporttypeavailablefor', 'totara_userdata'), $options);
        $this->model->add($availablefor);

        $includefiledir = new \totara_form\form\element\checkbox('includefiledir', get_string('exportincludefiledir', 'totara_userdata'));
        $includefiledir->add_help_button('exportincludefiledir', 'totara_userdata');
        $this->model->add($includefiledir);

        $exportitemselection = new \totara_form\form\group\section('itemselection', get_string('exportitemselection', 'totara_userdata'));
        $exportitemselection->set_collapsible(false);
        $this->model->add($exportitemselection);

        $groupeditems = \totara_userdata\local\export::get_exportable_items_grouped_list();
        foreach ($groupeditems as $maincomponent => $items) {
            $options = array();
            $optionhelps = array();
            /** @var \totara_userdata\userdata\item $item this is not an instance, but it helps with autocomplete */
            foreach ($items as $item) {
                $value = $item::get_component() . '-' . $item::get_name();
                $options[$value] = $item::get_fullname();
                if (!isset($saveditems[$value])) {
                    $options[$value] .= ' <span class="label label-info">' . get_string('newitem', 'totara_userdata') . '</span>';
                }
                if ($item::help_available()) {
                    list($identifier, $component) = $item::get_fullname_string();
                    $optionhelps[] = array($value, $identifier, $component);
                }
            }
            $grouplabel = \totara_userdata\local\util::get_component_name($maincomponent);
            $group = new \totara_form\form\element\checkboxes('grp_' . $maincomponent, $grouplabel, $options);
            foreach ($optionhelps as $info) {
                list($value, $identifier, $component) = $info;
                $group->add_option_help($value, $identifier, $component);
            }
            $this->model->add($group);
        }

        if ($exporttype->id) {
            $this->model->add_action_buttons(true, get_string('update'));
        } else {
            $this->model->add_action_buttons(true, get_string('add'));
        }

        $this->model->add(new \totara_form\form\element\hidden('id', PARAM_INT));
    }

    public function validation(array $data, array $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        $existing = $DB->get_record_select('totara_userdata_export_type', "LOWER(idnumber) = LOWER(:idnumber)", array('idnumber' => $data['idnumber']));
        if ($existing and $existing->id != $data['id']) {
            $errors['idnumber'] = get_string('error');
        }

        return $errors;
    }
}
