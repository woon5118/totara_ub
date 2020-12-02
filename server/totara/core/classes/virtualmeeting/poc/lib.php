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
 * @package core_virtualmeeting
 */

use totara_core\http\client;
use totara_core\virtualmeeting\plugin\factory\auth_factory;
use totara_core\virtualmeeting\plugin\provider\auth_provider;
use totara_core\virtualmeeting\poc\poc_auth_provider;
use totara_core\virtualmeeting\poc\poc_factory;

/**
 * PoC App plugin
 */
class virtualmeeting_poc_app_factory extends poc_factory {
    protected const NAME = 'app';
    protected const USER_AUTH = false;
}

/**
 * PoC User plugin
 */
class virtualmeeting_poc_user_factory extends poc_factory implements auth_factory {
    protected const NAME = 'user';
    protected const USER_AUTH = true;

    /**
     * @inheritDoc
     */
    public function create_auth_service_provider(client $client): auth_provider {
        return new poc_auth_provider(static::NAME);
    }
}
