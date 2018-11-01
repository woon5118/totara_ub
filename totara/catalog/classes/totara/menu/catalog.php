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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package totara_catalog
 */

namespace totara_catalog\totara\menu;

defined('MOODLE_INTERNAL') || die();

use \totara_core\totara\menu\item;
use \totara_core\totara\menu\menu;

class catalog extends item {

    protected function get_default_title() {
        return get_string('catalog', 'totara_catalog');
    }

    protected function get_default_url() {
        return '/totara/catalog/index.php';
    }

    public function get_default_sortorder() {
        return 70500;
    }

    public function get_default_visibility() {
        return menu::SHOW_WHEN_REQUIRED;
    }

    protected function get_default_parent() {
        return '\totara_coursecatalog\totara\menu\findlearning';
    }

    protected function check_visibility() {
        global $CFG;
        // Only show this item when totara_catalog is activated and there are other visible items on the same level.
        // Otherwise the parent "Find Learning" item will link to the catalog.
        if ($CFG->catalogtype === 'totara' && $this->has_visible_sibling()) {
            return menu::SHOW_ALWAYS;
        }
        return menu::HIDE_ALWAYS;
    }
}
