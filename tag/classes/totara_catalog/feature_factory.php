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

use totara_catalog\datasearch\all;
use totara_catalog\feature;
use totara_catalog\local\config;

class feature_factory {

    /**
     * Get tag features
     *
     * @param string $itemtype the tag item type (not objecttype!) that is relevant
     * @param string $objecttype
     * @return array
     */
    public static function get_features(string $itemtype, string $objecttype): array {
        global $CFG;

        if (empty($CFG->usetags)) {
            return [];
        }

        $areas = \core_tag_area::get_areas();
        $component = array_keys($areas[$itemtype])[0];

        if (!\core_tag_area::is_enabled($component, $itemtype)) {
            return [];
        }

        $datafilter = new all(
            'tag_featured',
            'catalog',
            ['objecttype', 'objectid'],
            'LEFT JOIN'
        );

        $tagidparamkey = 'tfe_tagid_' . $objecttype;
        $itemtypeparamkey = 'tfe_type_' . $objecttype;
        $alias = 'tfe_' . $objecttype;

        $datafilter->add_source(
            'notused',
            "(SELECT tag_instance.itemid, 1 AS featured
                      FROM {tag_instance} tag_instance
                     WHERE tag_instance.tagid = :{$tagidparamkey}
                       AND tag_instance.itemtype = :{$itemtypeparamkey})",
            $alias,
            [
                'objecttype' => "'{$objecttype}'",
                'objectid' => "{$alias}.itemid",
            ],
            "",
            [
                $tagidparamkey => config::instance()->get_value('featured_learning_value'),
                $itemtypeparamkey => $itemtype,
            ],
            [
                'featured' => 1
            ]
        );

        return [
            new feature(
                'tag',
                new \lang_string('tags', 'moodle'),
                $datafilter,
                self::get_options($itemtype)
            )
        ];
    }

    private static function get_options(string $itemtype): array {
        global $DB;

        $sql = "
            SELECT DISTINCT tag.id, tag.name
              FROM {tag_instance} tag_instance
              JOIN {tag} tag
                ON tag_instance.tagid = tag.id
             WHERE tag_instance.itemtype = :itemtype
        ";
        $params = ['itemtype' => $itemtype];

        $records = $DB->get_records_sql($sql, $params);

        $systemcontext = \context_system::instance();

        $options = [];
        foreach ($records as $record) {
            $options[$record->id] = format_string($record->name, true, ['context' => $systemcontext]);
        }

        return $options;
    }
}
