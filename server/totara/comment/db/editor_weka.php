<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_comment
 */

defined('MOODLE_INTERNAL') || die();

// This is leaving empty for a reason - which is this very comment. As totara_comment will be treated as a
// proxy to configure the editor. And the component that used the totara_comment component will have to define
// the editor they wanted in the same metadata filename but different file path.
// Then totara_comment will try to put those configuration as a part of totara_comment - and there we go, we have
// a BLOODY dynamic totara_comment's editor
//
// If you desparately want to define any areas, please use any other areas rather than 'comment' or 'reply', as
// these two areas are being used to mask the configuration of component that is using this totara_comment
$editor = [];