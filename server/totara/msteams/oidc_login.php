<?php
/**
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package totara_msteams
 */

use auth_oauth2\auth;
use totara_msteams\oidcclient;
use totara_msteams\auth_helper;

require_once(__DIR__ . '/../../config.php');

\totara_core\advanced_feature::require('totara_msteams');

$returnurl = required_param('returnurl', PARAM_LOCALURL);

require_sesskey();

$issuer = auth_helper::get_oauth2_issuer(true);
$wantsurl = new moodle_url($returnurl);

// Pass through OAuth2 login.
$returnparams = ['wantsurl' => $wantsurl->out(false), 'sesskey' => sesskey(), 'id' => $issuer->get('id')];
$returnurl = new moodle_url('/auth/oauth2/login.php', $returnparams);

$client = new oidcclient($issuer, $returnurl, '');
if (!$client->is_logged_in_oidc()) {
    redirect($client->get_login_url());
}

$auth = new auth();
$auth->complete_login($client, $wantsurl);
