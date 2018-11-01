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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package core_tag
 * @category totara_catalog
 */

namespace core_tag\totara_catalog;

defined('MOODLE_INTERNAL') || die();

use totara_catalog\datasearch\equal;
use totara_catalog\datasearch\in_or_equal;
use totara_catalog\filter;
use totara_catalog\merge_select\multi;
use totara_catalog\merge_select\single;

class filter_factory {

    /**
     * Get tag filters
     *
     * @param string $itemtype the tag item type (not objecttype!) that is relevant
     * @param string $objecttype
     * @return array
     */
    public static function get_filters(string $itemtype, string $objecttype): array {
        global $CFG;

        if (empty($CFG->usetags)) {
            return [];
        }

        $areas = \core_tag_area::get_areas();
        $component = array_keys($areas[$itemtype])[0];

        if (!\core_tag_area::is_enabled($component, $itemtype)) {
            return [];
        }

        $filters = [];

        $optionsloader = function () use ($itemtype) {
            global $DB;

            $sql = "SELECT DISTINCT tag.id, tag.name
                      FROM {tag_instance} tag_instance
                      JOIN {tag} tag
                        ON tag_instance.tagid = tag.id
                     WHERE tag_instance.itemtype = :itemtype";

            $records = $DB->get_records_sql($sql, ['itemtype' => $itemtype]);

            $systemcontext = \context_system::instance();

            $options = [];
            foreach ($records as $record) {
                $options[$record->id] = format_string($record->name, true, ['context' => $systemcontext]);
            }

            return $options;
        };

        // The panel filter can appear in the panel region.
        $paneldatafilter = new in_or_equal(
            'tag_panel',
            'catalog',
            ['objecttype', 'objectid']
        );

        $itemtypeparamkey = 'tfip_type_' . $objecttype;
        $paneltablealias = 'tfip_' . $objecttype;

        $paneldatafilter->add_source(
            "{$paneltablealias}.tagid",
            "{tag_instance}",
            $paneltablealias,
            [
                'objectid' => $paneltablealias . '.itemid',
                'objecttype' => "'{$objecttype}'"
            ],
            "{$paneltablealias}.itemtype = :{$itemtypeparamkey}",
            [
                $itemtypeparamkey => $itemtype,
            ]
        );

        $panelselector = new multi(
            'tag_panel',
            new \lang_string('tags', 'tag')
        );
        $panelselector->add_options_loader($optionsloader);

        $filters[] = new filter(
            'tag_panel',
            filter::REGION_PANEL,
            $paneldatafilter,
            $panelselector
        );

        // The browse filter can appear in the primary region.
        $browsedatafilter = new equal(
            'tag_browse',
            'catalog',
            ['objecttype', 'objectid']
        );

        $itemtypeparamkey = 'tfib_type_' . $objecttype;
        $browsetablealias = 'tfib_' . $objecttype;

        $browsedatafilter->add_source(
            "{$browsetablealias}.tagid",
            "{tag_instance}",
            $browsetablealias,
            [
                'objectid' => $browsetablealias . '.itemid',
                'objecttype' => "'{$objecttype}'"
            ],
            "{$browsetablealias}.itemtype = :{$itemtypeparamkey}",
            [
                $itemtypeparamkey => $itemtype,
            ]
        );

        $browseselector = new single(
            'tag_browse',
            new \lang_string('tags', 'tag')
        );
        $browseselector->add_all_option();
        $browseselector->add_options_loader($optionsloader);

        $filters[] = new filter(
            'tag_browse',
            filter::REGION_BROWSE,
            $browsedatafilter,
            $browseselector
        );

        return $filters;
    }
}
