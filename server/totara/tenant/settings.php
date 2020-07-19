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
 * @package totara_tenant
 */

defined('MOODLE_INTERNAL') || die();
/* @var admin_root $ADMIN */

if ($hassiteconfig) {
    /** @var admin_settingpage $temp */
    $temp = $ADMIN->locate('optionalsubsystems');
    if ($temp) {
        $temp->add(new totara_tenant_admin_setting_enable());
    }
}

$tenantsdisabled = empty($CFG->tenantsenabled);
$ADMIN->add('root', new admin_category('tenants', new lang_string('tenants', 'totara_tenant')));

$ADMIN->add('tenants', new admin_externalpage('tenantsmanage', get_string('tenantsmanage', 'totara_tenant'),
    $CFG->wwwroot . '/totara/tenant/index.php', 'totara/tenant:view', $tenantsdisabled));

if ($ADMIN->fulltree) {
    $temp = $ADMIN->locate('experimentalsettings');
    if ($temp) {
        $setting = new admin_setting_configcheckbox('tenantsisolated',
            new lang_string('tenantsisolated', 'totara_tenant'),
            new lang_string('tenantsisolated_desc', 'totara_tenant'), 0);
        $setting->set_updatedcallback('purge_all_caches');
        $temp->add($setting);
    }
}
