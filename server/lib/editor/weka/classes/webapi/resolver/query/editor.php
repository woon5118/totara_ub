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
namespace editor_weka\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\query_resolver;
use editor_weka\hook\find_context;

/**
 * Query resolver for editor_weka_editor
 */
final class editor implements query_resolver {
    /**
     * @param array             $args
     * @param execution_context $ec
     *
     * @return \weka_texteditor
     */
    public static function resolve(array $args, execution_context $ec): \weka_texteditor {
        global $CFG;
        require_login();

        require_once("{$CFG->dirroot}/lib/editor/weka/lib.php");
        $editor = new \weka_texteditor();

        // Sometimes the editor is being used to create a whole new instance.
        // Therefore, the instance_id might not be populated yet.
        $instance_id = null;

        if (isset($args['instance_id'])) {
            $instance_id = (int) $args['instance_id'];
        }

        $hook = new find_context($args['component'], $args['area'], $instance_id);
        $hook->execute();

        $context = $hook->get_context();
        if (null !== $context) {
            $editor->set_contextid($context->id);

            if (!$ec->has_relevant_context()) {
                $ec->set_relevant_context($context);
            }
        }

        return $editor;
    }
}