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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>>
 * @package   core
 */

use core\output\flex_icon_helper;

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit unit tests for \core\output\flex_icon_helper class.
 */
class totara_core_flex_icon_helper_testcase extends advanced_testcase {
    public function test_get_icons() {
        global $CFG;
        $this->resetAfterTest();

        $icons = flex_icon_helper::get_icons($CFG->theme);
        $this->assertInternalType('array', $icons);

        purge_all_caches();
        $this->assertSame($icons, flex_icon_helper::get_icons($CFG->theme));

        $this->assertSame($icons, flex_icon_helper::get_icons(null));
        $this->assertSame($icons, flex_icon_helper::get_icons(''));
        $this->assertSame($icons, flex_icon_helper::get_icons('xzxzzxzxzx'));
    }

    public function test_get_ajax_data() {
        global $CFG;
        $this->resetAfterTest();

        $icons = flex_icon_helper::get_icons($CFG->theme);
        $ajax = flex_icon_helper::get_ajax_data($CFG->theme);

        $this->assertSame(array('templates', 'datas', 'icons'), array_keys($ajax));

        $this->assertContains('core/flex_icon', $ajax['templates']);
        $this->assertContains('core/flex_icon_stack', $ajax['templates']);

        $this->assertSame(array_keys($icons), array_keys($ajax['icons']));

        foreach ($ajax['icons'] as $identified => $desc) {
            // This is the mapping to be used in JS.
            $template = $ajax['templates'][$desc[0]];
            $data = $ajax['datas'][$desc[1]];

            $this->assertSame($icons[$identified]['template'], $template);
            $this->assertSame($icons[$identified]['data'], $data);
        }
    }

    public function test_get_template_by_identifier() {
        global $CFG;

        $this->assertSame('core/flex_icon', flex_icon_helper::get_template_by_identifier($CFG->theme, 'edit'));
        $this->assertSame('core/flex_icon_stack', flex_icon_helper::get_template_by_identifier($CFG->theme, 'unsubscribe'));

        $missingiconstemplate = flex_icon_helper::get_template_by_identifier($CFG->theme, flex_icon_helper::MISSING_ICON);
        $this->assertSame($missingiconstemplate, flex_icon_helper::get_template_by_identifier($CFG->theme, 'xxxzxxzxzxz'));
    }

    public function test_get_data_by_identifier() {
        global $CFG;

        $expected = array('classes' => 'fa fa-edit');
        $this->assertSame($expected, flex_icon_helper::get_data_by_identifier($CFG->theme, 'edit'));

        $expected = array('classes' => array(
            'stack_first' => 'fa fa-question ft-stack-main',
            'stack_second' => 'fa fa-exclamation ft-stack-suffix'));
        $this->assertSame($expected, flex_icon_helper::get_data_by_identifier($CFG->theme, 'unsubscribe'));

        $missingiconsdata = flex_icon_helper::get_data_by_identifier($CFG->theme, flex_icon_helper::MISSING_ICON);
        $this->assertSame($missingiconsdata, flex_icon_helper::get_data_by_identifier($CFG->theme, 'xxxzxxzxzxz'));
    }

    public function test_get_flex_icon_candidate_dirs() {
        global $CFG;
        $this->assertSame('standardtotararesponsive', $CFG->theme);

        $theme = \theme_config::load('standardtotararesponsive');
        $candidatedirs = $theme->get_flex_icon_candidate_dirs();
        $this->assertCount(2, $candidatedirs);

        $this->assertSame(realpath("$CFG->dirroot/theme/bootstrapbase"), realpath($candidatedirs[0]));
        $this->assertSame(realpath("$CFG->dirroot/theme/standardtotararesponsive"), realpath($candidatedirs[1]));
    }

    public function test_protected_merge_flex_icons_file() {
        $reflectionclass = new \ReflectionClass('core\output\flex_icon_helper');
        $function = $reflectionclass->getMethod('merge_flex_icons_file');
        $function->setAccessible(true);

        $iconsdata = array(
            'translations' => array(),
            'deprecated' => array(),
            'defaults' => array(),
            'map' => array(),
        );
        $merged1 = $function->invoke(null, __DIR__ . '/fixtures/test_flex_icons1.php', $iconsdata);
        $this->assertSame(array_keys($iconsdata), array_keys($merged1));
        $this->assertSame(array('add' => 'plus'), $merged1['translations']);
        $this->assertSame(array('nav_exit' => 'caret-up'), $merged1['deprecated']);
        $this->assertSame(array(), $merged1['defaults']);
        $this->assertSame(array(
            'icon' => array('data' => array('classes' => 'fa fa-edit')),
            'fancy' => array('data' => array('classes' => 'fa fa-circle')),
        ), $merged1['map']);

        $merged1b = $function->invoke(null, __DIR__ . '/fixtures/test_flex_icons1.php', $merged1);
        $this->assertSame($merged1, $merged1b);

        $merged2 = $function->invoke(null, __DIR__ . '/fixtures/test_flex_icons2.php', $merged1);
        $this->assertSame($merged1, $merged2);

        $merged3 = $function->invoke(null, __DIR__ . '/fixtures/test_flex_icons3.php', $merged2);
        $this->assertSame(array_keys($iconsdata), array_keys($merged3));
        $this->assertSame(array('add' => 'minus', 'remove' => 'plus'), $merged3['translations']);
        $this->assertSame(array('nav_exit' => 'caret-up', 'nav_entry' => 'caret-down'), $merged3['deprecated']);
        $this->assertSame(array('data' => array('template' => 'core/flex_icon2')), $merged3['defaults']);
        $this->assertSame(array(
            'icon' => array('data' => array('classes' => 'fa fa-edit ft-state-warning')),
            'fancy' => array('template' => 'core/flex_icon_stack', 'data' => array('classes' => 'fa fa-circle')),
        ), $merged3['map']);
    }

    public function test_protected_resolve_translations() {
        $reflectionclass = new \ReflectionClass('core\output\flex_icon_helper');
        $function = $reflectionclass->getMethod('resolve_translations');
        $function->setAccessible(true);

        $iconsdata = array(
            'translations' => array(
                'mod_xxx|start' => 'open',
                'core|i-open' => 'open',
                'core|i-edit' => 'edit',
                'open' => 'offnen', // Ignored.
            ),
            'deprecated' => array(
                'mod_xx|close' => 'close',
                'mod_xxx|startnow' => 'mod_xxx|start',
            ),
            'defaults' => array(
                'data' => array(
                    'template' => 'core/flex_icon',
                ),
            ),
            'map' => array(
                'close' => array(
                    'data' =>
                        array(
                            'classes' => 'fa fa-close',
                        ),
                ),
                'edit' => array(
                    'template' => 'core/flex_icon_stack',
                    'data' =>
                        array(
                            'classes' =>
                                array(
                                    'stack_first' => 'fa fa-edit ft-stack-main',
                                    'stack_second' => 'fa fa-exclamation ft-stack-suffix'
                                ),
                        ),
                ),

                'open' => array(
                    'data' =>
                        array(
                            'classes' => 'fa fa-open',
                        ),
                ),
            ),
        );

        $icons = $function->invoke(null, $iconsdata);

        $expected = array(
            'close' =>
                array(
                    'data' =>
                        array(
                            'template' => 'core/flex_icon',
                            'classes' => 'fa fa-close',
                        ),
                    'template' => 'core/flex_icon',
                ),
            'edit' =>
                array(
                    'template' => 'core/flex_icon_stack',
                    'data' =>
                        array(
                            'template' => 'core/flex_icon',
                            'classes' =>
                                array(
                                    'stack_first' => 'fa fa-edit ft-stack-main',
                                    'stack_second' => 'fa fa-exclamation ft-stack-suffix',
                                ),
                        ),
                ),
            'open' =>
                array(
                    'data' =>
                        array(
                            'template' => 'core/flex_icon',
                            'classes' => 'fa fa-open',
                        ),
                    'template' => 'core/flex_icon',
                ),
            'mod_xxx|start' =>
                array(
                    'data' =>
                        array(
                            'template' => 'core/flex_icon',
                            'classes' => 'fa fa-open',
                        ),
                    'template' => 'core/flex_icon',
                    'translatesto' => 'open',
                ),
            'core|i-open' =>
                array(
                    'data' =>
                        array(
                            'template' => 'core/flex_icon',
                            'classes' => 'fa fa-open',
                        ),
                    'template' => 'core/flex_icon',
                    'translatesto' => 'open',
                ),
            'core|i-edit' =>
                array(
                    'template' => 'core/flex_icon_stack',
                    'data' =>
                        array(
                            'template' => 'core/flex_icon',
                            'classes' =>
                                array(
                                    'stack_first' => 'fa fa-edit ft-stack-main',
                                    'stack_second' => 'fa fa-exclamation ft-stack-suffix',
                                ),
                        ),
                    'translatesto' => 'edit',
                ),
            'mod_xx|close' =>
                array(
                    'data' =>
                        array(
                            'template' => 'core/flex_icon',
                            'classes' => 'fa fa-close',
                        ),
                    'template' => 'core/flex_icon',
                    'translatesto' => 'close',
                    'deprecated' => true,
                ),
            'mod_xxx|startnow' =>
                array(
                    'data' =>
                        array(
                            'template' => 'core/flex_icon',
                            'classes' => 'fa fa-open',
                        ),
                    'template' => 'core/flex_icon',
                    'translatesto' => 'open',
                    'deprecated' => true,
                ),
        );

        $this->assertSame($expected, $icons);
    }

    public function test_all_flex_icons_files() {
        global $CFG;

        $locations = array('core' => $CFG->dirroot . '/pix');

        // Load all plugins in the standard order.
        $plugintypes = \core_component::get_plugin_types();
        foreach ($plugintypes as $type => $unused) {
            $plugs = \core_component::get_plugin_list($type);
            foreach ($plugs as $name => $location) {
                if (file_exists($location . '/pix')) {
                    $locations[$type . '_' . $name] = $location . '/pix';
                }
            }
        }

        foreach ($locations as $component => $location) {
            if (strpos($component, 'tinymce') !== false) {
                // Skip deprecated stuff that is not supposed to be used or that will be removed in Totara 10.
                continue;
            }
            $knownfiles = $this->get_known_pix_files($location, $component);
            $knownfiles = array_flip($knownfiles);
            $pixfiles = $this->get_pix_files($location);

            $unknownfiles = array();
            foreach ($pixfiles as $file) {
                if (!isset($knownfiles[$file])) {
                    $identifier = substr($file, strlen($location . '/'));
                    $unknownfiles[] = preg_replace('/\.(gif|png|svg|)$/', '', $identifier);
                }
            }
            if ($unknownfiles) {
                $unknownfiles = array_unique($unknownfiles);
/* TODO finish flex icon conversion and fail test if any unknown pix icon file found
                echo "Location $location\n";
                echo "\$pixonlyimages = array(\n";
                foreach ($unknownfiles as $file) {
                    echo "    '$file',\n";
                }
                echo ");\n";
                echo "\n\n";
*/
            }
        }
    }

    protected function get_known_pix_files($location, $component) {
        $translations = array();
        $map = array();
        $deprecated = array();
        $pixonlyimages = array();

        $file = $location . '/flex_icons.php';
        if (file_exists($file)) {
            require($file);
        }

        $knownfiles = array();
        foreach ($translations as $id => $ignored) {
            $id = preg_replace('/^' . preg_quote($component). '\|/', '', $id);
            $knownfiles[] = $location . '/'. $id . '.gif';
            $knownfiles[] = $location . '/'. $id . '.png';
            $knownfiles[] = $location . '/'. $id . '.svg';
        }
        foreach ($map as $id => $ignored) {
            $id = preg_replace('/^' . preg_quote($component). '\|/', '', $id);
            $knownfiles[] = $location . '/'. $id . '.gif';
            $knownfiles[] = $location . '/'. $id . '.png';
            $knownfiles[] = $location . '/'. $id . '.svg';
        }
        foreach ($deprecated as $id => $ignored) {
            $id = preg_replace('/^' . preg_quote($component). '\|/', '', $id);
            $knownfiles[] = $location . '/'. $id . '.gif';
            $knownfiles[] = $location . '/'. $id . '.png';
            $knownfiles[] = $location . '/'. $id . '.svg';
        }
        foreach ($pixonlyimages as $file) {
            $knownfiles[] = $location . '/'. $file . '.gif';
            $knownfiles[] = $location . '/'. $file . '.png';
            $knownfiles[] = $location . '/'. $file . '.svg';
        }

        return $knownfiles;
    }

    protected function get_pix_files($location) {
        $pixfiles = array();
        foreach (new DirectoryIterator($location) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            $filename = $fileInfo->getFilename();
            if ($fileInfo->isDir()) {
                $pixfiles = array_merge($pixfiles, $this->get_pix_files($location . '/' . $filename));
                continue;
            }

            // Ignore anything that is not an image supported by pix_url.
            $extension = substr($filename, -4);
            if ($extension !== '.gif' and $extension !== '.png' and $extension !== '.svg') {
                continue;
            }

            // Get rid of size suffixes.
            $filename = preg_replace('/-\d\d+\.(gif|png|svg)$/', $extension, $filename);
            $pixfiles[] = $location . '/' . $filename;
        }

        $pixfiles = array_unique($pixfiles);
        return $pixfiles;
    }
}
