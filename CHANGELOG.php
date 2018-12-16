<?php
/*

Release Evergreen (19th December 2018):
=======================================

Key:           + Evergreen only

Important:

    TL-17182       Fixed the use of the "moodle/course:viewhiddencourses" capability in report builder reports

                   Previously, users with "moodle/course:viewhiddencourses" capability could
                   not see hidden courses and related content with enabled "Audience
                   visibility" consistently in Report Builder reports (including embedded
                   reports). This permission was largely applicable in System or Course
                   context but had no effect in Course category and other context levels.

                   Also this rule had no effect when Course Audience-based Visibility was set
                   to "Enrolled users only" or "Enrolled users and members of the selected
                   audiences".

                   Now, each course-related record is checked against this capability in the
                   course and all parent contexts regardless of Audience-based Visibility
                   setting.

Security issues:

    TL-19593       Improved handling of seminar attendee export fields

                   Validation was improved for fields that are set by a site admin to be
                   included when exporting seminar attendance, making user information that
                   can be exported consistent with other parts of the application.

                   Permissions checks are now also made to ensure that the user exporting has
                   permission to access the information of each user in the report.

New features:

    TL-19629   +   Added form to create a new evidence type
    TL-19661   +   Created a new report source and embedded report for users who are assigned to competencies

                   The report lists all assigned individual users together with the
                   competencies they were assigned to.

Improvements:

    TL-19292       Added behat test coverage to content marketplace filters
    TL-19442       Enable course completion via RPL in Programs when the course is not visible to the learner

                   Previously when a course was not visible to the learner it could not be
                   marked as complete in the required learning UI. Now users with permission
                   to mark courses as complete can grant RPL even if the course is not
                   available to the learner.

    TL-19448       Modified grid catalogue search placeholder text
    TL-19647       Changed the title of an email sent out to confirm trainer for waitlisted seminar event

                   Prior to this patch: When a trainer was added into a waitlisted seminar
                   event, an email would be sent out to the trainer. The title of the email
                   was confusing because it included 'unknown date' and 'unknown time' (due to
                   waitlisted event).

                   With this patch: These keywords 'unknown date' and 'unknown time' are no
                   longer in the title of confirmation email sent out to the trainer. Instead,
                   a string 'location and time to be announced later' appears in the title.
                   This is achieved by the introduction of new placeholder "[eventperiod]"
                   that converts to "[starttime]-[finishtime], [sessiondate]" when date is
                   present and to "location and time to be announced later" when date is not
                   present.

                   To update existing notifications, replace placeholders
                   "[starttime]-[finishtime], [sessiondate]" with "[eventperiod]" manually.

    TL-19656   +   Implemented basket functionality for competency assignments

Bug fixes:

    TL-18858       Fixed mismatching date format patterns in the Excel writer

                   Previously when exporting report builder reports to Excel, any dates that
                   were not otherwise explicitly formatted would be displayed in the mm/dd/yy
                   format, regardless of the user's locale. These dates are now formatted to a
                   default state so that they are displayed as per the user's operating system
                   locale when opening the Excel file.

    TL-18892       Fixed problem with redisplayed goal question in appraisals

                   Formerly, a redisplayed goal question would display the goal status as a
                   drop-down list - whether or not the user had rights to change/answer the
                   question. However, when the goal was changed, it was ignored. This patch
                   changes the drop-down into a text string when necessary so that it cannot
                   be changed.

    TL-18903       Deprecated the facetoface_fromaddress setting as all emails are now sent from the no reply address

                   The TL-13922 changes were required to deprecate the facetoface_fromaddress
                   setting.

    TL-19305       Fixed manager allocations on full seminar events

                   Previously when managers allocated users to a full seminar event, they
                   could end up in the "Approval required" state instead of being wait-listed
                   or overbooking the event.

    TL-19311       Added event observers for course restoration to update the course format

                   Prior to this patch, when uploading a course using "Restore from this
                   course after upload" where the existing and uploaded course formats differ,
                   there was no action to update the course activities based on its format.
                   After this patch, the course activities will be updated, via the event
                   observer.

    TL-19373       Added two new seminar date columns which support export

                   The new columns are "Local Session Start Date/Time" and "Local Session
                   Finish Date/Time" and they support exporting to Excel and Open Document
                   formats.

    TL-19481       Fixed the course restoration process for seminar event multi-select customfields

                   Previously during course restoration, the seminar event multi-select
                   customfield was losing the value(s) if there was more than one value
                   selected.

    TL-19485       Made tables scrollable when on iOS

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-19507       Expand and collapse icons in the current learning block are now displayed correctly in IE11

                   Previously when someone using IE11 was viewing the current learning block
                   with a program inside it, the expand and collapse icons were not displayed
                   if there was more than one course in the program.

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance

    TL-19579       Enabled multi-language support on the maintenance mode message
    TL-19615       Fixed a permission error when a user tried to edit a seminar calendar event
    TL-19679       Removed remaining references to cohorts changing to audiences
    TL-19690       Fixed bug on Seminar Cancellations tab that caused Time Signed Up to be 1 January 1970 for some users

                   When a Seminar event that required manager approval was cancelled,
                   attendees awaiting approval would show 1 January 1970 in the Time Signed Up
                   column of the Attendees View Cancellations tab.

                   The Time Signed Up for attendees awaiting approval when the event was
                   cancelled is now the date and time that attendance was requested.

    TL-19692       Fixed a naming error for an undefined user profile datatype in the observer class unit tests
    TL-19693       Role names now wrap when assigning them to a user inside a course

                   This will require CSS to be regenerated for themes that use LESS
                   inheritance.

    TL-19694       Fixed a capability notification for the launch of SCORM content

                   This fixed a small regression from TL-19014 where a notification about
                   requiring the 'mod/scorm:launch' capability was being displayed when it
                   should not have been.

    TL-19696       Fixed the handling of calendar events when editing the calendar display settings of a seminar with multiple sessions

                   Previously with Seminar *Calendar display settings = None* and if the
                   seminar with multiple events was updated, the user calendar seminar dates
                   were hidden and the user couldn't see the seminar event in the calendar.

    TL-19698       Fixed appraisal preview regression from TL-16015

                   TL-16015 caused a regression in which previewing the questions in an
                   appraisal displayed the text "Not yet answered". This patch fixes this and
                   now the actual UI control appears; e.g. for a file question, it is a file
                   picker, and for a date question, it is a date entry field.

                   Note that although values can be "entered" into the UI controls, nothing is
                   saved when closing the preview window.

    TL-19726       Fixed the string identifier that has been declared incorrectly for facetoface's notification scheduling
    TL-19760       Fixed multi-language support for custom headings in Report Builder
    TL-19778       Fixed an error in seminar report filters when generating SQL for a search

                   Prior to this patch: the columns relating to the filters could not be added
                   because these columns were in the wrong place, they would only be added if
                   the GLOBAL setting (facetoface_hidecost) of the seminar was set to FALSE.
                   Therefore it was causing the sql error due to the columns and sql joins not
                   being found.

                   With this patch: the columns are now put in the correct place, and these
                   columns will no longer be affected by the GLOBAL setting
                   facetoface_hidecost.

    TL-19779       Fixed an error when signing up to a seminar event that requires approval with no job assignment and temporary managers disabled

Contributions:

    * Ghada El-Zoghbi at Catalyst AU - TL-19692
    * Learning Pool - TL-19779
    * Michael Dunstan at Androgogic - TL-19292
*/
