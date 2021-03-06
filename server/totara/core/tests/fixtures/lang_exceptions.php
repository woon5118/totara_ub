<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralms.com>
 * @package totara_core
 */

/**
 * List of strings that are expected to have cases-insensitive 'Moodle' word in them.
 */
$exceptions = array(
    'core_admin' => array(
        'configallowoverride2',
        'configallowswitch',
        'configallowuserswitchrolestheycantassign',
        'showuseridentity_desc', // Caps.
        'requiredentrieschanged', // Upgrade notes.
        'cfgwwwrootslashwarning', // Link to moodle tracker
        'cfgwwwrootwarning', // Link to moodle tracker,
        'eventshandlersinuse', // Link to docs
        'profilevisible_help', // Capabilities for profile editing
        'moodlerelease', // Reference to real Moodle version in registration data
    ),
    'core_cohort' => array('visible_help'), // Caps.
    'core_completion' => array('manualcompletionbynote', 'err_noroles'), // Caps.
    'core_error' => array('pluginrequirementsnotmet'),
    'core_install' => array('welcomep40'), // Placeholders.
    'core_question' => array('cwrqpfsinfo'),
    'core_rating' => array('rolewarning_help'), // Caps.
    'core_webservice' => array('testauserwithtestclientdescription'), // Caps.
    'tool_xmldb' => array('confirmcheckoraclesemantics'),
    'totara_core' => array('error:cannotupgradefromnewermoodle', 'moodlecore'),
    'enrol_ldap' => array('autocreate'),
    'tool_premigration' => array('pluginname'),
);
