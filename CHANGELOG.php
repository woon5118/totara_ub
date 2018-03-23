<?php
/*

Totara Learn Changelog

Release Evergreen (23rd March 2018):
====================================

Key:           + Evergreen only

Important:

    TL-14114       Added support for Google ReCaptcha v2 (MDL-48501)

                   Google deprecated reCAPTCHA V1 in May 2016 and it will not work for newer
                   sites. reCAPTCHA v1 is no longer supported by Google and continued
                   functionality can not be guaranteed.

    TL-17228       Added description of environment requirements for Totara 12

                   Totara 12 will raise the minimum required version of PostgreSQL from 9.2 to
                   9.4

Security issues:

    TL-17225       Fixed security issues in course restore UI

Improvements:

    TL-9414        Required totara form Checkbox lists are validated in the browser (as opposed to a page reload)
    TL-12393       Added new system role filter for reports using standard user filters
    TL-16157       Improved the layout of progress bars inside the current learning block

                   This will require regeneration of the LESS for themes that use LESS
                   inheritance

    TL-16731   +   Added LESS structure to help maintain consistency with common styles
    TL-16797       Standardised the use of styling in the details of activity access restrictions

                   When some new activity access restrictions were introduced in Totara 11.0,
                   the display of restriction details in the course was not in bold like
                   existing restrictions. This patch corrects the styling.

    TL-16864       Improved the template of Seminar date/time change notifications to accommodate booked and wait-listed users

                   Clarified Seminar notification messages to specifically say that it is
                   related to the session that you are booked on, or are on the wait-list for.
                   Also removed the iCal invitations/cancellations from the templates of users
                   on the wait-list so that there is no confusion, as previously users who
                   were on the wait-list when the date of a seminar was changed received an
                   email saying that the session you are booked on has changed along with an
                   iCal invitation which was misleading.

    TL-16909       Increased the limit for the defaultid column in hierarchy scale database tables

                   Previously the defaultid column in the comp_scale and goal_scale tables was
                   a smallint, however the column contained the id of a corresponding
                   <type>_scale_values record which was a bigint. It is highly unlikely anyone
                   has encountered this limit, unless there are more than 32,000 scale values
                   on your site, however the defaultid column has been updated to remove any
                   possibility of a conflict.

    TL-16914       Added contextual details to the notification about broken audience rules

                   Additional information about broken rules and rule sets are added to email
                   notifications. This information is similar to what is displayed on
                   audiences "Overview" and "Rule Sets" tabs and contains the broken audience
                   name, the rule set with broken rule, and the internal name of the broken
                   rule.

                   This will be helpful to investigate the cause of the notifications if a
                   rule was fixed before administrator visited the audience pages.

    TL-16921       Converted utc10 Totara form field to use the same date picker that the date time field uses

                   This only affects desktop browsers

    TL-17149       Fixed undefined index for the 'Audience visibility' column in Report Builder when there is no course present
    TL-17214   +   InnoDB upgrade tool and deprecated authentication plugins were removed from distribution

                   The following authentication plugins were removed:
                    # auth_fc
                    # auth_imap
                    # auth_nntp
                    # auth_none
                    # auth_pam
                    # auth_pop3

                   The following upgrade tool was removed: tool_innodb

    TL-17232       Made the "Self-registration with approval" authentication type use the standard notification system

                   The "Self-registration with approval" authentication plugin is now using
                   standard notifications instead of alerts, for "unconfirmed request" and
                   "confirmed request awaiting approval" messages. A new notification was also
                   added for "automatically approved request" messages when the "require
                   approval" setting is disabled.

Bug fixes:

    TL-16549       Cancelling a multi-date session results in notifications that do not include the cancelled date

                   Changed the algorithm of iCal UID generation for seminar event dates. This
                   allows reliable dates to be sent for changed\cancelled notifications with
                   an attached iCal file that would update the existing events in the
                   calendar.

    TL-16555       Fixed email recipients not always being displayed for scheduled reports

                   Previously if you disabled a recipient option (audiences, users, emails)
                   existing items would remain on the scheduled report but not be displayed
                   making it impossible to remove them. Existing items are now displayed, but
                   new items can not be added for disabled recipient options.

    TL-16598       Fixed a problem with suspended users and the "ignore empty fields" setting in HR Import

                   When the deleted setting was set to "Suspend internal user", the "Empty
                   strings are ignored" setting was set and the suspend field in a CSV was
                   empty. It resulted in users becoming unsuspended. The suspended field is
                   now disabled and not imported when the deleted setting is "Suspend internal
                   user".

    TL-16820       Fixed the current learning block using the wrong course URL when enabling audience based visibility
    TL-16833       Added the 'Grades' link back into the 'Course Administration' menu
    TL-16838       Stopped reaggregating competencies using the ANY aggregation rule when the user is already proficient
    TL-16856       Fixed text area user profile fields when using Self-registration with approval plugin

                   Using text area user profile fields on the registration page was stopping
                   the user and site administrator from attempting to approve the account.

    TL-16858       Improved the location of the date time picker icon in the Report builder sidebar

                   This will require regeneration of the LESS for themes that use LESS
                   inheritance

    TL-16865       Fixed the length of the uniquedelimiter string used as separator for the MS SQL GROUP_CONCAT_D aggregate function

                   MS SQL Server custom GROUP_CONCAT_* aggregate functions have issues when
                   the delimeter is more than 4 characters.

                   Some report builder sources used 5 character delimiter "\.|./" which caused
                   display issues in report. To fix it, delimeter was changed to 3 characters
                   sequence: "^|:"

    TL-16878       Fixed the role attribute on notification and message icons

                   Previously the notification and message icons used an invalid "aria-role"
                   HTML attribute. This now uses the correct "role" HTML attribute

    TL-16882       Removed the "allocation of spaces" link when a seminar event is in progress
    TL-16920       Fixed the "show blank date records" option in date filters excluding null values

                   Reports that allow filtering records with blank dates were not being
                   retrieved if the date was null

    TL-16922       Fixed multiple enrolment types being displayed per course in the 'Course Completion' report source

                   The "Enrolment Types" column for the "Course Completion" report source was
                   previously displaying all the enrolment methods the user was enrolled via
                   across the whole site. For example if the user was enrolled in one course
                   via the program enrolment plugin, and in another course via manual
                   enrolment, both records in the report would say both "program" and "Manual
                   enrolment". The column now only shows the appropriate enrolment type for
                   the associated course.

    TL-16925       Fixed the calculation of SCORM display size when the Navigation panel is no longer displayed
    TL-17104       Fixed an error when disposing of left-over temporary tables in MS SQL Server 
    TL-17115       Fixed the time assigned column for the Record of Learning : Programs report source

                   The time assigned column was previously displaying the data for
                   timestarted, this patch has three main parts:

                   1) Changes the default header of the current column to "Time started" to be
                   consistent with what it displays
                   2) Adds a new column "Time assigned" to the report source that displays the
                   correct data
                   3) Switches the default column for the embedded report to the new "Time
                   assigned" column

                   This means any new sites will create the embedded report with the new
                   column, but any existing sites that want to display "Time assigned" instead
                   of "Time started" will have to go to Site administration > Reports > Report
                   builder > Manage embedded report and restore default settings for the
                   Record of Learning : Programs embedded report, or manually edit the columns
                   for the report.

    TL-17116       Firefox now shows the focused item in the Atto editor toolbar

                   When using Chrome, Edge and IE11, there is an indication of which toolbar
                   item is focused when using keyboard navigation in the toolbar. This issue
                   adds an indication to Firefox as well.

    TL-17207       Fixed a missing include in user/lib.php for the report table block
    TL-17221       Allowed non-standard JS files to be minified through grunt rather than manually
    TL-17229       Fixed the display of the page while modifying Site administrator role assignments

                   This page had invalid HTML causing all form controls to be in a single
                   column, instead of an add/remove 3 column

    TL-17230       Added a missing file requirement to the company goal userdata items
    TL-17234       Fixed an error while counting the userdata for a quicklinks block relating to a deleted user

                   When a user is deleted their records are removed from the context table,
                   causing the lookup being done by this function to throw a database error.

    TL-17259       Moved the previously hard-coded string 'Add tile' into a language string for Featured links templates

                   There was a hard-coded string in the main template in the Featured links
                   block, this has been shifted into the language strings file so that it can
                   now be translated and customised.

API changes:

    TL-16881   +   Update jQuery to 3.3.1

Contributions:

    * Ben Lobo at Kineo UK - TL-16549
    * Eugene Venter at Catalyst NZ - TL-16922
    * Russell England at Kineo USA - TL-17149

*/
