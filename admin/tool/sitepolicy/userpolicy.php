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
 * @author Courteney Brownie <courteney.brownie@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package tool_sitepolicy
 */

require(__DIR__ . '/../../../config.php');

use \tool_sitepolicy\userconsent;

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url("/{$CFG->admin}/tool/sitepolicy/userpolicy.php"));
$PAGE->set_popup_notification_allowed(false);

if (\core\session\manager::is_loggedinas()) {
    print_error('nopermissions', 'error', '', 'Site policy');
}

$language = optional_param('language', '', PARAM_LANG);
$currentcount = optional_param('currentcount', 1, PARAM_INT);
$totalcount = optional_param('totalcount', 0, PARAM_INT);
$policyversionid = optional_param('policyversionid', 0, PARAM_INT);
$versionnumber = optional_param('versionnumber', 0, PARAM_INT);

$consentdata = optional_param('consentdata', '', PARAM_TEXT);

if (!isloggedin()) {
    require_login();
}

$reload = new moodle_url("/{$CFG->admin}/tool/sitepolicy/userpolicy.php");
$home = $CFG->wwwroot . '/';
$logout = new moodle_url("/{$CFG->admin}/tool/sitepolicy/userexit.php");
$userid = $USER->id;
$userlisturl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/userlist.php", ['userid' => $userid]);
$translationurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/translationlist.php", ['policyversionid' => $policyversionid]);

if (empty($policyversionid)) {
    $unanswered = \tool_sitepolicy\userconsent::get_unansweredpolicies($userid);
    if (count($unanswered) == 0 && $policyversionid == 0) {
        $SESSION->tool_sitepolicy_consented = true;
        redirect($home);
    }

    if ($totalcount == 0 && count($unanswered) != 0) {
        $totalpolicyversion = [];
        foreach ($unanswered as $policy) {
            $totalpolicyversion[] = $policy->policyversionid;
        }
        $totalcount = count(array_unique($totalpolicyversion));
    }

    if ($totalcount != 0) {
        if (isguestuser()) {
            // For guest users all policies are always returned
            for ($i = 0; $i < $currentcount; $i++) {
                $current = array_shift($unanswered);
            }
        } else {
            $current = current($unanswered);
        }

        $policyversionid = $current->policyversionid;
        $version = new \tool_sitepolicy\policyversion($policyversionid);
    } else {
        throw new coding_exception('Parameter policyversionid is expected in all non-consent uses');
    }
} else {
    // User reviewing his answers. Can change
    $version = new \tool_sitepolicy\policyversion($policyversionid);
}

if ($version->is_draft() && !has_capability('tool/sitepolicy:manage', context_system::instance())) {
    throw new coding_exception("Policy not found");
}

$versionnumber = $version->get_versionnumber();
$availlanguages = get_string_manager()->get_list_of_translations();

if (empty($language) || !isset($availlanguages[$language])) {
    $language = \tool_sitepolicy\userconsent::get_user_consent_language($policyversionid, $userid, true);
}

$currentpolicy = \tool_sitepolicy\localisedpolicy::from_version($version, ['language' => $language]);
$options = $currentpolicy->get_statements(false);

// Add required string to mandatory options' statements here to properly allow for RTL languages
$currentdata = [];
foreach ($options as $option) {
    if ($option->mandatory) {
        $option->statement = get_string('userconsenttoaccess', 'tool_sitepolicy', $option->statement);
    }

    if (!empty($consentdata)) {
        $answers = explode(',', $consentdata);
        foreach ($answers as $answer) {
            $data = explode('-', $answer);
            $currentdata = array_merge($currentdata, ['option' . $data[0] => (int)$data[1]]);
        }
    } else if ($totalcount == 0) {
        // User reviewing his answers
        $hasconsent = userconsent::has_user_consented($option->dataid, $userid);
        $currentdata = array_merge($currentdata, ['option' . $option->dataid => (int)$hasconsent]);
    }
}

$currentdata = array_merge($currentdata, [
    'policyversionid' => $policyversionid,
    'versionnumber' => $versionnumber,
    'localisedpolicyid' => $currentpolicy->get_id(),
    'language' => $language,
    'currentcount' => $currentcount,
    'totalcount' => $totalcount
]);

$params = [
    'consent' => $options,
    'allowsubmit' => !$version->is_archived(),
    'allowcancel' => ($totalcount == 0)
];

$form = new \tool_sitepolicy\form\userconsentform($currentdata, $params);

if ($form->is_cancelled()) {
    redirect($userlisturl);
} elseif ($formdata = $form->get_data()) {
    $userconsent = new userconsent();
    $userconsent->set_userid($userid);

    $mandatorywithhelds = array_filter($options, function($option) use ($formdata) {
        $optionfieldname = 'option' . $option->dataid;
        $consent = $formdata->$optionfieldname;
        $mandatory = $option->mandatory;
        return (empty($consent) and $mandatory == true);
    });

    if (count($mandatorywithhelds) > 0) {
        // We need the consentoption data for saving on user as well as when user returns

        $answers = [];
        foreach ($options as $option) {
            $optionfieldname = 'option' . $option->dataid;
            $consent = $formdata->$optionfieldname;

            $answers[] = implode('-', [$option->dataid, (int)$consent]);
        }

        $answers = implode(',', $answers);

        redirect(new moodle_url($logout, ['policyversionid' => $policyversionid, 'language' => $language, 'currentcount' => $currentcount, 'totalcount' => $totalcount, 'consentdata' => $answers]));
    }

    foreach ($options as $option) {
        $optionfieldname = 'option' . $option->dataid;
        $consent = $formdata->$optionfieldname;
        $mandatory = $option->mandatory;

        $userconsent->set_hasconsented((int)$consent);
        $userconsent->set_consentoptionid($option->dataid);
        $userconsent->set_language($language);
        $userconsent->save();

    }

    // Will only get here is $SESSION->tool_sitepolicy_consented not previously set
    // We set it here if user has consented to all to handle guests correctly and also
    // avoid uneccessary db queries
    if ($totalcount > 0 && $currentcount == $totalcount) {
        $SESSION->tool_sitepolicy_consented = true;
        redirect($home);
    } else if ($totalcount == 0) {
        redirect($userlisturl);
    } else {
        redirect(new moodle_url($reload, ['currentcount' => $currentcount + 1, 'totalcount' => $totalcount]));
    }
}

$PAGE->set_title($currentpolicy->get_title());

//Navigation Bar
$PAGE->navbar->add(get_string('userconsentnavbar', 'tool_sitepolicy'), $userlisturl);
$PAGE->navbar->add($currentpolicy->get_title());


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('userconsentxofy', 'tool_sitepolicy', ['currentpolicy' => $currentcount, 'totalpolicies' => $totalcount]), 4);
echo $OUTPUT->heading($currentpolicy->get_title());

//Langugae Selection Dropdown
$verlanguages = $version->get_languages();
if (!array_key_exists($language, $availlanguages)) {
    // Handling case where language pack has been removed
    $language = 'en';
}
$langarray = [];
$availlanguages = get_string_manager()->get_list_of_translations($language);

foreach ($verlanguages as $lang => $row) {
    if (array_key_exists($lang, $availlanguages)) {
        $langarray[$lang] = $availlanguages[$lang];
    }
}

$renderer = $PAGE->get_renderer('tool_sitepolicy');
$langurl = new moodle_url($reload, ['policyversionid' => $policyversionid, 'versionnumber' => $versionnumber, 'currentcount' => $currentcount, 'totalcount' => $totalcount]);
if (!empty($langarray)) {
    $select = new \single_select($langurl, 'language', $langarray, $language, [], 'userpolicy');
    $select->class = 'singleselect pull-right';
    echo $OUTPUT->render($select);
}

//Whats Changed area
if ($versionnumber > 1 and !empty($currentpolicy->get_whatsnew())) {
    if (userconsent::has_consented_previous_version($version, $userid) == true) {
        echo html_writer::tag('h4', html_writer::tag('strong', get_string('userconsentwhatschanged', 'tool_sitepolicy')) . html_writer::tag('/strong', ''));
        echo $currentpolicy->get_whatsnew();
    }
}

echo html_writer::div(text_to_html($currentpolicy->get_policytext()), 'policybox');
echo html_writer::tag('h2', get_string('userconsentprovideconsent', 'tool_sitepolicy'));
echo $form->render();
echo $OUTPUT->footer();

