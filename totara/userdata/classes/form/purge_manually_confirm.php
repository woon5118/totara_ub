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
use totara_userdata\userdata\item;
use totara_userdata\userdata\target_user;
use totara_userdata\local\purge;

defined('MOODLE_INTERNAL') || die();

class purge_manually_confirm extends \totara_form\form {
    public function definition() {
        global $DB, $OUTPUT, $PAGE;
        $currentdata = (object)$this->model->get_current_data(null);

        $user = $DB->get_record('user', array('id' => $currentdata->id));
        $syscontext = \context_system::instance();

        /** @var \totara_userdata_renderer $renderer */
        $renderer = $PAGE->get_renderer('totara_userdata');
        $this->model->add(new \totara_form\form\element\static_html('staticidcard', '', $renderer->user_id_card($user, true)));

        $targetuser = new target_user($user);
        $options = manager::get_purge_types($targetuser->status, 'manual');
        $purgetypestatic = new \totara_form\form\element\static_html('staticpurgetypeid', get_string('purgetype', 'totara_userdata'), $options[$currentdata->purgetypeid]);
        $this->model->add($purgetypestatic);
        $this->model->add(new \totara_form\form\element\hidden('purgetypeid', PARAM_INT));

        // This is not pretty, maybe use renderers or something here.
        $items = $DB->get_records('totara_userdata_purge_type_item', array('purgetypeid' => $currentdata->purgetypeid, 'purgedata' => 1));
        $enabled = array();
        foreach ($items as $item) {
            $enabled[$item->component . '\\' . 'userdata' . '\\' . $item->name] = true;
        }
        $html = '';
        $prevcomponent = null;
        foreach (purge::get_purgeable_items_grouped_list($targetuser->status) as $maincomponent => $classes) {
            foreach ($classes as $class) {
                if (empty($enabled[$class])) {
                    // Not enabled, skip it.
                    continue;
                }
                /** @var item $class this is not an instance, but it helps with autocomplete */
                if (!$class::is_compatible_context_level($syscontext->contextlevel)) {
                    // Item not compatible with this level.
                    continue;
                }

                if ($prevcomponent !== $maincomponent) {
                    $prevcomponent = $maincomponent;
                    $maincomponentname = \totara_userdata\local\util::get_component_name($maincomponent);
                    $html .= $OUTPUT->heading($maincomponentname, 3);
                    $html .= '<ul>';
                }

                $html .= '<li>' . $class::get_fullname() . '</li>';
            }
        }
        if ($prevcomponent) {
            $html .= '</ul>';
        }
        $itemsstatic = new \totara_form\form\element\static_html('itemsstatic', '', $html);
        $this->model->add($itemsstatic);

        $warning = $OUTPUT->notification(get_string('purgemanuallyconfirm', 'totara_userdata'), 'warning');
        $confirmstatic = new \totara_form\form\element\static_html('confirmstatic', '', $warning);
        $this->model->add($confirmstatic);

        $this->model->add_action_buttons(true, get_string('purgemanually', 'totara_userdata'));

        $this->model->add(new \totara_form\form\element\hidden('id', PARAM_INT));
    }
}
