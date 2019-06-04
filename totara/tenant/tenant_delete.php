<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @package totara_tenant
 */

use \totara_tenant\local\util;

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$id = required_param('id', PARAM_INT);

require_login(null, false);
require_capability('totara/tenant:config', context_system::instance());
if (empty($CFG->tenantsenabled)) {
    redirect(new moodle_url('/'));
}

admin_externalpage_setup('tenantsmanage', '', null, new moodle_url('/totara/tenant/tenant_delete.php', ['id' => $id]));

$returnurl = new moodle_url('/totara/tenant/index.php');
$tenant = \core\record\tenant::fetch($id);

if ($confirm) {
    require_sesskey();
    $success = util::delete_tenant($id);
    if ($success) {
        redirect($returnurl);
    }
    $message = get_string('error');
    redirect($returnurl, $message, null, \core\output\notification::NOTIFY_ERROR);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('tenantdelete', 'totara_tenant'));

$a = new stdClass();
$a->name = format_string($tenant->name);
$message = get_string('tenantdeleteconfirm', 'totara_tenant', $a);

$yesurl = new moodle_url('/totara/tenant/tenant_delete.php', array('id' => $id, 'confirm' => 1, 'sesskey' => sesskey()));
$yebutton = new single_button($yesurl, get_string('delete'), 'post', true);
echo $OUTPUT->confirm($message, $yebutton, $returnurl);

echo $OUTPUT->footer();
