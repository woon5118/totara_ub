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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @package totara_userdata
 */

namespace totara_userdata\form;

use totara_userdata\userdata\manager;
use totara_userdata\userdata\target_user;

defined('MOODLE_INTERNAL') || die();

/**
 * Class purge_set_suspended_confirm
 *
 * Confirm the details before updating the suspended purge type for a user.
 */
class purge_set_suspended_confirm extends \totara_form\form {

    /**
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function definition() {
        global $DB, $PAGE;
        $currentdata = (object)$this->model->get_current_data(null);

        $user = $DB->get_record('user', array('id' => $currentdata->id));

        /** @var \totara_userdata_renderer $renderer */
        $renderer = $PAGE->get_renderer('totara_userdata');

        $options = manager::get_purge_types(target_user::STATUS_SUSPENDED, 'suspended', $currentdata->suspendedpurgetypeid);

        if (empty($currentdata->suspendedpurgetypeid)) {
            if ($suspendeddefault = get_config('totara_userdata', 'defaultsuspendedpurgetypeid') && $user->suspended == 0) {
                $name = get_string('purgeautodefault', 'totara_userdata', $options[$suspendeddefault]);
                // If there is a default and the item 'None' was selected, we'll list items for the default.
                $purgetype = $DB->get_record('totara_userdata_purge_type', array('id' => $suspendeddefault), '*', MUST_EXIST);
            } else {
                $name = get_string('none');
            }
        } else {
            $purgetype = $DB->get_record('totara_userdata_purge_type', array('id' => $currentdata->suspendedpurgetypeid), '*', MUST_EXIST);
            $name = $options[$currentdata->suspendedpurgetypeid];
        }

        $userdetailshtml = $renderer->heading(get_string('userdetails'), 3);
        $userdetailshtml .= '<dl class="dl-horizontal">' . $renderer->user_id_card($user, true, false);
        $userdetailshtml .= '<dt>' .get_string('purgetype', 'totara_userdata') .'</dt>';
        $userdetailshtml .= '<dd>' . $name . '</dd></dl>';

        $userdetails = new \totara_form\form\element\static_html('purgetypestatic', '', $userdetailshtml);
        $this->model->add($userdetails);

        $datatopurgehtml = $renderer->heading(get_string('purgeitemselectionsuspended', 'totara_userdata'), 3);
        if (empty($purgetype)) {
            // 'None' was selected and there must be no default purge type. Or the user is already suspended.
            $datatopurgehtml .= get_string('noadditionaldatadeleted', 'totara_userdata');
        } else {
            $datatopurgehtml .= $renderer->purge_type_active_items($purgetype);
        }

        $datatopurge = new \totara_form\form\element\static_html('datatopurge', '', $datatopurgehtml);
        $this->model->add($datatopurge);

        $this->model->add_action_buttons(true, get_string('savechanges'));

        $this->model->add(new \totara_form\form\element\hidden('id', PARAM_INT));
        $this->model->add(new \totara_form\form\element\hidden('suspendedpurgetypeid', PARAM_INT));
        $this->model->add(new \totara_form\form\element\hidden('loadconfirmform', PARAM_BOOL));
    }
}