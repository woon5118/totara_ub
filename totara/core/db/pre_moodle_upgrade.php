<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Ciaran Irvine <ciaran.irvine@totaralms.com>
 * @package totara_core
 */

/*
 * This file is executed before migration from vanilla Moodle installation.
 */

defined('MOODLE_INTERNAL') || die();
global $DB, $CFG;

$dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

// Always update all language packs if we can, because they are used in Totara upgrades/install.
totara_upgrade_installed_languages();

// Add custom Totara completion field to prevent fatal problems during upgrade.
$table = new xmldb_table('course_completions');
$field = new xmldb_field('invalidatecache', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'reaggregate');
if (!$dbman->field_exists($table, $field)) {
    $dbman->add_field($table, $field);
}

// Dealing with oauth2 plugins which we didn't take as of Totara 12.
// If the plugin is taken at some stage, the code below as well as this comment may be removed.

$plugin_exists = file_exists("{$CFG->dirroot}/auth/oauth2/version.php");
$users_exist = $DB->count_records_sql("SELECT count(id) from {user} WHERE auth = 'oauth2'") > 0;

if (!($plugin_exists && $users_exist)) {
    uninstall_plugin('auth', 'oauth2');
    if ($dbman->table_exists($table = 'auth_oauth2_linked_login')) {
        $xmldb_table = new xmldb_table($table);
        $DB->get_manager()->drop_table($xmldb_table);
    }
}

// Dealing with repository onedrive plugin which we didn't take as of Totara 12. (Requires oauth see above)
// If the plugin is taken at some stage, the code below as well as this comment may be removed.

$plugin_exists = file_exists("{$CFG->dirroot}/repository/onedrive/version.php");
$instances = $DB->count_records_sql("SELECT count(id) from {repository_onedrive_access}") > 0;

if (!($plugin_exists && $instances)) {
    uninstall_plugin('repository', 'onedrive');
    if ($dbman->table_exists($table = 'repository_onedrive_access')) {
        $xmldb_table = new xmldb_table($table);
        $DB->get_manager()->drop_table($xmldb_table);
    }
}