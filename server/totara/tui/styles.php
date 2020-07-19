<?php

//define('NO_DEBUG_DISPLAY', true);
define('ABORT_AFTER_CONFIG', true);

require('../../config.php');
require_once($CFG->dirroot.'/lib/csslib.php');

// Defaults
$option_svg = true;
$option_rtl = false;

$slasharguments = min_get_slash_argument();
if ($slasharguments) {

    $slasharguments = ltrim($slasharguments, '/');
    $slashargument_parts = explode('/', $slasharguments);

    if (count($slashargument_parts) !== 6) {
        debugging('Invalid number of arguments.', DEBUG_DEVELOPER);
        css_send_css_not_found();
    }

    // Order is: theme/rev/suffix/direction/svg/component
    list($themename, $rev, $suffix, $option_rtl, $option_svg, $component) = $slashargument_parts;
    unset($slasharguments, $slashargument_parts); // Clean these up, you can't reuse them.

} else {

    $themename = min_optional_param('theme', null, 'SAFEDIR');
    $rev = min_optional_param('rev', null, 'INT');
    $suffix = min_optional_param('suffix', null, 'SAFEDIR');
    $option_rtl = min_optional_param('direction', 'ltr', 'SAFEDIR');
    $option_svg = min_optional_param('svg', true, 'INT');
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
$option_svg = (bool)$option_svg;

if (file_exists("{$CFG->dirroot}/theme/{$themename}/config.php")) {
    // The theme exists in standard location - ok.
} else if (!empty($CFG->themedir) and file_exists("{$CFG->themedir}/{$themename}/config.php")) {
    // Alternative theme location contains this theme - ok.
} else {
    css_send_css_not_found();
}

if ($rev !== -1) {

    $cachefile = get_cachefile($rev, $themename, $component, $suffix);
    $etag = get_etag($rev, $themename, $component, $suffix, $option_svg, $option_rtl);
    if (file_exists($cachefile)) {
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            // We do not actually need to verify the etag value because our files
            // never change in cache because we increment the rev counter.
            css_send_unmodified(filemtime($cachefile), $etag);
        }
        css_send_cached_css($cachefile, $etag);
    }
}

// Ok, now we need to start normal moodle script, we need to load all libs and $DB.
define('ABORT_AFTER_CONFIG_CANCEL', true);

define('NO_MOODLE_COOKIES', true); // Session not used here.
define('NO_UPGRADE_CHECK', true);  // Ignore upgrade check.

require("$CFG->dirroot/lib/setup.php");

// Revision is in the URL just to facilitate caching, we don't actually trust it.
$rev = \totara_tui\local\locator\bundle::get_css_rev();
$suffix = \totara_tui\local\locator\bundle::get_css_desired_suffix(true);

$theme = \totara_tui\local\theme::load($themename);
$theme->force_svg_use($option_svg);
$theme->set_rtl_mode($option_rtl);
$theme->set_legacy_browser(strpos($suffix, 'legacy') !== false);

if ($rev === -1) {
    // No caching flies in this content.
    $csscontent = $theme->get_css_content_by($component);
    css_send_uncached_css($csscontent);
}

$etag = get_etag($rev, $themename, $component, $suffix, $option_svg, $option_rtl);
make_localcache_directory('totara_tui', false);

// Make sure that only one client is generating CSS at a time.
// All other clients who got to this path can wait until the first completes.
$lockfactory = \core\lock\lock_config::get_lock_factory('totara_tui_css_generation');
$lock = $lockfactory->get_lock($themename, rand(90, 120), 600);
// We're out of the lock, check if we were waiting and the file now exists thanks to someone else.

// Recalculate cachefile, even if we already have it, as rev may have changed.
$cachefile = get_cachefile($rev, $themename, $component, $suffix);
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

function get_etag($rev, $themename, $component, $suffix, $option_svg, $option_rtl) {
    $etag = sha1(join('-', [
        $rev,
        $themename,
        $component,
        $suffix,
        ($option_svg) ? 'svg' : 'nosvg',
        ($option_rtl) ? 'rtl' : 'ltr',
    ]));
    return $etag;
}

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