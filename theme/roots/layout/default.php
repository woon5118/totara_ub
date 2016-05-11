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
 * @copyright 2016 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   theme_roots
 */

defined('MOODLE_INTERNAL') || die();

// We need this for bootstrap_grid().
require_once("{$CFG->dirroot}/theme/bootstrap/lib.php");

$knownregionpre = $PAGE->blocks->is_known_region('side-pre');
$knownregionpost = $PAGE->blocks->is_known_region('side-post');

$grid = new theme_roots\bootstrap_grid();

if ($PAGE->blocks->region_has_content('side-pre', $OUTPUT)) {
    $grid->has_side_pre();
}

if ($PAGE->blocks->region_has_content('side-post', $OUTPUT)) {
    $grid->has_side_post();
}

$regions = $grid->get_regions_classes();

$PAGE->set_popup_notification_allowed(false);

$themerenderer = $PAGE->get_renderer('theme_roots');

// TODO improve on this legacy approach.
$hastotaramenu = false;
$totaramenu = '';
if (isloggedin() && empty($PAGE->layout_options['nocustommenu'])) {
    $menudata = totara_build_menu();
    $totara_core_renderer = $PAGE->get_renderer('totara_core');
    $totaramenu = $totara_core_renderer->totara_menu($menudata);
    $hastotaramenu = !empty($totaramenu);
}
// END

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<nav role="navigation" class="navbar navbar-default">
    <div class="container-fluid">

        <div class="navbar-header pull-left">
            <?php echo $themerenderer->render(new theme_roots\site_logo($SITE->shortname)); ?>
        </div>

        <div class="navbar-header pull-right">
            <?php echo $OUTPUT->navbar_button(); ?>
            <?php echo $OUTPUT->user_menu(); ?>
        </div>

    </div>
    <?php
    if ($hastotaramenu) {
        echo '<div class="container-fluid">';
        echo '    <div id="totara-navbar" class="totara-navbar navbar-collapse collapse">' . $totaramenu . '</div>';
        echo '</div>';
    }
    ?>
</nav>

<div id="page" class="container-fluid">
    <?php echo $OUTPUT->full_header(); ?>

    <div id="page-content" class="row">
        <div id="region-main" class="<?php echo $regions['content']; ?>">
            <?php
            echo $OUTPUT->course_content_header();

            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </div>

        <?php
        if ($knownregionpre) {
            echo $OUTPUT->blocks('side-pre', $regions['pre']);
        }?>
        <?php
        if ($knownregionpost) {
            echo $OUTPUT->blocks('side-post', $regions['post']);
        }?>
    </div>

</div>

<footer id="page-footer" class="footer">
    <div class="container-fluid">
        <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
        <?php
        echo $OUTPUT->login_info();
        echo $OUTPUT->powered_by_totara();
        echo $OUTPUT->standard_footer_html();
        ?>
    </div>
</footer>

<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>
