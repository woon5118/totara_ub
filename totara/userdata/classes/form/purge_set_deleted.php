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

use totara_userdata\userdata\manager;
use totara_userdata\userdata\target_user;

defined('MOODLE_INTERNAL') || die();

class purge_set_deleted extends \totara_form\form {
    public function definition() {
        global $DB, $PAGE;
        $currentdata = (object)$this->model->get_current_data(null);

        $user = $DB->get_record('user', array('id' => $currentdata->id));

        /** @var \totara_userdata_renderer $renderer */
        $renderer = $PAGE->get_renderer('totara_userdata');
        $this->model->add(new \totara_form\form\element\static_html('staticidcard', '', $renderer->user_id_card($user, true)));

        $options = manager::get_purge_types(target_user::STATUS_DELETED, 'deleted', $currentdata->deletedpurgetypeid);
        if ($deleteddefault = get_config('totara_userdata', 'defaultdeletedpurgetypeid')) {
            $none = get_string('purgeautodefault', 'totara_userdata', $options[$deleteddefault]);
        } else {
            $none = get_string('none');
        }
        $options = array('' => $none) + $options;
        $deletedpurgetypeid = new \totara_form\form\element\select('deletedpurgetypeid', get_string('purgeorigindeleted', 'totara_userdata'), $options);
        $this->model->add($deletedpurgetypeid);

        $this->model->add_action_buttons(true, get_string('update'));

        $this->model->add(new \totara_form\form\element\hidden('id', PARAM_INT));
    }
}