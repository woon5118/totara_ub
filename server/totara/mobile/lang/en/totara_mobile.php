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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package totara_mobile
 */

$string['applogin'] = 'Register new mobile device';
$string['apploginopen'] = 'Press to open mobile app';
$string['authenticationpage'] = 'Mobile authentication';
$string['authtype'] = 'Type of login';
$string['authtype_choice_browser'] = 'Mobile browser';
$string['authtype_choice_native'] = 'Native';
$string['authtype_choice_webview'] = 'Webview';
$string['authtype_desc'] = 'Determines how users can login to the mobile app.<ul>
<li><strong>Native</strong>: Users will be within a mobile app experience and will be able to complete a limited set of authentication actions.</li>
<li><strong>Webview</strong>: Users will be within a browser window within the app and will be able to complete most authentication actions.</li>
<li><strong>Mobile browser</strong>: Users will be directed to their mobile browser to complete authentication actions. Users will then be prompted to return to the app without the need to authenticate again.</li>
</ul>';
$string['colour_black'] = 'Black';
$string['colour_white'] = 'White';
$string['continueinbrowser'] = 'Continue in browser';
$string['coursecompat'] = 'Course compatible in-app';
$string['coursecompat_help'] = 'This setting defines whether the course can be accessed from within the Totara Mobile App.';
$string['device_loggedout'] = 'Successfully logged out';
$string['devices'] = 'Mobile devices';
$string['devices_logoutall'] = 'Log out from all devices';
$string['devicetable_accessed'] = 'Last access';
$string['devicetable_index'] = '#';
$string['devicetable_logout'] = 'Log out';
$string['devicetable_registered'] = 'Registered';
$string['enabletotaramobile'] = 'Enable mobile app';
$string['enabletotaramobile_desc'] = 'Enable mobile web services for the Totara Mobile App or another app requesting them.';
$string['errorgeneral'] = 'Mobile access error';
$string['errormobileunavailable'] = 'Mobile support unavailable';
$string['gotomobile'] = 'Go to mobile app';
$string['managedevices'] = 'Manage mobile devices';
$string['mobile:use'] = 'Connect and use mobile app';
$string['mobileicon'] = 'mobile icon';
$string['pluginname'] = 'Totara Mobile';
$string['profilecategory'] = 'Mobile app';
$string['settingscategory'] = 'Mobile';
$string['settingspage'] = 'Mobile settings';
$string['switchtoapp'] = 'Would you like to switch to the mobile app?';
$string['taskpurgeexpireddevices'] = 'Purge expired mobile device registrations';
$string['taskpurgeexpiredwebviews'] = 'Purge expired mobile WebView sessions';
$string['themepage'] = 'Mobile theme';
$string['themesetting_logo'] = 'Mobile app logo';
$string['themesetting_logodesc'] = 'Upload your logo here and it will appear on the authentication (login) screen.';
$string['themesetting_primarycolour'] = 'Primary Colour';
$string['themesetting_primarycolourdesc'] = 'This sets the primary colour for the app.';
$string['themesetting_textcolour'] = 'Text colour';
$string['themesetting_textcolourdesc'] = 'This sets the text colour to either black or white over the primary colour.';
$string['timeout'] = 'Time-out period';
$string['timeout_choice_0'] = 'Never';
$string['timeout_choice_1'] = '1 day';
$string['timeout_choice_30'] = '30 days';
$string['timeout_choice_60'] = '60 days';
$string['timeout_choice_90'] = '90 days';
$string['timeout_desc'] = 'Defines the length of time before the user is required to reauthenticate into the mobile app.';
$string['urlscheme'] = 'URL scheme';
$string['urlscheme_desc'] = 'If you want to allow only your custom branded app to be opened via a browser window, then specify its URL scheme here; otherwise leave the field empty.';
/**
 * App language strings go here, see /totara/mobile/util/convert_lang_strings.php to generate
 */
$string['app:general:loading'] = 'Loading...';
$string['app:general:error'] = 'Error :';
$string['app:general:enter'] = 'Enter';
$string['app:general:version'] = 'Version';
$string['app:general:try_again'] = 'Try again';
$string['app:general:cancel'] = 'Cancel';
$string['app:general:yes'] = 'Yes';
$string['app:general:error_unknown'] = 'Something went wrong, please try again later.';
$string['app:general:no_internet'] = 'You are offline';
$string['app:general:back'] = 'Back';
$string['app:general:ok'] = 'Ok';
$string['app:general:delete'] = 'Delete';
$string['app:site_url:title'] = 'Get started.';
$string['app:site_url:url_information'] = 'Enter your organisation\'s URL';
$string['app:site_url:url_text_placeholder'] = 'Enter URL';
$string['app:site_url:validation:enter_valid_url'] = 'Enter a valid site address';
$string['app:auth_invalid_site:title'] = 'Sorry';
$string['app:auth_invalid_site:description'] = 'The URL is not compatible. Please check the URL and try again.';
$string['app:auth_invalid_site:action_primary'] = 'OK';
$string['app:native_login:header_title'] = 'Login';
$string['app:native_login:login_information'] = 'Enter your site username and password.';
$string['app:native_login:username_text_placeholder'] = 'Username';
$string['app:native_login:password_text_placeholder'] = 'Password';
$string['app:native_login:forgot_username_password'] = 'Forgot your username or password?';
$string['app:native_login:error_unauthorized'] = 'Oops! Your username or password is incorrect.';
$string['app:native_login:validation:enter_valid_username'] = 'Enter a valid username';
$string['app:native_login:validation:enter_valid_password'] = 'Enter a valid password';
$string['app:browser_login:title'] = 'Confirm!';
$string['app:browser_login:description'] = 'You need to login through a mobile browser to authenticate. Would you like us to take you there now?';
$string['app:browser_login:primary_title'] = 'Yes, go to browser';
$string['app:browser_login:tertiary_title'] = 'Cancel';
$string['app:auth_general_error:title'] = 'Ooops!';
$string['app:auth_general_error:description'] = 'Something went wrong';
$string['app:auth_general_error:action_primary'] = 'OK';
$string['app:no_internet_alert:title'] = 'You are offline';
$string['app:no_internet_alert:message'] = 'To continue, you\'ll need to go online';
$string['app:no_internet_alert:go_back'] = 'OK';
$string['app:current_learning:action_primary'] = 'Current learning';
$string['app:current_learning:primary_info:zero'] = 'You have no learning';
$string['app:current_learning:primary_info:one'] = 'You have {{count}} learning item to complete';
$string['app:current_learning:primary_info:other'] = 'You have {{count}} learning items to complete';
$string['app:current_learning:no_learning_message'] = 'No current learning';
$string['app:current_learning:restriction_view:title'] = 'Sorry!';
$string['app:current_learning:restriction_view:description'] = 'This course is not compatible with the mobile app. Would you like to access this course in the browser?';
$string['app:current_learning:restriction_view:primary_button_title'] = 'Yes, go to browser';
$string['app:current_learning:restriction_view:tertiary_button_title'] = 'Cancel';
$string['app:totara_component:section_title_continue_learn'] = 'Continue your learning';
$string['app:totara_component:due_in'] = 'Due in';
$string['app:totara_component:extend_date'] = 'Extend Date';
$string['app:totara_component:overdue_by'] = 'Overdue by';
$string['app:additional_actions_modal:auth_model_title'] = 'Action required';
$string['app:additional_actions_modal:auth_model_description'] = 'You are required to visit a browser to complete some action(s).';
$string['app:additional_actions_modal:auth_model_go_to_browser'] = 'Go to browser';
$string['app:additional_actions_modal:auth_model_logout'] = 'Logout';
$string['app:general_error_feedback_modal:title'] = 'Ooops!';
$string['app:general_error_feedback_modal:description'] = 'Something went wrong';
$string['app:general_error_feedback_modal:action_primary'] = 'Refresh';
$string['app:general_error_feedback_modal:action_tertiary'] = 'Try in browser';
$string['app:incompatible_api:title'] = 'Sorry';
$string['app:incompatible_api:description'] = 'The URL is not compatible with the mobile app. Would you like to try in browser?';
$string['app:incompatible_api:action_primary'] = 'Yes, try in browser';
$string['app:incompatible_api:action_tertiary'] = 'Logout';
$string['app:incompatible_api:action_tertiary_cancel'] = 'Cancel';
$string['app:activity_not_available:title'] = 'This activity is not available unless';
$string['app:course:course_overview:course_summery'] = 'Course summary';
$string['app:course:course_overview_progress:title'] = 'Your course progress';
$string['app:course:course_overview_grade:title'] = 'Your grade overview';
$string['app:course:course_overview_mark_as_complete:title'] = 'Mark as complete';
$string['app:course:course_complete:title'] = 'Awesome!';
$string['app:course:course_complete:description'] = 'You have successfully completed the course.';
$string['app:course:course_complete:button_title'] = 'Continue learning';
$string['app:course:course_complete_confirmation:title'] = 'Confirm';
$string['app:course:course_complete_confirmation:description'] = 'Are you sure you want to mark this course as complete?';
$string['app:course:course_complete_confirmation:primary_button_title'] = 'Mark as complete';
$string['app:course:course_complete_confirmation:tertiary_button_title'] = 'cancel';
$string['app:course:course_criteria:title'] = 'Course criteria';
$string['app:course:course_details:activities'] = 'ACTIVITIES';
$string['app:course:course_details:overview'] = 'OVERVIEW';
$string['app:course:course_details:expand_or_collapse'] = 'Expand all';
$string['app:course:course_details:badge_title'] = 'course';
$string['app:course:course_activity_section:not_available'] = 'Not available';
$string['app:course_group:tabs:overview'] = 'OVERVIEW';
$string['app:course_group:tabs:courses'] = 'COURSES';
$string['app:course_group:details:badge_title_program'] = 'program';
$string['app:course_group:details:badge_title_certification'] = 'certification';
$string['app:course_group:overview:summary_title_program'] = 'Program Summary';
$string['app:course_group:overview:summary_title_certification'] = 'Certification Summary';
$string['app:course_group:courses:unavailable_sets'] = 'unavailable set';
$string['app:course_group:courses:compete'] = 'Successfully completed!';
$string['app:course_group:courses:current_learning_button_title'] = 'Current Learning';
$string['app:course_group:course_set:criteria'] = 'View criteria';
$string['app:scorm:last_synced'] = 'Last synced';
$string['app:scorm:ago'] = 'ago';
$string['app:scorm:info_completed_attempts'] = 'You have reached the maximum number of attempts';
$string['app:scorm:info_upcoming_activity'] = 'Sorry, this activity is not available until';
$string['app:scorm:summary:summary'] = 'Summary';
$string['app:scorm:summary:grade:title'] = 'Grade details';
$string['app:scorm:summary:grade:view_all'] = 'View all';
$string['app:scorm:summary:grade:method'] = 'Grading method';
$string['app:scorm:summary:grade:reported'] = 'Grade reported';
$string['app:scorm:summary:grade:in_attempt'] = 'In attempt';
$string['app:scorm:summary:attempt:title'] = 'Attempt details';
$string['app:scorm:summary:attempt:total_attempts'] = 'Total attempts allowed';
$string['app:scorm:summary:attempt:completed_attempts'] = 'Total attempts done';
$string['app:scorm:summary:attempt:unlimited'] = 'Unlimited';
$string['app:scorm:summary:new_attempt'] = 'New attempt';
$string['app:scorm:summary:last_attempt'] = 'Last attempt';
$string['app:scorm:feedback:grade_title'] = 'YOUR GRADE';
$string['app:scorm:feedback:awesome'] = 'Awesome!';
$string['app:scorm:feedback:sorry'] = 'Sorry!';
$string['app:scorm:feedback:back'] = 'Go back';
$string['app:scorm:feedback:action_info'] = 'Would you like to go back or do you want to try again?';
$string['app:scorm:attempts:title'] = 'All attempts grades';
$string['app:scorm:attempts:attempt'] = 'Attempt';
$string['app:scorm:attempts:failed'] = 'Failed';
$string['app:scorm:attempts:passed'] = 'Passed';
$string['app:scorm:grading_method:0'] = 'Highest attempt';
$string['app:scorm:grading_method:1'] = 'Average attempts';
$string['app:scorm:grading_method:2'] = 'First attempt';
$string['app:scorm:grading_method:3'] = 'Last completed attempt';
$string['app:scorm:confirmation:title'] = 'Are you sure?';
$string['app:scorm:confirmation:message'] = 'You want to exit the activity.';
$string['app:scorm:confirmation:ok'] = 'Yes';
$string['app:scorm:confirmation:cancel'] = 'No';
$string['app:notifications:title'] = 'Notifications';
$string['app:notifications:empty'] = 'No notifications yet!';
$string['app:notifications:selected'] = '{{count}} Selected';
$string['app:notifications:mark_as_read'] = 'Mark as read';
$string['app:downloads:title'] = 'Downloads';
$string['app:downloads:empty'] = 'No downloads yet!';
$string['app:downloads:selected'] = '{{count}} Selected';
$string['app:user_profile:title'] = 'Profile';
$string['app:user_profile:login_as'] = 'Logged in as: {{username}}';
$string['app:user_profile:manage_section'] = 'Manage';
$string['app:user_profile:setting_cell'] = 'Settings';
$string['app:user_profile:logout:button_text'] = 'Logout';
$string['app:user_profile:logout:title'] = 'Confirmation';
$string['app:user_profile:logout:message'] = 'Are you sure you want to logout?';
$string['app:user_profile:about'] = 'About';
$string['app:navigation_stack:view_criteria'] = 'View criteria';
/**
 * End of app language strings.
 */