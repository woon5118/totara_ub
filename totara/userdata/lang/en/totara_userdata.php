<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @package totara_userdata
 */

$string['actions'] = 'Actions';
$string['audit'] = 'Data audit';
$string['audit_desc'] = 'Overview of how much data is contained in the system for this user.';
$string['auditexecute'] = 'Audit user data';
$string['audititemsprocessed'] = 'Items processed';
$string['audititemserror'] = 'Items returning errors';
$string['audititemsnonemtpy'] = 'Items containing data';
$string['auditsummary'] = 'Audit summary';
$string['audittotalcount'] = 'Total data count';
$string['createdby'] = 'Created by';
$string['defaultsuspendedpurgetype'] = 'Default purging type for suspended users';
$string['defaultsuspendedpurgetype_desc'] = 'Select default user data purge type to be set during user suspension process. Accounts that already have a suspended purge type set are not affected.';
$string['defaultsuspendedpurgetypeerror'] = 'This type is used in setting for default suspension action';
$string['defaultdeletedpurgetype'] = 'Default purging type for deleted users';
$string['defaultdeletedpurgetype_desc'] = 'Select default user data purge type to be set during user deletion process. Accounts that already have a deleted purge type set are not affected.';
$string['defaultdeletedpurgetypeerror'] = 'This type is used in setting for default deletion action';
$string['deletedpurgetype'] = 'Deleted purge type';
$string['errorexporttypedelete'] = 'Purge type cannot be deleted';
$string['errornoexporttypes'] = 'There is no export type suitable for export of own data at the moment.';
$string['errorpurgecancel'] = 'Error cancelling purge';
$string['errorpurgetypedelete'] = 'Purge type cannot be deleted';
$string['export'] = 'User data export';
$string['exportfiledownload'] = 'Download data export file';
$string['exportfileready'] = 'Your data export is ready: {$a->file}

The export file will be available until {$a->until}, after which it will be removed.';
$string['exportincludefiledir'] = 'Include files';
$string['exportincludefiledir_help'] = 'Inclusions of file contents in export archives may result in very large archives, long execution times and performance issues.';
$string['exportrequest'] = 'Request data export';
$string['exportrequestpending'] = 'Data export in progress. You will receive a notification once the file is available for download.';
$string['exports'] = 'Exports';
$string['exportscount'] = 'Number of exports';
$string['exportitemselection'] = 'Export items';
$string['exportitemselection_desc'] = 'Select items below to specify which user data will be exported when this export type is applied.';
$string['exportorigin'] = 'Origin';
$string['exportoriginself'] = 'Export of own user data';
$string['exportoriginother'] = 'Other';
$string['exporttype'] = 'Export type';
$string['exporttypeadd'] = 'Add export type';
$string['exporttypeavailablefor'] = 'Permitted for';
$string['exporttypeavailablefor_help'] = 'If deselected pending exports are cancelled and access to previously created export files is rejected.';
$string['exporttypedelete'] = 'Delete export type';
$string['exporttypedeleteconfirm'] = 'Are you sure you want to delete export type "{$a}"?';
$string['exporttypes'] = 'Export types';
$string['exporttypeupdate'] = 'Update export type';
$string['fullname'] = 'Full name';
$string['fullnamelink'] = 'Full name (with link)';
$string['incontextid'] = 'Context';
$string['itemcomponent'] = 'Item component';
$string['itemexportdata'] = 'Export data';
$string['itemfullname'] = 'Item';
$string['itemgroup'] = 'Item group';
$string['itemname'] = 'Internal item name';
$string['itempurgedata'] = 'Purge data';
$string['messageprovider:purge_manual_finished'] = 'Manual user data purge finished';
$string['messageprovider:export_self_finished'] = 'Export of own user data finished';
$string['newitem'] = 'New';
$string['newitems'] = 'New items';
$string['notificationexportselfsubject'] = 'User data export completed';
$string['notificationexportselfmessage'] = 'Export of your user data was completed: {$a->result}';
$string['notificationpurgemanualsubject'] = 'Manual purge of user data completed';
$string['notificationpurgemanualmessage'] = 'Manual purge of user {$a->fullnameuser} data was completed: {$a->result}';
$string['pluginname'] = 'User data management';
$string['purgeautocompleted'] = '{$a->purge} - purged {$a->timefinished}';
$string['purgeautodefault'] = 'None (Site default: {$a})';
$string['purgeautopending'] = '{$a->purge} - pending';
$string['purgeautomatic'] = 'Automatic data purge';
$string['purgecancelled'] = 'Purge was cancelled';
$string['purgeispending'] = 'This data purge is already scheduled for execution';
$string['purgeitemselection'] = 'Purge items';
$string['purgeitemselection_desc'] = 'Select items below to specify which user data will be deleted when this purge type is applied.';
$string['purgemanually'] = 'Purge user data';
$string['purgemanuallyconfirm'] = 'Are you sure you want to purge the data?';
$string['purgemanuallytriggered'] = 'Ad-hoc task for user data purging was created, you will receive notification after it completes the execution.';
$string['purgeorigin'] = 'Origin';
$string['purgeorigindeleted'] = 'Automatic purging after user is Deleted';
$string['purgeoriginmanual'] = 'Manual data purge';
$string['purgeoriginother'] = 'Other';
$string['purgeoriginsuspended'] = 'Automatic purging after user is Suspended';
$string['purges'] = 'Purges';
$string['purgescount'] = 'Number of purges';
$string['purgesetdeleted'] = 'Configure purging of deleted account';
$string['purgesetsuspended'] = 'Configure purging of suspended account';
$string['purgesuserall'] = 'All purges';
$string['purgesuserpending'] = 'Pending purges';
$string['purgetype'] = 'Purge type';
$string['purgetypeadd'] = 'Add purge type';
$string['purgetypeavailablefor'] = 'Available for';
$string['purgetypeavailablefor_help'] = 'Deselecting options makes them unavailable for future use only. The deselected types and not unassigned from existing users and pending manual purges are completed.

Number indicates how many users are assigned for automatic purging types.';
$string['purgetypedelete'] = 'Delete purge type';
$string['purgetypedeleteconfirm'] = 'Are you sure you want to delete purge type "{$a}"?';
$string['purgetypes'] = 'Purge types';
$string['purgetypeupdate'] = 'Update purge type';
$string['purgetypeuserstatus'] = 'Restricted to user status';
$string['purgetypeuserstatus_help'] = 'Each user data purge is restricted to one user status, the selection cannot be changed later.';
$string['repurge'] = 'Reapply purging';
$string['repurge_help'] = 'Reapply this purging type to already suspended or deleted accounts that are using this type for automatic data purging';
$string['repurgewarning'] = 'This purge type will be reapplied to {$a} users. Site performance may be impacted while the purges are being completed.';
$string['result'] = 'Result';
$string['resultsuccess'] = 'Success';
$string['resulterror'] = 'Error';
$string['resultkipped'] = 'Skipped';
$string['resultcancelled'] = 'Cancelled';
$string['resulttimedout'] = 'Timed out';
$string['selfexportenable'] = 'Allow users to export their own data';
$string['selfexportenable_desc'] = 'To allow users to export their own user data you need to enable this setting and create export type suitable for own data export. Another requirement is to have permission "Export own user data".';
$string['settings'] = 'Settings';
$string['suspendedpurgetype'] = 'Suspended purge type';
$string['userdata:config'] = 'Configure user data management';
$string['userdata:exportself'] = 'Export own user data';
$string['userdata:purgemanual'] = 'Purge user data manually';
$string['userdata:purgesetdeleted'] = 'Set deleted user purge type';
$string['userdata:purgesetsuspended'] = 'Set suspended user purge type';
$string['userdata:viewexports'] = 'View user data exports';
$string['userdata:viewinfo'] = 'View user data configuration';
$string['userdata:viewpurges'] = 'View user data purges';
$string['userdata_core_user_customfields'] = 'All custom profile fields';
$string['userdata_core_user_idnumber'] = 'ID number';
$string['userdata_core_user_idnumber_help'] = 'ID number is used by authentication plugins and HR Sync. It can be removed from deleted accounts only.';
$string['userdata_core_user_names'] = 'First name and surname';
$string['userdata_core_user_names_help'] = 'Fist name and surname and required fields. Instead of deleting the the names are replaced with placeholders.';
$string['userdata_core_user_otherfields'] = 'All other profile fields';
$string['userdata_core_user_username'] = 'Username';
$string['userdata_core_user_username_help'] = 'Username is a required field for all active and suspended accounts. It can be randomised for deleted accounts only.';
$string['userdata_core_user_systemaccess'] = 'System access times';
$string['userinfo'] = 'User data';
$string['timecreated'] = 'Created';
$string['taskmisc'] = 'Miscellaneous maintenance tasks';
$string['taskpurgedeleted'] = 'Automatic user data purging of deleted users';
$string['taskpurgesuspended'] = 'Automatic user data purging of suspended users';
$string['timefinished'] = 'Finished';
$string['timechanged'] = 'Changed';
$string['timestarted'] = 'Started';
