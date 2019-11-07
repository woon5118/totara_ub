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
$string['activitylog_assignedaudience'] = 'Assigned: {$a->audience_name} (Audience)';
$string['activitylog_assignedcontinuous'] = 'Assignment transferred for continuous tracking';
$string['activitylog_assignedself'] = 'Assigned: Self-assigned';
$string['activitylog_assignedadmin'] = 'Assigned: {$a->assigner_name} (Admin)';
$string['activitylog_assignedorganisation'] = 'Assigned: {$a->organisation_name} (Organisation)';
$string['activitylog_assignedother'] = 'Assigned: {$a->assigner_name} ({$a->assigner_role})';
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
$string['activitylog_unassignedother'] = 'Unassigned: {$a->assigner_name} ({$a->assigner_role})';
$string['activitylog_unassignedposition'] = 'Unassigned: {$a->position_name} (Position)';
$string['addaltpath'] = 'Add alternative path';
$string['addachievementpath'] = 'Add achievement path';
$string['addlinkedcourses'] = 'Add linked courses';
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
$string['competency:manage'] = 'Manage competency assignments';
$string['competency:view'] = 'View competency assignments';
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
$string['editcompetency'] = 'Edit comptency: {$a}';
$string['event:linked_courses_updated'] = 'Linked courses updated';
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
$string['type'] = 'Type';
$string['unassigned'] = 'Unassigned';
$string['undoall'] = 'Undo all';
$string['undoRemoveLinkedCourse'] = 'Undo remove linked course';
$string['updatecompachievements'] = 'Update competency achievements';
$string['user_group_type:cohort'] = 'Audience';
$string['user_group_type:position'] = 'Position';
$string['user_group_type:organisation'] = 'Organisation';
$string['userdataitemachievement'] = 'Achievement records';
$string['userdataitemachievement_help'] = 'When purging, the user\'s achievement data will be removed. However, after purging is complete, the records may be added back again if they still meet the criteria. For instance, if a user has completed a course that is linked to a competency, then an achievement record will be created again despite being purged previously.';
$string['viewing'] = 'Viewing';
