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
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package message_popup
 */

namespace message_popup\webapi\resolver\type;

use core\format;
use core\webapi\execution_context;
use message_popup\webapi\formatter\message_formatter;
use core\webapi\type_resolver;

class message implements type_resolver {
    public static function resolve(string $field, $popup_message, array $args, execution_context $ec) {
        global $USER;

        // Note: There's no good way of checking popup_message object since it returns a db object.
        if (!$popup_message instanceof \stdClass) {
            throw new \coding_exception('Only popup_message objects are accepted: ' . gettype($popup_message));
        }

        if (!$user_context = \context_user::instance($USER->id, IGNORE_MISSING)) {
            // If there is no matching context we have a bad object, ignore missing so we can do our own error.
            throw new \coding_exception('Only valid user objects are accepted');
        }

        $format = $args['format'] ?? format::FORMAT_HTML;
        if (!self::authorize($field, $format, $user_context)) {
            return null;
        }

        // Transform the format field from the constants to a core_format string.
        if ($field == 'fullmessageformat') {
            switch ($popup_message->fullmessageformat) {
                case FORMAT_MOODLE:
                case FORMAT_HTML:
                    return 'HTML';
                    break;
                case FORMAT_PLAIN:
                    return 'PLAIN';
                    break;
                case FORMAT_RAW:
                    return 'RAW';
                    break;
                case FORMAT_MARKDOWN:
                    return 'MARKDOWN';
                    break;
                default:
                    // Note: There is also FORMAT_WIKI but it has been deprecated since 2005.
                    throw new \coding_exception("Unrecognised fullmessageformat '{$popup_message->fullmessageformat}'" );
                    break;
            }
        }

        if ($field == 'isread') {
            $popup_message->isread = (bool) $popup_message->timeread;
        }

        $formatter = new message_formatter($popup_message, $user_context);
        $formatted = $formatter->format($field, $format);

        return $formatted;
    }

    public static function authorize(string $field, ?string $format, \context_user $context) {
        // Permission to see RAW formatted string fields
        if (in_array($field, ['fullmessagehtml']) && $format == format::FORMAT_RAW) {
            // Note: this probably isn't the ideal capability, but there isn't a simple manage one like progs/certs.
            return has_capability('moodle/site:sendmessage', $context);
        }

        return true;
    }
}
