<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * LDAP authentication plugin upgrade code
 *
 * @package    auth_ldap
 * @copyright  2013 Iñaki Arenaza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to upgrade auth_ldap.
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_ldap_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 10 branching line.

    // Totara 11 branching line.

    // Totara 12 branching line.

    if ($oldversion < 2017020700) {
        // Convert info in config plugins from auth/ldap to auth_ldap.
        upgrade_fix_config_auth_plugin_names('ldap');

        // Totara: add default settings to make the upgrade settings page shorter.
        if (!is_enabled_auth('ldap')) {
            $defaults = array(
                'host_url' => '',
                'ldap_version' => '3',
                'start_tls' => '0',
                'ldapencoding' => 'utf-8',
                'pagesize' => '250',
                'preventpassindb' => '0',
                'bind_dn' => '',
                'bind_pw' => '',
                'user_type' => 'default',
                'contexts' => '',
                'search_sub' => '0',
                'opt_deref' => '0',
                'user_attribute' => '',
                'suspended_attribute' => '',
                'memberattribute' => '',
                'memberattribute_isdn' => '',
                'objectclass' => '',
                'forcechangepassword' => '0',
                'stdchangepassword' => '0',
                'passtype' => 'plaintext',
                'changepasswordurl' => '',
                'expiration' => '0',
                'expiration_warning' => '',
                'expireattr' => '',
                'gracelogins' => '0',
                'graceattr' => '',
                'auth_user_create' => '0',
                'create_context' => '',
                'managercontext' => '',
                'coursecreatorcontext' => '',
                'staffmanagercontext' => '',
                'removeuser' => '0',
                'sync_suspended' => '0',
                'ntlmsso_enabled' => '0',
                'ntlmsso_subnet' => '',
                'ntlmsso_ie_fastpath' => '0',
                'ntlmsso_type' => 'ntlm',
                'ntlmsso_remoteuserformat' => '',
                'field_map_firstname' => '',
                'field_updatelocal_firstname' => 'oncreate',
                'field_updateremote_firstname' => '0',
                'field_lock_firstname' => 'unlocked',
                'field_map_lastname' => '',
                'field_updatelocal_lastname' => 'oncreate',
                'field_updateremote_lastname' => '0',
                'field_lock_lastname' => 'unlocked',
                'field_map_email' => '',
                'field_updatelocal_email' => 'oncreate',
                'field_updateremote_email' => '0',
                'field_lock_email' => 'unlocked',
                'field_map_city' => '',
                'field_updatelocal_city' => 'oncreate',
                'field_updateremote_city' => '0',
                'field_lock_city' => 'unlocked',
                'field_map_country' => '',
                'field_updatelocal_country' => 'oncreate',
                'field_updateremote_country' => '0',
                'field_lock_country' => 'unlocked',
                'field_map_lang' => '',
                'field_updatelocal_lang' => 'oncreate',
                'field_updateremote_lang' => '0',
                'field_lock_lang' => 'unlocked',
                'field_map_description' => '',
                'field_updatelocal_description' => 'oncreate',
                'field_updateremote_description' => '0',
                'field_lock_description' => 'unlocked',
                'field_map_url' => '',
                'field_updatelocal_url' => 'oncreate',
                'field_updateremote_url' => '0',
                'field_lock_url' => 'unlocked',
                'field_map_idnumber' => '',
                'field_updatelocal_idnumber' => 'oncreate',
                'field_updateremote_idnumber' => '0',
                'field_lock_idnumber' => 'unlocked',
                'field_map_institution' => '',
                'field_updatelocal_institution' => 'oncreate',
                'field_updateremote_institution' => '0',
                'field_lock_institution' => 'unlocked',
                'field_map_department' => '',
                'field_updatelocal_department' => 'oncreate',
                'field_updateremote_department' => '0',
                'field_lock_department' => 'unlocked',
                'field_map_phone1' => '',
                'field_updatelocal_phone1' => 'oncreate',
                'field_updateremote_phone1' => '0',
                'field_lock_phone1' => 'unlocked',
                'field_map_phone2' => '',
                'field_updatelocal_phone2' => 'oncreate',
                'field_updateremote_phone2' => '0',
                'field_lock_phone2' => 'unlocked',
                'field_map_address' => '',
                'field_updatelocal_address' => 'oncreate',
                'field_updateremote_address' => '0',
                'field_lock_address' => 'unlocked',
                'field_map_firstnamephonetic' => '',
                'field_updatelocal_firstnamephonetic' => 'oncreate',
                'field_updateremote_firstnamephonetic' => '0',
                'field_lock_firstnamephonetic' => 'unlocked',
                'field_map_lastnamephonetic' => '',
                'field_updatelocal_lastnamephonetic' => 'oncreate',
                'field_updateremote_lastnamephonetic' => '0',
                'field_lock_lastnamephonetic' => 'unlocked',
                'field_map_middlename' => '',
                'field_updatelocal_middlename' => 'oncreate',
                'field_updateremote_middlename' => '0',
                'field_lock_middlename' => 'unlocked',
                'field_map_alternatename' => '',
                'field_updatelocal_alternatename' => 'oncreate',
                'field_updateremote_alternatename' => '0',
                'field_lock_alternatename' => 'unlocked',
            );
            foreach ($defaults as $name => $value) {
                if (get_config('auth_ldap', $name) === false) {
                    set_config($name, $value, 'auth_ldap');
                }
            }
        }

        upgrade_plugin_savepoint(true, 2017020700, 'auth', 'ldap');
    }

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017080100) {
        // The "auth_ldap/coursecreators" setting was replaced with "auth_ldap/coursecreatorcontext" (created
        // dynamically from system-assignable roles) - so migrate any existing value to the first new slot.
        if ($ldapcontext = get_config('auth_ldap', 'creators')) {
            // Get info about the role that the old coursecreators setting would apply.
            $creatorrole = get_archetype_roles('coursecreator');
            $creatorrole = array_shift($creatorrole); // We can only use one, let's use the first.

            // Create new setting.
            set_config($creatorrole->shortname . 'context', $ldapcontext, 'auth_ldap');

            // Delete old setting.
            set_config('creators', null, 'auth_ldap');

            upgrade_plugin_savepoint(true, 2017080100, 'auth', 'ldap');
        }
    }

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
