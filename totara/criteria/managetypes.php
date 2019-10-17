<?php
/*
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

use totara_competency\plugintypes;
use totara_core\advanced_feature;

$actiontype = optional_param('type', '', PARAM_ALPHANUMEXT);
$action = optional_param('action', '', PARAM_ALPHA);

advanced_feature::require('competency_assignment');
require_login();
$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

$types = plugintypes::get_installed_plugins('criteria', 'totara_criteria');
$enabledtypes = plugintypes::get_enabled_plugins('criteria', 'totara_criteria');

if (!empty($actiontype) && !empty($action)) {
    require_sesskey();

    if (!in_array($actiontype, array_keys($types))) {
        print_error('unknowntype', 'totara_criteria', null, $actiontype);
    }

    switch ($action) {
        case 'enable':
            $enabledtypes = plugintypes::enable_plugin($actiontype, 'criteria', 'totara_criteria');
            $types = plugintypes::get_installed_plugins('criteria', 'totara_criteria');
            break;

        case 'disable':
            $enabledtypes = plugintypes::disable_plugin($actiontype, 'criteria', 'totara_criteria');
            $types = plugintypes::get_installed_plugins('criteria', 'totara_criteria');
            break;
    }
}

admin_externalpage_setup('totara_criteria-managetypes');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managetypes', 'totara_criteria'));

// construct the flexible table ready to display
$table = new flexible_table('totara_criteria_manage_table');
$table->define_columns(['name', 'version', 'enable']);
$table->define_headers(
    [
        get_string('type', 'totara_criteria'),
        get_string('version'),
        get_string('enable')
    ]
);

$table->define_baseurl($PAGE->url);
$table->set_attribute('id', 'totara_criteria');
$table->set_attribute('class', 'admintable generaltable');
$table->setup();

$strenable = get_string('enable');
$strdisable = get_string('disable');
$iconenable = $OUTPUT->flex_icon('show', ['alt' => $strenable]);
$icondisable = $OUTPUT->flex_icon('hide', ['alt' => $strdisable]);

foreach ($types as $type => $detail) {
    $enabled = '';
    if ($detail->enabled) {
        $url = new moodle_url($PAGE->url, ['type' => $type, 'action' => 'disable', 'sesskey' => sesskey()]);
        $enabled = \html_writer::link(
            $url,
            $icondisable,
            ['id' => "disable_citeria_type_{$type}", 'title' => $strdisable]
        );
        $class   = '';
    } else {
        $url = new moodle_url($PAGE->url, ['type' => $type, 'action' => 'enable', 'sesskey' => sesskey()]);
        $enabled = \html_writer::link(
            $url,
            $iconenable,
            ['id' => "enable_citeria_type_{$type}", 'title' => $strenable]
        );
        $class =   'dimmed_text';
    }

    $table->add_data(
        [
            $detail->title,
            $detail->version,
            $enabled,
        ],
        $class
    );
}

$table->print_html();

echo $OUTPUT->footer();
