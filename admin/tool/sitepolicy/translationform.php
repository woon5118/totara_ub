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
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('tool_sitepolicy-managerpolicies');

$PAGE->set_context(context_system::instance());

use \tool_sitepolicy\localisedpolicy,
    \tool_sitepolicy\policyversion;

/**
 * @var \tool_sitepolicy\localisedpolicy $localisedpolicy
 */
$localisedpolicy = null;

// Localised policy can be identified in two ways: by id and by version and language.
$language = optional_param('language', '', PARAM_LANG);
if (!empty($language)) {
    $policyversionid = required_param('policyversionid', PARAM_INT);
    $version = new policyversion($policyversionid);

    if (localisedpolicy::exists($version,
                                ['language' => $language,
                                 'isprimary' => localisedpolicy::STATUS_NOTPRIMARY])) {
        $localisedpolicy = localisedpolicy::from_version($version,
            ['isprimary' => localisedpolicy::STATUS_NOTPRIMARY,
             'language' => $language]);
    } else {
        $localisedpolicy = localisedpolicy::from_data($version, $language);
    }

    $PAGE->set_url(new moodle_url ("/{$CFG->admin}/tool/sitepolicy/translationform.php",
        ['policyversionid' => $version->get_id(), 'language' => $language]));

} else {
    $localisedpolicyid = required_param('localisedpolicy', PARAM_INT);
    $localisedpolicy = new localisedpolicy($localisedpolicyid);
    $PAGE->set_url(new moodle_url ("/{$CFG->admin}/tool/sitepolicy/translationform.php", ['localisedpolicy' => $localisedpolicyid]));
}

$version = $localisedpolicy->get_policyversion();
$redirect = new moodle_url("/{$CFG->admin}/tool/sitepolicy/translationlist.php", ['policyversionid' => $version->get_id()]);

$primarypolicy = localisedpolicy::from_version($version, ['isprimary' => localisedpolicy::STATUS_PRIMARY]);

$primaryoptions = $primarypolicy->get_statements(false);
$options = $localisedpolicy->get_statements(false);
if (empty($options)) {
    $options = [];
    foreach ($primaryoptions as $option) {
        $option->primarystatement = $option->statement;
        $option->statement = '';
        $option->primaryprovided = $option->provided;
        $option->provided = '';
        $option->primarywithheld = $option->withheld;
        $option->withheld = '';

        $options[] = $option;
    }

} else {
    // We need to merge the primary and localised options
    // Find all options that are only in one list
    $newprimary = array_diff_key($primaryoptions, $options);

    foreach ($options as $idx => $option) {
        if (isset($primaryoptions[$idx])) {
            $primaryoption = $primaryoptions[$idx];
            $option->primarystatement = $primaryoption->statement;
            $option->primaryprovided = $primaryoption->provided;
            $option->primarywithheld = $primaryoption->withheld;
        } else {
            if ($idx < 0) {
                $option[$idx]->removedstatement = true;
            } else {
                unset($option[$idx]);
            }
        }
    }

    foreach ($newprimary as $idx) {
        $primaryoption = $primaryoptions[$idx];
        $option->primarystatement = $primaryoption->statement;
        $option->statement = '';
        $option->primaryprovided = $primaryoption->provided;
        $option->provided = '';
        $option->primarywithheld = $primaryoption->withheld;
        $option->withheld = '';

        $options[] = $option;
    }
}

$currentdata = [
    'localisedpolicy' => $localisedpolicy->get_id(),
    'language' => $localisedpolicy->get_language(),
    'policyversionid' => $version->get_id(),
    'title' => $localisedpolicy->get_title(),
    'policytext' => $localisedpolicy->get_policytext(),
    'statements' => $options,
    'whatsnew' => $localisedpolicy->get_whatsnew(),

    // Set primary fields required to display in textareas.
    'primarypolicytext' => $primarypolicy->get_policytext(),
    'primarywhatsnew' => $primarypolicy->get_whatsnew(),
];

$params = [
    'primarylanguage' => $primarypolicy->get_language(),
    'primarytitle' => $primarypolicy->get_title(),
    'versionnumber' => $version->get_versionnumber()
];

$form = new \tool_sitepolicy\form\translationform($currentdata, $params);

$languagestr = get_string_manager()->get_list_of_languages()[$localisedpolicy->get_language()];
$strparams = ['title' => $primarypolicy->get_title(), 'language' => $languagestr];

if ($form->is_cancelled()) {
    redirect($redirect);
} else if ($formdata = $form->get_data()) {
    $time = time();

    if ($version->get_timepublished()) {
        throw new coding_exception('Cannot edit translation of published version.');
    }

    $localisedpolicy->set_authorid($USER->id);
    $localisedpolicy->set_title($formdata->title);
    $localisedpolicy->set_policytext($formdata->policytext);

    if (!empty($formdata->whatsnew)) {
        $localisedpolicy->set_whatsnew($formdata->whatsnew);
    }

    $localisedpolicy->set_statements($formdata->statements);
    $localisedpolicy->save();

    $successmsg = get_string('translationsaved', 'tool_sitepolicy', $strparams);
    redirect($redirect, $successmsg,
        null,
        \core\output\notification::NOTIFY_SUCCESS);
}

$heading = get_string('translationheader', 'tool_sitepolicy');
$PAGE->set_pagelayout('admin');
$PAGE->set_heading($heading);
$PAGE->set_title($heading);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('translationtolang', 'tool_sitepolicy', $strparams));
echo $form->render();
echo $OUTPUT->footer();
