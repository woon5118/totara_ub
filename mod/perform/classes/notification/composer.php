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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\notification;

use coding_exception;
use core\message\message;
use lang_string;
use totara_core\relationship\relationship;

/**
 * The composer class. This is nothing with the PHP package manager.
 */
class composer {
    /** @var string */
    private $class_key;

    /** @var string|null */
    private $lang_key_prefix;

    /**
     * Constructor. *Do not instantiate this class directly. Use the factory class.*
     *
     * @param string $class_key
     */
    public function __construct(string $class_key) {
        $this->class_key = $class_key;
    }

    /**
     * @param relationship $relationship
     * @return boolean
     */
    public function set_relationship(relationship $relationship): bool {
        $resolvers = $relationship->get_resolvers();
        if (empty($resolvers)) {
            $this->lang_key_prefix = null;
            return false;
        }
        // ?? The first element ?? That's what relationship::get_name() does...
        $resolverclass = $resolvers[0];
        $resolvername = preg_replace('/^.*\\\\/', '', $resolverclass);
        $this->lang_key_prefix = 'template_' .  $this->class_key . '_' . $resolvername . '_';
        return true;
    }

    /**
     * @return string
     * @throws coding_exception
     */
    public function get_subject_lang_key(): string {
        if (!$this->lang_key_prefix) {
            throw new coding_exception('relationship is not set');
        }
        return $this->lang_key_prefix . 'subject';
    }

    /**
     * @return string
     * @throws coding_exception
     */
    public function get_body_lang_key(): string {
        if (!$this->lang_key_prefix) {
            throw new coding_exception('relationship is not set');
        }
        return $this->lang_key_prefix . 'body';
    }

    /**
     * @return lang_string
     * @throws coding_exception
     */
    public function get_subject_lang_string(): lang_string {
        return new lang_string($this->get_subject_lang_key(), 'mod_perform');
    }

    /**
     * @return lang_string
     * @throws coding_exception
     */
    public function get_body_lang_string(): lang_string {
        return new lang_string($this->get_body_lang_key(), 'mod_perform');
    }

    /**
     * @param relationship $relationship
     * @return message
     * @throws coding_exception
     */
    public function compose(): message {
        $subject = $this->get_subject_lang_string()->out();
        $body = $this->get_body_lang_string()->out();
        $message = new message();
        $message->subject = $subject;
        $message->fullmessage = $body;
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = text_to_html($body);
        $message->smallmessage = $body;
        return $message;
    }
}
