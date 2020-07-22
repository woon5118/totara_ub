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
 * @package core
 */
namespace core\webapi\resolver\type;

use core\webapi\execution_context;
use core\webapi\type_resolver;
use core_user\profile\card_display;
use core_user\profile\display_setting;

/**
 * For type 'core_user_card_display'
 */
final class user_card_display implements type_resolver {
    /**
     * @param string            $field
     * @param card_display      $source
     * @param array             $args
     * @param execution_context $ec
     *
     * @return mixed
     */
    public static function resolve(string $field, $source, array $args, execution_context $ec) {
        if (!($source instanceof card_display)) {
            throw new \coding_exception(
                "Invalid parameter of source, expecting an instance of " . card_display::class
            );
        }

        switch ($field) {
            case 'profile_picture_url':
                if (!display_setting::display_user_picture()) {
                    return null;
                }

                $resolver = $source->get_resolver();
                return $resolver->get_field_value('profileimageurl');

            case 'profile_picture_alt':
                if (!display_setting::display_user_picture()) {
                    return null;
                }

                $resolver = $source->get_resolver();
                return $resolver->get_field_value('profileimagealt');

            case 'profile_url':
                $resolver = $source->get_resolver();
                return $resolver->get_field_value('profileurl');

            case 'display_fields':
                return $source->get_card_display_fields();

            default:
                debugging("Field '{$field}' is not supported yet", DEBUG_DEVELOPER);
                return null;
        }
    }
}