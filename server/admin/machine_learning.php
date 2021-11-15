<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Vernon Denny <vernon.denny@totaralearning.com>
 * @package core_ml
 */

use core_ml\settings_helper;
use totara_core\advanced_feature;

require_once(__DIR__ . '/../config.php');
global $CFG, $OUTPUT;

$action = optional_param('action', null, PARAM_ALPHA);
$plugin = optional_param('plugin', null, PARAM_PLUGIN);

require_login();
require_capability('moodle/site:config', \context_system::instance());

require_once($CFG->libdir . '/adminlib.php');
admin_externalpage_setup('machine_learning_manage');

$features = advanced_feature::get_available();

if (null !== $action && null !== $plugin) {
    // Start doing the action
    require_sesskey();

    // Little bit of validation - if this plugin is an advanced feature
    // then it can only be toggled if the advanced feature is on.
    // The UI below will remove the dropdown completely, however this is a
    // check that nobody's bypassed the UI directly.
    $ml_plugin = 'ml_' . $plugin;
    if (in_array($ml_plugin, $features) && advanced_feature::is_disabled($ml_plugin)) {
        throw new \coding_exception("Cannot '{$action}' plugin '${plugin}' as the matching advanced feature is disabled.");
    }

    switch ($action) {
        case 'enable':
            settings_helper::enable_ml_plugin($plugin);
            break;

        case 'disable':
            settings_helper::disable_ml_plugin($plugin);
            break;

        default:
            throw new \coding_exception("Invalid action option '{$action}'");
    }
}

$table = new html_table();
$table->head = [
    get_string('ml', 'ml'),
    get_string('action', 'moodle'),
    get_string('settings', 'moodle')
];

$table->id = 'machine_learning_settings';
$table->data = [];

$manager = core_plugin_manager::instance();
$plugins = $manager->get_plugins_of_type('ml');

foreach ($plugins as $plugin) {
    $plugin->init_display_name();
    $row = [
        $plugin->displayname,
    ];

    // Figure out if this is an advanced feature & if it should be available or not
    $feature = $plugin->type . '_' . $plugin->name;
    $can_toggle = true;
    if (in_array($feature, $features) && advanced_feature::is_disabled($feature)) {
        $can_toggle = false;
    }

    $action_url = new \moodle_url(
        '/admin/machine_learning.php',
        [
            'sesskey' => sesskey(),
            'action' => 'disable',
            'plugin' => $plugin->name
        ]
    );

    if (!$plugin->is_enabled()) {
        $action_url->param('action', 'enable');
    }

    $select = new single_select(
        $action_url,
        'state',
        [
            0 => get_string('off', 'ml'),
            1 => get_string('on', 'ml')
        ],
        (int) $plugin->is_enabled(),
        []
    );

    // If the feature is off, we force the engine off
    $row[] = $can_toggle ? $OUTPUT->render($select) : get_string('off', 'ml');
    $setting_url = $plugin->get_settings_url();

    if (null !== $setting_url) {
        $row[] = html_writer::link($setting_url, get_string('settings'));
    } else {
        $row[] = '';
    }

    $table->data[] = $row;
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('ml_settings', 'ml'));

echo $OUTPUT->render($table);
echo html_writer::tag('p', get_string('warning', 'ml'));

echo $OUTPUT->footer();
//
//$recommender_manager = new recommender_manager();
//
//// Clean up bogus recommender states first.
//$plugininfos = core_plugin_manager::instance()->get_plugins_of_type('recommender');
//$recommenders = [];
//$states = $recommender_manager->get_states();
//foreach ($states as $state) {
//    if (!isset($plugininfos[$state->recommender]) and !get_config('recommender_'.$state->recommender, 'version')) {
//        // Purge messy leftovers after incorrectly uninstalled plugins and unfinished installs.
//        $DB->delete_records('recommender_active', ['recommender' => $state->recommender]);
//        $DB->delete_records('recommender_config', ['recommender' => $state->recommender]);
//        error_log('Deleted bogus "recommender_' . $state->recommender.'" states and config data.');
//    } else {
//        $recommenders[$state->recommender] = $state;
//    }
//}
//
//// Add properly installed and upgraded recommender to the global states table.
//foreach ($plugininfos as $recommender => $info) {
//    if (isset($recommenders[$recommender])) {
//        continue;
//    }
//
//    if ($info->is_installed_and_upgraded()) {
//        $recommender_manager->set_state($recommender, recommender_manager::RECOMMENDER_DISABLED);
//        $states = $recommender_manager->get_states();
//        foreach ($states as $state) {
//            if ($state->recommender === $recommender) {
//                $recommenders[$recommender] = $state;
//                break;
//            }
//        }
//    }
//}
//
//if ($action) {
//    require_sesskey();
//}
//
//
//// Process actions.
//switch ($action) {
//    case 'setstate':
//        if (isset($recommenders[$recommenderpath]) and $newstate = optional_param('newstate', '', PARAM_INT)) {
//            $recommender_manager->set_state($recommenderpath, $newstate);
//        }
//        break;
//}
//
//// Reset caches and return.
//if ($action) {
//    core_plugin_manager::reset_caches();
//    redirect(new moodle_url('/admin/recommenders.php'));
//}
//
//// Print the page heading.
//echo $OUTPUT->header();
//echo $OUTPUT->heading(get_string('recommendersettings', 'admin'));
//
//$table = new html_table();
//$table->head  = array(get_string('recommender'), get_string('isactive', 'recommender'),
//    get_string('settings'), get_string('uninstallplugin', 'admin'));
//$table->colclasses = array ('leftalign', 'leftalign', 'leftalign', 'leftalign', 'leftalign');
//$table->attributes['class'] = 'admintable generaltable';
//$table->id = 'recommenderssetting';
//$table->data  = [];
//
//// Sort recommenders by state.
//$states = $recommender_manager->get_states();
//$recommendersactive = $recommender_manager->get_recommenders();
//foreach ($states as $state) {
//    if ($state->active != recommender_manager::RECOMMENDER_DISABLED) {
//
//    }
//}
//
//// Iterate through recommenders adding to display table.
//$firstrow = true;
//foreach ($states as $state) {
//    $recommender = $state->recommender;
//    if (!isset($plugininfos[$recommender])) {
//        continue;
//    }
//    $plugininfo = $plugininfos[$recommender];
//    $row = get_table_row($plugininfo, $state);
//    $table->data[] = $row;
//    if ($state->active == recommender_manager::RECOMMENDER_DISABLED) {
//        $table->rowclasses[] = 'dimmed_text';
//    } else {
//        $table->rowclasses[] = '';
//    }
//    $firstrow = false;
//}
//
//echo $OUTPUT->render($table);
//
//echo '<p class="recommendersettingnote">' . get_string('recommenderallwarning', 'recommender') . '</p>';
//echo $OUTPUT->footer();
//die;
//
//
///**
// * Return action URL.
// *
// * @param string $recommenderpath
// * @param string $action
// * @return moodle_url
// */
//function recommenders_action_url($recommenderpath, $action) {
//    if ($action === 'delete') {
//        return core_plugin_manager::instance()->get_uninstall_url('recommender_'.$recommenderpath, 'manage');
//    }
//
//    return new moodle_url('/admin/recommenders.php', array('sesskey'=>sesskey(), 'recommenderpath'=>$recommenderpath, 'action'=>$action));
//}
//
///**
// * Construct table record.
// *
// * @param \core\plugininfo\recommender $plugininfo
// * @param stdClass $state
// * @return array data
// */
//function get_table_row(\core\plugininfo\recommender $plugininfo, $state) {
//    global $OUTPUT;
//    $row = [];
//    $recommender = $state->recommender;
//    $active = $plugininfo->is_installed_and_upgraded();
//
//    static $activechoices;
//    if (!isset($activechoices)) {
//        $activechoices = array(
//            recommender_manager::RECOMMENDER_ON => get_string('on', 'recommender'),
//            recommender_manager::RECOMMENDER_DISABLED => get_string('disabled', 'recommender'),
//        );
//    }
//
//    // Recommender name.
//    $displayname = $plugininfo->displayname;
//    if (!$plugininfo->rootdir) {
//        $displayname = '<span class="error">' . $displayname . ' - ' . get_string('status_missing', 'plugin') . '</span>';
//    } else if (!$active) {
//        $displayname = '<span class="error">' . $displayname . ' - ' . get_string('error') . '</span>';
//    }
//    $row[] = $displayname;
//
//    // Disable/off/on.
//    $select = new single_select(recommenders_action_url($recommender, 'setstate'), 'newstate', $activechoices, $state->active, null, 'active' . $recommender);
//    $select->set_label(get_string('isactive', 'recommender'), array('class' => 'accesshide'));
//    $row[] = $OUTPUT->render($select);
//
//    // Settings link, if required.
//    if ($active and recommender_manager::has_global_settings($recommender)) {
//        $row[] = html_writer::link(new moodle_url('/admin/settings.php', array('section'=>'recommendersetting_'.$recommender)), get_string('settings'));
//    } else {
//        $row[] = '';
//    }
//
//    // Uninstall.
//    $row[] = html_writer::link(recommenders_action_url($recommender, 'delete'), get_string('uninstallplugin', 'admin'));
//
//    return $row;
//}
