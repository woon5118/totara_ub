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
 * @author Sergey Vidusov <sergey.vidusov@androgogic.com>
 * @package totara_contentmarketplace
 */

use totara_contentmarketplace\plugininfo\contentmarketplace;

define('AJAX_SCRIPT', true);
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');

$context = context_system::instance();
$PAGE->set_context($context);
require_sesskey();
require_login();
\totara_contentmarketplace\local::require_contentmarketplace();
require_capability('totara/contentmarketplace:add', $context);

$marketplace = required_param('marketplace', PARAM_ALPHA);
$selection = optional_param_array('selection', [], PARAM_ALPHANUMEXT);
$action = optional_param('action', '', PARAM_ALPHA);

$mp = contentmarketplace::plugin($marketplace);
if (!$mp->is_enabled()) {
    echo json_encode(false);
    exit;
}

$result = null;
$collection = $mp->collection();
if ($action == 'add') {
    $result = $collection->add($selection);
} elseif ($action == 'remove') {
    $result = $collection->remove($selection);
}

echo json_encode($result);
