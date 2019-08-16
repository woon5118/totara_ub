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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package tassign_competency
 */

$string['action:activate'] = 'Activate assignment';
$string['action:activate:modal:header'] = 'Confirm assignment activation';
$string['action:activate_individual:modal:header'] = 'Confirm assignment activation';
$string['action:activate:modal'] = '<p>Competency records will be created for all assigned users, and their progress against the achievement criteria for this competency will begin to be tracked.</p><p>Do you want to proceed with activation?</p>';
$string['action:activate_individual:modal'] = '<p>A competency record will be created for the assigned user, and their progress against the achievement criteria for this competency will begin to be tracked.</p><p>Do you want to proceed with activation?</p>';
$string['action:activate:bulk:modal:header'] = 'Confirm bulk assignment activation';
$string['action:activate:bulk:modal'] = '<p>Competency records will be created for all assigned users and their progress against the achievement criteria for these competencies will begin to be tracked.</p><p>Only draft assignments will be activated (active and archived assignments will be ignored).</p><p>Do you want to proceed with activation?</p>';
$string['action:add_user_groups'] = 'Add user groups';

$string['action:archive'] = 'Archive assignment';
$string['action:archive:modal:header'] = 'Confirm archiving of assignment';
$string['action:archive_group:modal:header'] = 'Confirm archiving of assignment';
$string['action:archive_user:modal:header'] = 'Confirm archiving of assignment';
$string['action:archive_user:modal'] = 'The assigned user will be unassigned, and their assignment and record archived. Their progress against the achievement criteria for this competency will no longer be tracked.';
$string['action:archive_group:modal'] = 'All assigned users will be unassigned, and their assignments and records archived. Their progress against the achievement criteria for these competencies will no longer be tracked.';
$string['action:archive_group:modal:confirm'] = 'Enable continuous achievement tracking for users who have no other active assignments to the competency from which they are being unassigned.';
$string['action:archive:modal:question'] = 'Do you want to proceed with archiving?';
$string['action:archive:bulk:modal:header'] = 'Confirm bulk archiving of assignments';
$string['action:archive:bulk:modal:1'] = 'All assigned users will be unassigned, and their assignments and records archived. Their progress against the achievement criteria for these competencies will no longer be tracked.';
$string['action:archive:bulk:modal:2'] = 'Only active assignments will be archived (draft and archived assignments will be ignored).';
$string['action:archive:bulk:modal:question'] = 'Do you want to proceed with archiving?';
$string['action:archive:bulk:modal:confirm'] = 'Enable continuous achievement tracking for <strong>users in group-based</strong> assignments who have no other active assignments to the competency from which they are being unassigned';

$string['action:confirm:activate:success'] = 'Competency assignment successfully activated.';
$string['action:confirm:activate:error'] = 'Activation could not be completed due to a change in the assignment\'s status. The list has been updated.';
$string['action:confirm:activate:bulk'] = '{$a->affected} competency assignments successfully activated.';
$string['action:confirm:activate:bulk:skipped'] = '{$a->affected} competency assignments successfully activated. {$a->skipped} competency assignments ignored.';
$string['action:confirm:archive:success'] = 'Competency assignment successfully archived.';
$string['action:confirm:archive:error'] = 'Archiving could not be completed due to a change in the assignment\'s status. The list has been updated.';
$string['action:confirm:archive:bulk'] = '{$a->affected} competency assignments successfully archived.';
$string['action:confirm:archive:bulk:skipped'] = '{$a->affected} competency assignments successfully archived. {$a->skipped} competency assignments ignored.';
$string['action:confirm:delete:success'] = 'Competency assignment successfully deleted.';
$string['action:confirm:delete:error'] = 'Deletion could not be completed due to a change in the assignment\'s status. The list has been updated.';
$string['action:confirm:delete:bulk'] = '{$a->affected} competency assignments successfully deleted.';
$string['action:confirm:delete:bulk:skipped'] = '{$a->affected} competency assignments successfully deleted. {$a->skipped} competency assignments ignored.';

$string['action:delete'] = 'Delete assignment';
$string['action:delete_draft:modal:header'] = 'Confirm draft assignment deletion';
$string['action:delete_draft_individual:modal:header'] = 'Confirm draft assignment deletion';
$string['action:delete_draft:modal'] = '<p>This will have no impact on users, as they have not yet been assigned to the competency.</p><p>Do you want to proceed with deletion?</p>';
$string['action:delete_draft_individual:modal'] = '<p>This will have no impact on the associated user, as they have not yet been assigned to the competency.</p><p>Do you want to proceed with deletion?</p>';
$string['action:delete_archived:modal:header'] = 'Confirm archived assignment deletion';
$string['action:delete_archived_individual:modal:header'] = 'Confirm archived assignment deletion';
$string['action:delete_archived:modal'] = '<p>All associated user assignments and records will be deleted.</p><p>Do you want to proceed with deletion?</p>';
$string['action:delete_archived_individual:modal'] = '<p>The associated user assignment and record will be deleted.</p><p>Do you want to proceed with deletion?</p>';
$string['action:delete:bulk:modal:header'] = 'Confirm bulk assignment deletion';
$string['action:delete:bulk:modal'] = '<p>All selected draft assignments will be deleted – this will have no impact on users, as they have not yet been assigned to the competency.</p><p>All selected active assignments will be ignored (only draft and archived competencies can be deleted).</p><p><strong>All selected archived assignments will be deleted – all associated user assignments and records will be deleted.</strong></p><p>Do you want to proceed with deletion?</p>';

$string['all_assignments'] = 'All assignments';
$string['all_competencies'] = 'All competencies';
$string['all_competencies_framework'] = 'Framework home';

$string['assigned_type_detail'] = 'Detail';
$string['assigned_user_groups'] = 'Assigned user groups';

$string['assigner_role:admin'] = 'Admin';
$string['assigner_role:manager'] = 'Manager';

$string['assignment:back_to_assignments'] = '<< Back to manage competency assignments';
$string['assignment:createnew'] = 'Create assignments';
$string['assignment:viewcurrent'] = 'Currently assigned user report';

$string['assignment_reason:self'] = 'Self-assigned';
$string['assignment_reason:system'] = 'Continuous tracking';

$string['assignment_type:admin'] = 'Individual (Admin)';
$string['assignment_type:self'] = 'Self-assigned';
$string['assignment_type:other'] = 'Directly assigned';
$string['assignment_type:system'] = 'Individual (System)';

$string['basket:empty_basket_can_not_proceed_creating_assignment'] = 'You need to select at least one competency before you can proceed with assignment creation';

$string['browse_selected_user_groups'] = 'Users in selected groups';
$string['browse_users'] = 'Browse users';

$string['button:sync_users'] = 'Sync assigned users';

$string['change_competency_selection'] = 'Edit competency selection';
$string['competencies_selected'] = 'Creating assignments for <strong>{$a} competencies</strong>';

// Capabilities
$string['competency:view'] = 'View competency assignments';
$string['competency:manage'] = 'Manage competency assignments';
$string['competency:assignself'] = 'Assign competency to yourself';
$string['competency:assignother'] = 'Assign competency to other users';

$string['confirm_assignment_creation_active_singular'] = '{$a->created} active competency assignment was successfully created.';
$string['confirm_assignment_creation_active_plural'] = '{$a->created} active competency assignments were successfully created.';
$string['confirm_assignment_creation_draft_singular'] = '{$a->created} draft competency assignment was successfully created.';
$string['confirm_assignment_creation_draft_plural'] = '{$a->created} draft competency assignments were successfully created.';
$string['confirm_assignment_creation_singular_skipped_singular'] = '{$a->created} competency assignment was successfully created. {$a->skipped} competency assignment was not created, as it already exists.';
$string['confirm_assignment_creation_singular_skipped_plural'] = '{$a->created} competency assignment was successfully created. {$a->skipped} competency assignments were not created, as they already exist.';
$string['confirm_assignment_creation_plural_skipped_singular'] = '{$a->created} competency assignments were successfully created. {$a->skipped} competency assignment was not created, as it already exists.';
$string['confirm_assignment_creation_plural_skipped_plural'] = '{$a->created} competency assignments were successfully created. {$a->skipped} competency assignments were not created, as they already exist.';
$string['confirm_assignment_creation_none_singular'] = 'No new competency assignment was created, as it already exists.';
$string['confirm_assignment_creation_none_plural'] = 'No new competency assignments were created, as they already exist.';

$string['continuous_tracking'] = 'Continuous tracking';

$string['deleted_user'] = '(user deleted)';
$string['deleted_audience'] = '(audience deleted)';
$string['deleted_position'] = '(position deleted)';
$string['deleted_organisation'] = '(organisation deleted)';

$string['directly_assigned'] = 'Directly assigned';

$string['event:assignment_activated'] = 'Competency assignment activated';
$string['event:assignment_archived'] = 'Competency assignment archived';
$string['event:assignment_created'] = 'Competency assignment created';
$string['event:assignment_deleted'] = 'Competency assignment deleted';
$string['event:assignment_user_archived'] = 'User competency assignment archived';
$string['event:assignment_user_assigned'] = 'User competency assignment assigned';
$string['event:assignment_user_unassigned'] = 'User competency assignment unassigned';

$string['error_competencies_out_of_sync'] = 'An error occurred with your selection. {$a} competencies were removed from your selection due to no longer being available for assignment.';
$string['error_create_assignments'] = 'An error occurred during assignment creation, due to modifications to the competencies or user groups after you selected them. Your selection has been updated to reflect these changes – please verify before proceeding.';

$string['expand_assignments_task'] = 'Expand competencies to users relation for competency assignment';
$string['expand_task:notification:subject'] = 'Competency assignments: Sync assigned users task is complete';
$string['expand_task:notification:body'] = 'All users assigned to competencies have been synced.';

$string['filter'] = 'Filter';

$string['filter:assignment_status'] = 'Assignment status';
$string['filter:assignment_status:assigned'] = 'Assigned';
$string['filter:assignment_status:unassigned'] = 'Unassigned';

$string['filter:competency_type'] = 'Competency type';

$string['filter:framework'] = 'Competency framework';
$string['filter:framework:all'] = 'All frameworks';
$string['filter:framework:all_frameworks'] = 'All competency frameworks';

$string['filter:status'] = 'Activation status';
$string['filter:status:active'] = 'Active';
$string['filter:status:all'] = 'All competency assignments';
$string['filter:status:archived'] = 'Archived competency assignments';
$string['filter:status:draft'] = 'Draft competency assignments';
$string['filter:status:active'] = 'Active competency assignments';

$string['filter:user_group:position'] = 'Position';
$string['filter:user_group:organisation'] = 'Organisation';
$string['filter:user_group:cohort'] = 'Audience';
$string['filter:user_group:user'] = 'User';

$string['header:assignment_type'] = 'Assignment type';
$string['header:competency_name'] = 'Competency name';
$string['header:status'] = 'Status';

$string['individual'] = 'Individual';

$string['messageprovider:expand_task_finished'] = 'Sync assigned users finished';

$string['no_user_groups'] = 'No assignments';

$string['pluginname'] = 'Competency assignment';

$string['save:actions'] = 'Actions';
$string['save:activate_assignments'] = 'Activate assignments on creation';
$string['save:create_assignments'] = 'Create assignments';
$string['save:error_creating_assignments'] = 'Error creating assignments, please try again...';
$string['save:error_removing_user_group'] = 'Selected user group could not be removed.';
$string['save:modal:body'] = '{$a} competency assignments will be created.';
$string['save:modal:header'] = 'Confirm competency assignment creation';
$string['save:modal:question'] = 'Do you want to proceed with competency assignment creation?';
$string['save:name'] = 'Name';
$string['save:select'] = 'Select';
$string['save:select_at_least_one_user_group'] = 'Please select at least one user group to create an assignment';
$string['save:selected_audiences'] = 'Selected audiences';
$string['save:selected_organisations'] = 'Selected organisations';
$string['save:selected_positions'] = 'Selected positions';
$string['save:selected_users'] = 'Selected users';

$string['settings:unassignment:header'] = 'Automatic user unassignment behaviour';
$string['settings:unassignment:text'] = 'Configuration for determining the consequences of a user being unassigned from a competency due to them losing membership of an assigned audience, being removed from an assigned position or organisation, or their assigned audience, position or organisation being deleted.';
$string['settings:continuous_tracking'] = 'Continuous achievement tracking';
$string['settings:continuous_tracking:description'] = 'Users\' achievement progress, rating and proficiency status are only tracked while they are assigned to a competency, and tracking ceases once they are unassigned. Enabling continuous achievement tracking will ensure that unassigned users who have no other active assignments to the competency can continue to be tracked after unassignment via a new individual assignment generated for them by the system.';
$string['settings:continuous_tracking:enabled'] = 'Enabled';
$string['settings:continuous_tracking:disabled'] = 'Disabled';
$string['settings:unassign_behaviour'] = 'User assignment and record';
$string['settings:unassign_behaviour:description'] = '<p><strong>"Archive"</strong> means that a record that the user was assigned to the competency will be kept, as well as any achievement, rating and proficiency status recorded for them while assigned. Once archived, achievement progress on the competency will no longer be tracked.</p><p><strong>"Delete"</strong> means that all data about their assignment, and related achievement, rating and proficiency status will be deleted permanently.</p>';
$string['settings:unassign_behaviour:keep'] = 'Archive always (regardless of whether user has achieved a rating or not)';
$string['settings:unassign_behaviour:keep_not_null'] = 'Archive ONLY IF user has achieved a rating in the competency (otherwise delete)';
$string['settings:unassign_behaviour:delete'] = 'Delete always (regardless of whether user has achieved a rating or not)';

$string['sort'] = 'Sort by';
$string['sort:competency_name'] = 'Competency name';
$string['sort:framework_hierarchy'] = 'Framework hierarchy';
$string['sort:user_group_name'] = 'User group name';
$string['sort:most_recently_updated'] = 'Most recently updated';

$string['status:active'] = 'Active';
$string['status:active-alt'] = 'Current assignments';
$string['status:archived'] = 'Archived';
$string['status:archived-alt'] = 'Archived assignments';
$string['status:draft'] = 'Draft';

$string['sync:is_scheduled'] = 'The sync task has already been scheduled – it is running in the background, and you will be notified once it is finished. You will be able to schedule a new sync task once the current one is complete.';
$string['sync:success'] = 'Sync was successfully initiated. It is running in the background, and you will be notified once it is finished.';

$string['title:create'] = 'Create assignments';
$string['title:index'] = 'Manage competency assignments';
$string['title:sync'] = 'Sync assigned users';
$string['title:tool_menu'] = 'Competency assignments';
$string['title:users'] = 'Currently assigned users';

$string['users_to_assign'] = 'Users to assign';
$string['user_group_name'] = 'Assigned user group';
$string['user_groups_empty'] = 'No user groups added';
$string['user_groups_selected'] = 'user groups';

$string['userdataitemassignment_user'] = 'Competency assignments';
$string['userdataitemassignment_user_help'] = 'This includes individual assignments and assignments due to the user being a member of an audience, being in a position or in an organisation.';
