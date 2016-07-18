<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @copyright 2015 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Joby Harding <joby.harding@totaralms.com>>
 * @package   elementlibrary
 */

use core\output\flex_icon;

global $CFG, $PAGE, $OUTPUT;

require_once(dirname(__DIR__) . '/config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

$strheading = 'Element Library: Flexible icons';

admin_externalpage_setup('elementlibrary');

echo $OUTPUT->header();

echo html_writer::link(new moodle_url('/elementlibrary/flex_icon.php'), '&laquo; Back to index');

echo $OUTPUT->box_start();

// TODO: TL-9469
die;

$iconsfile = "{$CFG->libdir}/db/" . \core\ouput\flex_icon_helper::FLEX_ICON_MAP_FILENAME;
$iconsmap = json_decode(file_get_contents($iconsfile), true)['map'];
$identifiers = array_keys($iconsmap);
sort($identifiers);

$sizeclasses = array(
    'ft-size-100',
    'ft-size-200',
    'ft-size-300',
    'ft-size-400',
    'ft-size-500',
    'ft-size-600',
    'ft-size-700',
);

$stateclasses = array(
    'ft-state-default',
    'ft-state-danger',
    'ft-state-success',
    'ft-state-info',
    'ft-state-other',
    'ft-state-disabled',
);

echo $OUTPUT->heading('Flexible icons');

$fontawesomelink = html_writer::link('https://fortawesome.github.io/Font-Awesome/', 'Font Awesome');

$iconsexamplecode =<<<EOF
<div class="my-page-section">
    {{! Output a 'cog' icon }}
    {{#flex_icon}}cog{{/flex_icon}}
</div>
EOF;

$legacyiconsexamplecode =<<<EOF
<div class="my-page-section">
    {{! Legacy identifiers should be frankenstyled together using this format }}
    {{#flex_icon}}<component>-<legacy icon path>{{/flex_icon}}

    {{! For example: }}
    {{#flex_icon}}moodle-t/stuff{{/flex_icon}}
</div>
EOF;

$iconsexamplecode = htmlentities(trim($iconsexamplecode));
$legacyiconsexamplecode = htmlentities(trim($legacyiconsexamplecode));

$introductiontext =<<<EOF
<p>Flexible icons is an API intended to provide more control over working with and delivering icons in Totara
than the existing <code>pix_icon()</code> approach. Pix icon is still a great way to provide overridable
one-off <em>image</em> based assets and you should continue to use it for that. Flex icon makes a distinction
between these and icon systems and is intended to be used only for the latter.</p>

<p>This page illustrates flexible icons available in Totara core. The core flexible icons are based on
{$fontawesomelink} font-icons with some bespoke additions - but that doensn't mean you're limited to
only using font icons. They can be overridden within your plugins theme using an <code>pix/flex_icons.php</code> file which allows
you to swap out core icons and provide your own default data structures to be passed into all templates or target
them on a per-icon basis. Flexible icons are intended to be used in templates by passing an icon identifier into the
flex_icon template helper:<br />

<pre>
{$iconsexamplecode}
</pre>
</p>

<p>While the following illustrates the core flex icons provided you can customise the templates used for your icons
using flex_icon by providing defaults or icon-specific.</p>

<p>All legacy <code>pix_icon()</code> identifiers for core icons may be passed into the helper using the following
frankenstyle format and will be resolved to their new equivalent automatically:</p>

<pre>
{$legacyiconsexamplecode}
</pre>
EOF;

echo $introductiontext;
echo $OUTPUT->heading('Single icon markup example', 3);
echo html_writer::tag('pre', htmlentities($OUTPUT->render(new flex_icon('cog'))));
echo $OUTPUT->heading('Icon stack markup example', 3);
echo html_writer::tag('pre', htmlentities($OUTPUT->render(new flex_icon('check-permissions'))));

echo $OUTPUT->heading('Icon with alt text');
echo $OUTPUT->flex_icon('cog', array('alt' => get_string('settings')));
echo $OUTPUT->flex_icon('contact-add', array('alt' => get_string('addcontact', 'message')));

echo $OUTPUT->heading('JavaScript helper', 3);

echo $OUTPUT->heading('Icon sizes', 3);
echo html_writer::tag('p', 'The following classes can be added to set the size of the font-based flexible icons.');
echo render_icons_table($identifiers, $sizeclasses);

echo $OUTPUT->heading('Icon states');
echo render_icons_table($identifiers, $stateclasses);

echo html_writer::end_tag('dl');

echo $OUTPUT->box_end();

echo $OUTPUT->footer();

//
// Functions.
//
function render_icons_table($identifiers, $classes) {

    global $OUTPUT;

    $table = new html_table();

    $tableheaders = array_map(function($optionalclass) {
        return new html_table_cell(html_writer::tag('code', ".{$optionalclass}"));
    }, $classes);

    array_unshift($tableheaders, '');

    $table->head = $tableheaders;

    foreach ($identifiers as $identifier) {
        $cells = array_map(function($optionalclass) use ($OUTPUT, $identifier) {
            $flexicon = new flex_icon($identifier, array('classes' => $optionalclass));
            return new html_table_cell($OUTPUT->render($flexicon));
        }, $classes);

        array_unshift($cells, $identifier);

        $table->data[] = new html_table_row($cells);
    }

    return $OUTPUT->render($table);

}
