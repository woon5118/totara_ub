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

$string['cannotdisable'] = 'All tenants must be deleted before multitenancy can be disabled.';
$string['categoryname'] = 'Tenant category name';
$string['cohortname'] = 'Tenant participants audience name';
$string['dashboardname'] = 'Tenant dashboard name';
$string['domainmanagers'] = 'Domain managers';
$string['erroridnumberexists'] = 'Tenant with the same identifier already exists';
$string['erroridnumberinvalid'] = 'Invalid tenant identifier, use only lower case letters (a-z) and numbers';
$string['errornameexists'] = 'Tenant with the same name already exists';
$string['errornameinvalid'] = 'Invalid tenant name';
$string['membercount'] = 'Number of members';
$string['migrationtomemberwarning'] = 'Warning: user account will be migrated to dedicated Tenant account, participation in all other tenants will be terminated.';
$string['migrationtononmemberwarning'] = 'Warning: dedicated tenant user account will be migrated to global user account.';
$string['participant'] = 'Tenant participant';
$string['participantcount'] = 'Number of participants';
$string['participantmanage'] = 'Manage tenant participation';
$string['participants'] = 'Tenant participants';
$string['participantsreport'] = 'Tenant participants report for non-members';
$string['participantsother'] = 'Non-member participants';
$string['pluginname'] = 'Multitenancy support';
$string['settings'] = 'Settings';
$string['suspended'] = 'Suspended';
$string['tenant'] = 'Tenant';
$string['tenantcreate'] = 'Add tenant';
$string['tenantdelete'] = 'Delete tenant';
$string['tenantdeleteconfirm'] = 'Do you really want to delete tenant "{$a->name}"';
$string['tenantidnumber'] = 'Tenant identifier';
$string['tenantidnumber_help'] = 'Tenant identifier must be unique, and include lower case letters (a-z) and numbers (0-9). No capital letters, spaces or special characters. Must start in a letter.';
$string['tenantmember'] = 'Tenant member';
$string['tenantupdate'] = 'Update tenant';
$string['tenants'] = 'Tenants';
$string['tenantsenabled'] = 'Enable multitenancy support';
$string['tenantsenabled_desc'] = 'Enable if you want to create separate self contained tenant instances. Please note that some system features may not be available to tenant users. Report Builder caching is not compatible with multitenancy.

WARNING: Tenant data separation is not guaranteed, see documentation for more information on intended use cases.';
$string['tenantsisolated'] = 'Enable tenant isolation';
$string['tenantsisolated_desc'] = 'Enable if you want to remove all tenant members permissions outside of their tenant contexts.
Tenant isolation is not compatible with some Totara features.

Warning: Do not change this setting when tenant members are logged in.';
$string['tenantsmanage'] = 'Manage tenants';
$string['tenantsuspended'] = 'Tenant suspended';
$string['tenant:config'] = 'Create, update and delete';
$string['tenant:manageparticipants'] = 'Manage tenant participants';
$string['tenant:manageparticipants_help'] = 'Allows the user to manage tenant participants including migrating users to a tenant';
$string['tenant:usercreate'] = 'Create tenant users';
$string['tenant:usercreate_help'] = 'Allows the user to:

* Create new users within a tenant domain
* Import users into a tenant domain if they are permitted to import users also
* Approve user requests for a tenant domain from the auth approved plugin if they are also permitted to approve requests';
$string['tenant:view'] = 'View tenant details';
$string['tenant:view_help'] = 'Allows the user to view the list of tenants and details for each';
$string['tenant:viewparticipants'] = 'View participants in tenant domain';
$string['usermanagement'] = 'User management';
$string['usermanagers'] = 'User managers';
$string['usersreport'] = 'Tenant users report for members';
