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
 * Utility classes and functions for text editor integration.
 *
 * @package    core
 * @subpackage editor
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns users preferred editor for given format
 *
 * @param int $format text Format, or null for none
 * @param string $framework Name of front-end framework to get compatible editor for
 * @return texteditor object
 */
function editors_get_preferred_editor($format = null, $framework = null) {
    global $USER, $CFG;

    if (!empty($CFG->adminsetuppending)) {
        // Must not use other editors before install completed!
        return get_texteditor('textarea');
    }

    $enabled = editors_get_enabled();

    $preference = get_user_preferences('htmleditor', '', $USER);

    if (isset($enabled[$preference])) {
        // Edit the list of editors so the users preferred editor is first in the list.
        $editor = $enabled[$preference];
        unset($enabled[$preference]);
        array_unshift($enabled, $editor);
    }

    // now find some plugin that supports format and is available
    $editor = false;
    foreach ($enabled as $e) {
        if (!$e->supported_by_browser()) {
            // bad luck, this editor is not compatible
            continue;
        }
        if (!empty($framework) && !$e->get_js_module($framework)) {
            // no framework support
            continue;
        }
        if (!$supports = $e->get_supported_formats()) {
            // buggy editor!
            continue;
        }
        if (is_null($format) || in_array($format, $supports)) {
            // editor supports this format, yay!
            $editor = $e;
            break;
        }
    }

    if (!$editor) {
        $editor = get_texteditor('textarea'); // must exist and can edit anything
    }

    return $editor;
}

/**
 * Returns users preferred text format.
 * @return int standard text format
 */
function editors_get_preferred_format() {
    global $USER;

    $editor = editors_get_preferred_editor();
    return $editor->get_preferred_format();
}

/**
 * Returns list of enabled text editors
 * @return array of name=>texteditor
 */
function editors_get_enabled() {
    $active = array();
    foreach(editors_get_enabled_names() as $e) {
        if ($editor = get_texteditor($e)) {
            $active[$e] = $editor;
        }
    }
    return $active;
}

/**
 * Returns an array of enabled editor names.
 *
 * This function does not confirm that the editor code is correct.
 *
 * @return string[] A list of editor names.
 */
function editors_get_enabled_names(): array {
    global $CFG;
    if (empty($CFG->texteditors) || trim($CFG->texteditors) === '') {
        $CFG->texteditors = 'atto,weka,textarea';
    }
    $editors = explode(',', $CFG->texteditors);
    if (count($editors) === 0) {
        // It should be impossible to reach this situation.
        $editors[] = 'textarea';
    }
    return $editors;
}

/**
 * Returns instance of text editor
 *
 * @param string $editorname name of editor (textarea, atto, ...)
 * @return object|bool texeditor instance or false if does not exist
 */
function get_texteditor($editorname) {
    global $CFG;

    $libfile = "$CFG->libdir/editor/$editorname/lib.php";
    if (!file_exists($libfile)) {
        return false;
    }
    require_once($libfile);
    $classname = $editorname.'_texteditor';
    if (!class_exists($classname)) {
        return false;
    }
    return new $classname();
}

/**
 * Get the list of available editors
 *
 * @return array Array ('editorname'=>'localised editor name')
 */
function editors_get_available() {
    $editors = array();
    foreach (core_component::get_plugin_list('editor') as $editorname => $dir) {
        $editors[$editorname] = get_string('pluginname', 'editor_'.$editorname);
    }
    return $editors;
}

/**
 * Setup all JS and CSS needed for editors.
 * @return void
 */
function editors_head_setup() {
    global $CFG;

    if (empty($CFG->texteditors)) {
        $CFG->texteditors = 'atto,weka,textarea';
    }
    $active = explode(',', $CFG->texteditors);

    foreach ($active as $editorname) {
        if (!$editor = get_texteditor($editorname)) {
            continue;
        }
        if (!$editor->supported_by_browser()) {
            // bad luck, this editor is not compatible
            continue;
        }
        $editor->head_setup();
    }
}

/**
 * Base abstract text editor class.
 *
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
abstract class texteditor {
    /**
     * Is editor supported in current browser?
     * @return bool
     */
    public abstract function supported_by_browser();

    /**
     * Returns list of supported text formats
     * @return array Array (FORMAT=>FORMAT)
     */
    public abstract function get_supported_formats();

    /**
     * Returns main preferred text format.
     * @return int text format
     */
    public abstract function get_preferred_format();

    /**
     * Supports file picker and repos?
     * @return object book object
     */
    public abstract function supports_repositories();

    /**
     * @var string $text The text set to the editor in the form.
     * @since 3.0
     */
    protected $text = '';

    /**
     * Set the text set for this form field. Will be called before "use_editor".
     * @param string $text The text for the form field.
     */
    public function set_text($text) {
        $this->text = $text;
    }

    /**
     * Get the text set for this form field. Can be called from "use_editor".
     * @return string
     */
    public function get_text() {
        return $this->text;
    }

    /**
     * Add required JS needed for editor
     *
     * Valid options may vary by editor. See the individual editor
     * implementations of this function for documentation.
     *
     * @param string $elementid id of text area to be converted to editor
     * @param array $options Editor options
     * @param object $fpoptions file picker options
     * @return void
     */
    public abstract function use_editor($elementid, array $options=null, $fpoptions = null);

    /**
     * Setup all JS and CSS needed for editor.
     * @return void
     */
    public function head_setup() {
    }

    /**
     * Allow editor to customise template and init itself in Totara forms.
     *
     * @param array $result
     * @param array $editoroptions
     * @param array $fpoptions
     * @param array $fptemplates
     * @return void the $result parameter is modified if necessary
     */
    public function totara_form_use_editor(&$result, array $editoroptions, array $fpoptions, array $fptemplates) {
    }

    /**
     * Get JS module for front-end editor support for framework
     *
     * @param string $framework
     * @return string|null
     */
    public function get_js_module(string $framework): ?string {
        return null;
    }
}
