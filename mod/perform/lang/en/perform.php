<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

$string['pluginname'] = 'Performance activity';

$string['activity_action_activate'] = 'Activate';
$string['activity_action_delete'] = 'Delete';
$string['activity_action_options'] = 'Actions';
$string['activity_content_tab_heading'] = 'Content';
$string['activity_draft_not_ready'] = 'Draft is not yet ready for activation';
$string['activity_name'] = 'Performance activity name';
$string['activity_participants_add'] = 'Add participants';
$string['activity_participants_heading'] = 'Participants';
$string['activity_participants_select_done'] = 'Done';
$string['activity_participants_select_heading'] = 'Select participants';
$string['activity_section_save_changes'] = 'Save changes';
$string['activity_status_active'] = 'Active';
$string['activity_status_draft'] = 'Draft';
$string['activity_type'] = 'Activity type';
$string['add_activity'] = 'Add activity';
$string['back_to_activity'] = '« Back to performance activity';
$string['back_to_all_activities'] = '« Back to all performance activities';
$string['button_done']  = 'Done';
$string['button_cancel']  = 'Cancel';
$string['condition_actor_is_participant_fail'] = 'The currently logged in user is not the participant.';
$string['create_activity'] = 'Add activity';
$string['create_subject_instance_task'] = 'Create subject instance for performance activities';
$string['edit_content'] = 'Edit content';
$string['embedded_perform_participant_instance'] = 'Performance Participant Instance (Perform)';
$string['embedded_perform_subject_instance'] = 'Performance Subject Instance (Perform)';
$string['enable_performance_activities'] = 'Enable Performance Activities';
$string['enable_performance_activities_description'] = 'This option lets you enable or disable the performance activities feature on this site.

* If \'Enable\' is selected, all functionality related to performance activities will be visible and accessible to users.
* If \'Disable\' is selected, no performance activities functionality will be visible or accessible.';
$string['error_activity_id_wrong'] = 'Wrong activity id';
$string['error_activity_name_missing'] = 'You are not allowed to create an activity with an empty name';
$string['error_activity_type_missing'] = 'You are not allowed to create an activity without a type';
$string['error_activity_type_unknown'] = 'Invalid activity type id';
$string['error_activate'] = 'Cannot activate this activity due to invalid state or conditions are not satisfied.';
$string['error_create_permission_missing'] = 'You do not have the permission to create a performance activity';
$string['error_subject_instance_id_wrong'] = 'Wrong subject instance id';
$string['event_activity_activated'] = 'Performance activity activated';
$string['event_participant_section_availability_closed_description'] ='The availability of participant section with id {$a->id} has been closed by user with id {$a->user_id}';
$string['event_participant_section_availability_closed_name'] = 'Performance participant section availability closed';
$string['event_track_user_assigned'] = 'User assigned to a performance activity track';
$string['event_track_user_unassigned'] = 'User unassigned to a performance activity track';
$string['expand_assignments_task'] = 'Expand performance activity track assignments';
$string['expand_task_notification_body'] = 'All users assigned to performance activity tracks have been synced.';
$string['expand_task_notification_subject'] = 'Performance activities: Sync assigned users task is complete';
$string['general_info_label_activity_description'] = 'Description';
$string['general_info_label_activity_title'] = 'Activity title';
$string['general_info_label_activity_type'] = 'Activity type';
$string['general_info_select_activity_type'] = 'Select a type';
$string['get_started'] = 'Get started';
$string['invalid_activity'] = 'Invalid activity';
$string['invalid_state_switch'] = 'Cannot switch from {$a->from_state} to {$a->target_state}';
$string['manage_activities_tabs_assignment'] = 'Assignments';
$string['manage_activities_tabs_content'] = 'Content';
$string['manage_activities_tabs_general'] = 'General';
$string['manage_activity_page_title'] = 'Manage performance activity';
$string['manage_edit_draft_heading'] = 'Edit draft: “{$a}”';
$string['menu_title_activity_management'] = 'Activity Management';
$string['modal_activate_title'] = 'Confirm activity activation';
$string['modal_activate_message'] = '<p>Activation will make this activity live. Subjects will be assigned, and instances generated for them according to the schedule set on the activity. Once activated, changes can still be made to assignments, but content cannot be edited.</p><p><strong>{$a}</strong> users will be assigned on activation.</p><p>Are you sure you would like to activate this activity?</p>';
$string['modal_delete_confirmation_line'] = 'Are you sure you would like to delete this activity?';
$string['modal_delete_draft_message'] = 'This will permanently delete all content created for this activity. It will not affect assigned users, as assignments are only created on activity activation.';
$string['modal_delete_draft_title'] = 'Confirm draft activity deletion';
$string['modal_delete_message'] = 'This will permanently delete all content created for this activity, and all associated user records. This may affect aggregated data based on these records, and impact scheduling rules in other activities based on participation in this one.';
$string['modal_delete_message_data_recovery_warning'] = 'Deleted data cannot be recovered.';
$string['modal_delete_title'] = 'Confirm activity deletion';
$string['modulename'] = 'Performance activity';
$string['modulenameplural'] = 'Performance activities';
$string['participant_instance_availability_closed'] = 'Closed';
$string['participant_instance_availability_open'] = 'Open';
$string['participant_instance_status_complete'] = 'Complete';
$string['participant_instance_status_in_progress'] = 'In progress';
$string['participant_instance_status_not_started'] = 'Not started';
$string['participant_instances_title'] = '{$a->activity_name} : {$a->fullname}';
$string['participant_section_availability_closed'] = 'Closed';
$string['participant_section_availability_open'] = 'Open';
$string['participant_section_status_complete'] = 'Complete';
$string['participant_section_status_in_progress'] = 'In progress';
$string['participant_section_status_not_started'] = 'Not started';
$string['participation_reporting'] = 'Participation reporting';
$string['perform:create_activity'] = 'Create performance activities';
$string['perform:manage_activity'] = 'Manage performance activities';
$string['perform:view_manage_activities'] = 'Access the performance activities management interface';
$string['perform:view_participation_reporting'] = 'Access the participation reporting interface';
$string['pluginadministration'] = 'Performance activity administration';
$string['relation_to_subject_self'] = 'Self';
$string['save_changes'] = 'Save changes';
$string['section_add_element'] = 'Add element';
$string['schedule_activity_instance_creation_period'] = 'Activity instance creation period';
$string['schedule_creation_range_and_date_type'] = 'Creation range and date type';
$string['schedule_date_range_from'] = 'From';
$string['schedule_date_range_to'] = 'To';
$string['schedule_date_range_onwards'] = 'Onwards';
$string['schedule_error_date_required'] = 'Date required';
$string['schedule_error_date_range'] = 'Range end date cannot be before range start date';
$string['schedule_fixed_closed_order_validation'] = 'From cannot be after until';
$string['schedule_type_closed'] = 'Limited';
$string['schedule_type_fixed'] = 'Fixed';
$string['schedule_type_dynamic'] = 'Dynamic';
$string['schedule_type_open'] = 'Open-ended';
$string['section_add_element'] = 'Add element';
$string['section_default_name'] = 'Section {$a}';
$string['section_delete_element'] = 'Delete';
$string['section_element_questions'] = 'Questions';
$string['short_text'] = 'Short text';
$string['subject_instance_status'] = 'Status of subject instance';
$string['subject_instance_availability_closed'] = 'Closed';
$string['subject_instance_availability_open'] = 'Open';
$string['subject_instance_status_complete'] = 'Complete';
$string['subject_instance_status_in_progress'] = 'In progress';
$string['subject_instance_status_not_started'] = 'Not started';
$string['system_activity_type:appraisal'] = 'Appraisal';
$string['system_activity_type:check-in'] = 'Check-in';
$string['system_activity_type:feedback'] = 'Feedback';
$string['toast_error_create_activity'] = 'An error occurred while saving, and the activity could not be created.';
$string['toast_error_generic_update'] = 'An error occurred, and your latest changes have not been saved.';
$string['toast_error_save_response'] = 'An error occurred while saving, and the activity responses could not be updated.';
$string['toast_success_activity_activated'] = '"{$a}" was successfully activated.';
$string['toast_success_activity_deleted'] = 'Activity and all associated user records successfully deleted.';
$string['toast_success_activity_update'] = 'Activity saved.';
$string['toast_success_draft_activity_deleted'] = 'Draft activity successfully deleted.';
$string['toast_success_save_response'] = 'Activity responses saved.';
$string['toast_success_save_schedule'] = 'Activity schedule saved.';
$string['track_description'] = 'Track description';
$string['user_activities_status_complete'] = 'Complete';
$string['user_activities_activities_about_others_title'] = 'Activities about others';
$string['user_activities_activity_does_not_exist'] = 'The requested performance activity could not be found.';
$string['user_activities_page_title'] = 'Performance activities';
$string['user_activities_status_complete'] = 'Complete';
$string['user_activities_status_header_activity'] = 'Overall activity progress';
$string['user_activities_status_header_participation'] = 'Your progress';
$string['user_activities_status_header_relationship'] = 'Relationship to user';
$string['user_activities_status_in_progress'] = 'In progress';
$string['user_activities_status_not_started'] = 'Not yet started';
$string['user_activities_your_relationship_to_user'] = 'Your relationship to user';
$string['user_activities_other_response_response'] = '{$a->relationship} response';
$string['user_activities_other_response_show'] = "Show others' responses";
$string['user_activities_other_response_hide'] = "Hide others' responses";
$string['user_activities_other_response_no_participants_identified'] = 'No participants identified';
$string['user_activities_subject_header'] = 'User';
$string['user_activities_title_header'] = 'Activity title';
$string['user_activities_your_activities_title'] = 'Your activities';
$string['user_group_assignment_add_cohort'] = 'Select audiences to add';
$string['user_group_assignment_add_group'] = 'Add group';
$string['user_group_assignment_confirm_remove'] = 'Really remove selected group from this activity?';
$string['user_group_assignment_confirm_remove_title'] = 'User group assignment removal';
$string['user_group_assignment_group_cohort'] = 'Audience';
$string['user_group_assignment_group_cohort_name'] = 'Audience name';
$string['user_group_assignment_group_org'] = 'Organisation';
$string['user_group_assignment_group_pos'] = 'Position';
$string['user_group_assignment_group_user'] = 'Individual';
$string['user_group_assignment_name'] = 'Name';
$string['user_group_assignment_no_users'] = 'No groups assigned';
$string['user_group_assignment_title'] = 'Assigned groups';
$string['user_group_assignment_type'] = 'Group type';
$string['user_group_assignment_unique_user_count_link'] = 'View report';
$string['user_group_assignment_unique_user_count_title'] = 'Total unique users currently assigned as subjects';
$string['user_group_assignment_usercount'] = 'Users';
$string['view_actions'] = 'Actions';
$string['view_name'] = 'Name';
$string['view_status'] = 'Status';
$string['view_type'] = 'Type';
$string['workflow'] = 'Workflow';
$string['workflow_automatic_closure_label'] = 'Automatic closure';
$string['workflow_automatic_closure_label_help'] = 'While a section or instance is open, participants may submit responses. Once a section or instance is closed, responses cannot be modified or (re)submitted. This setting determines what causes closure to occur (if at all). Changes to this setting will be applied to future, but not already existing, subject instances.';
$string['workflow_automatic_closure_on_completion'] = 'On completion';
$string['workflow_automatic_closure_on_completion_help'] = 'Sections and instances will close once they have progressed to "Complete"';
