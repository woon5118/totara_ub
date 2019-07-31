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
 * @package totara_core
 */
namespace totara_core\content;

final class reader {
    /**
     * @var processor[]
     */
    private static $processors;

    /**
     * Pre-loading all the processors in the system.
     * @return void
     */
    public static function init(): void {
        if (isset(static::$processors) || !empty(static::$processors)) {
            return;
        }

        static::$processors = [];
        $classes = \core_component::get_namespace_classes('totara_core\\content', processor::class);

        foreach ($classes as $cls) {
            static::$processors[] = new $cls();
        }
    }

    /**
     * @param string      $content
     * @param string      $component
     * @param int|null    $format
     * @param int|null    $contextid
     * @param int|null    $instanceid
     * @param string|null $area
     *
     * @return void
     */
    public static function read_with_params(string $content, string $component, ?int $format = null,
                                            ?int $contextid = null, ?int $instanceid = null,
                                            ?string $area = null): void {
        $item = item::create($content, $component, $contextid, $instanceid, $area);
        static::read($item, $format);
    }

    /**
     * @param item     $item
     * @param int|null $format
     *
     * @return void
     */
    public static function read(item $item, ?int $format = null): void {
        if (null === $format) {
            $format = FORMAT_MOODLE;
        } else {
            $valids = [FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_HTML, FORMAT_JSON_EDITOR];

            if (!in_array($format, $valids)) {
                throw new \coding_exception("Invalid format value '{$format}'");
            }
        }

        static::init();

        if (empty(static::$processors)) {
            return;
        }

        foreach (static::$processors as $processor) {
            $processor->process($item, $format);
        }
    }
}