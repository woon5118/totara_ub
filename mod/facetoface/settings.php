<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @author David Curry <david.curry@totaralms.com>
 * @author Aaron Barnes <aaron.barnes@totaralms.com>
 * @author Francois Marier <francois@catalyst.net.nz>
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */
defined('MOODLE_INTERNAL') || die();

require_once "$CFG->dirroot/mod/facetoface/lib.php";

$ADMIN->add('root', new admin_category('modfacetofacefolder', new lang_string('pluginname', 'mod_facetoface'), $module->is_enabled() === false), 'grades');

$sessionreporturl = new moodle_url('/mod/facetoface/sessionreport.php');
$ADMIN->add('modfacetofacefolder', new admin_externalpage('modfacetofacesessionreport', new lang_string('managesessions','mod_facetoface'), $sessionreporturl, 'mod/facetoface:viewallsessions'));

$settings = new admin_settingpage($section, get_string('globalsettings', 'mod_facetoface'), 'moodle/site:config', $module->is_enabled() === false);
$ADMIN->add('modfacetofacefolder', $settings);

if ($ADMIN->fulltree) { // Improve performance.
    $settings->add(new admin_setting_heading('facetoface_general_header', get_string('generalsettings', 'facetoface'), ''));

    $settings->add(new admin_setting_pickroles('facetoface_session_roles', new lang_string('setting:sessionroles_caption', 'facetoface'),
        new lang_string('setting:sessionroles', 'facetoface'), array()));

    $settings->add(new admin_setting_configcheckbox('facetoface_allowschedulingconflicts', new lang_string('setting:allowschedulingconflicts_caption', 'facetoface'),
        new lang_string('setting:allowschedulingconflicts', 'facetoface'), 0));

    $settings->add(new admin_setting_configtext('facetoface_export_userprofilefields', new lang_string('exportuserprofilefields', 'facetoface'), new lang_string('exportuserprofilefields_desc', 'facetoface'), 'firstname,lastname,idnumber,institution,department,email', PARAM_TEXT));

    $settings->add(new admin_setting_configtext('facetoface_export_customprofilefields', new lang_string('exportcustomprofilefields', 'facetoface'), new lang_string('exportcustomprofilefields_desc', 'facetoface'), '', PARAM_TEXT));

// Create array with existing custom fields (if any), empty array otherwise.
    $customfields = array();
    $allcustomfields = facetoface_get_session_customfields();
    // Exclude customfields type file.
    $allcustomfields = totara_search_for_value($allcustomfields, 'datatype', TOTARA_SEARCH_OP_NOT_EQUAL, 'file');
    foreach ($allcustomfields as $fieldid => $fielname) {
        $customfields[$fieldid] = $fielname->fullname;
    }

    // List of facetoface session fields that can be selected as filters.
    $calendarfilters = array(
        'timestart' => get_string('startdateafter', 'facetoface'),
        'timefinish' => get_string('finishdatebefore', 'facetoface'),
        'room' => get_string('room', 'facetoface'),
        'building' => get_string('building', 'facetoface'),
        'address' => get_string('address', 'facetoface'),
        'capacity' => get_string('capacity', 'facetoface')
    );
    $calendarfilters = $calendarfilters + $customfields;
    $settings->add(new admin_setting_configmultiselect('facetoface_calendarfilters', new lang_string('setting:calendarfilterscaption', 'facetoface'), new lang_string('setting:calendarfilters', 'facetoface'), array('room', 'building', 'address'), $calendarfilters));

    $settings->add(new admin_setting_heading('facetoface_notifications_header', get_string('notificationsheading', 'facetoface'), ''));

    $settings->add(new admin_setting_configcheckbox('facetoface_notificationdisable', new lang_string('setting:notificationdisable_caption', 'facetoface'),
        new lang_string('setting:notificationdisable', 'facetoface'), 0));

    $settings->add(new admin_setting_configtext('facetoface_fromaddress', new lang_string('setting:fromaddress_caption', 'facetoface'),
        new lang_string('setting:fromaddress', 'facetoface'), new lang_string('setting:fromaddressdefault', 'facetoface'),
        "/^((?:[\w\.\-])+\@(?:(?:[a-zA-Z\d\-])+\.)+(?:[a-zA-Z\d]{2,4}))$/", 30));

    $settings->add(new admin_setting_pickroles('facetoface_session_rolesnotify', new lang_string('setting:sessionrolesnotify_caption', 'facetoface'),
        new lang_string('setting:sessionrolesnotify', 'facetoface'), array('editingteacher')));

    $settings->add(new admin_setting_configcheckbox('facetoface_oneemailperday', new lang_string('setting:oneemailperday_caption', 'facetoface'), new lang_string('setting:oneemailperday', 'facetoface'), 0));

    $settings->add(new admin_setting_configcheckbox('facetoface_disableicalcancel', new lang_string('setting:disableicalcancel_caption', 'facetoface'), new lang_string('setting:disableicalcancel', 'facetoface'), 0));

    $settings->add(new admin_setting_heading('facetoface_additional_features_header', get_string('additionalfeaturesheading', 'facetoface'), ''));

    $setting = new admin_setting_configcheckbox('facetoface_displaysessiontimezones', new lang_string('setting:displaysessiontimezones_caption', 'facetoface'),
        new lang_string('setting:displaysessiontimezones', 'facetoface'), 1);
    $setting->set_updatedcallback('facetoface_displaysessiontimezones_updated');
    $settings->add($setting);

    $settings->add(
        new admin_setting_configcheckbox(
            'facetoface_selectpositiononsignupglobal',
            new lang_string('setting:selectpositiononsignupglobal', 'facetoface'),
            new lang_string('setting:selectpositiononsignupglobal_caption', 'facetoface'),
            0
        )
    );

    $settings->add(new admin_setting_configcheckbox('facetoface_allowwaitlisteveryone',
        new lang_string('setting:allowwaitlisteveryone_caption', 'facetoface'),
        new lang_string('setting:allowwaitlisteveryone', 'facetoface'), 0));

    $settings->add(new admin_setting_configcheckbox('facetoface_lotteryenabled',
        new lang_string('setting:lotteryenabled_caption', 'facetoface'),
        new lang_string('setting:lotteryenabled', 'facetoface'), 0));

    $settings->add(new admin_setting_configtext('facetoface/defaultminbookings',
        new lang_string('setting:defaultminbookings', 'facetoface'),
        new lang_string('setting:defaultminbookings_help', 'facetoface'), 0, PARAM_INT));

    $settings->add(new admin_setting_configcheckbox('facetoface_hidecost', new lang_string('setting:hidecost_caption', 'facetoface'), new lang_string('setting:hidecost', 'facetoface'), 0));
    $settings->add(new admin_setting_configcheckbox('facetoface_hidediscount', new lang_string('setting:hidediscount_caption', 'facetoface'), new lang_string('setting:hidediscount', 'facetoface'), 0));
}
// Activity defaults
$settings = new admin_settingpage('modfacetofacactivitydefaults', get_string('activitydefaults', 'mod_facetoface'), 'moodle/site:config', $module->is_enabled() === false);
$ADMIN->add('modfacetofacefolder', $settings);
if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox('facetoface_multiplesessions', get_string('setting:multiplesessions_caption', 'facetoface'), get_string('setting:multiplesessions', 'facetoface'), 0));

    $settings->add(new admin_setting_heading('facetoface_signupapproval_header', new lang_string('setting:signupapproval_header', 'facetoface'), ''));

    $available = explode(',', get_config(null, 'facetoface_session_roles'));
    $rolenames = role_fix_names(get_all_roles());
    $options = array();
    $options['approval_none'] =  new lang_string('setting:approval_none', 'facetoface');
    $options['approval_self'] =  new lang_string('setting:approval_self', 'facetoface');
    foreach ($available as $roleid) {
        if (!empty($roleid)) { // This makes it work when empty on installation.
            $options["approval_role_{$roleid}"] = $rolenames[$roleid]->localname;
        }
    }
    $options['approval_manager'] =  new lang_string('setting:approval_manager', 'facetoface');
    $options['approval_admin'] =  new lang_string('setting:approval_admin', 'facetoface');

    $settings->add(new admin_setting_configmulticheckbox('facetoface_approvaloptions', new lang_string('setting:approvaloptions_caption', 'facetoface'),
        new lang_string('setting:approvaloptions_default', 'facetoface'), array('approval_none' => 1, 'approval_self' => 1,'approval_manager' => 1), $options));

    $settings->add(new admin_setting_configtextarea('facetoface_termsandconditions', new lang_string('setting:termsandconditions_caption', 'facetoface'),
        new lang_string('setting:termsandconditions_format', 'facetoface'), new lang_string('setting:termsandconditions_default', 'facetoface')));

    $settings->add(new admin_setting_users_with_capability('facetoface_adminapprovers', new lang_string('setting:adminapprovers_caption', 'facetoface'),
        new lang_string('setting:adminapprovers_format', 'facetoface'), array(), 'mod/facetoface:approveanyrequest'));

    $settings->add(new admin_setting_configcheckbox('facetoface_managerselect',
        new lang_string('setting:managerselect_caption', 'facetoface'),
        new lang_string('setting:managerselect_format', 'facetoface'), 0));

    $settings->add(new admin_setting_heading('facetoface/managerreserveheader',
        new lang_string('setting:managerreserveheader', 'mod_facetoface'), ''));

    $settings->add(new admin_setting_configcheckbox('facetoface/managerreserve',
        new lang_string('setting:managerreserve', 'mod_facetoface'),
        new lang_string('setting:managerreserve_desc', 'mod_facetoface'), 0));

    $settings->add(new admin_setting_configtext('facetoface/maxmanagerreserves',
        new lang_string('setting:maxmanagerreserves', 'mod_facetoface'),
        new lang_string('setting:maxmanagerreserves_desc', 'mod_facetoface'), 1, PARAM_INT));

    $settings->add(new admin_setting_configtext('facetoface/reservecanceldays',
        new lang_string('setting:reservecanceldays', 'mod_facetoface'),
        new lang_string('setting:reservecanceldays_desc', 'mod_facetoface'), 1, PARAM_INT));

    $settings->add(new admin_setting_configtext('facetoface/reservedays',
        new lang_string('setting:reservedays', 'mod_facetoface'),
        new lang_string('setting:reservedays_desc', 'mod_facetoface'), 2, PARAM_INT));
}

// Session default settings page.
$settings = new admin_settingpage('modfacetofacesessiondefaults', get_string('sessiondefaults', 'mod_facetoface'), 'moodle/site:config', $module->is_enabled() === false);
$ADMIN->add('modfacetofacefolder', $settings);
if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('facetoface/defaultdaystosession', new lang_string('defaultdaystosession', 'facetoface'), new lang_string('defaultdaystosession_desc', 'facetoface'), '1', PARAM_INT));
    $settings->add(new admin_setting_configcheckbox('facetoface/defaultdaysskipweekends', new lang_string('defaultdaysskipweekends', 'facetoface'),new lang_string('defaultdaysskipweekends_desc', 'facetoface'), 1));
    $settings->add(new admin_setting_configtime('facetoface/defaultstarttime_hours', 'facetoface/defaultstarttime_minutes', new lang_string('defaultstarttime', 'facetoface'), new lang_string('defaultstarttimehelp', 'facetoface'), array('h' => 9, 'm' => 0)));
    $settings->add(new admin_setting_configtext('facetoface/defaultdaysbetweenstartfinish', new lang_string('defaultdaysbetweenstartfinish', 'facetoface'), new lang_string('defaultdaysbetweenstartfinish_desc', 'facetoface'), '0', PARAM_INT));
    $settings->add(new admin_setting_configtime('facetoface/defaultfinishtime_hours', 'facetoface/defaultfinishtime_minutes', new lang_string('defaultfinishtime', 'facetoface'), new lang_string('defaultfinishtimehelp', 'facetoface'), array('h' => 10, 'm' => 0)));
}

// Tell core we already added the settings structure.
$settings = null;

$customfieldurl = new moodle_url('/mod/facetoface/customfields.php', array('prefix' => 'facetofacesession'));
$ADMIN->add('modfacetofacefolder', new admin_externalpage('modfacetofacecustomfields', new lang_string('customfieldsheading','facetoface'), $customfieldurl, 'mod/facetoface:managecustomfield'));
$ADMIN->add('modfacetofacefolder', new admin_externalpage('modfacetofacetemplates', new lang_string('notificationtemplates','facetoface'), "$CFG->wwwroot/mod/facetoface/notification/template/index.php"));
$ADMIN->add('modfacetofacefolder', new admin_externalpage('modfacetofacerooms', new lang_string('rooms','facetoface'), "$CFG->wwwroot/mod/facetoface/room/manage.php"));
