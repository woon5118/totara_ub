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
 * Language file for 'badges' component
 *
 * @package    core_badges
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

$string['actions'] = 'Actions';
$string['activate'] = 'Enable access';
$string['activatesuccess'] = 'Access to the badges was successfully enabled.';
$string['addbadgecriteria'] = 'Add badge criteria';
$string['addcriteria'] = 'Add criteria';
$string['addbackpack'] = 'Add backpack';
$string['addcriteriatext'] = 'To start adding criteria, please select one of the options from the drop-down menu.';
$string['addcohort'] = 'Qualifying audience(s)';
$string['addcohort_help'] = 'Select all audiences that qualify a user for this badge. Hold CTRL key to select multiple items.';
$string['addcourse'] = 'Add courses';
$string['addcourse_help'] = 'Select all courses that should be added to this badge requirement. Hold CTRL key to select multiple items.';
$string['addprogram'] = 'Qualifying program(s)';
$string['addprogram_help'] = 'Select all programs that qualify a user for this badge. Hold CTRL key to select multiple items.';
$string['addrelated'] = 'Add related badge';
$string['addtobackpack'] = 'Add to backpack';
$string['addedtobackpack'] = 'Added badge to backpack';
$string['adminonly'] = 'This page is restricted to site administrators only.';
$string['after'] = 'after the date of issue.';
$string['aggregationmethod'] = 'Aggregation method';
$string['all'] = 'All';
$string['allmethod'] = 'All of the selected conditions are met';
$string['allmethodactivity'] = 'All of the selected activities are complete';
$string['allmethodcohort'] = 'Membership in all the selected audiences';
$string['allmethodcourseset'] = 'All of the selected courses are complete';
$string['allmethodmanual'] = 'All of the selected roles award the badge';
$string['allmethodprofile'] = 'All of the selected profile fields have been completed';
$string['allmethodprogram'] = 'All of the selected programs have been completed';
$string['allowcoursebadges'] = 'Enable course badges';
$string['allowcoursebadges_desc'] = 'Allow badges to be created and awarded in the course context.';
$string['allowexternalbackpack'] = 'External backpack connection';
$string['allowexternalbackpack_desc'] = 'If enabled, users can connect to an external backpack and share their badges from this site. Users may also choose to display any public badge collections from their external backpack on their profile page on this site. It is recommended to leave this option disabled if your site is not accessible from the Internet.';
$string['any'] = 'Any';
$string['anymethod'] = 'Any of the selected conditions is met';
$string['anymethodactivity'] = 'Any of the selected activities is complete';
$string['anymethodcohort'] = 'Membership in any of the selected audiences';
$string['anymethodcourseset'] = 'Any of the selected courses is complete';
$string['anymethodmanual'] = 'Any of the selected roles awards the badge';
$string['anymethodprofile'] = 'Any of the selected profile fields has been completed';
$string['anymethodprogram'] = 'Any of the selected programs have been completed';
$string['apiversion'] = 'API version supported';
$string['archivebadge'] = 'Would you like to delete badge \'{$a}\', but keep existing issued badges?';
$string['archiveconfirm'] = 'Delete and keep existing issued badges';
$string['archivehelp'] = '<p>This option means that the badge will be marked as "retired" and will no longer appear in the list of badges. Users will no longer be able to earn this badge, however existing badge recipients will still be able to display this badge on their profile page and push it to their external backpacks.</p>
<p>If you would like your users to retain access to the earned badges it is important to select this option instead of fully deleting badges.</p>';
$string['attachment'] = 'Attach badge to message';
$string['attachment_help'] = 'If enabled, an issued badge file will be attached to the recipient\'s email for download. (Attachments must be enabled in *Site administration > Plugins > Message outputs > Email* to use this option.)';
$string['award'] = 'Award badge';
$string['awardedtoyou'] = 'Issued to me';
$string['awardoncron'] = 'Access to the badges was successfully enabled. Too many users can instantly earn this badge. To ensure site performance, this action will take some time to process.';
$string['awards'] = 'Recipients';
$string['backpackavailability'] = 'External badge verification';
$string['backpackconnectionok'] = 'Backpack connection successfully established';
$string['backpackconnectionnottested'] = 'Connection can not be tested for this backpack because only OBv2.0 backpacks support it.';
$string['backpackneedsupdate'] = 'The backpack connected to this profile does not match the backpack for the site. You need to disconnect and reconnect the backpack.';
$string['backpackavailability_help'] = 'For badge recipients to be able to prove they earned their badges from you, an external backpack service should be able to access your site and verify badges issued from it. Your site does not currently appear to be accessible, which means that badges you have already issued or will issue in the future cannot be verified.

**Why am I seeing this message?**

It may be that your firewall prevents access from users outside your network, your site is password protected, or you are running the site on a computer that is not available from the Internet (such as a local development machine).

**Is this a problem?**

You should fix this issue on any production site where you are planning to issue badges, otherwise the recipients will not be able to prove they earned their badges from you. If your site is not yet live you can create and issue test badges, as long as the site is accessible before you go live.

**What if I can\'t make my whole site publicly accessible?**

The only URL required for verification is [your-site-url]/badges/assertion.php so if you are able to modify your firewall to allow external access to that file, badge verification will still work.';
$string['backpackbadgessummary'] = 'You have {$a->totalbadges} badge(s) displayed from {$a->totalcollections} collection(s).';
$string['backpackbadgessettings'] = 'Change backpack settings';
$string['backpackcannotsendverification'] = 'Cannot send verification email';
$string['backpackconnected'] = 'Backpack has been connected';
$string['backpackconnection'] = 'Backpack connection';
$string['backpackconnection_help'] = 'This page allows you to set up connection to an external backpack provider. Connecting to a backpack lets you display external badges within this site and push badges earned here to your backpack.';
$string['backpackdetails'] = 'Backpack settings';
$string['backpackdisconnected'] = 'Backpack has been disconnected';
$string['backpackemail'] = 'Email address';
$string['backpackemail_help'] = 'The email address associated with your backpack. While you are connected, any badges earned on this site will be associated with this email address.';
$string['badgeprivacy'] = 'Show badges';
$string['personaconnection'] = 'Sign in with your email';
$string['personaconnection_help'] = 'Persona is a system for identifying yourself across the web, using an email address that you own. The Open Badges backpack uses Persona as a login system, so to be able to connect to a backpack you will need a Persona account.

For more information about Persona visit <a href="https://login.persona.org/about">https://login.persona.org/about</a>.';
$string['backpackconnectioncancelattempt'] = 'Connect using a different email address';
$string['backpackconnectionconnect'] = 'Connect to Backpack';
$string['backpackconnectionresendemail'] = 'Resend verification email';
$string['backpackconnectionunexpectedresult'] = 'There was a problem contacting the Backpack. Please try again.<br><br>If this problem persists, please contact your system administrator.';
$string['backpackconnectionunexpectedmessage'] = 'The backpack returned the error: "{$a}".';
$string['backpackemailverificationpending'] = 'Verification pending';
$string['backpackemailverifyemailbody'] = 'Hi,

A new connection to your OpenBadges backpack has been requested from \'{$a->sitename}\' using your email address.

To confirm and activate the connection to your backpack, please click the link below.

{$a->link}

In most mail programs, this should appear as a blue link which you can just click on. If that doesn\'t work, then cut and paste the address into the address line at the top of your web browser.

If you need help, please contact the site administrator,
{$a->admin}';
$string['backpackemailverifyemailsubject'] = '{$a}: OpenBadges Backpack email verification';
$string['backpackemailverifypending'] = 'A verification email has been sent to <strong>{$a}</strong>. Click on the verification link in the email to activate your Backpack connection.';
$string['backpackemailverifysuccess'] = 'Thanks for verifying your email address. You are now connected to your Backpack.';
$string['backpackemailverifytokenmismatch'] = 'The token in the link you clicked does not match the stored token. Make sure you clicked the link in most recent email you received.';
$string['backpackexporterror'] = 'Can\'t export the badge to backpack';
$string['backpackimport'] = 'Badge import settings';
$string['backpackimport_help'] = 'After the backpack connection is successfully established, badges from your backpack can be displayed on your badges page and your profile page.

In this area, you can select collections of badges from your backpack that you would like to display in your profile.';
$string['backpacksettings'] = 'Backpack settings';
$string['backpackapiurl'] = 'Backpack API URL';
$string['backpackweburl'] = 'Backpack Website URL';
$string['badge'] = 'Badge';
$string['badges'] = 'Badges';
$string['badgedetails'] = 'Badge details';
$string['badgeimage'] = 'Image';
$string['badgeimage_help'] = 'This is an image that will be used when this badge is issued.

To add a new image, browse and select an image (in JPG or PNG format) then click **Save changes**. The image will be cropped to a square and resized to match badge image requirements.';
$string['badgeprivacysetting'] = 'Badge privacy settings';
$string['badgeprivacysetting_help'] = 'Badges you earn can be displayed on your account profile page. This setting allows you to automatically set the visibility of the newly earned badges.

You can still control individual badge privacy settings on your badges page.';
$string['badgeprivacysetting_str'] = 'Automatically show badges I earn on my profile page';
$string['badgesalt'] = 'Salt for hashing the recipient\'s email address';
$string['badgesalt_desc'] = 'Using a hash allows backpack services to confirm the badge earner without having to expose their email address. This setting should only use numbers and letters.

Note: For recipient verification purposes, please avoid changing this setting once you start issuing badges.';
$string['badgesdisabled'] = 'Badges are not enabled on this site.';
$string['badgesearned'] = 'Number of badges earned: {$a}';
$string['badgesettings'] = 'Badges settings';
$string['badgestatus_0'] = 'Not available to users';
$string['badgestatus_1'] = 'Available to users';
$string['badgestatus_2'] = 'Locked - Not available to users';
$string['badgestatus_3'] = 'Locked - Available to users';
$string['badgestatus_4'] = 'Archived';
$string['badgestoadd'] = 'Badges to add';
$string['badgestoearn'] = 'Number of badges available: {$a}';
$string['badgesview'] = 'Course badges';
$string['badgetype_1'] = 'Site';
$string['badgetype_2'] = 'Course';
$string['badgeurl'] = 'Issued badge link';
$string['bawards'] = 'Recipients ({$a})';
$string['bcriteria'] = 'Criteria';
$string['bdetails'] = 'Edit details';
$string['bendorsement'] = 'Endorsement';
$string['bmessage'] = 'Message';
$string['boverview'] = 'Overview';
$string['brelated'] = 'Related badges ({$a})';
$string['bydate'] = ' complete by';
$string['imagecaption'] = 'Image caption';
$string['imagecaption_help'] = 'Caption for the image.';
$string['claim'] = 'Claim';
$string['claimcomment'] = 'Endorsement comment';
$string['claimid'] = 'Claim URL';
$string['claimid_help'] = 'Add the URL provided by the endorser to support their claim.';
$string['choosebadges'] = 'Choose badges';
$string['clearsettings'] = 'Clear settings';
$string['completionnotenabled'] = 'Course completion is not enabled for this course, so it cannot be included in badge criteria. Course completion may be enabled in the course settings.';
$string['completioninfo'] = 'This badge was issued for completing: ';
$string['configenablebadges'] = 'When enabled you can create badges and award them to users.';
$string['configuremessage'] = 'Badge message';
$string['connect'] = 'Connect';
$string['connected'] = 'Connected';
$string['connecting'] = 'Connecting...';
$string['contact'] = 'Contact';
$string['contact_help'] = 'An email address associated with the badge issuer.';
$string['copyof'] = 'Copy of {$a}';
$string['coursebadgesdisabled'] = 'Course badges are not enabled on this site.';
$string['coursecompletion'] = 'Users must complete this course.';
$string['coursebadges'] = 'Badges';
$string['create'] = 'New badge';
$string['createbutton'] = 'Create badge';
$string['creatorbody'] = '<p>{$a->user} has completed all badge requirements and has been awarded the badge. View issued badge at {$a->link} </p>';
$string['creatorsubject'] = '\'{$a}\' has been awarded!';
$string['criteriasummary'] = 'Criteria summary';
$string['criteriacreated'] = 'Badge criteria successfully created';
$string['criteriadeleted'] = 'Badge criteria successfully deleted';
$string['criteriaupdated'] = 'Badge criteria successfully updated';
$string['criteria_descr'] = 'Users are awarded this badge when they complete the following requirement:';
$string['criteria_descr_bydate'] = ' by <em>{$a}</em> ';
$string['criteria_descr_grade'] = ' with minimum grade of <em>{$a}</em> ';
$string['criteria_descr_short0'] = 'Complete <strong>{$a}</strong> of: ';
$string['criteria_descr_short1'] = 'Complete <strong>{$a}</strong> of: ';
$string['criteria_descr_short2'] = 'Awarded by <strong>{$a}</strong> of: ';
$string['criteria_descr_short4'] = 'Complete the course ';
$string['criteria_descr_short5'] = 'Complete <strong>{$a}</strong> of: ';
$string['criteria_descr_short6'] = 'Complete <strong>{$a}</strong> of: ';
$string['criteria_descr_short7'] = 'Audience membership in <strong>{$a}</strong> of: ';
$string['criteria_descr_short8'] = 'Complete <strong>{$a}</strong> of: ';
$string['criteria_descr_single_short1'] = 'Complete: ';
$string['criteria_descr_single_short2'] = 'Awarded by: ';
$string['criteria_descr_single_short4'] = 'Complete the course ';
$string['criteria_descr_single_short5'] = 'Complete: ';
$string['criteria_descr_single_short6'] = 'Complete: ';
$string['criteria_descr_single_short7'] = 'Membership in: ';
$string['criteria_descr_single_short8'] = 'Complete: ';
$string['criteria_descr_single_1'] = 'The following activity has to be completed:';
$string['criteria_descr_single_2'] = 'This badge has to be awarded by a user with the following role:';
$string['criteria_descr_single_4'] = 'Users must complete the course';
$string['criteria_descr_single_5'] = 'The following course has to be completed:';
$string['criteria_descr_single_6'] = 'The following user profile field has to be completed:';
$string['criteria_descr_single_7'] = 'Membership in the following audience is required:';
$string['criteria_descr_single_8'] = 'The following program has to be completed:';
$string['criteria_descr_0'] = 'Users are awarded this badge when they complete <strong>{$a}</strong> of the listed requirements.';
$string['criteria_descr_1'] = '<strong>{$a}</strong> of the following activities are completed:';
$string['criteria_descr_2'] = 'This badge has to be awarded by the users with <strong>{$a}</strong> of the following roles:';
$string['criteria_descr_4'] = 'Users must complete the course';
$string['criteria_descr_5'] = '<strong>{$a}</strong> of the following courses have to be completed:';
$string['criteria_descr_6'] = '<strong>{$a}</strong> of the following user profile fields have to be completed:';
$string['criteria_descr_7'] = 'Membership in <strong>{$a}</strong> of the following audiences is required:';
$string['criteria_descr_8'] = '<strong>{$a}</strong> of the following programs have to be completed:';
$string['criteria_0'] = 'This badge is awarded when...';
$string['criteria_1'] = 'Activity completion';
$string['criteria_1_help'] = 'Allows a badge to be awarded to users based on the completion of a set of activities within a course.';
$string['criteria_2'] = 'Manual issue by role';
$string['criteria_2_help'] = 'Allows a badge to be awarded manually by users who have a particular role within the site or course.';
$string['criteria_3'] = 'Social participation';
$string['criteria_3_help'] = 'Social';
$string['criteria_4'] = 'Course completion';
$string['criteria_4_help'] = 'Allows a badge to be awarded to users who have completed the course. This criterion can have additional parameters such as minimum grade and date of course completion.';
$string['criteria_5'] = 'Completing a set of courses';
$string['criteria_5_help'] = 'Allows a badge to be awarded to users who have completed a set of courses. Each course can have additional parameters such as minimum grade and date of course completion.';
$string['criteria_6'] = 'Profile completion';
$string['criteria_6_help'] = 'Allows a badge to be awarded to users for completing certain fields in their profile. You can select from default and custom profile fields that are available to users.';
$string['criteria_7'] = 'Audience membership';
$string['criteria_7_help'] = 'Allows a badge to be awarded to users for becoming a member of certain audiences.';
$string['criteria_8'] = 'Program completion';
$string['criteria_8_help'] = 'Allows a badge to be awarded to users for completing certain programs.';
$string['criterror'] = 'Current parameters issues';
$string['criterror_help'] = 'This field set shows all parameters that were initially added to this badge requirement but are no longer available. It is recommended that you uncheck such parameters to make sure that users can earn this badge in the future.';
$string['currentimage'] = 'Current image';
$string['currentstatus'] = 'Current status: ';
$string['dateawarded'] = 'Date issued';
$string['dateearned'] = 'Date: {$a}';
$string['day'] = 'Day(s)';
$string['deactivate'] = 'Disable access';
$string['deactivatesuccess'] = 'Access to the badges was successfully disabled.';
$string['defaultissuercontact'] = 'Badge issuer email address';
$string['defaultissuercontact_desc'] = 'An email address associated with the badge issuer. For an Open Badges v2.0 backpack, this is used for authentication when publishing badges to a backpack.';
$string['defaultissuerpassword'] = 'Badge issuer password';
$string['defaultissuerpassword_help'] = 'When publishing Badges to a Backpack using Open Badges v2.0, the "Badge issuer email address" and "Badge issuer password" must correspond to an account on the Backpack site that is used to issue Badges representing this site.';
$string['defaultissuername'] = 'Badge issuer name';
$string['defaultissuername_desc'] = 'Name of the issuing agent or authority.';
$string['delbadge'] = 'Would you like to delete badge \'{$a}\' and remove all existing issued badges?';
$string['delexternalbackpackconfirm'] = 'Delete site backpack \'{$a}\'?';
$string['delconfirm'] = 'Delete and remove existing issued badges';
$string['deletehelp'] = '<p>Fully deleting a badge means that all its information and criteria records will be permanently removed. Users who have earned this badge will no longer be able to access it and display it on their profile pages.</p>
<p>Note: Users who have earned this badge and have already pushed it to their external backpack, will still have this badge in their external backpack. However, they will not be able to access criteria and evidence pages linking back to this web site.</p>';
$string['delcritconfirm'] = 'Are you sure that you want to delete this criterion?';
$string['delparamconfirm'] = 'Are you sure that you want to delete this parameter?';
$string['description'] = 'Description';
$string['description_help'] = 'A short description of the badge. This can only be plain text without any formatting. (This is a requirement of the Mozilla Open Badges standard.)';
$string['disconnect'] = 'Disconnect';
$string['donotaward'] = 'Currently, this badge is not active, so it cannot be awarded to users. If you would like to award this badge, please set its status to active.';
$string['enablebadges'] = 'Enable badges';
$string['endorsement'] = 'Endorsement';
$string['error:backpackdatainvalid'] = 'The data return from the backpack was invalid.';
$string['error:backpackemailnotfound'] = 'The email \'{$a}\' is not associated with a backpack. You need to create a backpack for that account or sign in with another email address.';
$string['error:backpacknotavailable'] = 'Your site is not accessible from the Internet, so any badges issued from this site cannot be verified by external backpack services.';
$string['error:backpackloginfailed'] = 'You could not be connected to an external backpack for the following reason: {$a}';
$string['error:backpackproblem'] = 'There was a problem connecting to your backpack service provider. Please try again later.';
$string['error:badgeawardnotfound'] = 'Cannot verify this awarded badge.  This badge may have been revoked.';
$string['error:badgenotfound'] = 'Badge not found';
$string['error:badjson'] = 'The connection attempt returned invalid data.';
$string['error:cannotact'] = 'Cannot activate the badge "{$a}". ';
$string['error:cannotawardbadge'] = 'Cannot award badge to a user.';
$string['error:cannotrevokebadge'] = 'Cannot revoke badge from a user.';
$string['error:cannotdeletecriterion'] = 'This criterion cannot be deleted. ';
$string['error:connectionunknownreason'] = 'The connection was unsuccessful but no reason was given.';
$string['error:clone'] = 'Cannot clone the badge.';
$string['error:dialognotreeitems'] = 'No badges';
$string['error:duplicatename'] = 'Badge with such name already exists in the system.';
$string['error:externalbadgedoesntexist'] = 'Badge not found';
$string['error:guestuseraccess'] = 'You are currently using guest access. To see badges you need to log in with your user account.';
$string['error:invalidcriteriaparam'] = 'Invalid criteria parameters. ';
$string['error:invalidcriteriatype'] = 'Invalid criteria type.';
$string['error:invalidexpiredate'] = 'Expiry date has to be in the future.';
$string['error:invalidexpireperiod'] = 'Expiry period cannot be negative or equal 0.';
$string['error:invalidparamactivity'] = 'Activity does not exist. ';
$string['error:invalidparamcohort'] = 'An audience does not exist.';
$string['error:invalidparamcohorts'] = '{$a} audiences do not exist.';
$string['error:invalidparamcourse'] = 'Course does not exist. ';
$string['error:invalidparamprofile'] = 'Profile field does not exist. ';
$string['error:invalidparamprogram'] = 'Program does not exist. ';
$string['error:invalidparamprograms'] = '{$a} programs do not exist. ';
$string['error:invalidparamrole'] = 'Role does not exist. ';
$string['error:missingprogram'] = 'Warning: A program is no longer available.';
$string['error:missingprograms'] = 'Warning: {$a} programs are no longer available.';
$string['error:missingcohort'] = 'Warning: An audience is no longer available.';
$string['error:missingcohortplural'] = 'Warning: {$a} audiences are no longer available.';
$string['error:noactivities'] = 'There are no activities with completion criteria enabled in this course.';
$string['error:noassertion'] = 'No assertion was returned by Persona. You may have closed the dialog before completing the login process.';
$string['error:nocohorts'] = 'No audiences available';
$string['error:nocourses'] = 'Course completion is not enabled for any of the courses in this site, so none can be displayed. Course completion may be enabled in the course settings.';
$string['error:nogroupssummary'] = '<p>There are no public collections of badges available in your backpack. </p>';
$string['error:nogroupslink'] = '<p>Only public collections are shown, <a href="{$a}" target="_blank" rel="nofollow">visit your backpack</a> to create some public collections.</p>';
$string['error:nopermissiontoview'] = 'You have no permissions to view badge recipients';
$string['error:noprograms'] = 'No programs';
$string['error:nosuchbadge'] = 'Badge with id {$a} does not exist.';
$string['error:nosuchcourse'] = 'Warning: This course is no longer available.';
$string['error:nosuchfield'] = 'Warning: This user profile field is no longer available.';
$string['error:nosuchmod'] = 'Warning: This activity is no longer available.';
$string['error:nosuchrole'] = 'Warning: This role is no longer available.';
$string['error:nosuchuser'] = 'User with this email address does not have an account with the current backpack provider.';
$string['error:notifycoursedate'] = 'Badges associated with course and activity completions will be awarded after the course start date.';
$string['error:parameter'] = 'Warning: At least one parameter should be selected to ensure correct badge issuing workflow.';
$string['error:personaneedsjs'] = 'Currently, Javascript is required to connect to your backpack. If you can, enable Javascript and reload the page.';
$string['error:requesttimeout'] = 'The connection request timed out before it could complete.';
$string['error:requesterror'] = 'The connection request failed (error code {$a}).';
$string['error:relatedbadgedoesntexist'] = 'There is no public badge with this identifier';
$string['error:save'] = 'Cannot save the badge.';
$string['error:userdeleted'] = '{$a->user} (This user no longer exists in {$a->site})';
$string['eventbadgearchived'] = 'Badge archived';
$string['eventbadgeawarded'] = 'Badge awarded';
$string['eventbadgecreated'] = 'Badge created';
$string['eventbadgecriteriacreated'] = 'Badge criteria created';
$string['eventbadgecriteriadeleted'] = 'Badge criteria deleted';
$string['eventbadgecriteriaupdated'] = 'Badge criteria updated';
$string['eventbadgedeleted'] = 'Badge deleted';
$string['eventbadgedisabled'] = 'Badge disabled';
$string['eventbadgeduplicated'] = 'Badge duplicated';
$string['eventbadgeenabled'] = 'Badge enabled';
$string['eventbadgelistingviewed'] = 'Badge listing viewed';
$string['eventbadgerevoked'] = 'Badge revoked';
$string['eventbadgeupdated'] = 'Badge updated';
$string['eventbadgeviewed'] = 'Badge viewed';
$string['evidence'] = 'Evidence';
$string['existingrecipients'] = 'Existing badge recipients';
$string['expired'] = 'Expired';
$string['expiredate'] = 'This badge expires on {$a}.';
$string['expireddate'] = 'This badge expired on {$a}.';
$string['expireperiod'] = 'This badge expires {$a} day(s) after being issued.';
$string['expireperiodh'] = 'This badge expires {$a} hour(s) after being issued.';
$string['expireperiodm'] = 'This badge expires {$a} minute(s) after being issued.';
$string['expireperiods'] = 'This badge expires {$a} second(s) after being issued.';
$string['expirydate'] = 'Expiry date';
$string['expirydate_help'] = 'Optionally, badges can expire on a specific date, or the date can be calculated based on the date when the badge was issued to a user.';
$string['existsinbackpack'] = 'Badge already exists in backpack';
$string['externalconnectto'] = 'To display external badges you need to <a href="{$a}">connect to a backpack</a>.';
$string['externalbadges'] = 'Badges from my backpack';
$string['externalbadgesp'] = 'Badges from other web sites:';
$string['externalbadges_help'] = 'This area displays badges from your external backpack.';
$string['fixed'] = 'Fixed date';
$string['hidden'] = 'Hidden';
$string['hiddenbadge'] = 'Unfortunately, the badge owner has not made this information available.';
$string['imageauthoremail'] = 'Image author\'s email';
$string['imageauthoremail_help'] = 'An email address associated with author of the image.';
$string['imageauthorname'] = 'Image author\'s name';
$string['imageauthorname_help'] = 'The name of the badge image\'s author';
$string['imageauthorurl'] = 'Image author\'s URL';
$string['imageauthorurl_help'] = 'The URL of the image\'s author';
$string['invalidurl'] = 'Invalid URL';
$string['imageofbadgex'] = 'Image of badge {$a}';
$string['issuedbadge'] = 'Issued badge information';
$string['issuancedetails'] = 'Badge expiry';
$string['issuerdetails'] = 'Issuer details';
$string['issueremail'] = 'Email';
$string['issueremail_help'] = 'Endorser\'s contact address';
$string['issuername'] = 'Issuer name';
$string['issuername_help'] = 'Name of the issuing agent or authority.';
$string['issuername_endorsement'] = 'Name';
$string['issuername_endorsement_help'] = 'The name of the endorser.';
$string['issuerurl'] = 'Issuer URL';
$string['issuerurl_help'] = 'The homepage or social media profile of the endorser. Should be a URL/URI Accessible via HTTP.';
$string['language'] = 'Language';
$string['language_help'] = 'Identify which language your badge expressed in.';
$string['listbackpacks'] = 'List of backpacks';
$string['localconnectto'] = 'To share these badges outside this web site you need to <a href="{$a}">connect to a backpack</a>.';
$string['localbadgesh'] = 'My badges from this web site';
$string['localbadgesh_help'] = 'All badges earned within this website by completing courses, course activities, and other requirements.

You can manage your badges here by making them public or private for your profile page.

You can download all of your badges or each badge separately and save them on your computer. Downloaded badges can be added to your external backpack service.';
$string['localbadgesp'] = 'Badges from {$a}:';
$string['makeprivate'] = 'Make private';
$string['makepublic'] = 'Make public';
$string['managebadges'] = 'Manage badges';
$string['managebackpacks'] = 'Manage backpacks';
$string['message'] = 'Message body';
$string['messagebody'] = '<p>You have been awarded the badge "%badgename%"!</p>
<p>More information about this badge can be found on the %badgelink% badge information page.</p>
<p>You can manage and download the badge from your {$a} page.</p>';
$string['messagesubject'] = 'Congratulations! You just earned a badge!';
$string['method'] = 'This criterion is complete when...';
$string['mingrade'] = 'Minimum grade required';
$string['month'] = 'Month(s)';
$string['mybadges'] = 'My badges';
$string['mybackpack'] = 'My backpack settings';
$string['never'] = 'Never';
$string['newbadge'] = 'Add a new badge';
$string['newimage'] = 'New image';
$string['noawards'] = 'This badge has not been earned yet.';
$string['nobackpack'] = 'There is no backpack service connected to this account.<br/>';
$string['nobackpackbadgessummary'] = 'There are no badges in the collections you have selected.';
$string['nobackpackcollectionssummary'] = 'No badge collections have been selected.';
$string['nobackpacks'] = 'There are no backpacks available';
$string['nobadges'] = 'There are no badges available.';
$string['nocriteria'] = 'Criteria for this badge have not been set up yet.';
$string['noendorsement'] = 'This badge does not have an endorsement.';
$string['noexpiry'] = 'This badge does not have an expiry date.';
$string['nolocalbadges'] = 'No local badges.';
$string['nolocalbadgesfound'] = 'No local badges found.';
$string['noparamstoadd'] = 'There are no additional parameters available to add to this badge requirement.';
$string['norelated'] = 'This badge does not have any related badges.';
$string['nosupportedbackpack'] = 'In August 2019, the Mozilla Foundation retired its Open Badges backpack. Connections to other backpack services are not currently available.';
$string['nosupportedbackpack_header'] = 'The Mozilla Backpack service is no longer available';
$string['notacceptedrole'] = 'Your current role assignment is not among the roles that can manually issue this badge.<br/>
If you would like to see users who have already earned this badge, you can visit {$a} page. ';
$string['notconnected'] = 'Not connected';
$string['noteendorsement'] = 'Endorsement is used to provide a comment expressing approval of a Badge from a third party, that it is a good representation of the achievement it describes.';
$string['noterelated'] = 'When you have multiple badges, you may want to keep track of the history behind their developments, choose \'Add related badge\' button then specify the badges relating to this one and their version.';
$string['nothingtoadd'] = 'There are no available criteria to add.';
$string['notification'] = 'Notify badge creator';
$string['notification_help'] = 'This setting manages notifications sent to a badge creator to let them know that the badge has been issued.

The following options are available:

* **Never**: Do not send notifications.
* **Every time**: Send a notification every time this badge is awarded.
* **Daily**: Send notifications once a day.
* **Weekly**: Send notifications once a week.
* **Monthly**: Send notifications once a month.';
$string['notifydaily'] = 'Daily';
$string['notifyevery'] = 'Every time';
$string['notifymonthly'] = 'Monthly';
$string['notifyweekly'] = 'Weekly';
$string['numawards'] = 'This badge has been issued to <a href="{$a->link}">{$a->count}</a> user(s).';
$string['numawardstat'] = 'This badge has been issued {$a} user(s).';
$string['overallcrit'] = 'of the selected criteria are complete.';
$string['oauth2issuer'] = 'OAuth 2 services';
$string['openbadgesv1'] = 'Open Badges v1.0';
$string['openbadgesv2'] = 'Open Badges v2.0';
$string['openbadgesv2p1'] = 'Open Badges v2.1';
$string['potentialrecipients'] = 'Potential badge recipients';
$string['recipients'] = 'Badge recipients';
$string['recipientdetails'] = 'Recipient details';
$string['recipientidentificationproblem'] = 'Cannot find a recipient of this badge among the existing users.';
$string['recipientvalidationproblem'] = 'Current user cannot be verified as a recipient of this badge.';
$string['relative'] = 'Relative date';
$string['relatedbages'] = 'Related badges';
$string['revoke'] = 'Revoke badge';
$string['requiredcohort'] = 'At least one audience should be added to the audience criterion.';
$string['requiredcourse'] = 'At least one course should be added to the courseset criterion.';
$string['requiredprogram'] = 'At least one program should be added to the program criterion.';
$string['reviewbadge'] = 'Changes in badge access';
$string['reviewconfirm'] = '<p>This will make your badge visible to users and allow them to start earning it.</p>

<p>It is possible that some users already meet this badge\'s criteria and will be issued this badge immediately after you enable it.</p>

<p>Once a badge has been issued it will be <strong>locked</strong> - certain settings including the criteria and expiry settings can no longer be changed.</p>

<p>Are you sure you want to enable access to the badge \'{$a}\'?</p>';
$string['save'] = 'Save';
$string['searchname'] = 'Search by name';
$string['searchbadgesfor'] = 'Search badges for';
$string['selectaward'] = 'Please select the role you would like to use to award this badge: ';
$string['selectgroup_end'] = 'Only public collections are shown, <a href="{$a}">visit your backpack</a> to create more public collections.';
$string['selectgroup_start'] = 'Select collections from your backpack to display on this site:';
$string['selecting'] = 'With selected badges...';
$string['setup'] = 'Set up connection';
$string['sitebackpack'] = 'Active external backpack';
$string['sitebackpack_help'] = 'The external backpack that users can connect to from this site. Note that changing this setting after users have connected their backpacks will require each user to go to their backpack settings page and disconnect then reconnect.';
$string['sitebackpackwarning'] = 'Could not connect to backpack. <br/><br/>Check that the "Badge issuer email address" admin setting is the valid email for an account on the backpack website. <br/><br/>Check that the "Badge issuer password" on the <a href="{$a->url}">site backpack settings page</a>, is the correct password for the account on the backpack website. <br/><br/>The backpack returned: "{$a->warning}"';
$string['signinwithyouremail'] = 'Sign in with your email';
$string['sitebadges'] = 'Site badges';
$string['sitebadges_help'] = 'Site badges can only be awarded to users for site-related activities. These include completing a set of courses or parts of user profiles. Site badges can also be issued manually by one user to another.

Badges for course-related activities must be created at the course level. Course badges can be found under *Course Administration > Badges*.';
$string['statusmessage_0'] = 'This badge is currently not available to users. Enable access if you want users to earn this badge. ';
$string['statusmessage_1'] = 'This badge is currently available to users. Disable access to make any changes. ';
$string['statusmessage_2'] = 'This badge is currently not available to users, and its criteria are locked. Enable access if you want users to earn this badge. ';
$string['statusmessage_3'] = 'This badge is currently available to users, and its criteria are locked. ';
$string['statusmessage_4'] = 'This badge is currently archived.';
$string['status'] = 'Badge status';
$string['status_help'] = 'Status of a badge determines its behaviour in the system:

* **Available**: Means that this badge can be earned by users. While a badge is available to users, its criteria cannot be modified.
* **Not available**: Means that this badge is not available to users and cannot be earned or manually issued. If such badge has never been issued before, its criteria can be changed.

Once a badge has been issued to at least one user, it automatically becomes **Locked**. Locked badges can still be earned by users, but their criteria can no longer be changed. If you need to modify details or criteria of a locked badge, you can duplicate this badge and make all the required changes.

Badges are locked because we want to make sure that all users complete the same requirements to earn a badge. Currently, it is not possible to revoke badges. If we allowed badges requirements to be modified all the time, we would most likely end up with users having the same badge for meeting completely different requirements.';
$string['subject'] = 'Message subject';
$string['testbackpack'] = 'Test backpack \'{$a}\'';
$string['testsettings'] = 'Test settings';
$string['type'] = 'Type';
$string['userdataitemconnectedbackpacks'] = 'Connected backpacks';
$string['userdataitemissuedbadges'] = 'Issued badges';
$string['variablesubstitution'] = 'Variable substitution in messages.';
$string['variablesubstitution_help'] = 'In a badge message, certain variables can be inserted into the subject and/or body of a message so that they will be replaced with real values when the message is sent. The variables should be inserted into the text exactly as they are shown below. The following variables can be used:

%badgename%
: This will be replaced by the badge\'s full name.

%username%
: This will be replaced by the recipient\'s full name.

%badgelink%
: This will be replaced by the public URL with information about the issued badge.';
$string['viewbadge'] = 'View issued badge';
$string['visible'] = 'Visible';
$string['version'] = 'Version';
$string['version_help'] = 'When you have multiple related badges and want to keep track of the history behind the badge\'s developements, use this field to version each badge. You can add all related badges on Related badge page.';
$string['warnexpired'] = ' (This badge has expired!)';
$string['year'] = 'Year(s)';

// Deprecated since Totara 13.
$string['error:invalidbadgeurl'] = 'Invalid issuer URL format. The URL should have a prefix http:// or https://.';
$string['backpackbadges'] = 'You have {$a->totalbadges} badge(s) displayed from {$a->totalcollections} collection(s). <a href="mybackpack.php">Change backpack settings</a>.';
$string['error:nogroups'] = '<p>There are no public collections of badges available in your backpack. </p> <p>Only public collections are shown. Visit your backpack to create some public collections.</p>';
$string['localbadges'] = 'My badges from {$a} web site';
$string['nobackpackbadges'] = 'There are no badges in the collections you have selected. <a href="mybackpack.php">Add more collections</a>.';
$string['nobackpackcollections'] = 'No badge collections have been selected. <a href="mybackpack.php">Add collections</a>.';
$string['preferences'] = 'Badge preferences';
$string['sitebackpackverify'] = 'Backpack connection';
$string['editsettings'] = 'Edit settings';
