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
     * Set the relationship.
     *
     * @param relationship $relationship
     * @return boolean
     */
    public function set_relationship(relationship $relationship): bool {
        $resolver_idnumber = $relationship->idnumber;
        if (empty($resolver_idnumber)) {
            $this->lang_key_prefix = null;
            return false;
        }
        $this->lang_key_prefix = 'template_' .  $this->class_key . '_' . $resolver_idnumber . '_';
        return true;
    }

    /**
     * Get the class key of the current instance.
     *
     * @return string
     * @internal Do not use this function in production code!!
     */
    public function get_class_key(): string {
        return $this->class_key;
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
     * @param placeholder $placeholders
     * @return lang_string
     * @throws coding_exception
     */
    public function get_subject_lang_string(placeholder $placeholders): lang_string {
        return new lang_string($this->get_subject_lang_key(), 'mod_perform', $placeholders);
    }

    /**
     * @param placeholder $placeholders
     * @return lang_string
     * @throws coding_exception
     */
    public function get_body_lang_string(placeholder $placeholders): lang_string {
        return new lang_string($this->get_body_lang_key(), 'mod_perform', $placeholders);
    }

    /**
     * @return bool
     * @throws coding_exception
     */

    public function is_reminder(): bool {
        return factory::create_loader()->is_reminder($this->class_key);
    }

    /**
     * @param placeholder $placeholders
     * @return message
     * @throws coding_exception
     */
    public function compose(placeholder $placeholders): message {
        $subject = $this->get_subject_lang_string($placeholders)->out();
        $html = text_to_html($this->get_body_lang_string($placeholders)->out());
        $text = format_text_email($html, FORMAT_HTML);
        $message = new message();
        $message->subject = $subject;
        $message->fullmessage = $text;
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = $html;
        $message->smallmessage = $text;
        return $message;
    }
}
