<?php
/**
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package tool_sitepolicy
 */

require(__DIR__ . '/../../../config.php');

use \tool_sitepolicy\localisedpolicy,
    \tool_sitepolicy\userconsent;

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url("/{$CFG->admin}/tool/sitepolicy/viewpolicy.php"));
$PAGE->set_popup_notification_allowed(false);

$policyversionid = required_param('policyversionid', PARAM_INT);
$language = required_param('language', PARAM_LANG);
$versionnumber = optional_param('versionnumber', 0, PARAM_INT);

if (!isloggedin()) {
    require_login();
}

$reload = new moodle_url("/{$CFG->admin}/tool/sitepolicy/viewpolicy.php");
$home = $CFG->wwwroot . '/';
$userid = $USER->id;
$userlisturl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/userlist.php", ['userid' => $userid]);
$translationurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/translationlist.php", ['policyversionid' => $policyversionid]);

$version = new \tool_sitepolicy\policyversion($policyversionid);
if (empty($language)) {
    $language = \tool_sitepolicy\userconsent::get_user_consent_language($policyversionid, $userid, false);
}

if ($version->is_draft() && !has_capability('tool/sitepolicy:manage', context_system::instance())) {
    throw new coding_exception("Policy not found");
}

$currentpolicy = \tool_sitepolicy\localisedpolicy::from_version($version, ['language' => $language]);
$options = $currentpolicy->get_statements(false);

$currentdata = [];
foreach ($options as $option) {
    if ($option->mandatory) {
        // Add required string to mandatory options' statements here to properly allow for RTL languages
        $option->statement = get_string('userconsenttoaccess', 'tool_sitepolicy', $option->statement);
    }
    if (!is_siteadmin()) {
        $hasconsent = userconsent::has_user_consented($option->dataid, $userid);
        $currentdata = array_merge($currentdata, ['option' . $option->dataid => (int)$hasconsent]);
    }
}

$currentdata = array_merge($currentdata, [
    'policyversionid' => $policyversionid,
    'versionnumber' => $versionnumber,
    'localisedpolicyid' => $currentpolicy->get_id(),
    'language' => $language,
]);

$params = [
    'consent' => $options,
    'allowsubmit' => false,
    'allowcancel' => false,
];

$form = new \tool_sitepolicy\form\userconsentform($currentdata, $params);

$PAGE->set_title($currentpolicy->get_title());

//Navigation Bar
if (!is_siteadmin()) {
    $PAGE->navbar->add(get_string('userconsentnavbar', 'tool_sitepolicy'), $userlisturl);
} else {
    $PAGE->navbar->add(get_string('userconsentadminnavbar', 'tool_sitepolicy'), $translationurl);
}
$PAGE->navbar->add($currentpolicy->get_title());


echo $OUTPUT->header();
echo $OUTPUT->heading($currentpolicy->get_title());


$renderer = $PAGE->get_renderer('tool_sitepolicy');
$langurl = new moodle_url($reload, ['policyversionid' => $policyversionid, 'versionnumber' => $versionnumber]);

//Whats Changed area
if ($versionnumber > 1 and !empty($currentpolicy->get_whatsnew())) {
    if (is_siteadmin() or userconsent::has_consented_previous_version($version, $userid)) {
        echo html_writer::tag('h4', html_writer::tag('strong', get_string('userconsentwhatschanged', 'tool_sitepolicy')) . html_writer::tag('/strong', ''));
        echo $currentpolicy->get_whatsnew();
    }
}

echo html_writer::div(text_to_html($currentpolicy->get_policytext()), 'policybox');
echo html_writer::tag('h2', get_string('userconsentprovideconsent', 'tool_sitepolicy'));
echo $form->render();
echo $OUTPUT->footer();
