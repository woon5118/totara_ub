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

/**
 * Page to facilitate the creation of a new site policy.
 *
 * This is a management page.
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use \tool_sitepolicy\url_helper;

admin_externalpage_setup('tool_sitepolicy-managerpolicies', '', null, url_helper::sitepolicy_create());

$form = new tool_sitepolicy\form\versionform(['versionnumber' => 1]);

if ($form->is_cancelled()) {
    redirect(url_helper::sitepolicy_list());
}
if ($formdata = $form->get_data()) {
    \tool_sitepolicy\sitepolicy::create_new_policy($formdata->title, $formdata->policytext, $formdata->statements, $formdata->language);
    $message = get_string('policynewsaved', 'tool_sitepolicy', $formdata->title);
    redirect(url_helper::sitepolicy_list(), $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('policyformheader', 'tool_sitepolicy'));
$PAGE->navbar->add($PAGE->title);

/** @var \tool_sitepolicy\output\page_renderer $renderer */
$renderer = $PAGE->get_renderer('tool_sitepolicy', 'page');
echo $renderer->sitepolicy_create_new_policy($form);
