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
 * @package totara_catalog
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 */

namespace totara_catalog\totara\menu;

defined('MOODLE_INTERNAL') || die();

/**
 * New "Find learning" that replaces multiple items in old menu.
 *
 * This item is displayed only if grid catalogue is active.
 */
class findlearning extends \totara_core\totara\menu\item {

    protected function get_default_title() {
        global $CFG;
        if ($CFG->catalogtype === 'totara') {
            return get_string('menuitemfindlearning', 'totara_catalog');
        } else {
            return get_string('menuitemfindlearningdisabled', 'totara_catalog');
        }
    }

    protected function get_default_url() {
        return '/totara/catalog/index.php';
    }

    public function is_disabled() {
        global $CFG;
        return ($CFG->catalogtype !== 'totara');
    }

    public function get_default_sortorder() {
        return 33000;
    }

    protected function get_default_parent() {
        return '\totara_core\totara\menu\learn';
    }
}
