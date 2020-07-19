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

// Disable debug messages and any errors in output, comment out when debugging or look into error log!
define('NO_DEBUG_DISPLAY', true);
// We need just the values from config.php and minlib.php if we have the JS cached already.
define('ABORT_AFTER_CONFIG', true);

require('../../config.php'); // this stops immediately at the beginning of lib/setup.php
require_once("$CFG->dirroot/lib/jslib.php");

if ($slashargument = min_get_slash_argument()) {
    $slashargument = ltrim($slashargument, '/');
    if (substr_count($slashargument, '/') < 1) {
        header('HTTP/1.0 404 not found');
        die('Slash argument must contain both a revision and a file path');
    }
    // Pattern is: revision (int) / suffix (a-z) / component (a-z_)
    $bits = explode('/', $slashargument, 3);
    $rev  = min_clean_param($bits[0], 'INT');
    $suffix = $bits[1];
    $component = min_clean_param($bits[2], 'SAFEPATH');


} else {
    $rev  = min_optional_param('rev', -1, 'INT');
    $suffix = min_optional_param('suffix', '', 'RAW');
    $component = min_optional_param('component', '', 'SAFEDIR');
}

if ($suffix !== min_clean_param($suffix, 'SAFEDIR')) {
    $suffix = '';
}

$etag = sha1('tui ' . $rev . ' ' . $component . ' ' . $suffix);
$cache = ($rev > 0 and $rev < (time() + 60*60));

// Use the caching only for meaningful revision numbers which prevents future cache poisoning.
if ($cache) {
    $candidate = $CFG->localcachedir.'/totara_tui-javascript/'.$etag;

    if (file_exists($candidate)) {
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            // we do not actually need to verify the etag value because our files
            // never change in cache because we increment the rev parameter
            js_send_unmodified(filemtime($candidate), $etag);
        }
        js_send_cached($candidate, $etag);
        die; // Not needed, except for readability.
    }
}

// Ok, now we need to start a normal script, load all libs, $DB etc
define('ABORT_AFTER_CONFIG_CANCEL', true);

define('NO_MOODLE_COOKIES', true); // Session not used here.
define('NO_UPGRADE_CHECK', true);  // Ignore upgrade check.

require("$CFG->dirroot/lib/setup.php");

// OK, don't trust suffix, or rev
// Suffix is in the URL only for proxies and web caches, and so that we can optimally hit our own cache.
$suffix = \totara_tui\local\locator\bundle::get_js_suffix_for_url();
$rev = \totara_tui\local\locator\bundle::get_js_rev();

// Regenerate etag, candidate file, and cache.
$etag = sha1('tui ' . $rev . ' ' . $component . ' ' . $suffix);
$candidate = $CFG->localcachedir.'/totara_tui-javascript/'.$etag;
$cache = ($rev > 0 and $rev < (time() + 60*60));

if ($component === 'vendors') {
    $file = \totara_tui\local\locator\bundle::get_vendors_file();
} else {
    $file = \totara_tui\local\locator\bundle::get_bundle_js_file($component);
}

if ($cache) {
    if ($file) {
        js_write_cache_file_from_path($candidate, $file);
    } else {
        // We don't have a file, but the requests will keep coming through.
        // To reduce the footprint and protect against DOS attacks lets write
        // some content to the cache, and ensure we use the cache.
        // If you get here, purge your caches.
        $content = '/** File not found */';
    }

    // verify nothing failed in cache file creation
    clearstatcache();
    if (file_exists($candidate)) {
        js_send_cached($candidate, $etag);
        die; // Not needed, except for readability.
    }
}

if ($file) {
    $content = file_get_contents($file);
} else {
    // We don't have a file, but the requests will keep coming through.
    // To reduce the footprint and protect against DOS attacks lets write
    // some content to the cache, and ensure we use the cache.
    // If you get here, purge your caches.
    $content = '/** File not found */';
}
js_send_uncached($content);
