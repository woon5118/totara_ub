<?php
/*

Totara LMS Changelog

Release 2.6.2 (3rd June 2014):
==================================================

Security Fixes:
    T-12441    Fixed potential XSS vulnerability in quicklinks block

Improvements:
    T-11961    Added ability to assign Audience members based on position & organisation types
    T-12326    Extended execution time on completion reaggregation script
    T-12483    Added new alternate name fields when importing users with totara_sync
    T-12364    Improved contrast on Hierarchy selected items to meet Accessibility guidelines

Bug Fixes:
    T-12467    Fixed display of SCORM packages on secure HTTPS sites
    T-12463    Fixed critical SCORM error where subsequent attempts after an initial failed attempt are not recorded
    T-12471    Fixed display of grades in Course Completion Report for grades uploaded by completion import tool
    T-12469    Fixed sending of notifications when a Facetoface booking date/time is changed
    T-12121    Fixed transaction error when quiz completion triggers sending of messages
    T-12307    Fixed days not being translated in weekly scheduled reports
    T-12327    Fixed issue with dialog boxes being too wide for some screens
    T-12179    Fixed choosing of position on email self-registration when Javascript is disabled
    T-12263    Fixed Javascript for type filter dropdown in Audience Visibility
    T-12451    Fixed sort order of dependent courses in Course Completion settings
    T-12461    Fixed display of move and settings admin options for Quicklinks block
    T-12184    Fixed capitalisation of Program and Certification columns in Course Catalog
    T-12455    Fixed changing of visibility of a Certification on Audience Visible Learning tab
    T-12368    Fixed hidden labels in Hierarchy search dialog
    T-12371    Fixed alt attribute on course icons
    T-12362    Fixed alt and title attributes on competency icons
    T-12376    Fixed labels when creating a scheduled report in ReportBuilder
    T-12379    Fixed page title when deleting scheduled report
    T-12349    Fixed page title when deleting a Learning Plan
    T-12348    Fixed table column header on list of Learning Plans
    T-12237    Fixed HTML table in Alerts information popup dialog
    T-12473    Removed redundant get_totara_menu function in totara_core
    T-12478    Removed blink tag from element library


Release 2.6.1 (20th May 2014):
==================================================

Security Fixes:
    MoodleHQ    http://docs.moodle.org/dev/Moodle_2.6.3_release_notes

Improvements:
    T-12195    Improved error handling in F2F bulk add attendees
    T-12238    The alerts block is now a list instead of a table
    T-12313    Removed request approval button in Learning Plans while request is pending
    T-12375    Improved accessibility by combining links under My Reports
    T-12399    Improved look of the events filter on the calendar page
    T-12433    Show participants in appraisal overview page and pdf snapshots

Bug Fixes:
    T-12307    Fixed days not being translated in weekly scheduled reports
    T-12306    Added styling back into the program assignments page
    T-12017    Fixed alternate name fields for external badges
    T-12017    Fixed alternate name fields for trainer roles in face to face
    T-12017    Fixed alternate name fields on manager rules
    T-12234    Fixed highlight effect on Kiwifruit themes
    T-12446    Fixed display issue where save search button was overlaying column headers
    T-12326    Recover activity completion, grade and previous course completion data
    T-12314    Fixed unknown column error when creating a program with multi_select custom field
    T-12434    The search and clear button on the find courses now are hidden immediately
    T-12278    Fixed facetoface attendance export not showing data if a users do not have a manager assigned to them
    T-12248    Fixed SCORM redirect when it is opened in a new window
    T-12318    Fixed issue where custom field menus did not work as expected in responsive themes
    T-12310    Fixed display of custom field images in the enhanced catalog
    T-12153    Fixed the setting of users timecreated field when new users are created by Totara Sync
    T-12160    Fixed breadcrumbs when viewing staffs record of learning
    T-12204    Fixed incorrect error message being displayed when uploading huge files


Release 2.6.0.1 (7th May 2014):
==================================================

Bug Fixes:

    T-12880    Fix critical error causing deletion of course completion criteria data
    T-12149    Fix navigation menu when adding course custom fields


Release 2.6.0 (5th May 2014):
==================================================

New features:

T-7865    Allow recursive searches down the management hierarchy.
T-8592    Option to allow users to select their own organisation/position/manager during self-registration.
T-9736    Improve saved search interface.
T-9783    Allow manager to add a reason when declining/accepting learning plan and program extension requests.
T-10226   New report source for displaying face to face session information.
T-10239   Additional variables available in program messages.
T-10347   Relative date support for dynamic audience course/program completion rules.
T-10850   Ability to turn off face to face notifications at the site level.
T-10914   Ability for administrators to disable or hide certain functionality.
T-11067   Ability to assign system roles to all members of an audience.
T-11112   Totara sync now supports importing the 'emailstop' field.
T-11497   Ability to upload custom course/program icons.
T-11593   Enhanced Catalog with faceted search.
T-11593   Program custom fields now available.
T-11593   Report builder now supports sidebar filters, automatic results reloading and simple toolbar search options.
T-11593   New multi-select custom field type for hierarchy and course custom fields.
T-11597   Ability to mark face to face attendance in bulk.
T-11722   Organisation and position content restrictions added to appraisal reports.
T-11741   Managers can now reserve spaces in face to face sessions without naming the attendees. Thanks to Xtractor and Synergy Learning.
T-11752   New session start and end filters for the face to face sessions report source.
T-11879   Ability to force password changes for new users in Totara sync.
T-11988   Add report builder support to enrolment plugins. Thanks to Phil Lello from Catalyst EU.
T-11999   Add report builder embedded report support to plugins. Thanks to Phil Lello from Catalyst EU.
T-12109   Add links to completed stages on appraisal summary page.


2.6 Database schema changes:
============================

New tables:

Bug ID      New table name
--------------------------
T-11067     cohort_role
T-11593     course_info_data_param
T-11593     comp_info_data_param
T-11593     pos_info_data_param
T-11593     org_info_data_param
T-11593     goal_info_data_param
T-11593     prog_info_field
T-11593     prog_info_data
T-11593     prog_info_data_param
T-11593     report_builder_search_cols

New fields:

Bug ID      Table name                  New field name
------------------------------------------------------
T-9783      dp_plan_history             reasonfordecision
T-9783      dp_plan_competency_assign   reasonfordecision
T-9783      dp_plan_course_assign       reasonfordecision
T-9783      dp_plan_program_assign      reasonfordecision
T-9783      dp_plan_objective_assign    reasonfordecision
T-9783      prog_extension              reasonfordecision
T-11593     report_builder              toolbarsearch
T-11593     report_builder_filters      region

Other database changes:

T-11166     Report builder exportoptions converted from bitwise to comma separated list.
T-7865      Report builder settings updated: 'user_content', 'who' value switched from string to bitwise integer constant.
T-10914     Totara advanced feature settings migrated to new format.
T-11593     MSSQL group concat extension added. Due to requirement to install group concat plugin, MSSQL DB user requires additional permissions during install/upgrade: ALTER SETTINGS(SERVER)


2.6 API Changes:
================

== Enhanced catalog (T-11593) ==

* display_table() should now be always called, even if there are no rows in the results. This
  function will display a message if there are no rows to display. Remove "if ($countfiltered>0)"
  from embedded pages. This was done because the toolbar search is built into the display table
  header.

* Capability checks should be moved from embedded pages to is_capable() function in embedded
  classes. This function is called during the report constructor of embedded reports. If the
  is_capable method is not implemented then report builder assumes that the capabilities have
  not yet been recoded and will disable instant filters (instant filters go directly to the
  embedded class and bypass the embedded page, which is why the capability checks had to be moved).
  is_capable is passed the report object which can be used to access params, if required.

* rb_filter_type constructor and get_filter have been changed to include a region parameter. If any
  custom filter types have been added which define their own constructor method then they need to
  be updated to accept the additional parameter and pass it to the parent constructor. Any call
  get_filter must be updated (there are unlikely to be any custom calls to get_filter).

* get_extrabuttons() is a new function for embedded report sources that lets you specify a button or
  buttons to go in the top right of the table's toolbar. Simply override the inherited function in
  the desired report source and make it return the rendered output of any buttons you want to add.
  See the embedded catalog report sources for an example.

== Indirect reports patch (T-7865) ==

* The rb_content_option constructor method now accepts either a string or an array for the 3rd
  argument (previously it was just a string). The argument in
  totara/reportbuilder/classes/rb_content_option.php has changed from $field to $fields.

* To maintain backward compatibility, content options will still work with strings, so any custom
  content restrictions _do not_ need to be updated.

* However, the 'user' content option has been updated to pass additional information so any report
  sources that use the 'user' content option need to update the code.

Previously the code would look something like this:

             new rb_content_option(
                 'user',
                 get_string('users'),
                 '[TABLENAME].[FIELDNAME]'
             ),

Whereas now it must look like this:

             new rb_content_option(
                 'user',
                 get_string('users'),
                 array(
                     'userid' => '[TABLENAME].[FIELDNAME]',
                     'managerid' => 'position_assignment.managerid',
                     'managerpath' => 'position_assignment.managerpath',
                     'postype' => 'position_assignment.type',
                 ),
                 'position_assignment'
             ),

Where [TABLENAME] and [FIELDNAME] are typically something like 'base' and 'userid'.

The two key changes are the 3rd argument (where the string is replaced with the array with extra
data), and the 4th argument (where 'position_assignment' is added as a join). In the example above
there were no other joins (the 4th argument was empty). If there are already one or more join
options you will need to convert the 4th argument to an array and add 'position_assignment'. So if
the fourth argument was this:

'dp'

You would need to update to be:

array('dp', 'position_assignment')

Finally you need to make sure that the 'position_assignment' join is available. This can be done
with a line like this:

 $this->add_position_tables_to_joinlist($joinlist, 'base', 'userid');

in the define_joinlist() method. The 2nd and third arguments should reference a table and field used
above for [TABLENAME] and [FIELDNAME].


== Changes to Totara email user function (T-12077) ==

The function totara_generate_email_user() is now deprecated. Update references to use:
\totara_core\totara_user::get_external_user() instead.

== Deprecation of 'standardtotara' theme ==

In Totara 2.6 the 'standardtotara' theme is deprecated in favour of 'standardtotararesponsive'.
'standardtotara' is still present in 2.6 but will be removed in 2.7.

See this guide for how to migrate your 2.5 theme to 2.6:

http://community.totaralms.com/mod/resource/view.php?id=1869

== MSSQL only ==

Now require additional permissions to install:

MSSQL DB user required additional permissions: ALTER SETTINGS(SERVER)

This is due to requirement to install group concat plugin.

*/
?>
