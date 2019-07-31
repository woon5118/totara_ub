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
 * @package engage_article
 */
namespace engage_article\watcher;

use editor_weka\hook\find_context;
use engage_article\totara_engage\resource\article;

/**
 * A watcher to load the context for editor weka.
 */
final class editor_weka_watcher {
    /**
     * @param find_context $hook
     * @return void
     */
    public static function load_context(find_context $hook): void {
        global $DB, $USER;

        $component = $hook->get_component();
        $article_component = article::get_resource_type();

        if ($article_component !== $component) {
            return;
        }

        $area = $hook->get_area();
        if (article::CONTENT_AREA === $area) {
            $resource_id = $hook->get_instance_id();
            if (empty($resource_id)) {
                // Resource id is empty, then most likely this is for creating new instance, therefore we
                // will try to use the user in session context.
                $context = \context_user::instance($USER->id);
                $hook->set_context($context);

                return;
            }

            $user_id = $DB->get_field('engage_resource', 'userid', ['id' => $resource_id]);

            $context = \context_user::instance($user_id);
            $hook->set_context($context);
        }
    }
}
