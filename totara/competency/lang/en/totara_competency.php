<?php
/*
 * This file is part of Totara LMS
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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Competencies';

$string['achievementpathsetup'] = 'Achievement paths setup';
$string['achievementpaths'] = 'Achievement paths';
$string['active'] = 'Active';
$string['activity_log'] = 'Activity log';
$string['activitylog_aggregationchanged'] = 'Overall rating calculation change';
$string['activitylog_competencyaggregationchanged'] = 'Aggregation method on the competency changed';
$string['activitylog_assignedaudience'] = 'Assigned: {$a->audience_name} (Audience)';
$string['activitylog_assignedcontinuous'] = 'Assignment transferred for continuous tracking';
$string['activitylog_assignedself'] = 'Assigned: Self-assigned';
$string['activitylog_assignedadmin'] = 'Assigned: {$a->assigner_name} (Admin)';
$string['activitylog_assignedorganisation'] = 'Assigned: {$a->organisation_name} (Organisation)';
$string['activitylog_assignedother'] = 'Assigned: {$a->assigner_name}';
$string['activitylog_assignedposition'] = 'Assigned: {$a->position_name} (Position)';
$string['activitylog_criteriachange'] = 'Criteria change';
$string['activitylog_criteriamet'] = 'Criteria met: {$a->criteria_met}. Achieved \'{$a->scale_value_name}\' rating.';
$string['activitylog_minprofchanged'] = 'Minimum required proficient value changed to \'{$a->scale_value_name}\'';
$string['activitylog_no_rating'] = 'Rating: None';
$string['activitylog_proficientstatus'] = 'Proficient status';
$string['activitylog_rating'] = 'Rating: {$a->scale_value_name}';
$string['activitylog_rating_value_reset'] = 'Rating value reset';
$string['activitylog_trackingstarted'] = 'Competency active: Achievement tracking started';
$string['activitylog_trackingstopped'] = 'Competency active: Achievement tracking stopped';
$string['activitylog_unassignedaudience'] = 'Unassigned: {$a->audience_name} (Audience)';
$string['activitylog_unassignedcontinuous'] = 'Unassigned: Continuous tracking';
$string['activitylog_unassignedself'] = 'Unassigned: Self-assigned';
$string['activitylog_unassignedadmin'] = 'Unassigned: {$a->assigner_name} (Admin)';
$string['activitylog_unassignedorganisation'] = 'Unassigned: {$a->organisation_name} (Organisation)';
$string['activitylog_unassignedother'] = 'Unassigned: {$a->assigner_name}';
$string['activitylog_unassignedposition'] = 'Unassigned: {$a->position_name} (Position)';
$string['addaltpath'] = 'Add alternative path';
$string['addachievementpath'] = 'Add achievement path';
$string['addlinkedcourses'] = 'Add linked courses';
$string['aggregate_all_competencies_task'] = 'Aggregate competency achievements for all users with active competency assignments';
$string['aggregate_queued_competencies_task'] = 'Aggregate competency achievements for queued aggregation only';
$string['all'] = 'All';
$string['allcategories'] = 'All categories';
$string['allscalevalues'] = 'All scale value';
$string['addpathway'] = 'Add path';
$string['and'] = 'and';
$string['any'] = 'Any';
$string['anyscalevalue'] = 'Any scale value';
$string['applychanges'] = 'Apply changes';
$string['applysuccess'] = 'Changes applied successfully';
$string['archived'] = 'Archived';
$string['assign_competencies'] = 'Assign competencies';
$string['assigned'] = 'Assigned';
$string['assignment'] = 'Assignment';
$string['assignment_archived_at'] = 'Assignment archived {$a}';
$string['assignmentcreationavailability'] = 'Assignment creation availability';
$string['back_to'] = '« Back to {$a}';
$string['back_to_competency_profile'] = '« Back to your competency profile';
$string['bulkachievementcriteria'] = 'Bulk set achievements criteria';
$string['cancel'] = 'Cancel';
$string['changesnotsaved:body'] = 'Changes will be lost if you continue. Do you want to continue?';
$string['changesnotsaved:title'] = 'Changes have not been save.';

// Capabilities
$string['competency:assign_self'] = 'Assign competency to yourself';
$string['competency:assign_other'] = 'Assign competency to other users';
$string['competency:manage_assignments'] = 'Manage competency assignments';
$string['competency:rate_other_competencies'] = 'Rate competencies of other users';
$string['competency:rate_own_competencies'] = 'Rate own competencies';
$string['competency:view_assignments'] = 'View competency assignments';
$string['competency:view_own_profile'] = 'View own competency profile';
$string['competency:view_other_profile'] = 'View profile of other users';

$string['competency_profile'] = 'Competency profile';
$string['competencies'] = '{$a} competencies';
$string['complete_criteria'] = 'Complete the following criteria';
$string['coursefullname'] = 'Full Name';
$string['createachievementpath'] = 'Create achievement path';
$string['createpath'] = 'Create path';
$string['criteriatype'] = 'Criteria type';
$string['description'] = 'Description';
$string['disabled'] = 'Disabled';
$string['done'] = 'Done';
$string['enablecompetency_assignment'] = 'Enable Competency Assignment';
$string['enablecompetency_assignment_desc'] = 'This option will let you: Enable(show)/Disable Competency Assignment feature on this site.';
$string['enabled'] = 'Enabled';
$string['editcompetency'] = 'Edit competency: {$a}';
$string['event:linked_courses_updated'] = 'Linked courses updated';

// Expand task related
$string['expand_assignments_task'] = 'Expand competencies to users relation for competency assignment';
$string['expand_task:notification:subject'] = 'Competency assignments: Sync assigned users task is complete';
$string['expand_task:notification:body'] = 'All users assigned to competencies have been synced.';

$string['framework'] = 'Framework';
$string['fullname'] = 'Full name';
$string['general'] = 'General';
$string['header:competency_name'] = 'Competency';
$string['header:assignment_status'] = 'Status';
$string['header:assignment_reasons'] = 'Reason assigned';
$string['idnumber'] = 'ID number';
$string['latest_achievement'] = 'Latest achievement';
$string['legacy_assignment_rating_discontinued'] = 'This rating was determined through methods which have been discontinued.';
$string['legacy_assignment_rating_description'] = 'These include learning plans, course completion, or proficiency in child competencies, in previous versions of the system.';
$string['invalidsection'] = 'Invalid section {$a}';
$string['linkdefaultpreset'] = 'Link default preset';
$string['linkedcourses'] = 'Linked courses';
$string['linkedcoursessaved'] = 'Linked courses have been saved';
$string['linkedcourses_mustsavechange'] = 'Changes must be saved before they will be applied';
$string['loading'] = 'Loading...';
$string['managetypes_aggregation'] = 'Manage aggregation types';
$string['managetypes_pathway'] = 'Manage pathway types';
$string['mandatory'] = 'Mandatory';

$string['messageprovider:expand_task_finished'] = 'Sync assigned users finished';

$string['my_competency_profile'] = '{$a} Competency profile';
$string['my_rating'] = 'My rating';
$string['next'] = 'Next';
$string['no_competencies_assigned'] = 'There are no competencies currently assigned.';
$string['no_competency_to_assign'] = 'There are no competencies available to assign.';
$string['nocourseslinkedyet'] = 'No courses linked yet';
$string['none'] = 'None';
$string['nopaths'] = 'No achievement paths added';
$string['no_value_achieved'] = 'No value achieved';
$string['optional'] = 'Optional';
$string['or'] = 'or';
$string['overallratingcalc'] = 'Overall rating calculation';
$string['overview'] = 'Overview';
$string['pathtype'] = 'Path type';
$string['pathwaymultivalue'] = 'Multivalue';
$string['pathwaysinglevalue'] = 'Singlevalue';
$string['pathwaystatusarchived'] = 'Archived';
$string['pathwaystatusactive'] = 'Active';
$string['pathwaystatusarchived'] = 'Archived';
$string['proficiency_not_achieved'] = 'Proficiency not achieved';
$string['not_proficient'] = 'Not proficient';
$string['proficient'] = 'Proficient';
$string['proficient_on'] = 'Proficient {$a}';
$string['proficient_value'] = 'Proficient value';
$string['progress_name_by_user'] = '{$a->progress_name} by {$a->user_fullname_role}';
$string['rating_none'] = 'None';
$string['rating_scale'] = 'Rating scale';
$string['removelinkedcourse'] = 'Remove linked course';
$string['removedlinkedcourse'] = 'Removed linked course';
$string['revertallchanges:body'] = 'Are you sure you want to revert all changes? All changes not yet activated will be permanently deleted.';
$string['revertallchanges:title'] = 'Revert all changes';
$string['savechanges'] = 'Save changes';
$string['search_competencies_descriptive'] = 'Search for competencies to self-assign';
$string['searchcourses'] = 'Search courses';
$string['selectpathtype'] = 'Select path type';
$string['selectcourses'] = 'Select courses';
$string['selectcriteriontype'] = 'Select additional criteria for this path';

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

$string['setupbasic'] = 'Basic';
$string['setupbasictitle'] = 'Basic set up';
$string['setupcustom'] = 'Custom';
$string['setuptemplate'] = 'Use template';
$string['setuptemplatetitle'] = 'Select template';
$string['singlevaluepaths'] = 'Criteria-based paths';
$string['singlevalueproficientmark_help'] = 'Proficient values marked with';
$string['sort:alphabetical'] = 'Alphabetical';
$string['sort:recently_archived'] = 'Recently archived';
$string['sort:recently_assigned'] = 'Recently assigned';
$string['status_0'] = 'Active';
$string['status_1'] = 'Draft';
$string['status_2'] = 'Deleted';
$string['title:tool_menu'] = 'Competency assignments';
$string['title:users'] = 'Currently assigned users';
$string['type'] = 'Type';
$string['unassigned'] = 'Unassigned';
$string['undoall'] = 'Undo all';
$string['undoRemoveLinkedCourse'] = 'Undo remove linked course';
$string['user_group_type:cohort'] = 'Audience';
$string['user_group_type:position'] = 'Position';
$string['user_group_type:organisation'] = 'Organisation';
$string['userdataitemachievement'] = 'Achievement records';
$string['userdataitemachievement_help'] = 'When purging, the user\'s achievement data will be removed. However, after purging is complete, the records may be added back again if they still meet the criteria. For instance, if a user has completed a course that is linked to a competency, then an achievement record will be created again despite being purged previously.';
$string['userdataitemassignment_user'] = 'Competency assignments';
$string['userdataitemassignment_user_help'] = 'This includes individual assignments and assignments due to the user being a member of an audience, being in a position or in an organisation. When purging, any achievement data associated with the assignments will also be purged (even if these data items are not selected). After purging, group-based assignments may be dynamically created again, if they user still meets the criteria for the assignment (by being a member of a group).';
$string['viewing'] = 'Viewing';

// Strings from assignments
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

$string['assignment_reason'] = '{$a->assignment} ({$a->type})';
$string['assignment_reason:self'] = 'Self-assigned';
$string['assignment_reason:system'] = 'Continuous tracking';

$string['assignment_type:admin'] = 'Individual (Admin)';
$string['assignment_type:legacy'] = 'Legacy Assignment';
$string['assignment_type:self'] = 'Self-assigned';
$string['assignment_type:other'] = 'Directly assigned';
$string['assignment_type:system'] = 'Individual (System)';

$string['basket:empty_basket_can_not_proceed_creating_assignment'] = 'You need to select at least one competency before you can proceed with assignment creation';

$string['browse_selected_user_groups'] = 'Users in selected groups';
$string['browse_users'] = 'Browse users';

$string['button:sync_users'] = 'Sync assigned users';

$string['change_competency_selection'] = 'Edit competency selection';
$string['competencies_selected'] = 'Creating assignments for <strong>{$a} competencies</strong>';

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
$string['error:invalidconfiguration'] = 'The pathway configuration is invalid';

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

$string['no_user_groups'] = 'No assignments';

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

$string['users_to_assign'] = 'Users to assign';
$string['user_group_name'] = 'Assigned user group';
$string['user_groups_empty'] = 'No user groups added';
$string['user_groups_selected'] = 'user groups';
