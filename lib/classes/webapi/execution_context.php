<?php
/*
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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package core
 */

namespace core\webapi;

/**
 * GraphQL execution context.
 */
class execution_context {
    /** @var string */
    private $type;

    /** @var string|null */
    private $operationname;

    /** @var \GraphQL\Type\Definition\ResolveInfo|null */
    private $resolveinfo;

    /**
     * Constructor.
     *
     * @param string $type type of end point 'ajax', 'external', 'mobile', 'dev', etc.
     * @param string|null $operationname the name of query or mutation
     */
    protected function __construct(string $type, ?string $operationname) {
        $this->type = $type;
        if ($type !== 'dev' and !$operationname) {
            throw new \coding_exception('Persisted operations must be used outside of development mode');
        }
        $this->operationname = $operationname;
    }

    /**
     * Factory method for creation of execution context.
     *
     * @param string $type  type of end point 'ajax', 'external', 'mobile', 'dev', etc.
     * @param string|null $operationname the name of query or mutation
     * @return execution_context
     */
    final public static function create(string $type, ?string $operationname) {
        // NOTE: the main purpose of this method is to allow us to introduce subclasses for different types
        //       without breaking BC.
        return new self($type, $operationname);
    }

    /**
     * @internal
     * @param \GraphQL\Type\Definition\ResolveInfo|null $info
     */
    final public function set_resolve_info(?\GraphQL\Type\Definition\ResolveInfo $info) {
        $this->resolveinfo = $info;
    }

    /**
     * Returns advanced information for the current resolve step.
     *
     * @return \GraphQL\Type\Definition\ResolveInfo|null
     */
    final public function get_resolve_info() {
        return $this->resolveinfo;
    }

    /**
     * Returns the type of Web API entry point.
     *
     * @return string|null
     */
    final public function get_type() {
        return $this->type;
    }

    /**
     * Returns persisted query/mutation name.
     *
     * @return string|null
     */
    final public function get_operationname() {
        return $this->operationname;
    }

    // === Utility functions for resolvers ===

    /**
     * Format timestamp for core_date scalar fields using current user timezone.
     *
     * @param int|null $timestamp unix timestamp
     * @param array $args field arguments
     * @return string|null
     */
    public function format_core_date(?int $timestamp, array $args) {
        if (!$timestamp) {
            return null;
        }
        $format = empty($args['format']) ? 'TIMESTAMP' : $args['format'];
        if ($format === 'TIMESTAMP') {
            return (string)$timestamp;
        }
        $date = new \DateTime('@' . $timestamp);
        $date->setTimezone(\core_date::get_user_timezone_object());

        if ($format === 'ISO8601') {
            return $date->format(\DateTime::ISO8601);
        }

        return userdate($timestamp, get_string('strftime' . strtolower($format), 'langconfig'));
    }

    /**
     * Format text to HTML and link files to pluginfile.php script if all options specified.
     *
     * @param string $text
     * @param string $format
     * @param array $options - includes 'context', 'component' and 'filearea' for pluginfile.php relinking
     * @return string
     */
    public function format_text(?string $text, $format = FORMAT_HTML, array $options = []) {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        if ($text === null) {
            return null;
        }
        if (!empty($options['context']) and !empty($options['component']) and !empty($options['filearea'])) {
            $itemid = isset($options['itemid']) ? $options['itemid'] : null;
            $text = file_rewrite_pluginfile_urls($text, 'pluginfile.php', $options['context']->id, $options['component'], $options['filearea'], $itemid);
        }
        return format_text($text, $format, $options);
    }
}