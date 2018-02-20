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

/**
 * Confirmation of manual purging request.
 */
final class purge_manually_confirm extends \totara_form\form {
    /**
     * Form definition.
     *
     * @return void
     */
    public function definition() {
        global $DB, $OUTPUT, $PAGE;
        $currentdata = (object)$this->model->get_current_data(null);

        $user = $DB->get_record('user', array('id' => $currentdata->id));
        $purgetype = $DB->get_record('totara_userdata_purge_type', array('id' => $currentdata->purgetypeid), '*', MUST_EXIST);

        /** @var \totara_userdata_renderer $renderer */
        $renderer = $PAGE->get_renderer('totara_userdata');

        $this->model->add(new \totara_form\form\element\static_html('staticidcard', '', $renderer->user_id_card($user, true)));

        $targetuser = new target_user($user);
        $options = manager::get_purge_types($targetuser->status, 'manual');
        $purgetypestatic = new \totara_form\form\element\static_html('staticpurgetypeid', get_string('purgetype', 'totara_userdata'), $options[$currentdata->purgetypeid]);
        $this->model->add($purgetypestatic);
        $this->model->add(new \totara_form\form\element\hidden('purgetypeid', PARAM_INT));

        $itemsstatic = new \totara_form\form\element\static_html('itemsstatic', '', $renderer->purge_type_active_items($purgetype));
        $this->model->add($itemsstatic);

        $warning = $OUTPUT->notification(get_string('purgemanuallyconfirm', 'totara_userdata'), 'warning');
        $confirmstatic = new \totara_form\form\element\static_html('confirmstatic', '', $warning);
        $this->model->add($confirmstatic);

        $this->model->add_action_buttons(true, get_string('purgemanually', 'totara_userdata'));

        $this->model->add(new \totara_form\form\element\hidden('id', PARAM_INT));
    }
}
