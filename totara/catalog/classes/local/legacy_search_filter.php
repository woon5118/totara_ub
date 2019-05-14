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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_catalog
 */
namespace totara_catalog\local;

defined('MOODLE_INTERNAL') || die();

use totara_catalog\datasearch\legacy_search;
use totara_catalog\filter;
use totara_catalog\merge_select\search_text;

/**
 * Create the catalog like search fitler.
 */
class legacy_search_filter {

    /**
     * @return filter
     */
    public static function create(): filter {
        $datafilter = new legacy_search(
            'catalog_like',
            'catalog',
            ['id']
        );
        $datafilter->set_prefix_and_suffix('%', '%');

        $datafilter->add_source(
            'ftshigh',
            '{catalog}',
            'catalog_like',
            ['id' => 'catalog_like.id']
        );
        $datafilter->add_source(
            'ftsmedium',
            '{catalog}',
            'catalog_like',
            ['id' => 'catalog_like.id']
        );
        $datafilter->add_source(
            'ftslow',
            '{catalog}',
            'catalog_like',
            ['id' => 'catalog_like.id']
        );

        // This selector has the same key as the selector created in full_text_search_filter so
        // that these filters can share the same data passed in.
        $selector = new search_text(
            'catalog_fts',
            new \lang_string('fts_search_input', 'totara_catalog')
        );
        $selector->set_title_hidden(true);
        $selector->set_hintidentifier('fts_search_hint_legacy');
        $selector->set_hintcomponent('totara_catalog');

        return new filter(
            'catalog_like',
            filter::REGION_FTS,
            $datafilter,
            $selector
        );
    }
}