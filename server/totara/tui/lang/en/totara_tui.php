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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_tui
 */

$string['number'] = 'Number';
$string['pluginname'] = 'Totara TUI frontend framework';
$string['samples'] = 'Samples';
$string['setting_cache_scss'] = 'Cache SCSS';
$string['setting_cache_scss_desc'] = 'When enabled Tui front end framework SCSS will not be cached on the server and will be regenerated each time it is requested.
This will delay page load times as processing SCSS takes several seconds. It is only useful when developing styles for the product.
It should never be enabled on production instances.';
$string['setting_cache_js'] = 'Cache JS';
$string['setting_cache_js_desc'] = 'When enabled Tui front end framework JavaScript will not be cached on the server and will be regenerated each time it is requested.';
$string['setting_development_mode'] = 'Development mode';
$string['setting_development_mode_desc'] = 'When enabled development versions of the Tui JavaScript and SCSS will be served to pages requiring the Tui components.
This is useful when developing components or debugging front end code at runtime.';