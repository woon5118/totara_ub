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
 * @author Michael Dunstan <michael.dunstan@androgogic.com>
 * @package totara_contentmarketplace
 */

namespace totara_contentmarketplace;

use totara_contentmarketplace\plugininfo\contentmarketplace;

defined('MOODLE_INTERNAL') || die();

/**
 * @package totara_contentmarketplace
 */
final class explorer {

    /** @var string */
    private $marketplace;

    /** @var contentmarketplace */
    private $plugin;

    /** @var string */
    private $mode;

    /** @var int */
    private $category;

    /**
     * explorer constructor.
     *
     * @param string $marketplace
     * @param string $mode
     * @param int $category
     */
    public function __construct($marketplace, $mode, $category) {
        $this->marketplace = $marketplace;
        $this->plugin = contentmarketplace::plugin($marketplace);
        $this->mode = $mode;
        $this->category = $category;
    }

    /**
     * @return bool|string
     */
    public function render() {
        global $OUTPUT;

        $search = $this->plugin->search();

        $data = new \stdClass();
        $data->marketplace = $this->marketplace;
        $data->category = $this->category;
        $data->sortby = array();
        foreach ($search->sort_options() as $option) {
            $data->sortby[] = array(
                    "value" => $option,
                    "title" => get_string("sort:$option", "contentmarketplace_$this->marketplace"),
                    "selected" => false
            );
        }
        $data->sortby_has_items = !empty($data->sortby);
        $data->heading = $this->get_heading();
        $data->intro = $this->get_intro();
        $data->searchplaceholder = get_string('search:placeholder', "contentmarketplace_$this->marketplace");

        $data->mode = $this->mode;
        $data->createpagepath = $this->plugin->contentmarketplace()->course_create_page();

        return $OUTPUT->render_from_template('totara_contentmarketplace/explorer', $data);
    }

    /**
     * @return string
     */
    public function get_heading() {
        if ($this->mode == 'create-course') {
            return get_string("explorecreatecourseheading", "totara_contentmarketplace");
        } else {
            return get_string("explore_totara_content_x", "totara_contentmarketplace", $this->plugin->displayname);
        }
    }

    /**
     * @return string
     */
    public function get_intro() {
        if ($this->mode == 'create-course') {
            return get_string("explorecreatecourseintro", "totara_contentmarketplace");
        } else {
            return '';
        }
    }
}
