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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package totara_userdata
 */

use totara_userdata\userdata\item;

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/totara/reportbuilder/lib.php');

$id = required_param('id', PARAM_INT);

admin_externalpage_setup('userdatapurgetypes');

$purgetype = $DB->get_record('totara_userdata_purge_type', array('id' => $id), '*', MUST_EXIST);
$items = $DB->get_records('totara_userdata_purge_type_item', array('purgetypeid' => $id, 'purgedata' => 1));
$usercreated = $DB->get_record('user', array('id' => $purgetype->usercreated));

$PAGE->navbar->add(format_string($purgetype->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($purgetype->fullname));

$availablefor = array();
if ($purgetype->allowmanual) {
    $availablefor[] = get_string('purgeoriginmanual', 'totara_userdata');
}
if ($purgetype->allowdeleted) {
    $availablefor[] = get_string('purgeorigindeleted', 'totara_userdata');
}
if ($purgetype->allowsuspended) {
    $availablefor[] = get_string('purgeoriginsuspended', 'totara_userdata');
}
$statuses = \totara_userdata\userdata\target_user::get_user_statuses();

echo '<dl class="dl-horizontal">';
echo '<dt>' . get_string('idnumber') . '</dt>';
echo '<dd>' . (trim($purgetype->idnumber) === '' ? '&nbsp;' : s($purgetype->idnumber)) . '</dd>';
echo '<dt>' . get_string('purgetypeuserstatus', 'totara_userdata') . '</dt>';
echo '<dd>';
    echo $statuses[$purgetype->userstatus];
echo '</dd>';
echo '<dt>' . get_string('description') . '</dt>';
echo '<dd>';
$description = format_text($purgetype->description, FORMAT_HTML);
if (trim($description) === '') {
    echo '&nbsp;';
} else {
    echo $description;
}
echo '</dd>';
echo '<dt>' . get_string('purgetypeavailablefor', 'totara_userdata') . '</dt>';
echo '<dd>';
if (!$availablefor) {
    echo '&nbsp;';
} else {
    echo implode(', ', $availablefor);
}
echo '</dd>';
echo '<dt>' . get_string('createdby', 'totara_userdata') . '</dt>';
echo '<dd>';
echo ($usercreated ? fullname($usercreated) : '&nbsp;');
echo '</dd>';
echo '<dt>' . get_string('timecreated', 'totara_userdata') . '</dt>';
echo '<dd>';
echo userdate($purgetype->timecreated);
echo '</dd>';
echo '<dt>' . get_string('timechanged', 'totara_userdata') . '</dt>';
echo '<dd>';
echo userdate($purgetype->timechanged);
echo '</dd>';
echo '<dt>' . get_string('purgescount', 'totara_userdata') . '</dt>';
echo '<dd>';
$count =  $DB->count_records('totara_userdata_purge', array('purgetypeid' => $purgetype->id));
if (!$count) {
    echo 0;
} else {
    echo html_writer::link(new moodle_url('/totara/userdata/purges.php', array('purgetypeid' => $purgetype->id)), $count);
}
echo '</dd>';
echo '</dl>';

echo $OUTPUT->heading(get_string('purgeitemselection', 'totara_userdata'), 3);

$selecteditems = array();
foreach ($items as $item) {
    $selecteditems[$item->component . '-' . $item->name] = true;
}
$groups = \totara_userdata\local\purge::get_purgeable_items_grouped_list($purgetype->userstatus);
$lastmaincomponent = null;
foreach ($groups as $maincomponent => $classes) {
    foreach ($classes as $class) {
        /** @var item $class this is not a real instance, just autocomplete hint */
        $component = $class::get_component();
        $name = $class::get_name();
        if (empty($selecteditems[$component . '-' . $name])) {
            continue;
        }
        if ($lastmaincomponent !== $maincomponent) {
            $lastmaincomponent = $maincomponent;
            echo $OUTPUT->heading(totara_userdata\local\util::get_component_name($maincomponent), 4);
            echo '<ul>';
        }
        echo '<li>' . $class::get_fullname() . '</li>';
    }
}
if ($lastmaincomponent) {
    echo '</ul>';
}

echo $OUTPUT->footer();
