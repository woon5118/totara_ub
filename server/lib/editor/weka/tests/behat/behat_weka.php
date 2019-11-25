<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package editor_weka
 */

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

use Behat\Mink\Element\NodeElement as NodeElement,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

class behat_weka extends behat_base {
    private const EDITOR_SELECT_HELPER = "
function selectRange(range) {
    const sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
}
function textSelectRange(textNode, text) {
    const index = textNode.textContent.indexOf(text);
    if (index == -1) throw new Error('select range: text not found');
    const range = document.createRange();
    range.setStart(textNode, index);
    range.setEnd(textNode, index + text.length);
    return range;
}
";

    private const EDITOR_TEXT_NODE_HELPER = "
function findEditorTextNode(start, text) {
    const stack = [start];
    while (stack.length) {
        const current = stack.pop();
        if (current instanceof Text) {
            if (current.textContent.includes(text)) return current;
        } else if (current.childNodes.length > 0) {
            stack.push(...[...current.childNodes].reverse());
        }
    }
}
";

    private const EDITOR_WEKA_NODE_HELPER = "
function findWekaNode(type, word) {
    return [...el.querySelectorAll('.tui-editorWeka-' + type)].find(node => {
        if (type == 'linkMedia') {
            return (node.getAttribute('data-url') || '').includes(word);
        } else if (type == 'linkBlock') {
            return findEditorTextNode(node, word) || (node.getAttribute('data-url') || '').includes(word)
        } else {
            return findEditorTextNode(node, word);
        }
    });
}
function wekaNodeClickTarget(domNode, type) {
    let clickTarget = 'button[aria-label=More]';
    if (type == 'linkBlock') {
        clickTarget = '.tui-linkBlock';
    }
    return domNode.querySelector(clickTarget);
}
";

    /** 
     * @var NodeElement The current Weka DOM node.
     */
    private $current_weka = null;
    /**
     * @var NodeElement The current Weka field (ProseMirror) DOM node.
     */
    private $current_weka_field = null;

    /**
     * Get a Weka editor by CSS.
     *
     * @param string $selector
     * @return NodeElement
     */
    private function find_weka_editor_by_css($selector) {
        $specified_node = $this->find('css', $selector);
        return $this->find('css', '.tui-weka', false, $specified_node);
    }

    /**
     * Get JS expression evaluating to Weka field (ProseMirror) DOM node.
     *
     * @param NodeElement $editor_node Weka DOM node.
     * @return string
     */
    private function editor_inner_el_js($editor_node) {
        $editorid = $editor_node->getAttribute('id');
        return "document.querySelector('#' + " . json_encode($editorid) . " + ' .tui-weka-editor')";
    }

    /**
     * Type the specified characters in the Weka field.
     *
     * @param string|bool|array $value
     */
    private function type_chars($value) {
        $this->current_weka_field->setValue($value); // because this is just a div, it calls postValue(), which just types the characters
    }

    /**
     * Set the weka editor content to the specified text.
     *
     * @Given /^I activate the weka editor with css "(?P<field_selector>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $selector
     */
    public function i_activate_the_weka_editor_with_css($selector) {
        \behat_hooks::set_step_readonly(false);
        if (!$this->running_javascript()) {
            throw new coding_exception('Weka editor tests require JavaScript.');
        }
        $this->current_weka = $this->find_weka_editor_by_css($selector);
        $this->ensure_node_is_visible($this->current_weka);
        $this->current_weka_field = $this->find('css', '.tui-weka-editor', false, $this->current_weka);
        $this->current_weka_field->focus();
    }

    /**
     * Set the weka editor content to the specified text.
     *
     * @Given /^I set the weka editor with css "(?P<field_selector>(?:[^"]|\\")*)" to "(?P<field_value_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $selector
     * @param string $value
     */
    public function i_set_the_weka_editor_with_css_to($selector, $value) {
        $this->i_activate_the_weka_editor_with_css($selector);
        $this->i_set_the_weka_editor_to($value);
    }

    /**
     * Set the weka editor content to the specified text.
     *
     * @Given /^I set the weka editor to "(?P<field_value_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $selector
     * @param string $value
     */
    public function i_set_the_weka_editor_to($value) {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        $this->i_select_all_text_in_the_weka_editor();
        $this->i_delete_the_selected_text_in_the_weka_editor();
        $this->i_type_in_the_weka_editor($value);
    }

    /**
     * Type in the weka editor. Selected text will not be replaced.
     *
     * @Given /^I type "(?P<field_value_string>(?:[^"]|\\")*)" in the weka editor$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $value
     */
    public function i_type_in_the_weka_editor($value) {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        \behat_hooks::set_step_readonly(false);
        $value = str_replace('\n', "\n", $value);
        $this->type_chars($value);
    }

    /**
     * Click the specified toolbar button.
     *
     * @Given /^I click on the "(?P<button_name>(?:[^"]|\\")*)" toolbar button in the weka editor$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $button_name
     */
    public function i_click_on_the_toolbar_button_in_the_weka_editor($button_name) {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        \behat_hooks::set_step_readonly(false);
        $xpathtarget = "//*[@aria-label=" . behat_context_helper::escape($button_name) . "]";
        $button_node = $this->find('xpath', $xpathtarget, false, $this->current_weka);
        $this->ensure_node_is_visible($button_node);
        $button_node->click();
    }

    /**
     * Select all text in the editor.
     *
     * @Given /^I select all text in the weka editor$/
     */
    public function i_select_all_text_in_the_weka_editor() {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        \behat_hooks::set_step_readonly(false);
        $js = "(function(){" . self::EDITOR_SELECT_HELPER . "
const el = " . $this->editor_inner_el_js($this->current_weka) . ";
el.focus();
var range = document.createRange();
range.selectNodeContents(el);
selectRange(range);
}());";
        $this->getSession()->executeScript($js);
    }

    /**
     * Select the given text in the editor.
     *
     * Note that the whole text must be part of the same paragraph and have the same style to work.
     *
     * @Given /^I select the text "(?P<text>(?:[^"]|\\")*)" in the weka editor$/
     * @param string $text
     */
    public function i_select_the_text_in_the_weka_editor($text) {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        \behat_hooks::set_step_readonly(false);
        $js = "(function(){" . self::EDITOR_SELECT_HELPER . self::EDITOR_TEXT_NODE_HELPER . "
const el = " . $this->editor_inner_el_js($this->current_weka) . ";
el.focus();
const search = " . json_encode($text) . ";
selectRange(textSelectRange(findEditorTextNode(el, search), search));
}());";
        $this->getSession()->executeScript($js);
    }

    /**
     * Move the cursor to the end of the editor
     *
     * @Given /^I move the cursor to the end of the weka editor$/
     */
    public function i_move_the_cursor_to_the_end_of_the_weka_editor() {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        \behat_hooks::set_step_readonly(false);
        $js = "(function(){" . self::EDITOR_SELECT_HELPER . "
const el = " . $this->editor_inner_el_js($this->current_weka) . ";
el.focus();
const range = document.createRange();
if (el.lastChild) {
    range.setStartAfter(el.lastChild);
    range.setEndAfter(el.lastChild);
} else {
    range.setStart(el.lastChild, 0);
    range.setEnd(el.lastChild, 0);
}
selectRange(range);
}());";
        $this->getSession()->executeScript($js);
    }


    /**
     * Delete the selected text.
     *
     * @Given /^I delete the selected text in the weka editor$/
     */
    public function i_delete_the_selected_text_in_the_weka_editor() {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        \behat_hooks::set_step_readonly(false);
        $js = "(function(){
var sel = window.getSelection();
if (sel.rangeCount < 1) return;
sel.getRangeAt(0).deleteContents();
}());";
        $this->getSession()->executeScript($js);
    }

    /**
     * Show the menu of a weka node.
     *
     * @Given /^I activate the menu of the "(?P<word>(?:[^"]|\\")*)" "(?P<type>(?:[^"]|\\")*)" in the weka editor$/
     * @param string $word Type-dependent identifier.
     * @param string $type
     */
    public function i_activate_the_menu_of_the_node_in_the_weka_editor($word, $type) {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        \behat_hooks::set_step_readonly(false);
        $js = "(function(){" . self::EDITOR_TEXT_NODE_HELPER . self::EDITOR_WEKA_NODE_HELPER . "
const el = " . $this->editor_inner_el_js($this->current_weka) . ";
el.focus();
const word = " . json_encode($word) . ";
const type = " . json_encode($type) . ";
const domNode = findWekaNode(type, word);
const clickTarget = wekaNodeClickTarget(domNode, type);
clickTarget.click();
}());";
        $this->getSession()->executeScript($js);
    }

    /**
     * Select a weka node.
     *
     * @Given /^I select the "(?P<word>(?:[^"]|\\")*)" "(?P<type>(?:[^"]|\\")*)" in the weka editor$/
     * @param string $word Type-dependent identifier.
     * @param string $type
     */
    public function i_select_the_node_in_the_weka_editor($word, $type) {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        \behat_hooks::set_step_readonly(false);
        $js = "(function(){" . self::EDITOR_SELECT_HELPER . self::EDITOR_TEXT_NODE_HELPER . self::EDITOR_WEKA_NODE_HELPER . "
const el = " . $this->editor_inner_el_js($this->current_weka) . ";
el.focus();
const word = " . json_encode($word) . ";
const type = " . json_encode($type) . ";
const domNode = findWekaNode(type, word);
// simulate a mousedown/mouseup to make ProseMirror select the node
const clientRect = domNode.getBoundingClientRect();
const x = clientRect.x + (clientRect.width / 2), y = clientRect.y + (clientRect.height / 2);
domNode.dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true, clientX: x, clientY: y }));
domNode.dispatchEvent(new MouseEvent('mouseup', { bubbles: true, cancelable: true, clientX: x, clientY: y }));
}());";
        $this->getSession()->executeScript($js);
    }

    /**
     * Delete the currently selected weka node.
     *
     * @Given /^I delete the selected node in the weka editor$/
     */
    public function i_delete_the_selected_node_in_the_weka_editor() {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        \behat_hooks::set_step_readonly(false);
        $js = "(function(){" . self::EDITOR_SELECT_HELPER . self::EDITOR_TEXT_NODE_HELPER . self::EDITOR_WEKA_NODE_HELPER . "
const el = " . $this->editor_inner_el_js($this->current_weka) . ";
el.querySelector('.ProseMirror-selectednode').remove();
}());";
        $this->getSession()->executeScript($js);
    }

    /**
     * Open the weka block menu.
     *
     * @Given /^I click on the block menu in the weka editor$/
     */
    public function i_click_on_the_block_menu_in_the_weka_editor() {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        \behat_hooks::set_step_readonly(false);
        $trigger = $this->find('css', '.tui-editorWeka-toolbar__currentBlock', false, $this->current_weka);
        $trigger->click();
    }

    /**
     * Send an enter keypress.
     *
     * @Given /^I press enter in the weka editor$/
     */
    public function i_press_enter_in_the_weka_editor() {
        $this->i_type_in_the_weka_editor("\n");
    }
    /**
     * Send a backspace keypress.
     *
     * @Given /^I press backspace in the weka editor$/
     */
    public function i_press_backspace_in_the_weka_editor() {
        $this->i_type_in_the_weka_editor("\x08"); // backspace control char
    }

    /**
     * Assert that the text is present in the weka editor.
     *
     * @Given /^I should see "(?P<text>(?:[^"]|\\")*)" in the weka editor$/
     * @param string $text
     */
    public function i_should_see_in_the_weka_editor($text) {
        $this->text_in_weka_base($text, true);
    }

    /**
     * Assert that the text is not present in the weka editor.
     *
     * @Given /^I should not see "(?P<text>(?:[^"]|\\")*)" in the weka editor$/
     * @param string $text
     */
    public function i_should_not_see_in_the_weka_editor($text) {
        $this->text_in_weka_base($text, false);
    }

    /**
     * Base text present assert.
     *
     * @param string $text
     * @param bool $expected
     */
    private function text_in_weka_base($text, $expected) {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        \behat_hooks::set_step_readonly(true);
        $field_text = $this->current_weka_field->getText();
        $present = strpos($field_text, $text) !== false;
        if ($present !== $expected) {
            throw new \Behat\Mink\Exception\ExpectationException(
                'Text "'.$text.'" ' . ($expected ? 'could not be found' : 'is present') . ' in the weka editor',
                $this->getSession()
            );
        }
    }

    /**
     * Assert that the node is present in the weka editor.
     *
     * @Given /^I should see "(?P<word>(?:[^"]|\\")*)" "(?P<type>(?:[^"]|\\")*)" in the weka editor$/
     * @param string $word
     * @param string $type
     */
    public function i_should_see_node_in_the_weka_editor($word, $type) {
        $this->node_in_weka_base($word, $type, true);
    }

    /**
     * Assert that the node is not present in the weka editor.
     *
     * @Given /^I should not see "(?P<word>(?:[^"]|\\")*)" "(?P<type>(?:[^"]|\\")*)" in the weka editor$/
     * @param string $word
     * @param string $type
     */
    public function i_should_not_see_node_in_the_weka_editor($word, $type) {
        $this->node_in_weka_base($word, $type, false);
    }

    /**
     * Base node present assert.
     *
     * @param string $word
     * @param string $type
     * @param bool $expected
     */
    private function node_in_weka_base($word, $type, $expected) {
        if (!$this->current_weka) throw new coding_exception('Activate a Weka editor first.');
        \behat_hooks::set_step_readonly(true);
        $js = "(function(){" . self::EDITOR_SELECT_HELPER . self::EDITOR_TEXT_NODE_HELPER . self::EDITOR_WEKA_NODE_HELPER . "
const el = " . $this->editor_inner_el_js($this->current_weka) . ";
const word = " . json_encode($word) . ";
const type = " . json_encode($type) . ";
const domNode = findWekaNode(type, word);
if (" . ($expected ? '!' : '') . "domNode) {
    throw new Error('Weka node ' + type + ' identified by ' + word + ' " . ($expected ? 'not' : '') . " found');
}
}());";
        $this->getSession()->executeScript($js);
    }
}
