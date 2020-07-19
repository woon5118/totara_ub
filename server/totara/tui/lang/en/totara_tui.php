<?php
/**
 * This file is part of Totara Core
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_tui
 */

defined('MOODLE_INTERNAL') || die();

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