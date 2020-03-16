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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package core
 */

namespace core\webapi\resolver\type;

use core\date_format;
use core\format;
use core\webapi\execution_context;
use core_user\access_controller;
use totara_core\formatter\field\date_field_formatter;
use totara_core\formatter\field\text_field_formatter;

class user implements \core\webapi\type_resolver {

    private const FIELDS_DB = [
        'id',                       // Type: bigint                 , default nextval (not null)
        'auth',                     // Type: character varying(20)  , default 'manual' (not null)
        'confirmed',                // Type: smallint               , default 0 (not null)
        'policyagreed',             // Type: smallint               , default 0 (not null)
        'deleted',                  // Type: smallint               , default 0 (not null)
        'suspended',                // Type: smallint               , default 0 (not null)
        'mnethostid',               // Type: bigint                 , default 0 (not null)
        'username',                 // Type: character varying(100) , default '' (not null)
        'password',                 // Type: character varying(255) , default '' (not null)
        'idnumber',                 // Type: character varying(255) , default '' (not null)
        'firstname',                // Type: character varying(100) , default '' (not null)
        'lastname',                 // Type: character varying(100) , default '' (not null)
        'email',                    // Type: character varying(100) , default '' (not null)
        'emailstop',                // Type: smallint               , default 0 (not null)
        'skype',                    // Type: character varying(50)  , default '' (not null)
        'phone1',                   // Type: character varying(20)  , default '' (not null)
        'phone2',                   // Type: character varying(20)  , default '' (not null)
        'institution',              // Type: character varying(255) , default '' (not null)
        'department',               // Type: character varying(255) , default '' (not null)
        'address',                  // Type: character varying(255) , default '' (not null)
        'city',                     // Type: character varying(120) , default '' (not null)
        'country',                  // Type: character varying(2)   , default '' (not null)
        'lang',                     // Type: character varying(30)  , default 'en' (not null)
        'calendartype',             // Type: character varying(30)  , default 'gregorian' (not null)
        'theme',                    // Type: character varying(50)  , default '' (not null)
        'timezone',                 // Type: character varying(100) , default '99' (not null)
        'firstaccess',              // Type: bigint                 , default 0 (not null)
        'lastaccess',               // Type: bigint                 , default 0 (not null)
        'lastlogin',                // Type: bigint                 , default 0 (not null)
        'currentlogin',             // Type: bigint                 , default 0 (not null)
        'lastip',                   // Type: character varying(45)  , default '' (not null)
        'secret',                   // Type: character varying(15)  , default '' (not null)
        'picture',                  // Type: bigint                 , default 0 (not null)
        'url',                      // Type: character varying(255) , default '' (not null)
        'description',              // Type: text                   , default  ()
        'descriptionformat',        // Type: smallint               , default 1 (not null)
        'mailformat',               // Type: smallint               , default 1 (not null)
        'maildigest',               // Type: smallint               , default 0 (not null)
        'maildisplay',              // Type: smallint               , default 2 (not null)
        'autosubscribe',            // Type: smallint               , default 1 (not null)
        'trackforums',              // Type: smallint               , default 0 (not null)
        'timecreated',              // Type: bigint                 , default 0 (not null)
        'timemodified',             // Type: bigint                 , default 0 (not null)
        'trustbitmask',             // Type: bigint                 , default 0 (not null)
        'imagealt',                 // Type: character varying(255) , default  ()
        'lastnamephonetic',         // Type: character varying(255) , default  ()
        'firstnamephonetic',        // Type: character varying(255) , default  ()
        'middlename',               // Type: character varying(255) , default  ()
        'alternatename',            // Type: character varying(255) , default  ()
        'totarasync',               // Type: smallint               , default 0 (not null)
    ];

    private const FIELDS_COMPUTED = [
        'fullname',
        'interests',
        'profileimagealt',
        'profileimageurl',
        'profileimageurlsmall',
    ];

    /**
     * Resolves the user fields.
     *
     * @param string $field
     * @param \stdClass $user A user record from the database
     * @param array $args
     * @param execution_context $ec
     * @return mixed|string|null
     * @throws \coding_exception If the requested field does not exist, or the current user cannot see the given user.
     */
    public static function resolve(string $field, $user, array $args, execution_context $ec) {
        global $PAGE, $USER;

        if ($field === 'password' or $field === 'secret') {
            // Extra safety - these must never ever be exposed.
            return null;
        }

        if (!in_array($field, self::FIELDS_DB) && !in_array($field, self::FIELDS_COMPUTED)) {
            throw new \coding_exception('Unknown user field', $field);
        }

        if ($user instanceof \stdClass) {
            if (!isset($user->id) or $user->id <= 0) {
                // Fake users not allowed!
                throw new \coding_exception('Invalid user record provided to '.__METHOD__);
            }
        } else {
            throw new \coding_exception(__METHOD__ . ' must be given a user record from the database', gettype($user));
        }

        $controller = self::get_user_access_controller($user, $ec);
        if (!$controller->can_view_field($field)) {
            $requiredfields = ['id', 'fullname'];
            if (in_array($field, $requiredfields)) {
                // You got here because you did not check permissions because using this type, and now you can't
                // view this users information. The fields are required in GraphQL, but because we're kind you're
                // getting a coding_exception rather than a cryptic GraphQL exception.
                throw new \coding_exception('You did not check you can view a user before resolving them.', $user->id);
            }
            // All other fields are nullable.
            return null;
        }

        // The following fields require special handling.
        switch ($field) {
            case 'profileimageurl':
                return (new \user_picture($user, 1))->get_url($PAGE)->out(false);

            case 'profileimageurlsmall':
                return (new \user_picture($user, 0))->get_url($PAGE)->out(false);

            case 'fullname':
                return fullname($user);

            case 'interests':
                $interests = \core_tag_tag::get_item_tags_array('core', 'user', $user->id, \core_tag_tag::BOTH_STANDARD_AND_NOT, 0, false);
                if ($interests) {
                    return join(', ', $interests);
                }
                return null;

            case 'firstaccess':
            case 'lastaccess':
                $timestamp = self::get_property($user, $field) ?? null;
                if (empty($timestamp)) {
                    return null;
                }

                $format = $args['format'] ?? date_format::FORMAT_TIMESTAMP;
                $context = \context_user::instance($user->id);
                return (new date_field_formatter($format, $context))->format($timestamp);

            case 'description':
                $value = self::get_property($user, $field) ?? null;
                if ($value === null) {
                    return null;
                }

                $format = $args['format'] ?? format::FORMAT_HTML;
                $context = \context_user::instance($user->id);
                $formatter = new text_field_formatter($format, $context);
                $formatter->set_pluginfile_url_options($context, 'user', 'profile', null);

                // Don't use the detail description and format, that has already been munged for external services :(
                if ($format === format::FORMAT_RAW) {
                    $capabilities = ['moodle/user:update'];
                    $capabilities[] = ($USER->id == $user->id) ? 'moodle/user:editownprofile' : 'moodle/user:editprofile';
                    if (!has_any_capability($capabilities, $context)) {
                        return null;
                    }
                }

                return $formatter->format($value);

            case 'profileimagealt':
                return $user->imagealt ?? null;
        }

        return self::get_property($user, $field) ?? null;
    }

    /**
     * Returns an access controller for the given user, in the context of the execution context.
     *
     * @param \stdClass $user
     * @param execution_context $ec
     * @return access_controller
     */
    private static function get_user_access_controller($user, execution_context $ec) {
        $courseid = null;
        if ($ec->has_relevant_context()) {
            $coursecontext = $ec->get_relevant_context()->get_course_context(false);
            if ($coursecontext && $coursecontext->instanceid != SITEID) {
                $courseid = $coursecontext->instanceid;
            }
        }
        return access_controller::for($user, $courseid);
    }

    /**
     * Returns a user property, loading it from the database if it is not there.
     *
     * @param \stdClass $user
     * @param string $property
     * @return mixed
     * @throws \coding_exception If the user object does match the database or if the expected property does not exist.
     */
    private static function get_property(\stdClass $user, string $property) {
        global $DB;
        if (property_exists($user, $property)) {
            return $user->{$property};
        } else if (in_array($property, self::FIELDS_DB)) {
            $record = $DB->get_record('user', ['id' => $user->id], '*', MUST_EXIST);
            foreach ((array)$record as $field => $value) {
                if (!isset($user->{$field})) {
                    $user->{$field} = $value;
                    continue;
                }
                if ($user->{$field} != $value) {
                    throw new \coding_exception('Properties have been modified, DO NOT modify the user record.');
                }
            }
            return $user->{$property};
        }
        throw new \coding_exception('The user record did not contain the expected property.', $property);
    }
}