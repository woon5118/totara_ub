<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package editor_weka
 */
namespace editor_weka\webapi\resolver\type;

use core\webapi\execution_context;
use core\webapi\type_resolver;
use editor_weka\local\file_helper;

/**
 * Resolver for type editor.
 */
final class editor implements type_resolver {
    /**
     * @param string            $field
     * @param \weka_texteditor  $source
     * @param array             $args
     * @param execution_context $ec
     *
     * @return mixed|void
     */
    public static function resolve(string $field, $source, array $args, execution_context $ec) {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/editor/weka/lib.php");

        if (!($source instanceof \weka_texteditor)) {
            $cls = \weka_texteditor::class;
            throw new \coding_exception("Expecting source to be an instance of '{$cls}'");
        }

        switch ($field) {
            case 'extensions':
                $component = $args['component'] ?? 'editor_weka';
                $area = $args['area'] ?? 'learn';

                return $source->get_extensions($component, $area);

            case 'showtoolbar':
                $component = $args['component'] ?? 'editor_weka';
                $area = $args['area'] ?? 'learn';

                return $source->show_toolbar($component, $area);

            case 'files':
                $component = $args['component'] ?? null;
                $file_area = $args['file_area'] ?? null;
                $item_id  = $args['item_id'] ?? null;

                if (empty($component) || empty($file_area) || empty($item_id)) {
                    return [];
                }

                return $source->get_files($component, $file_area, $item_id);

            case 'repository_data':
                $context_id = $source->get_contextid();
                if (null === $context_id) {
                    $context_id = \context_system::instance()->id;
                }

                return file_helper::get_upload_repository($context_id);

            case 'context_id':
                $context_id = $source->get_contextid();
                if (null === $context_id) {
                    return \context_system::instance()->id;
                }

                return $context_id;

            default:
                debugging("No field '{$field}' found for type editor", DEBUG_DEVELOPER);
                return null;
        }
    }
}