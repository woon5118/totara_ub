<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package editor_weka
 */

$editor = [
    'phpunit' => [
        'includeextensions' => [
            '\editor_weka\extension\attachment'
        ],
    ],

    'behat' => [
        'includeextensions' => [
            '\editor_weka\extension\attachment',
            '\editor_weka\extension\emoji',
            '\editor_weka\extension\hashtag',
            '\editor_weka\extension\list_extension',
            '\editor_weka\extension\mention',
            '\editor_weka\extension\media'
        ],
    ],

    'learn' => [
        'includeextensions' => [
            '\editor_weka\extension\attachment',
            '\editor_weka\extension\emoji',
            '\editor_weka\extension\hashtag',
            '\editor_weka\extension\list_extension',
            '\editor_weka\extension\mention',
            '\editor_weka\extension\media'
        ],
    ],

    'default' => [
        'includeextensions' => [
            '\editor_weka\extension\attachment',
            '\editor_weka\extension\emoji',
            '\editor_weka\extension\hashtag',
            '\editor_weka\extension\list_extension',
            '\editor_weka\extension\mention',
            '\editor_weka\extension\media'
        ],
    ]
];
