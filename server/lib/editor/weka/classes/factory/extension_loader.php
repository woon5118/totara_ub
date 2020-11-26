<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package editor_weka
 */
namespace editor_weka\factory;

use editor_weka\extension\attachment;
use editor_weka\extension\hashtag;
use editor_weka\extension\mention;
use editor_weka\extension\media;
use editor_weka\extension\list_extension;
use editor_weka\extension\emoji;
use editor_weka\extension\link;
use editor_weka\extension\text;
use editor_weka\extension\ruler;

class extension_loader {
    /**
     * extension_loader constructor.
     */
    private function __construct() {
        // Prevent the construction of
    }

    /**
     * @return string[]
     */
    public static function get_all_extension_classes(): array {
        return static::get_standard_extension_classes();
    }

    /**
     * This is to return all the extension classes that are introduced as a
     * part of the weka editor.
     *
     * Note: please use {@see extension_loader::get_all_extension_classes()} to fetch all the
     * extension classes from the system. As for now we do not support the extensions to be added
     * as a sub plugin of editor weka. However, with the method above, we will have space
     * to do that in the future without reworking on the current API.
     *
     * @return string[]
     */
    final public static function get_standard_extension_classes(): array {
        return array_merge(
            self::get_minimal_required_extension_classes(),
            [
                attachment::class,
                hashtag::class,
                mention::class,
                media::class,
                list_extension::class,
                emoji::class
            ]
        );
    }

    /**
     * @return string[]
     */
    final public static function get_minimal_required_extension_classes(): array {
        return [
            link::class,
            text::class,
            ruler::class
        ];
    }
}