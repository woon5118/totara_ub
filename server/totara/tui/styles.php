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

// Load config, but don't run the full setup yet!
require('../../lib/init.php');
$CFG = \core\internal\config::initialise(__DIR__ . '/../../../config.php');

// Required libraries.
require_once($CFG->dirroot . '/lib/configonlylib.php');
require_once($CFG->dirroot . '/lib/csslib.php');

// Defaults
$option_rtl = false;

$slasharguments = min_get_slash_argument();
if ($slasharguments) {

    $slasharguments = ltrim($slasharguments, '/');
    $slashargument_parts = explode('/', $slasharguments);

    if (count($slashargument_parts) !== 5) {
        debugging('Invalid number of arguments.', DEBUG_DEVELOPER);
        css_send_css_not_found();
    }

    // Order is: theme/rev/suffix/direction/component
    list($themename, $rev, $suffix, $option_rtl, $component) = $slashargument_parts;
    unset($slasharguments, $slashargument_parts); // Clean these up, you can't reuse them.

} else {

    $themename = min_optional_param('theme', null, 'SAFEDIR');
    $rev = min_optional_param('rev', null, 'INT');
    $suffix = min_optional_param('suffix', null, 'SAFEDIR');
    $option_rtl = min_optional_param('direction', 'ltr', 'SAFEDIR');
    $component = min_optional_param('component', null, 'SAFEDIR');

    // The following arguments are required. If any are null then we do not have the information required.
    if (is_null($themename) || is_null($rev) || is_null($component) || is_null($suffix)) {
        debugging('Required arguments were not provided', DEBUG_DEVELOPER);
        css_send_css_not_found();
    }
}

// Consistently clean these things.
$themename = min_clean_param($themename, 'SAFEDIR');
$rev = (int)min_clean_param($rev, 'INT');
$component = min_clean_param($component, 'SAFEDIR');
$suffix = min_clean_param($suffix, 'SAFEDIR');
$option_rtl = ($option_rtl === 'rtl');

if (file_exists("{$CFG->dirroot}/theme/{$themename}/config.php")) {
    // The theme exists in standard location - ok.
} else if (!empty($CFG->themedir) and file_exists("{$CFG->themedir}/{$themename}/config.php")) {
    // Alternative theme location contains this theme - ok.
} else {
    css_send_css_not_found();
}

if ($rev !== -1) {
    $cachefile = get_cachefile($rev, $themename, $component, $suffix);
    $etag = get_etag($rev, $themename, $component, $suffix, $option_rtl);
    if (file_exists($cachefile)) {
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            // We do not actually need to verify the etag value because our files
            // never change in cache because we increment the rev counter.
            css_send_unmodified(filemtime($cachefile), $etag);
        }
        css_send_cached_css($cachefile, $etag);
    }
}

define('NO_MOODLE_COOKIES', true); // Session not used here.
define('NO_UPGRADE_CHECK', true);  // Ignore upgrade check.

require("$CFG->dirroot/lib/setup.php");

// Revision is in the URL just to facilitate caching, we don't actually trust it.
$rev = \totara_tui\local\locator\bundle::get_css_rev();
$suffix = \totara_tui\local\locator\bundle::get_css_suffix_for_url();

$theme = \totara_tui\local\theme_config::load($themename);
$theme->force_svg_use(true);
$theme->set_rtl_mode($option_rtl);
if (core_useragent::is_ie()) {
    $theme->set_legacy_browser(true);
}

$etag = get_etag($rev, $themename, $component, $suffix, $option_rtl);
make_localcache_directory('totara_tui', false);
// Recalculate cachefile, even if we already have it, as rev may have changed.
$cachefile = get_cachefile($rev, $themename, $component, $suffix);

if ($rev === -1) {
    $cache = \cache::make_from_params(cache_store::MODE_APPLICATION, 'totara_tui', 'scss_cache');
    $last_sha = $cache->get($etag);
    $sha = false;

    $cachefile_exists = file_exists($cachefile);

    if ($cachefile_exists && $last_sha === false) {
        // We have a cachefile but we don't know the last sha that was used.
        // This shouldn't happen but... regenerate the file to ensure we have the correct content.
        @unlink($cachefile);
        $cachefile_exists = false;
    }
    if ($cachefile_exists) {
        // The cache file exists, compare the SHA of the theme files to the SHA we have stored in the cache and if they
        // match then we can use the cachefile, otherwise we'll need to generate.
        $sha = $theme->get_component_sha($component);
        if ($sha !== $last_sha) {
            // The sha' have changed, we need to regenerate.
            @unlink($cachefile);
            $cachefile_exists = false;
        }
    }

    $csscontent = false;
    if (!$cachefile_exists) {
        // The cache file does not
        if ($sha === false) {
            $sha = $theme->get_component_sha($component);
        }
        $csscontent = $theme->get_css_content_by($component);
        css_store_css($theme, $cachefile, $csscontent);
        $cache->set($etag, $sha);
    }

    // Regenerate the etag to include the sha now that we know what is it.
    $etag = get_etag($sha, $themename, $component, $suffix, $option_rtl);

    // Next we're going to check if the browser has an old version of the css file, but that is unchanged (even though
    // it is expired). If that is the case then the sha will match the etag and we can tell the browser to use the
    // stale but correct file that it has.
    if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
        // There can be multiple etags provided :D
        $tags = explode(',', $_SERVER['HTTP_IF_NONE_MATCH']);
        $tags = array_map(
            function ($tag) {
                return trim($tag, '"');
            },
            $tags
        );
        if (in_array($etag, $tags)) {
            // The client has an old version that should not be cached, but it is correct and accurate to what we are about
            // to server. This will be quicker.
            header("HTTP/1.1 304 Not Modified");
            exit;
        }
    }

    if ($csscontent === false) {
        // We need to read it from the cache file.
        $csscontent = file_get_contents($cachefile);
    }
    css_send_uncached_css($csscontent, $etag);
}

// Make sure that only one client is generating CSS at a time.
// All other clients who got to this path can wait until the first completes.
$lockfactory = \core\lock\lock_config::get_lock_factory('totara_tui_css_generation');
$lock = $lockfactory->get_lock($themename, rand(90, 120), 600);
// We're out of the lock, check if we were waiting and the file now exists thanks to someone else.

if (file_exists($cachefile)) {
    if ($lock) {
        $lock->release();
    }
    css_send_cached_css($cachefile, $etag);
}

$csscontent = $theme->get_css_content_by($component);

css_store_css($theme, $cachefile, $csscontent);

if ($lock) {
    // Now that the CSS has been generated and/or stored, release the lock.
    // This will allow waiting clients to use the newly generated and stored CSS.
    $lock->release();
}

// Real browsers - this is the expected result!
css_send_cached_css_content($csscontent, $etag);

/**
 * Generate a etag for the CSS file.
 * @param string $rev
 * @param string $themename
 * @param string $component
 * @param string $suffix
 * @param bool $option_rtl
 * @return string
 */
function get_etag($rev, $themename, $component, $suffix, $option_rtl) {
    $etag = sha1(join('-', [
        'tui',
        $rev,
        $themename,
        $component,
        $suffix,
        ($option_rtl) ? 'rtl' : 'ltr',
    ]));
    return $etag;
}

/**
 * Gets the name for the cache files
 * @param string $rev
 * @param string $themename
 * @param string $component
 * @param string $suffix
 * @return string
 */
function get_cachefile($rev, $themename, $component, $suffix) {
    global $CFG;
    $cachefile = join('-', [
        $CFG->localcachedir,
        'totara_tui',
        $rev,
        $themename,
        "{$component}{$suffix}.css"
    ]);
    return $cachefile;
}