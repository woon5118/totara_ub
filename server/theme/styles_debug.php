<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file is responsible for serving of individual style sheets in designer mode.
 *
 * @package   core
 * @copyright 2009 Petr Skoda (skodak)  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
define('NO_DEBUG_DISPLAY', true);
define('NO_UPGRADE_CHECK', true);
define('NO_MOODLE_COOKIES', true);

require('../config.php');
require_once($CFG->dirroot.'/lib/csslib.php');

$themename = optional_param('theme', 'standard', PARAM_SAFEDIR);
$type      = optional_param('type', '', PARAM_SAFEDIR);
$subtype   = optional_param('subtype', '', PARAM_SAFEDIR);
$sheet     = optional_param('sheet', '', PARAM_SAFEDIR);
$usesvg    = optional_param('svg', 1, PARAM_BOOL);
$rtl       = optional_param('rtl', false, PARAM_BOOL);
$legacy    = optional_param('legacy', false, PARAM_BOOL);
$report    = optional_param('report', null, PARAM_RAW);

if (file_exists("$CFG->dirroot/theme/$themename/config.php")) {
    // The theme exists in standard location - ok.
} else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/$themename/config.php")) {
    // Alternative theme location contains this theme - ok.
} else {
    css_send_css_not_found();
}

$theme = theme_config::load($themename);
$theme->force_svg_use($usesvg);
$theme->set_rtl_mode($rtl);
$theme->set_legacy_browser($legacy);

if ($type === 'editor') {
    $csscontent = $theme->get_css_content_editor();
    css_send_uncached_css($csscontent);
}

// Totara: Removed chunking support as it's not used by currently supported browsers

// We need some kind of caching here because otherwise the page navigation becomes
// way too slow in theme designer mode.
$key = "$type $subtype $sheet $usesvg $rtl $legacy";
// Totara: updated to use cache definition
$cache = cache::make('core', 'themedesigner', array('theme' => $themename));
if ($content = $cache->get($key)) {
    if ($content['created'] > time() - THEME_DESIGNER_CACHE_LIFETIME) {
        $csscontent = $content['data'];

        // Totara: Removed chunking support as it's not used by currently supported browsers

        css_send_uncached_css($csscontent);
    }
}

try {
    $csscontent = $theme->get_css_content_debug($type, $subtype, $sheet, $rtl);
} catch(Exception $e) {
    if ($report === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache');
        echo json_encode([
            'status' => 'error',
            'stack' => get_class($e) . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString()
        ]);
        die;
    } else {
        throw $e;
    }
}
$cache->set($key, array('data' => $csscontent, 'created' => time()));

// Totara: Removed chunking support as it's not used by currently supported browsers

if ($report === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache');
    echo '{"status":"ok","message": "Rendered without errors"}';
    die;
}

css_send_uncached_css($csscontent);
