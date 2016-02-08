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
 * Tests the theme config class.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2012 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/outputlib.php');

/**
 * Tests the theme config class.
 *
 * @copyright 2012 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_flex_icon_helper_testcase extends advanced_testcase {

    /**
     * @var string
     */
    protected $originalthemedir = null;

    /**
     * @var theme_config
     */
    protected $defaultthemeconfig = null;

    /**
     * @var array|null
     */
    protected $flex_icon_translation_mapping = null;

    public function setUp() {

        parent::setUp();

        $this->defaultthemeconfig = theme_config::load(theme_config::DEFAULT_THEME);

    }

    /**
     * Return an accessible version of a given [protected|private] property.
     *
     * @param string $methodname
     * @param mixed $instance Instance of the class we're getting the property from.
     * @return ReflectionMethod
     */
    protected function get_inaccessible_property($propertyname, $instance) {

        $reflectionclass = new \ReflectionClass(get_class($instance));
        $property = $reflectionclass->getProperty($propertyname);
        $property->setAccessible(true);

        return $property->getValue($instance);

    }

    /**
     * Create mock themes inside a given directory.
     *
     * @param string $themedir Path to test themes directory.
     * @param array $themesconfig Config for dummy themes config.php
     */
    protected function flex_icon_create_themes($themedir, $themesconfig) {

        foreach ($themesconfig as $themename => $configproperties) {
            $dummythemepath = "{$themedir}/{$themename}";
            $filecontent = array(
                '<?php',
                '$THEME->name = ' . "'{$themename}';",
            );
            foreach ($configproperties as $property => $value) {
                $filecontent[] = '$THEME->' . $property . ' = ' . $value . ';';
            }

            $filecontent = implode(PHP_EOL, $filecontent);
            mkdir($dummythemepath);
            file_put_contents("{$dummythemepath}/config.php", $filecontent);
        }

    }

    /**
     * Convenience method.
     */
    protected function flex_icon_get_default_theme_candidate_paths() {

        global $CFG;

        $parentconfigs = $this->get_inaccessible_property('parent_configs', $this->defaultthemeconfig);

        $themeconfigs = array_merge(array($this->defaultthemeconfig), array_values($parentconfigs));

        $candidatepaths = array_map(function ($themeconfig) {
            return $themeconfig->dir . '/' . \core\flex_icon_helper::FLEX_ICON_MAP_FILENAME;
        }, $themeconfigs);

        // Core map file.
        $candidatepaths[] = $CFG->libdir . '/db/' . \core\flex_icon_helper::FLEX_ICON_MAP_FILENAME;

        return $candidatepaths;

    }

    /**
     * Change global configuration to test themes location.
     *
     * @param string $themedir Path to test themes directory.
     */
    protected function flex_icon_set_test_theme_settings($themedir) {

        global $CFG;

        if (isset($CFG->themedir)) {
            $this->originalthemedir = $CFG->themedir;
        }

        $CFG->themedir = $themedir;

    }

    /**
     * Reset original theme directory in global config.
     */
    protected function flex_icon_reset_theme_settings() {

        global $CFG;

        if ($this->originalthemedir === null) {
            unset($CFG->themedir);
        } else {
            $CFG->themedir = $this->originalthemedir;
        }

    }

    /**
     * Delete a given theme local cache directory if it exists.
     *
     * We only expect an icons cache file to be present.
     *
     * @param $themename
     */
    protected function flex_icon_delete_theme_cache($themename) {
        $cachefile = \core\flex_icon_helper::get_cache_file_path($themename);

        @unlink($cachefile);
        @rmdir(dirname($cachefile));

    }

    /**
     * Transform an array of legacy identifiers into their resolved equivalents.
     *
     * @param $identifiers
     * @return array
     */
    protected function flex_icon_resolve_legacy_identifiers($identifiers) {
        $translation = $this->get_flex_icon_translation_mapping();
        $result = array();
        foreach ($identifiers as $key => $identifier) {
            $result[$key] = \core\flex_icon_helper::resolve_identifier_using_translationarray($identifier, $translation);
        }
        return $result;
    }

    /**
     * Returns the flex_icon_translation_mapping
     *
     * @return array
     */
    protected function get_flex_icon_translation_mapping() {
        if ($this->flex_icon_translation_mapping === null) {
            $mappath = \core\flex_icon_helper::get_core_map_path();
            $translation = file_get_contents($mappath);
            $translationarray = json_decode($translation, true);
            $this->flex_icon_translation_mapping = $translationarray['translation'];
        }
        return $this->flex_icon_translation_mapping;
    }

    protected function flex_icon_assert_legacy_identifiers_translate_to($legacyidentifiers, $expected) {

        $resolved = $this->flex_icon_resolve_legacy_identifiers($legacyidentifiers);

        foreach ($resolved as $actual) {
            $this->assertEquals($expected, $actual);
        }

    }
    /**
     * It should return an ordered array of paths to potential icons.json files in order of precedence.
     */
    public function test_flex_icon_get_file_candidate_paths_output() {

        global $CFG;

        $themedir = make_temp_directory('theme');

        // Create a dummy theme hierarchy.
        $themesconfig = array(
            'childtheme' => array(
                'parents' => 'array("parent1", "parent2")',
            ),
            'parent1' => array(
                'parents' => 'array("parent2")',
            ),
            'parent2' => array(
                'parents' => 'array()',
            ),
        );

        // Create some dummy theme structures to scan.
        $this->flex_icon_create_themes($themedir, $themesconfig);
        $this->flex_icon_set_test_theme_settings($themedir);

        $expected = array(
            $themedir . '/childtheme/' . \core\flex_icon_helper::FLEX_ICON_MAP_FILENAME,
            $themedir . '/parent1/' . \core\flex_icon_helper::FLEX_ICON_MAP_FILENAME,
            $themedir . '/parent2/' . \core\flex_icon_helper::FLEX_ICON_MAP_FILENAME,
            $CFG->libdir . '/db/' . \core\flex_icon_helper::FLEX_ICON_MAP_FILENAME,
        );
        $actual = \theme_config::flex_icon_get_file_candidate_paths('childtheme');

        $this->assertEquals($expected, $actual);

        // Error gets thrown if system detects config has changed.
        $this->flex_icon_reset_theme_settings();

    }

    /**
     * It should return candidate paths based on the default theme if given theme is not found.
     *
     * Note: Core theme behaviour is to revert to the default theme if given theme
     * or one of its parents is not found.
     */
    public function test_get_flex_icons_candidate_paths_output_notfound() {

        $expected = self::flex_icon_get_default_theme_candidate_paths();
        $actual = \theme_config::flex_icon_get_file_candidate_paths('footheme');

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should return candidate paths based on the default theme if a given theme parent is not found.
     */
    public function test_get_flex_icons_candidate_paths_output_parent_notfound() {

        $themedir = make_temp_directory('theme');

        // Note the missing parent theme 'parent1'.
        $themesconfig = array(
            'childtheme' => array(
                'parents' => 'array("parent1", "parent2")',
            ),
            'parent2' => array(
                'parents' => 'array()',
            ),
        );

        // Create some dummy theme structures to scan.
        $this->flex_icon_create_themes($themedir, $themesconfig);
        $this->flex_icon_set_test_theme_settings($themedir);

        $expected = $this->flex_icon_get_default_theme_candidate_paths();
        $actual = \theme_config::flex_icon_get_file_candidate_paths('childtheme');

        $this->assertEquals($expected, $actual);

        // Error gets thrown if system detects config has changed.
        $this->flex_icon_reset_theme_settings();

    }

    /**
     * It should return the path to the current theme's flex icon cache file.
     */
    public function test_get_cache_file_path_output() {

        global $CFG;

        $revision = theme_get_revision();
        $filename = \core\flex_icon_helper::FLEX_ICON_CACHE_FILENAME;

        $expected = "{$CFG->localcachedir}/theme/{$revision}/theme_foo/{$filename}";
        $actual = \core\flex_icon_helper::get_cache_file_path('theme_foo');;

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should parse the content of a given file and return a PHP data structure.
     */
    public function test_parse_json_file_output() {

        $tmpdir = make_temp_directory(__METHOD__);
        $tmpfile = $tmpdir . '/' . \core\flex_icon_helper::FLEX_ICON_CACHE_FILENAME;
        file_put_contents($tmpfile, '[This is not JSON}');

        $this->setExpectedException('coding_exception');
        \core\flex_icon_helper::parse_json_file($tmpfile);
    }

    /**
     * It should throw an exception if JSON file doesn't exist.
     */
    public function test_parse_json_file_throws_nofile() {

        $tmpdir = make_temp_directory(__METHOD__);
        $tmpfile = $tmpdir . '/' . 'doesntexist.json';

        $this->setExpectedException('coding_exception');
        \core\flex_icon_helper::parse_json_file($tmpfile);

    }

    /**
     * It should merge icon data respecting override precedence.
     */
    public function test_merge_icons_data_output() {

        $basedata = array(
            'translation' => array(
                'bar' => 'whatever',
                'baz' => 'whatever',
            ),
            'map' => array(
                'one' => array(),
                'two' => array(),
            ),
        );

        $overrides = array(
            'translation' => array(
                'foo' => 'whatever',
                'bar' => 'overridden',
            ),
            'defaults' => array(
                'stuff' => array(),
                'bobbins' => array(),
            ),
            'map' => array(
                'three' => array(),
            ),
        );

        $expected = array(
            'translation' => array(
                'foo' => 'whatever',
                'bar' => 'overridden',
                'baz' => 'whatever',
            ),
            'defaults' => array(
                'stuff' => array(),
                'bobbins' => array(),
            ),
            'map' => array(
                'one' => array(),
                'two' => array(),
                'three' => array(),
            ),
        );
        $actual = \core\flex_icon_helper::merge_icons_data($basedata, $overrides);

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should flatten a parsed icons.json file translations section.
     */
    public function test_flatten_translations_output() {

        $iconsdata = array(
            'translation' => array(
                'mod_lti-icon' => 'lti',
                'mod_forum-t/subscribed' => 'subscribed',
                'core-a/download_all' => 'download',
                'core-t/download' => 'download',
                'core-i/import' => 'download',
                'core-i/restore' => 'download',
                'core-t/restore' => 'download',
            ),
            'defaults' => array(
                'template' => 'core/flex_icon',
            ),
            'map' => array(
                'download' => array(
                    'data' => array(
                        'classes' => 'fa-download'
                    ),
                ),
                'subscribed' => array(
                    'template' => 'core/flex_icon_stack',
                    'data' => array(
                        'mainclasses' => 'fa-envelope-o ft-stack-main',
                        'overlayclasses' => 'fa-check ft-stack-suffix ft-state-success'
                    ),
                ),
                'lti' => array(
                    'data' => array(
                        'classes' => 'fa-puzzle-piece'
                    )
                ),
            ),
        );

        $flatteneddata = array(
            'defaults' => array(
                'template' => 'core/flex_icon',
            ),
            'map' => array(
                'mod_lti-icon' => array(
                    'data' => array(
                        'classes' => 'fa-puzzle-piece'
                    ),
                    'translatesto' => 'lti'
                ),
                'mod_forum-t/subscribed' => array(
                    'template' => 'core/flex_icon_stack',
                    'data' => array(
                        'mainclasses' => 'fa-envelope-o ft-stack-main',
                        'overlayclasses' => 'fa-check ft-stack-suffix ft-state-success'
                    ),
                    'translatesto' => 'subscribed'
                ),
                'core-a/download_all' => array(
                    'data' => array(
                        'classes' => 'fa-download',
                    ),
                    'translatesto' => 'download'
                ),
                'core-t/download' => array(
                    'data' => array(
                        'classes' => 'fa-download',
                    ),
                    'translatesto' => 'download'
                ),
                'core-i/import' => array(
                    'data' => array(
                        'classes' => 'fa-download',
                    ),
                    'translatesto' => 'download'
                ),
                'core-i/restore' => array(
                    'data' => array(
                        'classes' => 'fa-download',
                    ),
                    'translatesto' => 'download'
                ),
                'core-t/restore' => array(
                    'data' => array(
                        'classes' => 'fa-download',
                    ),
                    'translatesto' => 'download'
                ),
                'download' => array(
                    'data' => array(
                        'classes' => 'fa-download'
                    ),
                ),
                'subscribed' => array(
                    'template' => 'core/flex_icon_stack',
                    'data' => array(
                        'mainclasses' => 'fa-envelope-o ft-stack-main',
                        'overlayclasses' => 'fa-check ft-stack-suffix ft-state-success'
                    ),
                ),
                'lti' => array(
                    'data' => array(
                        'classes' => 'fa-puzzle-piece'
                    ),
                ),
            ),
        );

        $this->assertEquals($flatteneddata, \core\flex_icon_helper::flatten_translations($iconsdata));

    }

    /**
     * It should return the same data if there is no translation section.
     */
    public function test_flatten_translations_output_notranslations() {

        $iconsdata = array(
            'defaults' => array(
                'template' => 'flex_icon',
            ),
            'map' => array(
                'cog' => array(
                    'classes' => 'fa-cog'
                ),
            ),
        );

        $this->assertEquals($iconsdata, \core\flex_icon_helper::flatten_translations($iconsdata));

    }

    /**
     * It should throw an exception if a translation resolves to a non-existent map key.
     */
    public function test_flatten_translations_throws() {

        $iconsdata = array(
            'translation' => array(
                'foo' => 'doesnotexist',
            ),
            'defaults' => array(
                'template' => 'flex_icon',
            ),
            'map' => array(
                'cog' => array(
                    'classes' => 'fa-cog'
                ),
            ),
        );

        $this->setExpectedException('coding_exception');
        \core\flex_icon_helper::flatten_translations($iconsdata);

    }

    /**
     * It should return the fully qualified (namespaced) function name.
     */
    public function test_get_fully_qualified_cache_functionname_output() {

        $themename = theme_config::DEFAULT_THEME;
        $functionname = \core\flex_icon_helper::FLEX_ICON_CACHE_FUNCTIONNAME;

        $expected = "theme_{$themename}\\{$functionname}";
        $actual = \core\flex_icon_helper::get_fully_qualified_cache_functionname($themename);

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should build the cache and return a PHP data structure if the file doesn't exist.
     */
    public function test_get_icons_cache_output_nofile() {

        $themename = theme_config::DEFAULT_THEME;

        $expected = \core\flex_icon_helper::build_cache_file_data($themename);
        $actual = \core\flex_icon_helper::get_cache($themename);

        $this->assertEquals($expected, $actual);

        // Clean up.
        $this->flex_icon_delete_theme_cache($themename);

    }

    /**
     * It should return the parsed content of the cache file as a PHP data structure when the file exists.
     */
    public function test_get_icons_cache_output_fileexists() {

        global $CFG;

        $themename = 'testtheme';
        $testdata = array('testdata' => 123);

        $filepath = \core\flex_icon_helper::get_cache_file_path($themename);

        make_localcache_directory('theme', false);
        mkdir(dirname($filepath), $CFG->directorypermissions, true);

        $testcontent = \core\flex_icon_helper::get_cache_file_content_from_data($themename, $testdata);

        file_put_contents($filepath, $testcontent);

        $expected = $testdata;
        $actual = \core\flex_icon_helper::get_cache($themename);

        $this->assertEquals($expected, $actual);

        // Clean up.
        $this->flex_icon_delete_theme_cache($themename);

    }

    /**
     * It should return boolean whether an identifier has an entry in the theme's icon cache file.
     */
    public function test_flex_icon_identifier_has_map_data_output() {

        $themename = theme_config::DEFAULT_THEME;
        $identifier = core\output\flex_icon::legacy_identifier_from_pix_data('t/backup');

        // Call once here so that we also cover the simple static cache code.
        \core\flex_icon_helper::identifier_has_map_data($themename, $identifier);

        $expected = true;
        $actual = \core\flex_icon_helper::identifier_has_map_data($themename, $identifier);

        $this->assertEquals($expected, $actual);

        $expected  = false;
        $actual = \core\flex_icon_helper::identifier_has_map_data($themename, '_______DOES_NOT_EXIST');

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should translate a legacy icon identifier to a flex icon identifier.
     */
    public function test_resolve_identifier_using_translationarray_output_translates() {

        $expected = 'download';
        $actual = \core\flex_icon_helper::resolve_identifier_using_translationarray('t/import', ['t/import' => 'download']);

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should translate an identifier until there are no more mappings
     */
    public function test_resolve_identifier_using_translationarray_output_translates_nested() {

        $expected = 'download';
        $actual = \core\flex_icon_helper::resolve_identifier_using_translationarray('t/import', [
            't/import'  => 'banana',
            'banana'    => 'noparking',
            'noparking' => 'download',
        ]);

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should throw an exception if there is a circular reference.
     */
    public function test_resolve_identifier_using_translationarray_circular_reference_throws() {

        $this->setExpectedException('coding_exception');
        \core\flex_icon_helper::resolve_identifier_using_translationarray('t/import', [
            't/import'  => 'download',
            'download'  => 't/import',
        ]);

    }

    /**
     * It should return the passed identifier if there is no translation.
     */
    public function test_resolve_identifier_using_translationarray_output_no_translation() {

        $expected = 'banana';
        $actual = \core\flex_icon_helper::resolve_identifier_using_translationarray('banana', ['t/import' => 'download']);

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should return boolean whether a given pix_icon [identifier] should be replaced with a flex_icon.
     */
    public function test_flex_icon_should_replace_pix_icon_output() {

        $themename = theme_config::DEFAULT_THEME;

        $expectedoutcomes = array(
            'noreplacement' => false,
            'core-i/restore' => true,
        );

        $firstitem = true;

        foreach ($expectedoutcomes as $identifier => $expected) {
            if ($firstitem === true) {
                // Call the first expected outcome so we cover the method's static caching.
                \core\flex_icon_helper::flex_icon_should_replace_pix_icon($themename, $identifier);
                $firstitem = false;
            }
            $actual = \core\flex_icon_helper::flex_icon_should_replace_pix_icon($themename, $identifier);
            $this->assertEquals($expected, $actual);
        }

    }

    /**
     * It should fall back to pix_icon if there is no map data.
     */
    public function test_flex_icon_should_replace_pix_icon_output_no_map_data() {

        $themename = theme_config::DEFAULT_THEME;
        $identifier = 'core-i/notreallyanicon';

        $expected = false;
        $actual = \core\flex_icon_helper::flex_icon_should_replace_pix_icon($themename, $identifier);

        $this->assertEquals($expected, $actual);

    }

    /**
     * @param $legacyid
     * @param $component
     * @param $iconhtml
     * @param $iconmapping
     */
    public function assert_icon_data($legacyid, $component, $iconhtml, $iconmapping) {

        $page = new moodle_page();
        $page->set_url('/index.php');
        $page->set_context(context_system::instance());
        /** @var core_renderer $renderer */
        $renderer = $page->get_renderer('core');

        $flexiconmap = $this->get_flex_icon_translation_mapping();

        $this->assertArrayHasKey($iconmapping, $flexiconmap, "No mapping found for $iconmapping");
        $flexicon = new \core\output\flex_icon($iconmapping);

        $template = $flexicon->get_template();
        $data = $flexicon->export_for_template($renderer);
        if (!is_scalar($template)) {
            debug($template);
            die;
        }
        $flexiconhtml = preg_replace('#>\s*<#s', '><', $renderer->render_from_template($template, $data));
        $this->assertSame(
            $iconhtml,
            $flexiconhtml,
            "Mapping for $iconmapping references the incorrect icon."
        );
        $pixicon = new pix_icon($legacyid, '', $component);
        $pixiconhtml = preg_replace('#>\s*<#s', '><', $renderer->render($pixicon));
        if (strpos($legacyid, 'i/') === 0) {
            $pixiconhtml = str_replace(' ft-size-200', '', $pixiconhtml);
        }
        $this->assertSame(
            $flexiconhtml,
            $pixiconhtml,
            "Pix icon and flexicon html do not match for $iconmapping"
        );
    }

    public function test_resolve_identifier_output() {

        $this->markTestIncomplete();

//        $expectations = array(
//            'core-f/pdf' => 'core-f/pdf',
//            'core-i/permissionlock' => 'core-i/permissionlock',
//            'core-i/risk_spam' => 'core',
//        );
//
//        foreach ($expectations as $legacyidentifier => $expected) {
//            $actual = \core\flex_icon_helper::resolve_identifier(theme_config::DEFAULT_THEME, $legacyidentifier);
//            $message = "Expected '{$legacyidentifier}' to resolve to '{$expected}' not '{$actual}'";
//            $this->assertEquals($expected, $actual, $message);
//        }

    }

    public function test_get_template_path_by_identifier() {
        $this->markTestIncomplete();
    }

    public function test_get_data_by_identifier() {
        $this->markTestIncomplete();
    }

    public function test_flex_icon_should_replace_pix_icon() {

    }

}