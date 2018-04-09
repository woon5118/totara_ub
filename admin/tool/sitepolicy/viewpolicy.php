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

$policyversionid = required_param('policyversionid', PARAM_INT);
$language = required_param('language', PARAM_SAFEDIR); // Don't use PARAM_LANG here, the language pack does not need to be installed.
$versionnumber = optional_param('versionnumber', null, PARAM_INT);
$returntouser = optional_param('returntouser', null, PARAM_INT);

require_login(null, false);

$PAGE->set_context(context_system::instance());
$PAGE->set_url(\tool_sitepolicy\url_helper::sitepolicy_view($policyversionid, $language, $versionnumber));
$PAGE->set_popup_notification_allowed(false);

$version = new \tool_sitepolicy\policyversion($policyversionid);
if ($version->is_draft() && !has_capability('tool/sitepolicy:manage', context_system::instance())) {
    throw new coding_exception("Policy not found");
}

if (empty($language)) {
    $language = \tool_sitepolicy\userconsent::get_user_consent_language($policyversionid, $USER->id, false);
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
        $hasconsent = \tool_sitepolicy\userconsent::has_user_consented($option->dataid, $USER->id);
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

$PAGE->set_title($currentpolicy->get_title(false));

if ($returntouser) {
    // Make this page look like its within the user profile area.
    $user = $DB->get_record('user', ['id' => $returntouser], '*', MUST_EXIST);
    $PAGE->navigation->extend_for_user($user);
    if ($node = $PAGE->navigation->find('user' . $user->id, navigation_node::TYPE_USER)) {
        $node->make_active();
    }
    $userlisturl = \tool_sitepolicy\url_helper::user_sitepolicy_list($user->id);
    $PAGE->navbar->add(get_string('userconsentnavbar', 'tool_sitepolicy'), $userlisturl);
    $PAGE->navbar->add($currentpolicy->get_title(true));
} else {
    // Make this page look like the admin page so that the navigation knows where we are.
    $translationlisturl = \tool_sitepolicy\url_helper::localisedpolicy_list($currentpolicy->get_policyversion()->get_id());
    $versionlisturl = \tool_sitepolicy\url_helper::version_list($currentpolicy->get_policyversion()->get_sitepolicy()->get_id());
    global_navigation::override_active_url(\tool_sitepolicy\url_helper::sitepolicy_list());
    $PAGE->navbar->add($currentpolicy->get_primary_title(true), $versionlisturl);
    $PAGE->navbar->add(get_string('translationheader', 'tool_sitepolicy'), $translationlisturl);
    $PAGE->navbar->add($currentpolicy->get_language(true));
}

/** @var \tool_sitepolicy\output\page_renderer $renderer */
$renderer = $PAGE->get_renderer('tool_sitepolicy', 'page');
echo $renderer->sitepolicy_preview($currentpolicy, $form);
