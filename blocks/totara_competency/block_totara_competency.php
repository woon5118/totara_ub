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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package block_totara_competency
 */

use pathway_manual\models\roles;

final class block_totara_competency extends block_base {

    private const COMPONENT = 'block_totara_competency';

    /**
     * Initialise block
     */
    public function init() {
        $this->title = get_string('title', self::COMPONENT);
    }

    /**
     * Set the applicable formats for this block.
     * @return array
     */
    public function applicable_formats() {
        return ['all' => true];
    }

    /**
     * There is no configuration for this block
     *
     * @return bool
     */
    public function instance_allow_config() {
        return false;
    }

    /**
     * Set block content
     *
     * @return stdClass
     */
    public function get_content() {
        return $this->content = (object) [
            'text' => $this->render(),
        ];
    }

    /**
     * @return string
     */
    private function render(): string {
        global $OUTPUT;
        return $OUTPUT->render_from_template('block_totara_competency/content', $this->get_template_data());
    }

    /**
     * @return array
     */
    private function get_template_data(): array {
        $data = [];

        $has_staff = count(roles::get_current_user_roles_for_any()) > 0;
        if ($has_staff) {
            $data['rate_competencies_url'] = new moodle_url('/totara/competency/rate_users.php');
        }
        $data['has_staff'] = $has_staff;

        return $data;
    }

}
