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
 * @author 2016 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/outputlib.php');

/**
 * Tests the flex_icon icons information known to Totara.
 *
 * @author 2016 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_flex_icon_icon_testcase extends advanced_testcase {

    /**
     * @var array|null
     */
    protected $flex_icon_translation_mapping = null;

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

    /**
     * Asserts the given icon data is all fine.
     *
     * @param string $newid
     * @param string $legacyid
     * @param string $component
     * @param string $expectediconhtml
     * @param string $iconmapping
     * @param string $legacyiconpath
     */
    public function assert_icon_data($newid, $legacyid, $component, $expectediconhtml, $iconmapping, $legacyiconpath) {
        global $CFG;

        $page = new moodle_page();
        $page->set_url('/index.php');
        $page->set_context(context_system::instance());
        /** @var core_renderer $renderer */
        $renderer = $page->get_renderer('core');

        $flexiconmap = $this->get_flex_icon_translation_mapping();

        $this->assertArrayHasKey($iconmapping, $flexiconmap, "No mapping found for $iconmapping");

        $flexicon = new \core\output\flex_icon($newid);
        $template =  $flexicon->get_template();
        $data = $flexicon->export_for_template($renderer);
        $flexicon_html = preg_replace('#>\s*<#s', '><', $renderer->render_from_template($template, $data));

        $flexicon_legacy = new \core\output\flex_icon($iconmapping);
        $template_legacy = $flexicon_legacy->get_template();
        $data_legacy = $flexicon_legacy->export_for_template($renderer);
        $flexicon_html_legacy = preg_replace('#>\s*<#s', '><', $renderer->render_from_template($template_legacy, $data_legacy));

        $this->assertSame(
            $expectediconhtml,
            $flexicon_html,
            "Mapping for $iconmapping references the incorrect icon."
        );

        $this->assertSame(
            $expectediconhtml,
            $flexicon_html_legacy,
            "Translation for $iconmapping references the incorrect icon."
        );

        $this->assertSame(
            $flexicon_html,
            $flexicon_html_legacy,
            "Mapping and translation generate different HTML."
        );

        $pixicon = new pix_icon($legacyid, '', $component);
        $pixiconhtml = preg_replace('#>\s*<#s', '><', $renderer->render($pixicon));
        if (strpos($legacyid, 'i/') === 0) {
            $pixiconhtml = str_replace(' ft-size-200', '', $pixiconhtml);
        }
        $this->assertSame(
            $flexicon_html_legacy,
            $pixiconhtml,
            "Pix icon and flexicon html do not match for $iconmapping"
        );
        $this->assertFileExists($CFG->dirroot.'/'.$legacyiconpath);
    }

    /**
     * Tests that download exists, and that tool_installaddon-icon maps to it.
     */
    public function test_tool_installaddon_icon() {
        $this->assert_icon_data(
            'download',
            'icon',
            'tool_installaddon',
            '<span aria-hidden="true" class="flex-icon fa fa-download"></span>',
            'tool_installaddon-icon',
            'admin/tool/installaddon/pix/icon.png'
        );
    }

    /**
     * Tests that download exists, and that core-i/import maps to it.
     */
    public function test_core_i_import() {
        $this->assert_icon_data(
            'download',
            'i/import',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-download"></span>',
            'core-i/import',
            'pix/i/import.png'
        );
    }

    /**
     * Tests that download exists, and that core-i/restore maps to it.
     */
    public function test_core_i_restore() {
        $this->assert_icon_data(
            'download',
            'i/restore',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-download"></span>',
            'core-i/restore',
            'pix/i/restore.png'
        );
    }

    /**
     * Tests that download exists, and that core-t/download maps to it.
     */
    public function test_core_t_download() {
        $this->assert_icon_data(
            'download',
            't/download',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-download"></span>',
            'core-t/download',
            'pix/t/download.png'
        );
    }

    /**
     * Tests that download exists, and that core-t/restore maps to it.
     */
    public function test_core_t_restore() {
        $this->assert_icon_data(
            'download',
            't/restore',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-download"></span>',
            'core-t/restore',
            'pix/t/restore.png'
        );
    }

    /**
     * Tests that plug exists, and that auth_connect-icon maps to it.
     */
    public function test_auth_connect_icon() {
        $this->assert_icon_data(
            'plug',
            'icon',
            'auth_connect',
            '<span aria-hidden="true" class="flex-icon fa fa-plug"></span>',
            'auth_connect-icon',
            'auth/connect/pix/icon.png'
        );
    }

    /**
     * Tests that calendar exists, and that block_gaccess-calendar maps to it.
     */
    public function test_block_gaccess_calendar() {
        $this->assert_icon_data(
            'calendar',
            'calendar',
            'block_gaccess',
            '<span aria-hidden="true" class="flex-icon fa fa-calendar"></span>',
            'block_gaccess-calendar',
            'blocks/gaccess/pix/calendar.png'
        );
    }

    /**
     * Tests that calendar exists, and that core-c/event maps to it.
     */
    public function test_core_c_event() {
        $this->assert_icon_data(
            'calendar',
            'c/event',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-calendar"></span>',
            'core-c/event',
            'pix/c/event.gif'
        );
    }

    /**
     * Tests that calendar exists, and that core-e/insert_date maps to it.
     */
    public function test_core_e_insert_date() {
        $this->assert_icon_data(
            'calendar',
            'e/insert_date',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-calendar"></span>',
            'core-e/insert_date',
            'pix/e/insert_date.png'
        );
    }

    /**
     * Tests that calendar exists, and that core-i/calendar maps to it.
     */
    public function test_core_i_calendar() {
        $this->assert_icon_data(
            'calendar',
            'i/calendar',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-calendar"></span>',
            'core-i/calendar',
            'pix/i/calendar.png'
        );
    }

    /**
     * Tests that calendar exists, and that core-i/siteevent maps to it.
     */
    public function test_core_i_siteevent() {
        $this->assert_icon_data(
            'calendar',
            'i/siteevent',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-calendar"></span>',
            'core-i/siteevent',
            'pix/i/siteevent.png'
        );
    }

    /**
     * Tests that calendar exists, and that core-t/calendar maps to it.
     */
    public function test_core_t_calendar() {
        $this->assert_icon_data(
            'calendar',
            't/calendar',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-calendar"></span>',
            'core-t/calendar',
            'pix/t/calendar.gif'
        );
    }

    /**
     * Tests that calendar exists, and that totara_core-bookings maps to it.
     */
    public function test_totara_core_bookings() {
        $this->assert_icon_data(
            'calendar',
            'bookings',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-calendar"></span>',
            'totara_core-bookings',
            'totara/core/pix/bookings.png'
        );
    }

    /**
     * Tests that calendar exists, and that totara_core-t/calendar maps to it.
     */
    public function test_totara_core_t_calendar() {
        $this->assert_icon_data(
            'calendar',
            't/calendar',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-calendar"></span>',
            'totara_core-t/calendar',
            'totara/core/pix/t/calendar.gif'
        );
    }

    /**
     * Tests that file-text exists, and that block_gaccess-gdocs maps to it.
     */
    public function test_block_gaccess_gdocs() {
        $this->assert_icon_data(
            'file-text',
            'gdocs',
            'block_gaccess',
            '<span aria-hidden="true" class="flex-icon fa fa-file-text"></span>',
            'block_gaccess-gdocs',
            'blocks/gaccess/pix/gdocs.png'
        );
    }

    /**
     * Tests that envelope-o exists, and that block_gaccess-gmail maps to it.
     */
    public function test_block_gaccess_gmail() {
        $this->assert_icon_data(
            'envelope-o',
            'gmail',
            'block_gaccess',
            '<span aria-hidden="true" class="flex-icon fa fa-envelope-o"></span>',
            'block_gaccess-gmail',
            'blocks/gaccess/pix/gmail.png'
        );
    }

    /**
     * Tests that envelope-o exists, and that core-i/email maps to it.
     */
    public function test_core_i_email() {
        $this->assert_icon_data(
            'envelope-o',
            'i/email',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-envelope-o"></span>',
            'core-i/email',
            'pix/i/email.gif'
        );
    }

    /**
     * Tests that envelope-o exists, and that core-t/email maps to it.
     */
    public function test_core_t_email() {
        $this->assert_icon_data(
            'envelope-o',
            't/email',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-envelope-o"></span>',
            'core-t/email',
            'pix/t/email.png'
        );
    }

    /**
     * Tests that no-key exists, and that enrol_guest-withoutpassword maps to it.
     */
    public function test_enrol_guest_withoutpassword() {
        $this->assert_icon_data(
            'no-key',
            'withoutpassword',
            'enrol_guest',
            '<span class="flex-icon ft-stack"><span class="fa fa-key ft-stack-main"></span><span class="ft ft-slash ft-stack-over ft-state-danger"></span></span>',
            'enrol_guest-withoutpassword',
            'enrol/guest/pix/withoutpassword.png'
        );
    }

    /**
     * Tests that no-key exists, and that enrol_self-withoutkey maps to it.
     */
    public function test_enrol_self_withoutkey() {
        $this->assert_icon_data(
            'no-key',
            'withoutkey',
            'enrol_self',
            '<span class="flex-icon ft-stack"><span class="fa fa-key ft-stack-main"></span><span class="ft ft-slash ft-stack-over ft-state-danger"></span></span>',
            'enrol_self-withoutkey',
            'enrol/self/pix/withoutkey.png'
        );
    }

    /**
     * Tests that no-key exists, and that enrol_totara_facetoface-withoutkey maps to it.
     */
    public function test_enrol_totara_facetoface_withoutkey() {
        $this->assert_icon_data(
            'no-key',
            'withoutkey',
            'enrol_totara_facetoface',
            '<span class="flex-icon ft-stack"><span class="fa fa-key ft-stack-main"></span><span class="ft ft-slash ft-stack-over ft-state-danger"></span></span>',
            'enrol_totara_facetoface-withoutkey',
            'enrol/totara_facetoface/pix/withoutkey.png'
        );
    }

    /**
     * Tests that key exists, and that enrol_guest-withpassword maps to it.
     */
    public function test_enrol_guest_withpassword() {
        $this->assert_icon_data(
            'key',
            'withpassword',
            'enrol_guest',
            '<span aria-hidden="true" class="flex-icon fa fa-key"></span>',
            'enrol_guest-withpassword',
            'enrol/guest/pix/withpassword.png'
        );
    }

    /**
     * Tests that key exists, and that enrol_self-withkey maps to it.
     */
    public function test_enrol_self_withkey() {
        $this->assert_icon_data(
            'key',
            'withkey',
            'enrol_self',
            '<span aria-hidden="true" class="flex-icon fa fa-key"></span>',
            'enrol_self-withkey',
            'enrol/self/pix/withkey.png'
        );
    }

    /**
     * Tests that key exists, and that enrol_totara_facetoface-withkey maps to it.
     */
    public function test_enrol_totara_facetoface_withkey() {
        $this->assert_icon_data(
            'key',
            'withkey',
            'enrol_totara_facetoface',
            '<span aria-hidden="true" class="flex-icon fa fa-key"></span>',
            'enrol_totara_facetoface-withkey',
            'enrol/totara_facetoface/pix/withkey.png'
        );
    }

    /**
     * Tests that key exists, and that core-i/key maps to it.
     */
    public function test_core_i_key() {
        $this->assert_icon_data(
            'key',
            'i/key',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-key"></span>',
            'core-i/key',
            'pix/i/key.gif'
        );
    }

    /**
     * Tests that paypal exists, and that enrol_paypal-icon maps to it.
     */
    public function test_enrol_paypal_icon() {
        $this->assert_icon_data(
            'paypal',
            'icon',
            'enrol_paypal',
            '<span aria-hidden="true" class="flex-icon fa fa-paypal"></span>',
            'enrol_paypal-icon',
            'enrol/paypal/pix/icon.png'
        );
    }

    /**
     * Tests that comment exists, and that assignfeedback_editpdf-comment maps to it.
     */
    public function test_assignfeedback_editpdf_comment() {
        $this->assert_icon_data(
            'comment',
            'comment',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon fa fa-comment"></span>',
            'assignfeedback_editpdf-comment',
            'mod/assign/feedback/editpdf/pix/comment.png'
        );
    }

    /**
     * Tests that comment exists, and that totara_core-t/comments maps to it.
     */
    public function test_totara_core_t_comments() {
        $this->assert_icon_data(
            'comment',
            't/comments',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-comment"></span>',
            'totara_core-t/comments',
            'totara/core/pix/t/comments.gif'
        );
    }

    /**
     * Tests that slash exists, and that assignfeedback_editpdf-line maps to it.
     */
    public function test_assignfeedback_editpdf_line() {
        $this->assert_icon_data(
            'slash',
            'line',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon ft ft-slash"></span>',
            'assignfeedback_editpdf-line',
            'mod/assign/feedback/editpdf/pix/line.png'
        );
    }

    /**
     * Tests that caret-down exists, and that core-i/dropdown maps to it.
     */
    public function test_core_i_dropdown() {
        $this->assert_icon_data(
            'caret-down',
            'i/dropdown',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-down"></span>',
            'core-i/dropdown',
            'pix/i/dropdown.png'
        );
    }

    /**
     * Tests that caret-down exists, and that core-t/dropdown maps to it.
     */
    public function test_core_t_dropdown() {
        $this->assert_icon_data(
            'caret-down',
            't/dropdown',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-down"></span>',
            'core-t/dropdown',
            'pix/t/dropdown.png'
        );
    }

    /**
     * Tests that caret-down exists, and that core-t/expanded maps to it.
     */
    public function test_core_t_expanded() {
        $this->assert_icon_data(
            'caret-down',
            't/expanded',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-down"></span>',
            'core-t/expanded',
            'pix/t/expanded.png'
        );
    }

    /**
     * Tests that caret-down exists, and that core-y/lm maps to it.
     */
    public function test_core_y_lm() {
        $this->assert_icon_data(
            'caret-down',
            'y/lm',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-down"></span>',
            'core-y/lm',
            'pix/y/lm.png'
        );
    }

    /**
     * Tests that caret-down exists, and that core-y/tm maps to it.
     */
    public function test_core_y_tm() {
        $this->assert_icon_data(
            'caret-down',
            'y/tm',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-down"></span>',
            'core-y/tm',
            'pix/y/tm.png'
        );
    }

    /**
     * Tests that caret-up exists, and that mod_book-nav_exit maps to it.
     */
    public function test_mod_book_nav_exit() {
        $this->assert_icon_data(
            'caret-up',
            'nav_exit',
            'mod_book',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-up"></span>',
            'mod_book-nav_exit',
            'mod/book/pix/nav_exit.png'
        );
    }

    /**
     * Tests that caret-right exists, and that assignfeedback_editpdf-nav_next maps to it.
     */
    public function test_assignfeedback_editpdf_nav_next() {
        $this->assert_icon_data(
            'caret-right',
            'nav_next',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-right"></span>',
            'assignfeedback_editpdf-nav_next',
            'mod/assign/feedback/editpdf/pix/nav_next.png'
        );
    }

    /**
     * Tests that caret-right exists, and that mod_book-nav_next maps to it.
     */
    public function test_mod_book_nav_next() {
        $this->assert_icon_data(
            'caret-right',
            'nav_next',
            'mod_book',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-right"></span>',
            'mod_book-nav_next',
            'mod/book/pix/nav_next.png'
        );
    }

    /**
     * Tests that caret-right exists, and that core-t/collapsed maps to it.
     */
    public function test_core_t_collapsed() {
        $this->assert_icon_data(
            'caret-right',
            't/collapsed',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-right"></span>',
            'core-t/collapsed',
            'pix/t/collapsed.png'
        );
    }

    /**
     * Tests that caret-right exists, and that core-y/lp maps to it.
     */
    public function test_core_y_lp() {
        $this->assert_icon_data(
            'caret-right',
            'y/lp',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-right"></span>',
            'core-y/lp',
            'pix/y/lp.png'
        );
    }

    /**
     * Tests that caret-right exists, and that core-y/tp maps to it.
     */
    public function test_core_y_tp() {
        $this->assert_icon_data(
            'caret-right',
            'y/tp',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-right"></span>',
            'core-y/tp',
            'pix/y/tp.png'
        );
    }

    /**
     * Tests that caret-left exists, and that assignfeedback_editpdf-nav_prev maps to it.
     */
    public function test_assignfeedback_editpdf_nav_prev() {
        $this->assert_icon_data(
            'caret-left',
            'nav_prev',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-left"></span>',
            'assignfeedback_editpdf-nav_prev',
            'mod/assign/feedback/editpdf/pix/nav_prev.png'
        );
    }

    /**
     * Tests that caret-left exists, and that mod_book-nav_prev maps to it.
     */
    public function test_mod_book_nav_prev() {
        $this->assert_icon_data(
            'caret-left',
            'nav_prev',
            'mod_book',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-left"></span>',
            'mod_book-nav_prev',
            'mod/book/pix/nav_prev.png'
        );
    }

    /**
     * Tests that caret-left exists, and that core-t/collapsed_rtl maps to it.
     */
    public function test_core_t_collapsed_rtl() {
        $this->assert_icon_data(
            'caret-left',
            't/collapsed_rtl',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-left"></span>',
            'core-t/collapsed_rtl',
            'pix/t/collapsed_rtl.png'
        );
    }

    /**
     * Tests that caret-left exists, and that core-y/lp_rtl maps to it.
     */
    public function test_core_y_lp_rtl() {
        $this->assert_icon_data(
            'caret-left',
            'y/lp_rtl',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-left"></span>',
            'core-y/lp_rtl',
            'pix/y/lp_rtl.png'
        );
    }

    /**
     * Tests that caret-left exists, and that core-y/tp_rtl maps to it.
     */
    public function test_core_y_tp_rtl() {
        $this->assert_icon_data(
            'caret-left',
            'y/tp_rtl',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-left"></span>',
            'core-y/tp_rtl',
            'pix/y/tp_rtl.png'
        );
    }

    /**
     * Tests that caret-left-info exists, and that totara_core-comment-point-blue maps to it.
     */
    public function test_totara_core_comment_point_blue() {
        $this->assert_icon_data(
            'caret-left-info',
            'comment-point-blue',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-left ft-state-info"></span>',
            'totara_core-comment-point-blue',
            'totara/core/pix/comment-point-blue.gif'
        );
    }

    /**
     * Tests that caret-right-disabled exists, and that mod_book-nav_next_dis maps to it.
     */
    public function test_mod_book_nav_next_dis() {
        $this->assert_icon_data(
            'caret-right-disabled',
            'nav_next_dis',
            'mod_book',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-right ft-state-disabled"></span>',
            'mod_book-nav_next_dis',
            'mod/book/pix/nav_next_dis.png'
        );
    }

    /**
     * Tests that caret-right-disabled exists, and that core-t/collapsed_empty maps to it.
     */
    public function test_core_t_collapsed_empty() {
        $this->assert_icon_data(
            'caret-right-disabled',
            't/collapsed_empty',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-right ft-state-disabled"></span>',
            'core-t/collapsed_empty',
            'pix/t/collapsed_empty.png'
        );
    }

    /**
     * Tests that caret-right-disabled exists, and that totara_core-comment-point-grey maps to it.
     */
    public function test_totara_core_comment_point_grey() {
        $this->assert_icon_data(
            'caret-right-disabled',
            'comment-point-grey',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-right ft-state-disabled"></span>',
            'totara_core-comment-point-grey',
            'totara/core/pix/comment-point-grey.gif'
        );
    }

    /**
     * Tests that caret-left-disabled exists, and that mod_book-nav_prev_dis maps to it.
     */
    public function test_mod_book_nav_prev_dis() {
        $this->assert_icon_data(
            'caret-left-disabled',
            'nav_prev_dis',
            'mod_book',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-left ft-state-disabled"></span>',
            'mod_book-nav_prev_dis',
            'mod/book/pix/nav_prev_dis.png'
        );
    }

    /**
     * Tests that caret-left-disabled exists, and that core-t/collapsed_empty_rtl maps to it.
     */
    public function test_core_t_collapsed_empty_rtl() {
        $this->assert_icon_data(
            'caret-left-disabled',
            't/collapsed_empty_rtl',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-left ft-state-disabled"></span>',
            'core-t/collapsed_empty_rtl',
            'pix/t/collapsed_empty_rtl.png'
        );
    }

    /**
     * Tests that caret-left-disabled exists, and that totara_core-comment-point-grey-rtl maps to it.
     */
    public function test_totara_core_comment_point_grey_rtl() {
        $this->assert_icon_data(
            'caret-left-disabled',
            'comment-point-grey-rtl',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-left ft-state-disabled"></span>',
            'totara_core-comment-point-grey-rtl',
            'totara/core/pix/comment-point-grey-rtl.gif'
        );
    }

    /**
     * Tests that circle-o exists, and that assignfeedback_editpdf-oval maps to it.
     */
    public function test_assignfeedback_editpdf_oval() {
        $this->assert_icon_data(
            'circle-o',
            'oval',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon fa fa-circle-o"></span>',
            'assignfeedback_editpdf-oval',
            'mod/assign/feedback/editpdf/pix/oval.png'
        );
    }

    /**
     * Tests that pencil exists, and that assignfeedback_editpdf-pen maps to it.
     */
    public function test_assignfeedback_editpdf_pen() {
        $this->assert_icon_data(
            'pencil',
            'pen',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon fa fa-pencil"></span>',
            'assignfeedback_editpdf-pen',
            'mod/assign/feedback/editpdf/pix/pen.png'
        );
    }

    /**
     * Tests that square-o exists, and that assignfeedback_editpdf-rectangle maps to it.
     */
    public function test_assignfeedback_editpdf_rectangle() {
        $this->assert_icon_data(
            'square-o',
            'rectangle',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon fa fa-square-o"></span>',
            'assignfeedback_editpdf-rectangle',
            'mod/assign/feedback/editpdf/pix/rectangle.png'
        );
    }

    /**
     * Tests that square-o exists, and that mod_scorm-notattempted maps to it.
     */
    public function test_mod_scorm_notattempted() {
        $this->assert_icon_data(
            'square-o',
            'notattempted',
            'mod_scorm',
            '<span aria-hidden="true" class="flex-icon fa fa-square-o"></span>',
            'mod_scorm-notattempted',
            'mod/scorm/pix/notattempted.gif'
        );
    }

    /**
     * Tests that square-o exists, and that totara_core-i/completion-rpl-n maps to it.
     */
    public function test_totara_core_i_completion_rpl_n() {
        $this->assert_icon_data(
            'square-o',
            'i/completion-rpl-n',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-square-o"></span>',
            'totara_core-i/completion-rpl-n',
            'totara/core/pix/i/completion-rpl-n.gif'
        );
    }

    /**
     * Tests that check-square-o exists, and that mod_data-field/checkbox maps to it.
     */
    public function test_mod_data_field_checkbox() {
        $this->assert_icon_data(
            'check-square-o',
            'field/checkbox',
            'mod_data',
            '<span aria-hidden="true" class="flex-icon fa fa-check-square-o"></span>',
            'mod_data-field/checkbox',
            'mod/data/pix/field/checkbox.gif'
        );
    }

    /**
     * Tests that check-square-o exists, and that mod_scorm-passed maps to it.
     */
    public function test_mod_scorm_passed() {
        $this->assert_icon_data(
            'check-square-o',
            'passed',
            'mod_scorm',
            '<span aria-hidden="true" class="flex-icon fa fa-check-square-o"></span>',
            'mod_scorm-passed',
            'mod/scorm/pix/passed.gif'
        );
    }

    /**
     * Tests that check-square-o exists, and that totara_core-i/completion-rpl-y maps to it.
     */
    public function test_totara_core_i_completion_rpl_y() {
        $this->assert_icon_data(
            'check-square-o',
            'i/completion-rpl-y',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-square-o"></span>',
            'totara_core-i/completion-rpl-y',
            'totara/core/pix/i/completion-rpl-y.gif'
        );
    }

    /**
     * Tests that frown-o exists, and that assignfeedback_editpdf-sad maps to it.
     */
    public function test_assignfeedback_editpdf_sad() {
        $this->assert_icon_data(
            'frown-o',
            'sad',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon fa fa-frown-o"></span>',
            'assignfeedback_editpdf-sad',
            'mod/assign/feedback/editpdf/pix/sad.png'
        );
    }

    /**
     * Tests that smile-o exists, and that assignfeedback_editpdf-smile maps to it.
     */
    public function test_assignfeedback_editpdf_smile() {
        $this->assert_icon_data(
            'smile-o',
            'smile',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon fa fa-smile-o"></span>',
            'assignfeedback_editpdf-smile',
            'mod/assign/feedback/editpdf/pix/smile.png'
        );
    }

    /**
     * Tests that smile-o exists, and that core-e/emoticons maps to it.
     */
    public function test_core_e_emoticons() {
        $this->assert_icon_data(
            'smile-o',
            'e/emoticons',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-smile-o"></span>',
            'core-e/emoticons',
            'pix/e/emoticons.png'
        );
    }

    /**
     * Tests that smile-o exists, and that core-g/f1 maps to it.
     */
    public function test_core_g_f1() {
        $this->assert_icon_data(
            'smile-o',
            'g/f1',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-smile-o"></span>',
            'core-g/f1',
            'pix/g/f1.png'
        );
    }

    /**
     * Tests that smile-o exists, and that core-g/f2 maps to it.
     */
    public function test_core_g_f2() {
        $this->assert_icon_data(
            'smile-o',
            'g/f2',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-smile-o"></span>',
            'core-g/f2',
            'pix/g/f2.png'
        );
    }

    /**
     * Tests that mouse-pointer exists, and that assignfeedback_editpdf-select maps to it.
     */
    public function test_assignfeedback_editpdf_select() {
        $this->assert_icon_data(
            'mouse-pointer',
            'select',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon fa fa-mouse-pointer"></span>',
            'assignfeedback_editpdf-select',
            'mod/assign/feedback/editpdf/pix/select.png'
        );
    }

    /**
     * Tests that times-danger exists, and that assignfeedback_editpdf-cross maps to it.
     */
    public function test_assignfeedback_editpdf_cross() {
        $this->assert_icon_data(
            'times-danger',
            'cross',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'assignfeedback_editpdf-cross',
            'mod/assign/feedback/editpdf/pix/cross.png'
        );
    }

    /**
     * Tests that times-danger exists, and that mod_workshop-userplan/task-fail maps to it.
     */
    public function test_mod_workshop_userplan_task_fail() {
        $this->assert_icon_data(
            'times-danger',
            'userplan/task-fail',
            'mod_workshop',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'mod_workshop-userplan/task-fail',
            'mod/workshop/pix/userplan/task-fail.png'
        );
    }

    /**
     * Tests that times-danger exists, and that core-b/edit-delete maps to it.
     */
    public function test_core_b_edit_delete() {
        $this->assert_icon_data(
            'times-danger',
            'b/edit-delete',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'core-b/edit-delete',
            'pix/b/edit-delete.png'
        );
    }

    /**
     * Tests that times-danger exists, and that core-i/grade_incorrect maps to it.
     */
    public function test_core_i_grade_incorrect() {
        $this->assert_icon_data(
            'times-danger',
            'i/grade_incorrect',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'core-i/grade_incorrect',
            'pix/i/grade_incorrect.png'
        );
    }

    /**
     * Tests that times-danger exists, and that core-i/invalid maps to it.
     */
    public function test_core_i_invalid() {
        $this->assert_icon_data(
            'times-danger',
            'i/invalid',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'core-i/invalid',
            'pix/i/invalid.png'
        );
    }

    /**
     * Tests that times-danger exists, and that core-t/delete maps to it.
     */
    public function test_core_t_delete() {
        $this->assert_icon_data(
            'times-danger',
            't/delete',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'core-t/delete',
            'pix/t/delete.png'
        );
    }

    /**
     * Tests that times-danger exists, and that totara_core-i/bullet_delete maps to it.
     */
    public function test_totara_core_i_bullet_delete() {
        $this->assert_icon_data(
            'times-danger',
            'i/bullet_delete',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'totara_core-i/bullet_delete',
            'totara/core/pix/i/bullet_delete.png'
        );
    }

    /**
     * Tests that times-disabled exists, and that totara_core-i/delete_grey maps to it.
     */
    public function test_totara_core_i_delete_grey() {
        $this->assert_icon_data(
            'times-disabled',
            'i/delete_grey',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-disabled"></span>',
            'totara_core-i/delete_grey',
            'totara/core/pix/i/delete_grey.gif'
        );
    }

    /**
     * Tests that times-disabled exists, and that totara_core-t/delete_grey maps to it.
     */
    public function test_totara_core_t_delete_grey() {
        $this->assert_icon_data(
            'times-disabled',
            't/delete_grey',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-disabled"></span>',
            'totara_core-t/delete_grey',
            'totara/core/pix/t/delete_grey.png'
        );
    }

    /**
     * Tests that check exists, and that core-t/check maps to it.
     */
    public function test_core_t_check() {
        $this->assert_icon_data(
            'check',
            't/check',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-check"></span>',
            'core-t/check',
            'pix/t/check.png'
        );
    }

    /**
     * Tests that check exists, and that core-t/approve maps to it.
     */
    public function test_core_t_approve() {
        $this->assert_icon_data(
            'check',
            't/approve',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-check"></span>',
            'core-t/approve',
            'pix/t/approve.png'
        );
    }

    /**
     * Tests that check exists, and that core-t/markasread maps to it.
     */
    public function test_core_t_markasread() {
        $this->assert_icon_data(
            'check',
            't/markasread',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-check"></span>',
            'core-t/markasread',
            'pix/t/markasread.png'
        );
    }

    /**
     * Tests that check exists, and that core-t/unblock maps to it.
     */
    public function test_core_t_unblock() {
        $this->assert_icon_data(
            'check',
            't/unblock',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-check"></span>',
            'core-t/unblock',
            'pix/t/unblock.png'
        );
    }

    /**
     * Tests that check exists, and that totara_appraisal-tick2 maps to it.
     */
    public function test_totara_appraisal_tick2() {
        $this->assert_icon_data(
            'check',
            'tick2',
            'totara_appraisal',
            '<span aria-hidden="true" class="flex-icon fa fa-check"></span>',
            'totara_appraisal-tick2',
            'totara/appraisal/pix/tick2.png'
        );
    }

    /**
     * Tests that check exists, and that totara_flavour-tick maps to it.
     */
    public function test_totara_flavour_tick() {
        $this->assert_icon_data(
            'check',
            'tick',
            'totara_flavour',
            '<span aria-hidden="true" class="flex-icon fa fa-check"></span>',
            'totara_flavour-tick',
            'totara/flavour/pix/tick.svg'
        );
    }

    /**
     * Tests that check-success exists, and that assignfeedback_editpdf-tick maps to it.
     */
    public function test_assignfeedback_editpdf_tick() {
        $this->assert_icon_data(
            'check-success',
            'tick',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon fa fa-check ft-state-success"></span>',
            'assignfeedback_editpdf-tick',
            'mod/assign/feedback/editpdf/pix/tick.png'
        );
    }

    /**
     * Tests that check-success exists, and that mod_workshop-userplan/task-done maps to it.
     */
    public function test_mod_workshop_userplan_task_done() {
        $this->assert_icon_data(
            'check-success',
            'userplan/task-done',
            'mod_workshop',
            '<span aria-hidden="true" class="flex-icon fa fa-check ft-state-success"></span>',
            'mod_workshop-userplan/task-done',
            'mod/workshop/pix/userplan/task-done.png'
        );
    }

    /**
     * Tests that check-success exists, and that core-e/tick maps to it.
     */
    public function test_core_e_tick() {
        $this->assert_icon_data(
            'check-success',
            'e/tick',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-check ft-state-success"></span>',
            'core-e/tick',
            'pix/e/tick.png'
        );
    }

    /**
     * Tests that check-success exists, and that core-i/grade_correct maps to it.
     */
    public function test_core_i_grade_correct() {
        $this->assert_icon_data(
            'check-success',
            'i/grade_correct',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-check ft-state-success"></span>',
            'core-i/grade_correct',
            'pix/i/grade_correct.png'
        );
    }

    /**
     * Tests that check-success exists, and that core-i/valid maps to it.
     */
    public function test_core_i_valid() {
        $this->assert_icon_data(
            'check-success',
            'i/valid',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-check ft-state-success"></span>',
            'core-i/valid',
            'pix/i/valid.png'
        );
    }

    /**
     * Tests that check-warning exists, and that core-i/grade_partiallycorrect maps to it.
     */
    public function test_core_i_grade_partiallycorrect() {
        $this->assert_icon_data(
            'check-warning',
            'i/grade_partiallycorrect',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-check ft-state-warning"></span>',
            'core-i/grade_partiallycorrect',
            'pix/i/grade_partiallycorrect.png'
        );
    }

    /**
     * Tests that check-disabled exists, and that mod_workshop-userplan/task-* maps to it.
     */
    public function test_mod_workshop_userplan_task_todo() {
        $this->assert_icon_data(
            'check-disabled',
            'userplan/task-todo',
            'mod_workshop',
            '<span aria-hidden="true" class="flex-icon fa fa-check ft-state-disabled"></span>',
            'mod_workshop-userplan/task-todo',
            'mod/workshop/pix/userplan/task-todo.png'
        );
    }

    /**
     * Tests that trash exists, and that assignfeedback_editpdf-trash maps to it.
     */
    public function test_assignfeedback_editpdf_trash() {
        $this->assert_icon_data(
            'trash',
            'trash',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon fa fa-trash"></span>',
            'assignfeedback_editpdf-trash',
            'mod/assign/feedback/editpdf/pix/trash.png'
        );
    }

    /**
     * Tests that edit exists, and that mod_assign-gradefeedback maps to it.
     */
    public function test_mod_assign_gradefeedback() {
        $this->assert_icon_data(
            'edit',
            'gradefeedback',
            'mod_assign',
            '<span aria-hidden="true" class="flex-icon fa fa-edit"></span>',
            'mod_assign-gradefeedback',
            'mod/assign/pix/gradefeedback.png'
        );
    }

    /**
     * Tests that edit exists, and that core-i/edit maps to it.
     */
    public function test_core_i_edit() {
        $this->assert_icon_data(
            'edit',
            'i/edit',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-edit"></span>',
            'core-i/edit',
            'pix/i/edit.png'
        );
    }

    /**
     * Tests that edit exists, and that core-i/manual_item maps to it.
     */
    public function test_core_i_manual_item() {
        $this->assert_icon_data(
            'edit',
            'i/manual_item',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-edit"></span>',
            'core-i/manual_item',
            'pix/i/manual_item.png'
        );
    }

    /**
     * Tests that edit exists, and that core-t/edit_gray maps to it.
     */
    public function test_core_t_edit_gray() {
        $this->assert_icon_data(
            'edit',
            't/edit_gray',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-edit"></span>',
            'core-t/edit_gray',
            'pix/t/edit_gray.png'
        );
    }

    /**
     * Tests that edit exists, and that core-t/editstring maps to it.
     */
    public function test_core_t_editstring() {
        $this->assert_icon_data(
            'edit',
            't/editstring',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-edit"></span>',
            'core-t/editstring',
            'pix/t/editstring.png'
        );
    }

    /**
     * Tests that file-text-o exists, and that mod_assignment-icon maps to it.
     */
    public function test_mod_assignment_icon() {
        $this->assert_icon_data(
            'file-text-o',
            'icon',
            'mod_assignment',
            '<span aria-hidden="true" class="flex-icon fa fa-file-text-o"></span>',
            'mod_assignment-icon',
            'mod/assignment/pix/icon.png'
        );
    }

    /**
     * Tests that file-text-o exists, and that core-f/document maps to it.
     */
    public function test_core_f_document() {
        $this->assert_icon_data(
            'file-text-o',
            'f/document',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-text-o"></span>',
            'core-f/document',
            'pix/f/document.png'
        );
    }

    /**
     * Tests that file-text-o exists, and that core-f/text maps to it.
     */
    public function test_core_f_text() {
        $this->assert_icon_data(
            'file-text-o',
            'f/text',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-text-o"></span>',
            'core-f/text',
            'pix/f/text.png'
        );
    }

    /**
     * Tests that file-text-o exists, and that core-i/report maps to it.
     */
    public function test_core_i_report() {
        $this->assert_icon_data(
            'file-text-o',
            'i/report',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-text-o"></span>',
            'core-i/report',
            'pix/i/report.png'
        );
    }

    /**
     * Tests that file-text-o exists, and that totara_core-t/file maps to it.
     */
    public function test_totara_core_t_file() {
        $this->assert_icon_data(
            'file-text-o',
            't/file',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-text-o"></span>',
            'totara_core-t/file',
            'totara/core/pix/t/file.gif'
        );
    }

    /**
     * Tests that plus exists, and that mod_book-add maps to it.
     */
    public function test_mod_book_add() {
        $this->assert_icon_data(
            'plus',
            'add',
            'mod_book',
            '<span aria-hidden="true" class="flex-icon fa fa-plus"></span>',
            'mod_book-add',
            'mod/book/pix/add.png'
        );
    }

    /**
     * Tests that plus exists, and that core-t/add maps to it.
     */
    public function test_core_t_add() {
        $this->assert_icon_data(
            'plus',
            't/add',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus"></span>',
            'core-t/add',
            'pix/t/add.png'
        );
    }

    /**
     * Tests that plus exists, and that core-t/enroladd maps to it.
     */
    public function test_core_t_enroladd() {
        $this->assert_icon_data(
            'plus',
            't/enroladd',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus"></span>',
            'core-t/enroladd',
            'pix/t/enroladd.gif'
        );
    }

    /**
     * Tests that plus exists, and that core-t/more maps to it.
     */
    public function test_core_t_more() {
        $this->assert_icon_data(
            'plus',
            't/more',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus"></span>',
            'core-t/more',
            'pix/t/more.png'
        );
    }

    /**
     * Tests that book-open exists, and that mod_book-chapter maps to it.
     */
    public function test_mod_book_chapter() {
        $this->assert_icon_data(
            'book-open',
            'chapter',
            'mod_book',
            '<span aria-hidden="true" class="flex-icon ft ft-book-open"></span>',
            'mod_book-chapter',
            'mod/book/pix/chapter.png'
        );
    }

    /**
     * Tests that book exists, and that mod_book-icon maps to it.
     */
    public function test_mod_book_icon() {
        $this->assert_icon_data(
            'book',
            'icon',
            'mod_book',
            '<span aria-hidden="true" class="flex-icon fa fa-book"></span>',
            'mod_book-icon',
            'mod/book/pix/icon.png'
        );
    }

    /**
     * Tests that pipe exists, and that mod_book-nav_sep maps to it.
     */
    public function test_mod_book_nav_sep() {
        $this->assert_icon_data(
            'pipe',
            'nav_sep',
            'mod_book',
            '<span aria-hidden="true" class="flex-icon ft ft-pipe"></span>',
            'mod_book-nav_sep',
            'mod/book/pix/nav_sep.png'
        );
    }

    /**
     * Tests that export-imscp exists, and that booktool_exportimscp-generate maps to it.
     */
    public function test_booktool_exportimscp_generate() {
        $this->assert_icon_data(
            'export-imscp',
            'generate',
            'booktool_exportimscp',
            '<span class="flex-icon ft-stack"><span class="ft ft-package ft-stack-main"></span><span class="fa fa-arrow-right ft-stack-suffix"></span></span>',
            'booktool_exportimscp-generate',
            'mod/book/tool/exportimscp/pix/generate.png'
        );
    }

    /**
     * Tests that print-book exists, and that booktool_print-book maps to it.
     */
    public function test_booktool_print_book() {
        $this->assert_icon_data(
            'print-book',
            'book',
            'booktool_print',
            '<span class="flex-icon ft-stack"><span class="fa fa-book ft-stack-main"></span><span class="fa fa-print ft-stack-suffix"></span></span>',
            'booktool_print-book',
            'mod/book/tool/print/pix/book.png'
        );
    }

    /**
     * Tests that print-chapter exists, and that booktool_print-chapter maps to it.
     */
    public function test_booktool_print_chapter() {
        $this->assert_icon_data(
            'print-chapter',
            'chapter',
            'booktool_print',
            '<span class="flex-icon ft-stack"><span class="ft ft-book-open ft-stack-main"></span><span class="fa fa-print ft-stack-suffix"></span></span>',
            'booktool_print-chapter',
            'mod/book/tool/print/pix/chapter.png'
        );
    }

    /**
     * Tests that comments exists, and that mod_chat-icon maps to it.
     */
    public function test_mod_chat_icon() {
        $this->assert_icon_data(
            'comments',
            'icon',
            'mod_chat',
            '<span aria-hidden="true" class="flex-icon fa fa-comments"></span>',
            'mod_chat-icon',
            'mod/chat/pix/icon.png'
        );
    }

    /**
     * Tests that columns exists, and that mod_choice-column maps to it.
     */
    public function test_mod_choice_column() {
        $this->assert_icon_data(
            'columns',
            'column',
            'mod_choice',
            '<span aria-hidden="true" class="flex-icon ft ft-columns"></span>',
            'mod_choice-column',
            'mod/choice/pix/column.png'
        );
    }

    /**
     * Tests that align-justify exists, and that mod_choice-row maps to it.
     */
    public function test_mod_choice_row() {
        $this->assert_icon_data(
            'align-justify',
            'row',
            'mod_choice',
            '<span aria-hidden="true" class="flex-icon fa fa-align-justify"></span>',
            'mod_choice-row',
            'mod/choice/pix/row.png'
        );
    }

    /**
     * Tests that question-circle exists, and that mod_choice-icon maps to it.
     */
    public function test_mod_choice_icon() {
        $this->assert_icon_data(
            'question-circle',
            'icon',
            'mod_choice',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle"></span>',
            'mod_choice-icon',
            'mod/choice/pix/icon.png'
        );
    }

    /**
     * Tests that question-circle exists, and that core-a/help maps to it.
     */
    public function test_core_a_help() {
        $this->assert_icon_data(
            'question-circle',
            'a/help',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle"></span>',
            'core-a/help',
            'pix/a/help.png'
        );
    }

    /**
     * Tests that question-circle exists, and that core-e/help maps to it.
     */
    public function test_core_e_help() {
        $this->assert_icon_data(
            'question-circle',
            'e/help',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle"></span>',
            'core-e/help',
            'pix/e/help.png'
        );
    }

    /**
     * Tests that question-circle exists, and that core-f/help-32 maps to it.
     */
    public function test_core_f_help_32() {
        $this->assert_icon_data(
            'question-circle',
            'f/help-32',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle"></span>',
            'core-f/help-32',
            'pix/f/help-32.png'
        );
    }

    /**
     * Tests that question-circle exists, and that core-help maps to it.
     */
    public function test_core_help() {
        $this->assert_icon_data(
            'question-circle',
            'help',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle"></span>',
            'core-help',
            'pix/help.png'
        );
    }

    /**
     * Tests that calendar exists, and that mod_data-field/date maps to it.
     */
    public function test_mod_data_field_date() {
        $this->assert_icon_data(
            'calendar',
            'field/date',
            'mod_data',
            '<span aria-hidden="true" class="flex-icon fa fa-calendar"></span>',
            'mod_data-field/date',
            'mod/data/pix/field/date.gif'
        );
    }

    /**
     * Tests that file-o exists, and that mod_data-field/file maps to it.
     */
    public function test_mod_data_field_file() {
        $this->assert_icon_data(
            'file-o',
            'field/file',
            'mod_data',
            '<span aria-hidden="true" class="flex-icon fa fa-file-o"></span>',
            'mod_data-field/file',
            'mod/data/pix/field/file.gif'
        );
    }

    /**
     * Tests that file-o exists, and that core-f/unknown maps to it.
     */
    public function test_core_f_unknown() {
        $this->assert_icon_data(
            'file-o',
            'f/unknown',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-o"></span>',
            'core-f/unknown',
            'pix/f/unknown.png'
        );
    }

    /**
     * Tests that file-o exists, and that core-i/files maps to it.
     */
    public function test_core_i_files() {
        $this->assert_icon_data(
            'file-o',
            'i/files',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-o"></span>',
            'core-i/files',
            'pix/i/files.png'
        );
    }

    /**
     * Tests that globe exists, and that mod_data-field/latlong maps to it.
     */
    public function test_mod_data_field_latlong() {
        $this->assert_icon_data(
            'globe',
            'field/latlong',
            'mod_data',
            '<span aria-hidden="true" class="flex-icon fa fa-globe"></span>',
            'mod_data-field/latlong',
            'mod/data/pix/field/latlong.gif'
        );
    }

    /**
     * Tests that globe exists, and that mod_url-icon maps to it.
     */
    public function test_mod_url_icon() {
        $this->assert_icon_data(
            'globe',
            'icon',
            'mod_url',
            '<span aria-hidden="true" class="flex-icon fa fa-globe"></span>',
            'mod_url-icon',
            'mod/url/pix/icon.png'
        );
    }

    /**
     * Tests that image exists, and that mod_data-field/picture maps to it.
     */
    public function test_mod_data_field_picture() {
        $this->assert_icon_data(
            'image',
            'field/picture',
            'mod_data',
            '<span aria-hidden="true" class="flex-icon fa fa-image"></span>',
            'mod_data-field/picture',
            'mod/data/pix/field/picture.gif'
        );
    }

    /**
     * Tests that image exists, and that core-e/insert_edit_image maps to it.
     */
    public function test_core_e_insert_edit_image() {
        $this->assert_icon_data(
            'image',
            'e/insert_edit_image',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-image"></span>',
            'core-e/insert_edit_image',
            'pix/e/insert_edit_image.png'
        );
    }

    /**
     * Tests that dot-circle-o exists, and that mod_data-field/radiobutton maps to it.
     */
    public function test_mod_data_field_radiobutton() {
        $this->assert_icon_data(
            'dot-circle-o',
            'field/radiobutton',
            'mod_data',
            '<span aria-hidden="true" class="flex-icon fa fa-dot-circle-o"></span>',
            'mod_data-field/radiobutton',
            'mod/data/pix/field/radiobutton.gif'
        );
    }

    /**
     * Tests that link exists, and that mod_data-field/url maps to it.
     */
    public function test_mod_data_field_url() {
        $this->assert_icon_data(
            'link',
            'field/url',
            'mod_data',
            '<span aria-hidden="true" class="flex-icon fa fa-link"></span>',
            'mod_data-field/url',
            'mod/data/pix/field/url.gif'
        );
    }

    /**
     * Tests that link exists, and that core-e/insert_edit_link maps to it.
     */
    public function test_core_e_insert_edit_link() {
        $this->assert_icon_data(
            'link',
            'e/insert_edit_link',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-link"></span>',
            'core-e/insert_edit_link',
            'pix/e/insert_edit_link.png'
        );
    }

    /**
     * Tests that link exists, and that repository_url-icon maps to it.
     */
    public function test_repository_url_icon() {
        $this->assert_icon_data(
            'link',
            'icon',
            'repository_url',
            '<span aria-hidden="true" class="flex-icon fa fa-link"></span>',
            'repository_url-icon',
            'repository/url/pix/icon.png'
        );
    }

    /**
     * Tests that link exists, and that totara_appraisal-link maps to it.
     */
    public function test_totara_appraisal_link() {
        $this->assert_icon_data(
            'link',
            'link',
            'totara_appraisal',
            '<span aria-hidden="true" class="flex-icon fa fa-link"></span>',
            'totara_appraisal-link',
            'totara/appraisal/pix/link.png'
        );
    }

    /**
     * Tests that database exists, and that mod_data-icon maps to it.
     */
    public function test_mod_data_icon() {
        $this->assert_icon_data(
            'database',
            'icon',
            'mod_data',
            '<span aria-hidden="true" class="flex-icon fa fa-database"></span>',
            'mod_data-icon',
            'mod/data/pix/icon.png'
        );
    }

    /**
     * Tests that database exists, and that core-f/base maps to it.
     */
    public function test_core_f_base() {
        $this->assert_icon_data(
            'database',
            'f/base',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-database"></span>',
            'core-f/base',
            'pix/f/base.png'
        );
    }

    /**
     * Tests that database exists, and that core-f/database maps to it.
     */
    public function test_core_f_database() {
        $this->assert_icon_data(
            'database',
            'f/database',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-database"></span>',
            'core-f/database',
            'pix/f/database.png'
        );
    }

    /**
     * Tests that database exists, and that core-i/db maps to it.
     */
    public function test_core_i_db() {
        $this->assert_icon_data(
            'database',
            'i/db',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-database"></span>',
            'core-i/db',
            'pix/i/db.png'
        );
    }

    /**
     * Tests that database exists, and that core-i/repository maps to it.
     */
    public function test_core_i_repository() {
        $this->assert_icon_data(
            'database',
            'i/repository',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-database"></span>',
            'core-i/repository',
            'pix/i/repository.png'
        );
    }

    /**
     * Tests that bullhorn exists, and that mod_feedback-icon maps to it.
     */
    public function test_mod_feedback_icon() {
        $this->assert_icon_data(
            'bullhorn',
            'icon',
            'mod_feedback',
            '<span aria-hidden="true" class="flex-icon fa fa-bullhorn"></span>',
            'mod_feedback-icon',
            'mod/feedback/pix/icon.png'
        );
    }

    /**
     * Tests that folder-o exists, and that mod_folder-icon maps to it.
     */
    public function test_mod_folder_icon() {
        $this->assert_icon_data(
            'folder-o',
            'icon',
            'mod_folder',
            '<span aria-hidden="true" class="flex-icon fa fa-folder-o"></span>',
            'mod_folder-icon',
            'mod/folder/pix/icon.png'
        );
    }

    /**
     * Tests that folder-o exists, and that core-i/closed maps to it.
     */
    public function test_core_i_closed() {
        $this->assert_icon_data(
            'folder-o',
            'i/closed',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-folder-o"></span>',
            'core-i/closed',
            'pix/i/closed.gif'
        );
    }

    /**
     * Tests that folder-o exists, and that core-i/folder maps to it.
     */
    public function test_core_i_folder() {
        $this->assert_icon_data(
            'folder-o',
            'i/folder',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-folder-o"></span>',
            'core-i/folder',
            'pix/i/folder.png'
        );
    }

    /**
     * Tests that folder-o exists, and that totara_core-jquery_treeview/folder-closed maps to it.
     */
    public function test_totara_core_jquery_treeview_folder_closed() {
        $this->assert_icon_data(
            'folder-o',
            'jquery_treeview/folder-closed',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-folder-o"></span>',
            'totara_core-jquery_treeview/folder-closed',
            'totara/core/pix/jquery_treeview/folder-closed.gif'
        );
    }

    /**
     * Tests that comments-o exists, and that mod_forum-icon maps to it.
     */
    public function test_mod_forum_icon() {
        $this->assert_icon_data(
            'comments-o',
            'icon',
            'mod_forum',
            '<span aria-hidden="true" class="flex-icon fa fa-comments-o"></span>',
            'mod_forum-icon',
            'mod/forum/pix/icon.png'
        );
    }

    /**
     * Tests that subscribed exists, and that mod_forum-t/subscribed maps to it.
     */
    public function test_mod_forum_t_subscribed() {
        $this->assert_icon_data(
            'subscribed',
            't/subscribed',
            'mod_forum',
            '<span class="flex-icon ft-stack"><span class="fa fa-envelope-o ft-stack-main"></span><span class="fa fa-check ft-stack-suffix ft-state-success"></span></span>',
            'mod_forum-t/subscribed',
            'mod/forum/pix/t/subscribed.png'
        );
    }

    /**
     * Tests that unsubscribed exists, and that mod_forum-t/unsubscribed maps to it.
     */
    public function test_mod_forum_t_unsubscribed() {
        $this->assert_icon_data(
            'unsubscribed',
            't/unsubscribed',
            'mod_forum',
            '<span class="flex-icon ft-stack"><span class="fa fa-envelope-o ft-stack-main"></span><span class="fa fa-times ft-stack-suffix ft-state-danger"></span></span>',
            'mod_forum-t/unsubscribed',
            'mod/forum/pix/t/unsubscribed.png'
        );
    }

    /**
     * Tests that sort-asc exists, and that mod_glossary-asc maps to it.
     */
    public function test_mod_glossary_asc() {
        $this->assert_icon_data(
            'sort-asc',
            'asc',
            'mod_glossary',
            '<span aria-hidden="true" class="flex-icon fa fa-sort-asc"></span>',
            'mod_glossary-asc',
            'mod/glossary/pix/asc.gif'
        );
    }

    /**
     * Tests that sort-asc exists, and that core-t/sort_asc maps to it.
     */
    public function test_core_t_sort_asc() {
        $this->assert_icon_data(
            'sort-asc',
            't/sort_asc',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-sort-asc"></span>',
            'core-t/sort_asc',
            'pix/t/sort_asc.png'
        );
    }

    /**
     * Tests that sort-desc exists, and that mod_glossary-desc maps to it.
     */
    public function test_mod_glossary_desc() {
        $this->assert_icon_data(
            'sort-desc',
            'desc',
            'mod_glossary',
            '<span aria-hidden="true" class="flex-icon fa fa-sort-desc"></span>',
            'mod_glossary-desc',
            'mod/glossary/pix/desc.gif'
        );
    }

    /**
     * Tests that sort-desc exists, and that core-t/sort_desc maps to it.
     */
    public function test_core_t_sort_desc() {
        $this->assert_icon_data(
            'sort-desc',
            't/sort_desc',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-sort-desc"></span>',
            'core-t/sort_desc',
            'pix/t/sort_desc.png'
        );
    }

    /**
     * Tests that sort exists, and that core-t/sort maps to it.
     */
    public function test_core_t_sort() {
        $this->assert_icon_data(
            'sort',
            't/sort',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-sort"></span>',
            'core-t/sort',
            'pix/t/sort.png'
        );
    }

    /**
     * Tests that comment exists, and that mod_glossary-comment maps to it.
     */
    public function test_mod_glossary_comment() {
        $this->assert_icon_data(
            'comment',
            'comment',
            'mod_glossary',
            '<span aria-hidden="true" class="flex-icon fa fa-comment"></span>',
            'mod_glossary-comment',
            'mod/glossary/pix/comment.gif'
        );
    }

    /**
     * Tests that share-square-o exists, and that mod_glossary-export maps to it.
     */
    public function test_mod_glossary_export() {
        $this->assert_icon_data(
            'share-square-o',
            'export',
            'mod_glossary',
            '<span aria-hidden="true" class="flex-icon fa fa-share-square-o"></span>',
            'mod_glossary-export',
            'mod/glossary/pix/export.png'
        );
    }

    /**
     * Tests that minus exists, and that mod_glossary-minus maps to it.
     */
    public function test_mod_glossary_minus() {
        $this->assert_icon_data(
            'minus',
            'minus',
            'mod_glossary',
            '<span aria-hidden="true" class="flex-icon fa fa-minus"></span>',
            'mod_glossary-minus',
            'mod/glossary/pix/minus.gif'
        );
    }

    /**
     * Tests that minus exists, and that core-t/less maps to it.
     */
    public function test_core_t_less() {
        $this->assert_icon_data(
            'minus',
            't/less',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-minus"></span>',
            'core-t/less',
            'pix/t/less.png'
        );
    }

    /**
     * Tests that print exists, and that mod_glossary-print maps to it.
     */
    public function test_mod_glossary_print() {
        $this->assert_icon_data(
            'print',
            'print',
            'mod_glossary',
            '<span aria-hidden="true" class="flex-icon fa fa-print"></span>',
            'mod_glossary-print',
            'mod/glossary/pix/print.gif'
        );
    }

    /**
     * Tests that print exists, and that core-e/print maps to it.
     */
    public function test_core_e_print() {
        $this->assert_icon_data(
            'print',
            'e/print',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-print"></span>',
            'core-e/print',
            'pix/e/print.png'
        );
    }

    /**
     * Tests that print exists, and that core-t/print maps to it.
     */
    public function test_core_t_print() {
        $this->assert_icon_data(
            'print',
            't/print',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-print"></span>',
            'core-t/print',
            'pix/t/print.png'
        );
    }

    /**
     * Tests that package exists, and that mod_imscp-icon maps to it.
     */
    public function test_mod_imscp_icon() {
        $this->assert_icon_data(
            'package',
            'icon',
            'mod_imscp',
            '<span aria-hidden="true" class="flex-icon ft ft-package"></span>',
            'mod_imscp-icon',
            'mod/imscp/pix/icon.png'
        );
    }

    /**
     * Tests that tag exists, and that mod_label-icon maps to it.
     */
    public function test_mod_label_icon() {
        $this->assert_icon_data(
            'tag',
            'icon',
            'mod_label',
            '<span aria-hidden="true" class="flex-icon fa fa-tag"></span>',
            'mod_label-icon',
            'mod/label/pix/icon.png'
        );
    }

    /**
     * Tests that list-alt exists, and that mod_lesson-icon maps to it.
     */
    public function test_mod_lesson_icon() {
        $this->assert_icon_data(
            'list-alt',
            'icon',
            'mod_lesson',
            '<span aria-hidden="true" class="flex-icon fa fa-list-alt"></span>',
            'mod_lesson-icon',
            'mod/lesson/pix/icon.png'
        );
    }

    /**
     * Tests that puzzle-piece exists, and that mod_lti-icon maps to it.
     */
    public function test_mod_lti_icon() {
        $this->assert_icon_data(
            'puzzle-piece',
            'icon',
            'mod_lti',
            '<span aria-hidden="true" class="flex-icon fa fa-puzzle-piece"></span>',
            'mod_lti-icon',
            'mod/lti/pix/icon.png'
        );
    }

    /**
     * Tests that warning-warning exists, and that mod_lti-warning maps to it.
     */
    public function test_mod_lti_warning() {
        $this->assert_icon_data(
            'warning-warning',
            'warning',
            'mod_lti',
            '<span aria-hidden="true" class="flex-icon fa fa-warning ft-state-warning"></span>',
            'mod_lti-warning',
            'mod/lti/pix/warning.png'
        );
    }

    /**
     * Tests that warning-warning exists, and that core-i/warning maps to it.
     */
    public function test_core_i_warning() {
        $this->assert_icon_data(
            'warning-warning',
            'i/warning',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-warning ft-state-warning"></span>',
            'core-i/warning',
            'pix/i/warning.png'
        );
    }

    /**
     * Tests that file-text-o exists, and that mod_page-icon maps to it.
     */
    public function test_mod_page_icon() {
        $this->assert_icon_data(
            'file-text-o',
            'icon',
            'mod_page',
            '<span aria-hidden="true" class="flex-icon fa fa-file-text-o"></span>',
            'mod_page-icon',
            'mod/page/pix/icon.png'
        );
    }

    /**
     * Tests that file-o exists, and that mod_resource-icon maps to it.
     */
    public function test_mod_resource_icon() {
        $this->assert_icon_data(
            'file-o',
            'icon',
            'mod_resource',
            '<span aria-hidden="true" class="flex-icon fa fa-file-o"></span>',
            'mod_resource-icon',
            'mod/resource/pix/icon.png'
        );
    }

    /**
     * Tests that archive exists, and that mod_scorm-icon maps to it.
     */
    public function test_mod_scorm_icon() {
        $this->assert_icon_data(
            'archive',
            'icon',
            'mod_scorm',
            '<span aria-hidden="true" class="flex-icon fa fa-archive"></span>',
            'mod_scorm-icon',
            'mod/scorm/pix/icon.png'
        );
    }

    /**
     * Tests that minus-square-o exists, and that mod_scorm-minus maps to it.
     */
    public function test_mod_scorm_minus() {
        $this->assert_icon_data(
            'minus-square-o',
            'minus',
            'mod_scorm',
            '<span aria-hidden="true" class="flex-icon fa fa-minus-square-o"></span>',
            'mod_scorm-minus',
            'mod/scorm/pix/minus.gif'
        );
    }

    /**
     * Tests that minus-square-o exists, and that totara_core-t/minus maps to it.
     */
    public function test_totara_core_t_minus() {
        $this->assert_icon_data(
            'minus-square-o',
            't/minus',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-minus-square-o"></span>',
            'totara_core-t/minus',
            'totara/core/pix/t/minus.gif'
        );
    }

    /**
     * Tests that plus-square-o exists, and that mod_scorm-plus maps to it.
     */
    public function test_mod_scorm_plus() {
        $this->assert_icon_data(
            'plus-square-o',
            'plus',
            'mod_scorm',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-square-o"></span>',
            'mod_scorm-plus',
            'mod/scorm/pix/plus.gif'
        );
    }

    /**
     * Tests that plus-square-o exists, and that totara_core-t/plus maps to it.
     */
    public function test_totara_core_t_plus() {
        $this->assert_icon_data(
            'plus-square-o',
            't/plus',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-square-o"></span>',
            'totara_core-t/plus',
            'totara/core/pix/t/plus.gif'
        );
    }

    /**
     * Tests that moon-o exists, and that mod_scorm-suspend maps to it.
     */
    public function test_mod_scorm_suspend() {
        $this->assert_icon_data(
            'moon-o',
            'suspend',
            'mod_scorm',
            '<span aria-hidden="true" class="flex-icon fa fa-moon-o"></span>',
            'mod_scorm-suspend',
            'mod/scorm/pix/suspend.gif'
        );
    }

    /**
     * Tests that spinner-pulse exists, and that mod_scorm-wait maps to it.
     */
    public function test_mod_scorm_wait() {
        $this->assert_icon_data(
            'spinner-pulse',
            'wait',
            'mod_scorm',
            '<span aria-hidden="true" class="flex-icon fa fa-spinner fa-pulse"></span>',
            'mod_scorm-wait',
            'mod/scorm/pix/wait.gif'
        );
    }

    /**
     * Tests that spinner-pulse exists, and that core-i/ajaxloader maps to it.
     */
    public function test_core_i_ajaxloader() {
        $this->assert_icon_data(
            'spinner-pulse',
            'i/ajaxloader',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-spinner fa-pulse"></span>',
            'core-i/ajaxloader',
            'pix/i/ajaxloader.gif'
        );
    }

    /**
     * Tests that spinner-pulse exists, and that core-i/loading_small maps to it.
     */
    public function test_core_i_loading_small() {
        $this->assert_icon_data(
            'spinner-pulse',
            'i/loading_small',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-spinner fa-pulse"></span>',
            'core-i/loading_small',
            'pix/i/loading_small.gif'
        );
    }

    /**
     * Tests that spinner-pulse exists, and that core-i/loading maps to it.
     */
    public function test_core_i_loading() {
        $this->assert_icon_data(
            'spinner-pulse',
            'i/loading',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-spinner fa-pulse"></span>',
            'core-i/loading',
            'pix/i/loading.gif'
        );
    }

    /**
     * Tests that spinner-pulse exists, and that core-y/loading maps to it.
     */
    public function test_core_y_loading() {
        $this->assert_icon_data(
            'spinner-pulse',
            'y/loading',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-spinner fa-pulse"></span>',
            'core-y/loading',
            'pix/y/loading.gif'
        );
    }

    /**
     * Tests that spinner-pulse exists, and that totara_core-loading_small maps to it.
     */
    public function test_totara_core_loading_small() {
        $this->assert_icon_data(
            'spinner-pulse',
            'loading_small',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-spinner fa-pulse"></span>',
            'totara_core-loading_small',
            'totara/core/pix/loading_small.gif'
        );
    }

    /**
     * Tests that spinner-pulse exists, and that totara_core-loading maps to it.
     */
    public function test_totara_core_loading() {
        $this->assert_icon_data(
            'spinner-pulse',
            'loading',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-spinner fa-pulse"></span>',
            'totara_core-loading',
            'totara/core/pix/loading.gif'
        );
    }

    /**
     * Tests that spinner-pulse exists, and that totara_reportbuilder-wait maps to it.
     */
    public function test_totara_reportbuilder_wait() {
        $this->assert_icon_data(
            'spinner-pulse',
            'wait',
            'totara_reportbuilder',
            '<span aria-hidden="true" class="flex-icon fa fa-spinner fa-pulse"></span>',
            'totara_reportbuilder-wait',
            'totara/reportbuilder/pix/wait.gif'
        );
    }

    /**
     * Tests that spinner-pulse exists, and that totara_reportbuilder-waitbig maps to it.
     */
    public function test_totara_reportbuilder_waitbig() {
        $this->assert_icon_data(
            'spinner-pulse',
            'waitbig',
            'totara_reportbuilder',
            '<span aria-hidden="true" class="flex-icon fa fa-spinner fa-pulse"></span>',
            'totara_reportbuilder-waitbig',
            'totara/reportbuilder/pix/waitbig.gif'
        );
    }

    /**
     * Tests that paperclip exists, and that mod_wiki-attachment maps to it.
     */
    public function test_mod_wiki_attachment() {
        $this->assert_icon_data(
            'paperclip',
            'attachment',
            'mod_wiki',
            '<span aria-hidden="true" class="flex-icon fa fa-paperclip"></span>',
            'mod_wiki-attachment',
            'mod/wiki/pix/attachment.png'
        );
    }

    /**
     * Tests that wikipedia-w exists, and that mod_wiki-icon maps to it.
     */
    public function test_mod_wiki_icon() {
        $this->assert_icon_data(
            'wikipedia-w',
            'icon',
            'mod_wiki',
            '<span aria-hidden="true" class="flex-icon fa fa-wikipedia-w"></span>',
            'mod_wiki-icon',
            'mod/wiki/pix/icon.png'
        );
    }

    /**
     * Tests that wikipedia-w exists, and that repository_wikimedia-icon maps to it.
     */
    public function test_repository_wikimedia_icon() {
        $this->assert_icon_data(
            'wikipedia-w',
            'icon',
            'repository_wikimedia',
            '<span aria-hidden="true" class="flex-icon fa fa-wikipedia-w"></span>',
            'repository_wikimedia-icon',
            'repository/wikimedia/pix/icon.png'
        );
    }

    /**
     * Tests that info-circle exists, and that mod_workshop-userplan/task-info maps to it.
     */
    public function test_mod_workshop_userplan_task_info() {
        $this->assert_icon_data(
            'info-circle',
            'userplan/task-info',
            'mod_workshop',
            '<span aria-hidden="true" class="flex-icon fa fa-info-circle"></span>',
            'mod_workshop-userplan/task-info',
            'mod/workshop/pix/userplan/task-info.png'
        );
    }

    /**
     * Tests that info-circle exists, and that core-i/info maps to it.
     */
    public function test_core_i_info() {
        $this->assert_icon_data(
            'info-circle',
            'i/info',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-info-circle"></span>',
            'core-i/info',
            'pix/i/info.png'
        );
    }

    /**
     * Tests that info-circle exists, and that core-docs maps to it.
     */
    public function test_core_docs() {
        $this->assert_icon_data(
            'info-circle',
            'docs',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-info-circle"></span>',
            'core-docs',
            'pix/docs.png'
        );
    }

    /**
     * Tests that download exists, and that core-a/download_all maps to it.
     */
    public function test_core_a_download_all() {
        $this->assert_icon_data(
            'download',
            'a/download_all',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-download"></span>',
            'core-a/download_all',
            'pix/a/download_all.png'
        );
    }

    /**
     * Tests that sign-out exists, and that core-a/logout maps to it.
     */
    public function test_core_a_logout() {
        $this->assert_icon_data(
            'sign-out',
            'a/logout',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-sign-out"></span>',
            'core-a/logout',
            'pix/a/logout.png'
        );
    }

    /**
     * Tests that refresh exists, and that core-a/refresh maps to it.
     */
    public function test_core_a_refresh() {
        $this->assert_icon_data(
            'refresh',
            'a/refresh',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-refresh"></span>',
            'core-a/refresh',
            'pix/a/refresh.png'
        );
    }

    /**
     * Tests that refresh exists, and that core-i/reload maps to it.
     */
    public function test_core_i_reload() {
        $this->assert_icon_data(
            'refresh',
            'i/reload',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-refresh"></span>',
            'core-i/reload',
            'pix/i/reload.png'
        );
    }

    /**
     * Tests that refresh exists, and that core-t/reload maps to it.
     */
    public function test_core_t_reload() {
        $this->assert_icon_data(
            'refresh',
            't/reload',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-refresh"></span>',
            'core-t/reload',
            'pix/t/reload.png'
        );
    }

    /**
     * Tests that search exists, and that core-a/search maps to it.
     */
    public function test_core_a_search() {
        $this->assert_icon_data(
            'search',
            'a/search',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-search"></span>',
            'core-a/search',
            'pix/a/search.png'
        );
    }

    /**
     * Tests that search exists, and that core-e/search maps to it.
     */
    public function test_core_e_search() {
        $this->assert_icon_data(
            'search',
            'e/search',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-search"></span>',
            'core-e/search',
            'pix/e/search.png'
        );
    }

    /**
     * Tests that search exists, and that core-i/search maps to it.
     */
    public function test_core_i_search() {
        $this->assert_icon_data(
            'search',
            'i/search',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-search"></span>',
            'core-i/search',
            'pix/i/search.png'
        );
    }

    /**
     * Tests that cog exists, and that core-a/setting maps to it.
     */
    public function test_core_a_setting() {
        $this->assert_icon_data(
            'cog',
            'a/setting',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-cog"></span>',
            'core-a/setting',
            'pix/a/setting.png'
        );
    }

    /**
     * Tests that cog exists, and that core-i/admin maps to it.
     */
    public function test_core_i_admin() {
        $this->assert_icon_data(
            'cog',
            'i/admin',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-cog"></span>',
            'core-i/admin',
            'pix/i/admin.gif'
        );
    }

    /**
     * Tests that cog exists, and that core-i/settings maps to it.
     */
    public function test_core_i_settings() {
        $this->assert_icon_data(
            'cog',
            'i/settings',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-cog"></span>',
            'core-i/settings',
            'pix/i/settings.png'
        );
    }

    /**
     * Tests that cog exists, and that core-t/edit maps to it.
     */
    public function test_core_t_edit() {
        $this->assert_icon_data(
            'cog',
            't/edit',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-cog"></span>',
            'core-t/edit',
            'pix/t/edit.png'
        );
    }

    /**
     * Tests that th-large exists, and that core-a/view_icon_active maps to it.
     */
    public function test_core_a_view_icon_active() {
        $this->assert_icon_data(
            'th-large',
            'a/view_icon_active',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-th-large"></span>',
            'core-a/view_icon_active',
            'pix/a/view_icon_active.png'
        );
    }

    /**
     * Tests that align-justify exists, and that core-a/view_list_active maps to it.
     */
    public function test_core_a_view_list_active() {
        $this->assert_icon_data(
            'align-justify',
            'a/view_list_active',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-align-justify"></span>',
            'core-a/view_list_active',
            'pix/a/view_list_active.png'
        );
    }

    /**
     * Tests that bookmark-o exists, and that core-b/bookmark-new maps to it.
     */
    public function test_core_b_bookmark_new() {
        $this->assert_icon_data(
            'bookmark-o',
            'b/bookmark-new',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-bookmark-o"></span>',
            'core-b/bookmark-new',
            'pix/b/bookmark-new.png'
        );
    }

    /**
     * Tests that bookmark-o exists, and that core-e/anchor maps to it.
     */
    public function test_core_e_anchor() {
        $this->assert_icon_data(
            'bookmark-o',
            'e/anchor',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-bookmark-o"></span>',
            'core-e/anchor',
            'pix/e/anchor.png'
        );
    }

    /**
     * Tests that edit-document exists, and that core-b/document-edit maps to it.
     */
    public function test_core_b_document_edit() {
        $this->assert_icon_data(
            'edit-document',
            'b/document-edit',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-file-o ft-stack-main"></span><span class="fa fa-pencil ft-stack-suffix"></span></span>',
            'core-b/document-edit',
            'pix/b/document-edit.png'
        );
    }

    /**
     * Tests that new-document exists, and that core-b/document-new maps to it.
     */
    public function test_core_b_document_new() {
        $this->assert_icon_data(
            'new-document',
            'b/document-new',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-file-o ft-stack-main"></span><span class="fa fa-plus ft-stack-suffix"></span></span>',
            'core-b/document-new',
            'pix/b/document-new.png'
        );
    }

    /**
     * Tests that new-document exists, and that core-e/new_document maps to it.
     */
    public function test_core_e_new_document() {
        $this->assert_icon_data(
            'new-document',
            'e/new_document',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-file-o ft-stack-main"></span><span class="fa fa-plus ft-stack-suffix"></span></span>',
            'core-e/new_document',
            'pix/e/new_document.png'
        );
    }

    /**
     * Tests that new-document exists, and that core-a/add_file maps to it.
     */
    public function test_core_a_add_file() {
        $this->assert_icon_data(
            'new-document',
            'a/add_file',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-file-o ft-stack-main"></span><span class="fa fa-plus ft-stack-suffix"></span></span>',
            'core-a/add_file',
            'pix/a/add_file.png'
        );
    }

    /**
     * Tests that new-document exists, and that core-t/addfile maps to it.
     */
    public function test_core_t_addfile() {
        $this->assert_icon_data(
            'new-document',
            't/addfile',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-file-o ft-stack-main"></span><span class="fa fa-plus ft-stack-suffix"></span></span>',
            'core-t/addfile',
            'pix/t/addfile.png'
        );
    }

    /**
     * Tests that document-properties exists, and that core-b/document-properties maps to it.
     */
    public function test_core_b_document_properties() {
        $this->assert_icon_data(
            'document-properties',
            'b/document-properties',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-file-o ft-stack-main"></span><span class="fa fa-wrench ft-stack-suffix"></span></span>',
            'core-b/document-properties',
            'pix/b/document-properties.png'
        );
    }

    /**
     * Tests that file-wrench exists, and that core-e/document_properties maps to it.
     */
    public function test_core_e_document_properties() {
        $this->assert_icon_data(
            'file-wrench',
            'e/document_properties',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-file-o ft-stack-main"></span><span class="fa fa-wrench ft-stack-suffix"></span></span>',
            'core-e/document_properties',
            'pix/e/document_properties.png'
        );
    }

    /**
     * Tests that copy exists, and that core-b/edit-copy maps to it.
     */
    public function test_core_b_edit_copy() {
        $this->assert_icon_data(
            'copy',
            'b/edit-copy',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-copy"></span>',
            'core-b/edit-copy',
            'pix/b/edit-copy.png'
        );
    }

    /**
     * Tests that copy exists, and that core-e/copy maps to it.
     */
    public function test_core_e_copy() {
        $this->assert_icon_data(
            'copy',
            'e/copy',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-copy"></span>',
            'core-e/copy',
            'pix/e/copy.png'
        );
    }

    /**
     * Tests that copy exists, and that core-t/copy maps to it.
     */
    public function test_core_t_copy() {
        $this->assert_icon_data(
            'copy',
            't/copy',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-copy"></span>',
            'core-t/copy',
            'pix/t/copy.png'
        );
    }

    /**
     * Tests that wheelchair exists, and that core-e/accessibility_checker maps to it.
     */
    public function test_core_e_accessibility_checker() {
        $this->assert_icon_data(
            'wheelchair',
            'e/accessibility_checker',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-wheelchair"></span>',
            'core-e/accessibility_checker',
            'pix/e/accessibility_checker.png'
        );
    }

    /**
     * Tests that wheelchair exists, and that core-e/screenreader_helper maps to it.
     */
    public function test_core_e_screenreader_helper() {
        $this->assert_icon_data(
            'wheelchair',
            'e/screenreader_helper',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-wheelchair"></span>',
            'core-e/screenreader_helper',
            'pix/e/screenreader_helper.png'
        );
    }

    /**
     * Tests that align-center exists, and that core-e/align_center maps to it.
     */
    public function test_core_e_align_center() {
        $this->assert_icon_data(
            'align-center',
            'e/align_center',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-align-center"></span>',
            'core-e/align_center',
            'pix/e/align_center.png'
        );
    }

    /**
     * Tests that align-left exists, and that core-e/align_left maps to it.
     */
    public function test_core_e_align_left() {
        $this->assert_icon_data(
            'align-left',
            'e/align_left',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-align-left"></span>',
            'core-e/align_left',
            'pix/e/align_left.png'
        );
    }

    /**
     * Tests that align-right exists, and that core-e/align_right maps to it.
     */
    public function test_core_e_align_right() {
        $this->assert_icon_data(
            'align-right',
            'e/align_right',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-align-right"></span>',
            'core-e/align_right',
            'pix/e/align_right.png'
        );
    }

    /**
     * Tests that bold exists, and that core-e/bold maps to it.
     */
    public function test_core_e_bold() {
        $this->assert_icon_data(
            'bold',
            'e/bold',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-bold"></span>',
            'core-e/bold',
            'pix/e/bold.png'
        );
    }

    /**
     * Tests that list-ul exists, and that core-e/bullet_list maps to it.
     */
    public function test_core_e_bullet_list() {
        $this->assert_icon_data(
            'list-ul',
            'e/bullet_list',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-list-ul"></span>',
            'core-e/bullet_list',
            'pix/e/bullet_list.png'
        );
    }

    /**
     * Tests that quote-left exists, and that core-e/cite maps to it.
     */
    public function test_core_e_cite() {
        $this->assert_icon_data(
            'quote-left',
            'e/cite',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-quote-left"></span>',
            'core-e/cite',
            'pix/e/cite.png'
        );
    }

    /**
     * Tests that quote-left exists, and that core-e/toggle_blockquote maps to it.
     */
    public function test_core_e_toggle_blockquote() {
        $this->assert_icon_data(
            'quote-left',
            'e/toggle_blockquote',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-quote-left"></span>',
            'core-e/toggle_blockquote',
            'pix/e/toggle_blockquote.png'
        );
    }

    /**
     * Tests that scissors exists, and that core-e/cut maps to it.
     */
    public function test_core_e_cut() {
        $this->assert_icon_data(
            'scissors',
            'e/cut',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-scissors"></span>',
            'core-e/cut',
            'pix/e/cut.png'
        );
    }

    /**
     * Tests that outdent exists, and that core-e/decrease_indent maps to it.
     */
    public function test_core_e_decrease_indent() {
        $this->assert_icon_data(
            'outdent',
            'e/decrease_indent',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-outdent"></span>',
            'core-e/decrease_indent',
            'pix/e/decrease_indent.png'
        );
    }

    /**
     * Tests that indent exists, and that core-e/increase_indent maps to it.
     */
    public function test_core_e_increase_indent() {
        $this->assert_icon_data(
            'indent',
            'e/increase_indent',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-indent"></span>',
            'core-e/increase_indent',
            'pix/e/increase_indent.png'
        );
    }

    /**
     * Tests that strikethrough exists, and that core-e/delete maps to it.
     */
    public function test_core_e_delete() {
        $this->assert_icon_data(
            'strikethrough',
            'e/delete',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-strikethrough"></span>',
            'core-e/delete',
            'pix/e/delete.png'
        );
    }

    /**
     * Tests that strikethrough exists, and that core-e/strikethrough maps to it.
     */
    public function test_core_e_strikethrough() {
        $this->assert_icon_data(
            'strikethrough',
            'e/strikethrough',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-strikethrough"></span>',
            'core-e/strikethrough',
            'pix/e/strikethrough.png'
        );
    }

    /**
     * Tests that expand exists, and that core-e/fullscreen maps to it.
     */
    public function test_core_e_fullscreen() {
        $this->assert_icon_data(
            'expand',
            'e/fullscreen',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-expand"></span>',
            'core-e/fullscreen',
            'pix/e/fullscreen.png'
        );
    }

    /**
     * Tests that film exists, and that core-e/insert_edit_video maps to it.
     */
    public function test_core_e_insert_edit_video() {
        $this->assert_icon_data(
            'film',
            'e/insert_edit_video',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-film"></span>',
            'core-e/insert_edit_video',
            'pix/e/insert_edit_video.png'
        );
    }

    /**
     * Tests that film exists, and that core-f/avi maps to it.
     */
    public function test_core_f_avi() {
        $this->assert_icon_data(
            'film',
            'f/avi',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-film"></span>',
            'core-f/avi',
            'pix/f/avi.png'
        );
    }

    /**
     * Tests that dash exists, and that core-e/insert_horizontal_ruler maps to it.
     */
    public function test_core_e_insert_horizontal_ruler() {
        $this->assert_icon_data(
            'dash',
            'e/insert_horizontal_ruler',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-dash"></span>',
            'core-e/insert_horizontal_ruler',
            'pix/e/insert_horizontal_ruler.png'
        );
    }

    /**
     * Tests that clock-o exists, and that core-e/insert_time maps to it.
     */
    public function test_core_e_insert_time() {
        $this->assert_icon_data(
            'clock-o',
            'e/insert_time',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-clock-o"></span>',
            'core-e/insert_time',
            'pix/e/insert_time.png'
        );
    }

    /**
     * Tests that clock-o exists, and that core-i/scheduled maps to it.
     */
    public function test_core_i_scheduled() {
        $this->assert_icon_data(
            'clock-o',
            'i/scheduled',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-clock-o"></span>',
            'core-i/scheduled',
            'pix/i/scheduled.png'
        );
    }

    /**
     * Tests that underline exists, and that core-e/insert maps to it.
     */
    public function test_core_e_insert() {
        $this->assert_icon_data(
            'underline',
            'e/insert',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-underline"></span>',
            'core-e/insert',
            'pix/e/insert.png'
        );
    }

    /**
     * Tests that underline exists, and that core-e/underline maps to it.
     */
    public function test_core_e_underline() {
        $this->assert_icon_data(
            'underline',
            'e/underline',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-underline"></span>',
            'core-e/underline',
            'pix/e/underline.png'
        );
    }

    /**
     * Tests that italic exists, and that core-e/italic maps to it.
     */
    public function test_core_e_italic() {
        $this->assert_icon_data(
            'italic',
            'e/italic',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-italic"></span>',
            'core-e/italic',
            'pix/e/italic.png'
        );
    }

    /**
     * Tests that align-justify exists, and that core-e/justify maps to it.
     */
    public function test_core_e_justify() {
        $this->assert_icon_data(
            'align-justify',
            'e/justify',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-align-justify"></span>',
            'core-e/justify',
            'pix/e/justify.png'
        );
    }

    /**
     * Tests that calculator exists, and that core-e/math maps to it.
     */
    public function test_core_e_math() {
        $this->assert_icon_data(
            'calculator',
            'e/math',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-calculator"></span>',
            'core-e/math',
            'pix/e/math.png'
        );
    }

    /**
     * Tests that calculator exists, and that core-i/calc maps to it.
     */
    public function test_core_i_calc() {
        $this->assert_icon_data(
            'calculator',
            'i/calc',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-calculator"></span>',
            'core-i/calc',
            'pix/i/calc.png'
        );
    }

    /**
     * Tests that list-ol exists, and that core-e/numbered_list maps to it.
     */
    public function test_core_e_numbered_list() {
        $this->assert_icon_data(
            'list-ol',
            'e/numbered_list',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-list-ol"></span>',
            'core-e/numbered_list',
            'pix/e/numbered_list.png'
        );
    }

    /**
     * Tests that paste exists, and that core-e/paste maps to it.
     */
    public function test_core_e_paste() {
        $this->assert_icon_data(
            'paste',
            'e/paste',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-paste"></span>',
            'core-e/paste',
            'pix/e/paste.png'
        );
    }

    /**
     * Tests that prevent-autolink exists, and that core-e/prevent_autolink maps to it.
     */
    public function test_core_e_prevent_autolink() {
        $this->assert_icon_data(
            'prevent-autolink',
            'e/prevent_autolink',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-link ft-stack-main"></span><span class="ft ft-slash ft-stack-over ft-state-danger"></span></span>',
            'core-e/prevent_autolink',
            'pix/e/prevent_autolink.png'
        );
    }

    /**
     * Tests that eye exists, and that core-e/preview maps to it.
     */
    public function test_core_e_preview() {
        $this->assert_icon_data(
            'eye',
            'e/preview',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-eye"></span>',
            'core-e/preview',
            'pix/e/preview.png'
        );
    }

    /**
     * Tests that eye exists, and that core-i/preview maps to it.
     */
    public function test_core_i_preview() {
        $this->assert_icon_data(
            'eye',
            'i/preview',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-eye"></span>',
            'core-i/preview',
            'pix/i/preview.png'
        );
    }

    /**
     * Tests that eye exists, and that core-i/show maps to it.
     */
    public function test_core_i_show() {
        $this->assert_icon_data(
            'eye',
            'i/show',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-eye"></span>',
            'core-i/show',
            'pix/i/show.png'
        );
    }

    /**
     * Tests that eye exists, and that core-t/preview maps to it.
     */
    public function test_core_t_preview() {
        $this->assert_icon_data(
            'eye',
            't/preview',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-eye"></span>',
            'core-t/preview',
            'pix/t/preview.png'
        );
    }

    /**
     * Tests that eye exists, and that core-t/show maps to it.
     */
    public function test_core_t_show() {
        $this->assert_icon_data(
            'eye',
            't/show',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-eye"></span>',
            'core-t/show',
            'pix/t/show.png'
        );
    }

    /**
     * Tests that eye exists, and that core-t/viewdetails maps to it.
     */
    public function test_core_t_viewdetails() {
        $this->assert_icon_data(
            'eye',
            't/viewdetails',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-eye"></span>',
            'core-t/viewdetails',
            'pix/t/viewdetails.png'
        );
    }

    /**
     * Tests that question exists, and that core-e/question maps to it.
     */
    public function test_core_e_question() {
        $this->assert_icon_data(
            'question',
            'e/question',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-question"></span>',
            'core-e/question',
            'pix/e/question.png'
        );
    }

    /**
     * Tests that question exists, and that core-i/questions maps to it.
     */
    public function test_core_i_questions() {
        $this->assert_icon_data(
            'question',
            'i/questions',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-question"></span>',
            'core-i/questions',
            'pix/i/questions.gif'
        );
    }

    /**
     * Tests that repeat exists, and that core-e/redo maps to it.
     */
    public function test_core_e_redo() {
        $this->assert_icon_data(
            'repeat',
            'e/redo',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-repeat"></span>',
            'core-e/redo',
            'pix/e/redo.png'
        );
    }

    /**
     * Tests that unlink exists, and that core-e/remove_link maps to it.
     */
    public function test_core_e_remove_link() {
        $this->assert_icon_data(
            'unlink',
            'e/remove_link',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-unlink"></span>',
            'core-e/remove_link',
            'pix/e/remove_link.png'
        );
    }

    /**
     * Tests that arrows-alt exists, and that core-e/resize maps to it.
     */
    public function test_core_e_resize() {
        $this->assert_icon_data(
            'arrows-alt',
            'e/resize',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrows-alt"></span>',
            'core-e/resize',
            'pix/e/resize.png'
        );
    }

    /**
     * Tests that restore-draft exists, and that core-e/restore_draft maps to it.
     */
    public function test_core_e_restore_draft() {
        $this->assert_icon_data(
            'restore-draft',
            'e/restore_draft',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-file-o ft-stack-main"></span><span class="fa fa-undo ft-stack-suffix"></span></span>',
            'core-e/restore_draft',
            'pix/e/restore_draft.png'
        );
    }

    /**
     * Tests that save exists, and that core-e/save maps to it.
     */
    public function test_core_e_save() {
        $this->assert_icon_data(
            'save',
            'e/save',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-save"></span>',
            'core-e/save',
            'pix/e/save.png'
        );
    }

    /**
     * Tests that paragraph exists, and that core-e/show_invisible_characters maps to it.
     */
    public function test_core_e_show_invisible_characters() {
        $this->assert_icon_data(
            'paragraph',
            'e/show_invisible_characters',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-paragraph"></span>',
            'core-e/show_invisible_characters',
            'pix/e/show_invisible_characters.png'
        );
    }

    /**
     * Tests that code exists, and that core-e/source_code maps to it.
     */
    public function test_core_e_source_code() {
        $this->assert_icon_data(
            'code',
            'e/source_code',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-code"></span>',
            'core-e/source_code',
            'pix/e/source_code.png'
        );
    }

    /**
     * Tests that subscript exists, and that core-e/subscript maps to it.
     */
    public function test_core_e_subscript() {
        $this->assert_icon_data(
            'subscript',
            'e/subscript',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-subscript"></span>',
            'core-e/subscript',
            'pix/e/subscript.png'
        );
    }

    /**
     * Tests that superscript exists, and that core-e/superscript maps to it.
     */
    public function test_core_e_superscript() {
        $this->assert_icon_data(
            'superscript',
            'e/superscript',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-superscript"></span>',
            'core-e/superscript',
            'pix/e/superscript.png'
        );
    }

    /**
     * Tests that table exists, and that core-e/table maps to it.
     */
    public function test_core_e_table() {
        $this->assert_icon_data(
            'table',
            'e/table',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-table"></span>',
            'core-e/table',
            'pix/e/table.png'
        );
    }

    /**
     * Tests that text-color-picker exists, and that core-e/text_color_picker maps to it.
     */
    public function test_core_e_text_color_picker() {
        $this->assert_icon_data(
            'text-color-picker',
            'e/text_color_picker',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-font ft-stack-main"></span><span class="fa fa-eyedropper ft-stack-suffix"></span></span>',
            'core-e/text_color_picker',
            'pix/e/text_color_picker.png'
        );
    }

    /**
     * Tests that font-info exists, and that core-e/text_color maps to it.
     */
    public function test_core_e_text_color() {
        $this->assert_icon_data(
            'font-info',
            'e/text_color',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-font ft-state-info"></span>',
            'core-e/text_color',
            'pix/e/text_color.png'
        );
    }

    /**
     * Tests that undo exists, and that core-e/undo maps to it.
     */
    public function test_core_e_undo() {
        $this->assert_icon_data(
            'undo',
            'e/undo',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-undo"></span>',
            'core-e/undo',
            'pix/e/undo.png'
        );
    }

    /**
     * Tests that undo exists, and that core-i/return maps to it.
     */
    public function test_core_i_return() {
        $this->assert_icon_data(
            'undo',
            'i/return',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-undo"></span>',
            'core-i/return',
            'pix/i/return.png'
        );
    }

    /**
     * Tests that undo exists, and that core-t/reset maps to it.
     */
    public function test_core_t_reset() {
        $this->assert_icon_data(
            'undo',
            't/reset',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-undo"></span>',
            'core-t/reset',
            'pix/t/reset.png'
        );
    }

    /**
     * Tests that box-alt exists, and that core-f/archive maps to it.
     */
    public function test_core_f_archive() {
        $this->assert_icon_data(
            'box-alt',
            'f/archive',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-box-alt"></span>',
            'core-f/archive',
            'pix/f/archive.png'
        );
    }

    /**
     * Tests that volume-up exists, and that core-f/audio maps to it.
     */
    public function test_core_f_audio() {
        $this->assert_icon_data(
            'volume-up',
            'f/audio',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-volume-up"></span>',
            'core-f/audio',
            'pix/f/audio.png'
        );
    }

    /**
     * Tests that bar-chart exists, and that core-f/chart maps to it.
     */
    public function test_core_f_chart() {
        $this->assert_icon_data(
            'bar-chart',
            'f/chart',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-bar-chart"></span>',
            'core-f/chart',
            'pix/f/chart.png'
        );
    }

    /**
     * Tests that file-archive-o exists, and that core-f/dmg maps to it.
     */
    public function test_core_f_dmg() {
        $this->assert_icon_data(
            'file-archive-o',
            'f/dmg',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-archive-o"></span>',
            'core-f/dmg',
            'pix/f/dmg.gif'
        );
    }

    /**
     * Tests that pencil-square-o exists, and that core-f/edit maps to it.
     */
    public function test_core_f_edit() {
        $this->assert_icon_data(
            'pencil-square-o',
            'f/edit',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-pencil-square-o"></span>',
            'core-f/edit',
            'pix/f/edit.gif'
        );
    }

    /**
     * Tests that book-alt exists, and that core-f/epub maps to it.
     */
    public function test_core_f_epub() {
        $this->assert_icon_data(
            'book-alt',
            'f/epub',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-book-alt"></span>',
            'core-f/epub',
            'pix/f/epub.png'
        );
    }

    /**
     * Tests that explore exists, and that core-f/explore maps to it.
     */
    public function test_core_f_explore() {
        $this->assert_icon_data(
            'explore',
            'f/explore',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-folder-o ft-stack-main"></span><span class="fa fa-search ft-stack-suffix"></span></span>',
            'core-f/explore',
            'pix/f/explore.gif'
        );
    }

    /**
     * Tests that file-video-o exists, and that core-f/flash maps to it.
     */
    public function test_core_f_flash() {
        $this->assert_icon_data(
            'file-video-o',
            'f/flash',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-video-o"></span>',
            'core-f/flash',
            'pix/f/flash.png'
        );
    }

    /**
     * Tests that file-video-o exists, and that core-f/mov maps to it.
     */
    public function test_core_f_mov() {
        $this->assert_icon_data(
            'file-video-o',
            'f/mov',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-video-o"></span>',
            'core-f/mov',
            'pix/f/mov.png'
        );
    }

    /**
     * Tests that file-video-o exists, and that core-f/mpeg maps to it.
     */
    public function test_core_f_mpeg() {
        $this->assert_icon_data(
            'file-video-o',
            'f/mpeg',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-video-o"></span>',
            'core-f/mpeg',
            'pix/f/mpeg.png'
        );
    }

    /**
     * Tests that file-video-o exists, and that core-f/quicktime maps to it.
     */
    public function test_core_f_quicktime() {
        $this->assert_icon_data(
            'file-video-o',
            'f/quicktime',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-video-o"></span>',
            'core-f/quicktime',
            'pix/f/quicktime.png'
        );
    }

    /**
     * Tests that file-video-o exists, and that core-f/video maps to it.
     */
    public function test_core_f_video() {
        $this->assert_icon_data(
            'file-video-o',
            'f/video',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-video-o"></span>',
            'core-f/video',
            'pix/f/video.png'
        );
    }

    /**
     * Tests that file-video-o exists, and that core-f/wmv maps to it.
     */
    public function test_core_f_wmv() {
        $this->assert_icon_data(
            'file-video-o',
            'f/wmv',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-video-o"></span>',
            'core-f/wmv',
            'pix/f/wmv.png'
        );
    }

    /**
     * Tests that folder-open-o exists, and that core-f/folder-open maps to it.
     */
    public function test_core_f_folder_open() {
        $this->assert_icon_data(
            'folder-open-o',
            'f/folder-open',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-folder-open-o"></span>',
            'core-f/folder-open',
            'pix/f/folder-open.png'
        );
    }

    /**
     * Tests that folder-open-o exists, and that core-i/open maps to it.
     */
    public function test_core_i_open() {
        $this->assert_icon_data(
            'folder-open-o',
            'i/open',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-folder-open-o"></span>',
            'core-i/open',
            'pix/i/open.gif'
        );
    }

    /**
     * Tests that folder-open-o exists, and that repository_coursefiles-icon maps to it.
     */
    public function test_repository_coursefiles_icon() {
        $this->assert_icon_data(
            'folder-open-o',
            'icon',
            'repository_coursefiles',
            '<span aria-hidden="true" class="flex-icon fa fa-folder-open-o"></span>',
            'repository_coursefiles-icon',
            'repository/coursefiles/pix/icon.png'
        );
    }

    /**
     * Tests that folder-open-o exists, and that repository_local-icon maps to it.
     */
    public function test_repository_local_icon() {
        $this->assert_icon_data(
            'folder-open-o',
            'icon',
            'repository_local',
            '<span aria-hidden="true" class="flex-icon fa fa-folder-open-o"></span>',
            'repository_local-icon',
            'repository/local/pix/icon.png'
        );
    }

    /**
     * Tests that folder-open-o exists, and that repository_recent-icon maps to it.
     */
    public function test_repository_recent_icon() {
        $this->assert_icon_data(
            'folder-open-o',
            'icon',
            'repository_recent',
            '<span aria-hidden="true" class="flex-icon fa fa-folder-open-o"></span>',
            'repository_recent-icon',
            'repository/recent/pix/icon.png'
        );
    }

    /**
     * Tests that folder-open-o exists, and that repository_user-icon maps to it.
     */
    public function test_repository_user_icon() {
        $this->assert_icon_data(
            'folder-open-o',
            'icon',
            'repository_user',
            '<span aria-hidden="true" class="flex-icon fa fa-folder-open-o"></span>',
            'repository_user-icon',
            'repository/user/pix/icon.png'
        );
    }

    /**
     * Tests that folder-open-o exists, and that totara_core-jquery_treeview/folder maps to it.
     */
    public function test_totara_core_jquery_treeview_folder() {
        $this->assert_icon_data(
            'folder-open-o',
            'jquery_treeview/folder',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-folder-open-o"></span>',
            'totara_core-jquery_treeview/folder',
            'totara/core/pix/jquery_treeview/folder.gif'
        );
    }

    /**
     * Tests that folder-o exists, and that core-f/folder maps to it.
     */
    public function test_core_f_folder() {
        $this->assert_icon_data(
            'folder-o',
            'f/folder',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-folder-o"></span>',
            'core-f/folder',
            'pix/f/folder.png'
        );
    }

    /**
     * Tests that file-code-o exists, and that core-f/html maps to it.
     */
    public function test_core_f_html() {
        $this->assert_icon_data(
            'file-code-o',
            'f/html',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-code-o"></span>',
            'core-f/html',
            'pix/f/html.gif'
        );
    }

    /**
     * Tests that file-code-o exists, and that core-f/markup maps to it.
     */
    public function test_core_f_markup() {
        $this->assert_icon_data(
            'file-code-o',
            'f/markup',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-code-o"></span>',
            'core-f/markup',
            'pix/f/markup.png'
        );
    }

    /**
     * Tests that file-code-o exists, and that core-f/sourcecode maps to it.
     */
    public function test_core_f_sourcecode() {
        $this->assert_icon_data(
            'file-code-o',
            'f/sourcecode',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-code-o"></span>',
            'core-f/sourcecode',
            'pix/f/sourcecode.png'
        );
    }

    /**
     * Tests that arrows exists, and that core-f/move maps to it.
     */
    public function test_core_f_move() {
        $this->assert_icon_data(
            'arrows',
            'f/move',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrows"></span>',
            'core-f/move',
            'pix/f/move.gif'
        );
    }

    /**
     * Tests that arrows exists, and that core-i/dragdrop maps to it.
     */
    public function test_core_i_dragdrop() {
        $this->assert_icon_data(
            'arrows',
            'i/dragdrop',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrows"></span>',
            'core-i/dragdrop',
            'pix/i/dragdrop.png'
        );
    }

    /**
     * Tests that arrows exists, and that core-i/move_2d maps to it.
     */
    public function test_core_i_move_2d() {
        $this->assert_icon_data(
            'arrows',
            'i/move_2d',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrows"></span>',
            'core-i/move_2d',
            'pix/i/move_2d.png'
        );
    }

    /**
     * Tests that file-sound-o exists, and that core-f/mp3 maps to it.
     */
    public function test_core_f_mp3() {
        $this->assert_icon_data(
            'file-sound-o',
            'f/mp3',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-sound-o"></span>',
            'core-f/mp3',
            'pix/f/mp3.png'
        );
    }

    /**
     * Tests that file-sound-o exists, and that core-f/wav maps to it.
     */
    public function test_core_f_wav() {
        $this->assert_icon_data(
            'file-sound-o',
            'f/wav',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-sound-o"></span>',
            'core-f/wav',
            'pix/f/wav.png'
        );
    }

    /**
     * Tests that level-up exists, and that core-f/parent-32 maps to it.
     */
    public function test_core_f_parent_32() {
        $this->assert_icon_data(
            'level-up',
            'f/parent-32',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-level-up"></span>',
            'core-f/parent-32',
            'pix/f/parent-32.png'
        );
    }

    /**
     * Tests that file-pdf-o exists, and that core-f/pdf maps to it.
     */
    public function test_core_f_pdf() {
        $this->assert_icon_data(
            'file-pdf-o',
            'f/pdf',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-pdf-o"></span>',
            'core-f/pdf',
            'pix/f/pdf.png'
        );
    }

    /**
     * Tests that file-powerpoint-o exists, and that core-f/powerpoint maps to it.
     */
    public function test_core_f_powerpoint() {
        $this->assert_icon_data(
            'file-powerpoint-o',
            'f/powerpoint',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-powerpoint-o"></span>',
            'core-f/powerpoint',
            'pix/f/powerpoint.png'
        );
    }

    /**
     * Tests that file-image-o exists, and that core-f/psd maps to it.
     */
    public function test_core_f_psd() {
        $this->assert_icon_data(
            'file-image-o',
            'f/psd',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-image-o"></span>',
            'core-f/psd',
            'pix/f/psd.png'
        );
    }

    /**
     * Tests that file-image-o exists, and that core-f/bmp maps to it.
     */
    public function test_core_f_bmp() {
        $this->assert_icon_data(
            'file-image-o',
            'f/bmp',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-image-o"></span>',
            'core-f/bmp',
            'pix/f/bmp.png'
        );
    }

    /**
     * Tests that file-image-o exists, and that core-f/eps maps to it.
     */
    public function test_core_f_eps() {
        $this->assert_icon_data(
            'file-image-o',
            'f/eps',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-image-o"></span>',
            'core-f/eps',
            'pix/f/eps.png'
        );
    }

    /**
     * Tests that file-image-o exists, and that core-f/gif maps to it.
     */
    public function test_core_f_gif() {
        $this->assert_icon_data(
            'file-image-o',
            'f/gif',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-image-o"></span>',
            'core-f/gif',
            'pix/f/gif.png'
        );
    }

    /**
     * Tests that file-image-o exists, and that core-f/image maps to it.
     */
    public function test_core_f_image() {
        $this->assert_icon_data(
            'file-image-o',
            'f/image',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-image-o"></span>',
            'core-f/image',
            'pix/f/image.png'
        );
    }

    /**
     * Tests that file-image-o exists, and that core-f/jpeg maps to it.
     */
    public function test_core_f_jpeg() {
        $this->assert_icon_data(
            'file-image-o',
            'f/jpeg',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-image-o"></span>',
            'core-f/jpeg',
            'pix/f/jpeg.png'
        );
    }

    /**
     * Tests that file-image-o exists, and that core-f/png maps to it.
     */
    public function test_core_f_png() {
        $this->assert_icon_data(
            'file-image-o',
            'f/png',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-image-o"></span>',
            'core-f/png',
            'pix/f/png.png'
        );
    }

    /**
     * Tests that file-image-o exists, and that core-f/tiff maps to it.
     */
    public function test_core_f_tiff() {
        $this->assert_icon_data(
            'file-image-o',
            'f/tiff',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-image-o"></span>',
            'core-f/tiff',
            'pix/f/tiff.png'
        );
    }

    /**
     * Tests that file-excel-o exists, and that core-f/spreadsheet maps to it.
     */
    public function test_core_f_spreadsheet() {
        $this->assert_icon_data(
            'file-excel-o',
            'f/spreadsheet',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-file-excel-o"></span>',
            'core-f/spreadsheet',
            'pix/f/spreadsheet.png'
        );
    }

    /**
     * Tests that user-plus exists, and that core-i/assignroles maps to it.
     */
    public function test_core_i_assignroles() {
        $this->assert_icon_data(
            'user-plus',
            'i/assignroles',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user-plus"></span>',
            'core-i/assignroles',
            'pix/i/assignroles.png'
        );
    }

    /**
     * Tests that user-plus exists, and that core-i/enrolusers maps to it.
     */
    public function test_core_i_enrolusers() {
        $this->assert_icon_data(
            'user-plus',
            'i/enrolusers',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user-plus"></span>',
            'core-i/enrolusers',
            'pix/i/enrolusers.png'
        );
    }

    /**
     * Tests that user-plus exists, and that core-i/useradd maps to it.
     */
    public function test_core_i_useradd() {
        $this->assert_icon_data(
            'user-plus',
            'i/useradd',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user-plus"></span>',
            'core-i/useradd',
            'pix/i/useradd.png'
        );
    }

    /**
     * Tests that user-plus exists, and that core-t/assignroles maps to it.
     */
    public function test_core_t_assignroles() {
        $this->assert_icon_data(
            'user-plus',
            't/assignroles',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user-plus"></span>',
            'core-t/assignroles',
            'pix/t/assignroles.png'
        );
    }

    /**
     * Tests that user-plus exists, and that core-t/enrolusers maps to it.
     */
    public function test_core_t_enrolusers() {
        $this->assert_icon_data(
            'user-plus',
            't/enrolusers',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user-plus"></span>',
            'core-t/enrolusers',
            'pix/t/enrolusers.png'
        );
    }

    /**
     * Tests that upload exists, and that core-i/backup maps to it.
     */
    public function test_core_i_backup() {
        $this->assert_icon_data(
            'upload',
            'i/backup',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-upload"></span>',
            'core-i/backup',
            'pix/i/backup.png'
        );
    }

    /**
     * Tests that upload exists, and that core-i/export maps to it.
     */
    public function test_core_i_export() {
        $this->assert_icon_data(
            'upload',
            'i/export',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-upload"></span>',
            'core-i/export',
            'pix/i/export.png'
        );
    }

    /**
     * Tests that upload exists, and that core-t/backup maps to it.
     */
    public function test_core_t_backup() {
        $this->assert_icon_data(
            'upload',
            't/backup',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-upload"></span>',
            'core-t/backup',
            'pix/t/backup.png'
        );
    }

    /**
     * Tests that upload exists, and that repository_upload-icon maps to it.
     */
    public function test_repository_upload_icon() {
        $this->assert_icon_data(
            'upload',
            'icon',
            'repository_upload',
            '<span aria-hidden="true" class="flex-icon fa fa-upload"></span>',
            'repository_upload-icon',
            'repository/upload/pix/icon.png'
        );
    }

    /**
     * Tests that trophy exists, and that core-i/badge maps to it.
     */
    public function test_core_i_badge() {
        $this->assert_icon_data(
            'trophy',
            'i/badge',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-trophy"></span>',
            'core-i/badge',
            'pix/i/badge.png'
        );
    }

    /**
     * Tests that exclamation-circle exists, and that core-i/caution maps to it.
     */
    public function test_core_i_caution() {
        $this->assert_icon_data(
            'exclamation-circle',
            'i/caution',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-exclamation-circle"></span>',
            'core-i/caution',
            'pix/i/caution.png'
        );
    }

    /**
     * Tests that exclamation-circle exists, and that totara_plan-learning_plan_alert maps to it.
     */
    public function test_totara_plan_learning_plan_alert() {
        $this->assert_icon_data(
            'exclamation-circle',
            'learning_plan_alert',
            'totara_plan',
            '<span aria-hidden="true" class="flex-icon fa fa-exclamation-circle"></span>',
            'totara_plan-learning_plan_alert',
            'totara/plan/pix/learning_plan_alert.gif'
        );
    }

    /**
     * Tests that exclamation-circle exists, and that totara_program-program_warning maps to it.
     */
    public function test_totara_program_program_warning() {
        $this->assert_icon_data(
            'exclamation-circle',
            'program_warning',
            'totara_program',
            '<span aria-hidden="true" class="flex-icon fa fa-exclamation-circle"></span>',
            'totara_program-program_warning',
            'totara/program/pix/program_warning.gif'
        );
    }

    /**
     * Tests that check-permissions exists, and that core-i/checkpermissions maps to it.
     */
    public function test_core_i_checkpermissions() {
        $this->assert_icon_data(
            'check-permissions',
            'i/checkpermissions',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-user ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-warning"></span></span>',
            'core-i/checkpermissions',
            'pix/i/checkpermissions.png'
        );
    }

    /**
     * Tests that users exists, and that core-i/cohort maps to it.
     */
    public function test_core_i_cohort() {
        $this->assert_icon_data(
            'users',
            'i/cohort',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-users"></span>',
            'core-i/cohort',
            'pix/i/cohort.png'
        );
    }

    /**
     * Tests that users exists, and that core-i/group maps to it.
     */
    public function test_core_i_group() {
        $this->assert_icon_data(
            'users',
            'i/group',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-users"></span>',
            'core-i/group',
            'pix/i/group.png'
        );
    }

    /**
     * Tests that users exists, and that core-i/groupn maps to it.
     */
    public function test_core_i_groupn() {
        $this->assert_icon_data(
            'users',
            'i/groupn',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-users"></span>',
            'core-i/groupn',
            'pix/i/groupn.png'
        );
    }

    /**
     * Tests that users exists, and that core-i/groups maps to it.
     */
    public function test_core_i_groups() {
        $this->assert_icon_data(
            'users',
            'i/groups',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-users"></span>',
            'core-i/groups',
            'pix/i/groups.png'
        );
    }

    /**
     * Tests that users exists, and that core-i/groupv maps to it.
     */
    public function test_core_i_groupv() {
        $this->assert_icon_data(
            'users',
            'i/groupv',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-users"></span>',
            'core-i/groupv',
            'pix/i/groupv.png'
        );
    }

    /**
     * Tests that users exists, and that core-i/users maps to it.
     */
    public function test_core_i_users() {
        $this->assert_icon_data(
            'users',
            'i/users',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-users"></span>',
            'core-i/users',
            'pix/i/users.png'
        );
    }

    /**
     * Tests that users exists, and that core-t/cohort maps to it.
     */
    public function test_core_t_cohort() {
        $this->assert_icon_data(
            'users',
            't/cohort',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-users"></span>',
            'core-t/cohort',
            'pix/t/cohort.png'
        );
    }

    /**
     * Tests that users exists, and that core-t/groupn maps to it.
     */
    public function test_core_t_groupn() {
        $this->assert_icon_data(
            'users',
            't/groupn',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-users"></span>',
            'core-t/groupn',
            'pix/t/groupn.png'
        );
    }

    /**
     * Tests that users exists, and that core-t/groups maps to it.
     */
    public function test_core_t_groups() {
        $this->assert_icon_data(
            'users',
            't/groups',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-users"></span>',
            'core-t/groups',
            'pix/t/groups.png'
        );
    }

    /**
     * Tests that users exists, and that core-t/groupv maps to it.
     */
    public function test_core_t_groupv() {
        $this->assert_icon_data(
            'users',
            't/groupv',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-users"></span>',
            'core-t/groupv',
            'pix/t/groupv.png'
        );
    }

    /**
     * Tests that users exists, and that totara_core-teammembers maps to it.
     */
    public function test_totara_core_teammembers() {
        $this->assert_icon_data(
            'users',
            'teammembers',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-users"></span>',
            'totara_core-teammembers',
            'totara/core/pix/teammembers.png'
        );
    }

    /**
     * Tests that auto-completion-on exists, and that core-i/completion-auto-enabled maps to it.
     */
    public function test_core_i_completion_auto_enabled() {
        $this->assert_icon_data(
            'auto-completion-on',
            'i/completion-auto-enabled',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-check-circle-o ft-stack-main"></span><span class="fa fa-play ft-stack-suffix"></span></span>',
            'core-i/completion-auto-enabled',
            'pix/i/completion-auto-enabled.png'
        );
    }

    /**
     * Tests that times-circle-o-danger exists, and that core-i/completion-auto-fail maps to it.
     */
    public function test_core_i_completion_auto_fail() {
        $this->assert_icon_data(
            'times-circle-o-danger',
            'i/completion-auto-fail',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-times-circle-o ft-state-danger"></span>',
            'core-i/completion-auto-fail',
            'pix/i/completion-auto-fail.png'
        );
    }

    /**
     * Tests that circle-o exists, and that core-i/completion-auto-n maps to it.
     */
    public function test_core_i_completion_auto_n() {
        $this->assert_icon_data(
            'circle-o',
            'i/completion-auto-n',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-circle-o"></span>',
            'core-i/completion-auto-n',
            'pix/i/completion-auto-n.png'
        );
    }

    /**
     * Tests that check-circle-o-success exists, and that core-i/completion-auto-pass maps to it.
     */
    public function test_core_i_completion_auto_pass() {
        $this->assert_icon_data(
            'check-circle-o-success',
            'i/completion-auto-pass',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-circle-o ft-state-success"></span>',
            'core-i/completion-auto-pass',
            'pix/i/completion-auto-pass.png'
        );
    }

    /**
     * Tests that check-circle-o exists, and that core-i/completion-auto-y maps to it.
     */
    public function test_core_i_completion_auto_y() {
        $this->assert_icon_data(
            'check-circle-o',
            'i/completion-auto-y',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-circle-o"></span>',
            'core-i/completion-auto-y',
            'pix/i/completion-auto-y.png'
        );
    }

    /**
     * Tests that manual-completion-on exists, and that core-i/completion-manual-enabled maps to it.
     */
    public function test_core_i_completion_manual_enabled() {
        $this->assert_icon_data(
            'manual-completion-on',
            'i/completion-manual-enabled',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-check-square-o ft-stack-main"></span><span class="fa fa-play ft-stack-suffix"></span></span>',
            'core-i/completion-manual-enabled',
            'pix/i/completion-manual-enabled.png'
        );
    }

    /**
     * Tests that square-o exists, and that core-i/completion-manual-n maps to it.
     */
    public function test_core_i_completion_manual_n() {
        $this->assert_icon_data(
            'square-o',
            'i/completion-manual-n',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-square-o"></span>',
            'core-i/completion-manual-n',
            'pix/i/completion-manual-n.png'
        );
    }

    /**
     * Tests that check-square-o exists, and that core-i/completion-manual-y maps to it.
     */
    public function test_core_i_completion_manual_y() {
        $this->assert_icon_data(
            'check-square-o',
            'i/completion-manual-y',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-square-o"></span>',
            'core-i/completion-manual-y',
            'pix/i/completion-manual-y.png'
        );
    }

    /**
     * Tests that arrow-down exists, and that core-i/down maps to it.
     */
    public function test_core_i_down() {
        $this->assert_icon_data(
            'arrow-down',
            'i/down',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrow-down"></span>',
            'core-i/down',
            'pix/i/down.png'
        );
    }

    /**
     * Tests that arrow-down exists, and that core-t/disable_down maps to it.
     */
    public function test_core_t_disable_down() {
        $this->assert_icon_data(
            'arrow-down',
            't/disable_down',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrow-down"></span>',
            'core-t/disable_down',
            'pix/t/disable_down.png'
        );
    }

    /**
     * Tests that arrow-down exists, and that core-t/down maps to it.
     */
    public function test_core_t_down() {
        $this->assert_icon_data(
            'arrow-down',
            't/down',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrow-down"></span>',
            'core-t/down',
            'pix/t/down.png'
        );
    }

    /**
     * Tests that arrow-down exists, and that totara_program-progress_then maps to it.
     */
    public function test_totara_program_progress_then() {
        $this->assert_icon_data(
            'arrow-down',
            'progress_then',
            'totara_program',
            '<span aria-hidden="true" class="flex-icon fa fa-arrow-down"></span>',
            'totara_program-progress_then',
            'totara/program/pix/progress_then.png'
        );
    }

    /**
     * Tests that enrolment-suspended exists, and that core-i/enrolmentsuspended maps to it.
     */
    public function test_core_i_enrolmentsuspended() {
        $this->assert_icon_data(
            'enrolment-suspended',
            'i/enrolmentsuspended',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-user ft-stack-main"></span><span class="fa fa-pause ft-stack-suffix"></span></span>',
            'core-i/enrolmentsuspended',
            'pix/i/enrolmentsuspended.png'
        );
    }

    /**
     * Tests that add-feedback exists, and that core-i/feedback_add maps to it.
     */
    public function test_core_i_feedback_add() {
        $this->assert_icon_data(
            'add-feedback',
            'i/feedback_add',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-comment-o ft-stack-main"></span><span class="fa fa-plus ft-stack-suffix"></span></span>',
            'core-i/feedback_add',
            'pix/i/feedback_add.gif'
        );
    }

    /**
     * Tests that add-feedback exists, and that core-t/feedback_add maps to it.
     */
    public function test_core_t_feedback_add() {
        $this->assert_icon_data(
            'add-feedback',
            't/feedback_add',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-comment-o ft-stack-main"></span><span class="fa fa-plus ft-stack-suffix"></span></span>',
            'core-t/feedback_add',
            'pix/t/feedback_add.gif'
        );
    }

    /**
     * Tests that comment-o exists, and that core-i/feedback maps to it.
     */
    public function test_core_i_feedback() {
        $this->assert_icon_data(
            'comment-o',
            'i/feedback',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-comment-o"></span>',
            'core-i/feedback',
            'pix/i/feedback.gif'
        );
    }

    /**
     * Tests that comment-o exists, and that core-t/feedback maps to it.
     */
    public function test_core_t_feedback() {
        $this->assert_icon_data(
            'comment-o',
            't/feedback',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-comment-o"></span>',
            'core-t/feedback',
            'pix/t/feedback.gif'
        );
    }

    /**
     * Tests that comment-o exists, and that totara_core-t/comments-none maps to it.
     */
    public function test_totara_core_t_comments_none() {
        $this->assert_icon_data(
            'comment-o',
            't/comments-none',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-comment-o"></span>',
            'totara_core-t/comments-none',
            'totara/core/pix/t/comments-none.gif'
        );
    }

    /**
     * Tests that filter exists, and that core-i/filter maps to it.
     */
    public function test_core_i_filter() {
        $this->assert_icon_data(
            'filter',
            'i/filter',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-filter"></span>',
            'core-i/filter',
            'pix/i/filter.png'
        );
    }

    /**
     * Tests that flag exists, and that core-i/flagged maps to it.
     */
    public function test_core_i_flagged() {
        $this->assert_icon_data(
            'flag',
            'i/flagged',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-flag"></span>',
            'core-i/flagged',
            'pix/i/flagged.png'
        );
    }

    /**
     * Tests that flag-o exists, and that core-i/unflagged maps to it.
     */
    public function test_core_i_unflagged() {
        $this->assert_icon_data(
            'flag-o',
            'i/unflagged',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-flag-o"></span>',
            'core-i/unflagged',
            'pix/i/unflagged.png'
        );
    }

    /**
     * Tests that user-secret exists, and that core-i/guest maps to it.
     */
    public function test_core_i_guest() {
        $this->assert_icon_data(
            'user-secret',
            'i/guest',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user-secret"></span>',
            'core-i/guest',
            'pix/i/guest.gif'
        );
    }

    /**
     * Tests that eye-slash exists, and that core-i/hide maps to it.
     */
    public function test_core_i_hide() {
        $this->assert_icon_data(
            'eye-slash',
            'i/hide',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-eye-slash"></span>',
            'core-i/hide',
            'pix/i/hide.png'
        );
    }

    /**
     * Tests that eye-slash exists, and that core-t/hide maps to it.
     */
    public function test_core_t_hide() {
        $this->assert_icon_data(
            'eye-slash',
            't/hide',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-eye-slash"></span>',
            'core-t/hide',
            'pix/t/hide.png'
        );
    }

    /**
     * Tests that lock exists, and that core-i/lock maps to it.
     */
    public function test_core_i_lock() {
        $this->assert_icon_data(
            'lock',
            'i/lock',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-lock"></span>',
            'core-i/lock',
            'pix/i/lock.gif'
        );
    }

    /**
     * Tests that lock exists, and that core-t/lock maps to it.
     */
    public function test_core_t_lock() {
        $this->assert_icon_data(
            'lock',
            't/lock',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-lock"></span>',
            'core-t/lock',
            'pix/t/lock.png'
        );
    }

    /**
     * Tests that lock exists, and that core-t/locked maps to it.
     */
    public function test_core_t_locked() {
        $this->assert_icon_data(
            'lock',
            't/locked',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-lock"></span>',
            'core-t/locked',
            'pix/t/locked.png'
        );
    }

    /**
     * Tests that lightbulb-o exists, and that core-i/marked maps to it.
     */
    public function test_core_i_marked() {
        $this->assert_icon_data(
            'lightbulb-o',
            'i/marked',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-lightbulb-o"></span>',
            'core-i/marked',
            'pix/i/marked.png'
        );
    }

    /**
     * Tests that lightbulb-o-disabled exists, and that core-i/marker maps to it.
     */
    public function test_core_i_marker() {
        $this->assert_icon_data(
            'lightbulb-o-disabled',
            'i/marker',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-lightbulb-o ft-state-disabled"></span>',
            'core-i/marker',
            'pix/i/marker.png'
        );
    }

    /**
     * Tests that bars exists, and that core-i/menu maps to it.
     */
    public function test_core_i_menu() {
        $this->assert_icon_data(
            'bars',
            'i/menu',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-bars"></span>',
            'core-i/menu',
            'pix/i/menu.gif'
        );
    }

    /**
     * Tests that newspaper-o exists, and that core-i/news maps to it.
     */
    public function test_core_i_news() {
        $this->assert_icon_data(
            'newspaper-o',
            'i/news',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-newspaper-o"></span>',
            'core-i/news',
            'pix/i/news.gif'
        );
    }

    /**
     * Tests that pie-chart exists, and that core-i/outcomes maps to it.
     */
    public function test_core_i_outcomes() {
        $this->assert_icon_data(
            'pie-chart',
            'i/outcomes',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-pie-chart"></span>',
            'core-i/outcomes',
            'pix/i/outcomes.png'
        );
    }

    /**
     * Tests that pie-chart exists, and that totara_reportbuilder-report_icon maps to it.
     */
    public function test_totara_reportbuilder_report_icon() {
        $this->assert_icon_data(
            'pie-chart',
            'report_icon',
            'totara_reportbuilder',
            '<span aria-hidden="true" class="flex-icon fa fa-pie-chart"></span>',
            'totara_reportbuilder-report_icon',
            'totara/reportbuilder/pix/report_icon.png'
        );
    }

    /**
     * Tests that dollar exists, and that core-i/payment maps to it.
     */
    public function test_core_i_payment() {
        $this->assert_icon_data(
            'dollar',
            'i/payment',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-dollar"></span>',
            'core-i/payment',
            'pix/i/payment.gif'
        );
    }

    /**
     * Tests that dollar exists, and that core-m/USD maps to it.
     */
    public function test_core_m_USD() {
        $this->assert_icon_data(
            'dollar',
            'm/USD',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-dollar"></span>',
            'core-m/USD',
            'pix/m/USD.gif'
        );
    }

    /**
     * Tests that permissions exists, and that core-i/permissions maps to it.
     */
    public function test_core_i_permissions() {
        $this->assert_icon_data(
            'permissions',
            'i/permissions',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-user ft-stack-main"></span><span class="fa fa-key ft-stack-suffix ft-state-info"></span></span>',
            'core-i/permissions',
            'pix/i/permissions.png'
        );
    }

    /**
     * Tests that publish exists, and that core-i/publish maps to it.
     */
    public function test_core_i_publish() {
        $this->assert_icon_data(
            'publish',
            'i/publish',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-globe ft-stack-main"></span><span class="fa fa-play ft-stack-suffix ft-state-info"></span></span>',
            'core-i/publish',
            'pix/i/publish.png'
        );
    }

    /**
     * Tests that rss exists, and that core-i/rss maps to it.
     */
    public function test_core_i_rss() {
        $this->assert_icon_data(
            'rss',
            'i/rss',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-rss"></span>',
            'core-i/rss',
            'pix/i/rss.png'
        );
    }

    /**
     * Tests that user exists, and that core-i/self maps to it.
     */
    public function test_core_i_self() {
        $this->assert_icon_data(
            'user',
            'i/self',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user"></span>',
            'core-i/self',
            'pix/i/self.png'
        );
    }

    /**
     * Tests that user exists, and that core-i/user maps to it.
     */
    public function test_core_i_user() {
        $this->assert_icon_data(
            'user',
            'i/user',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user"></span>',
            'core-i/user',
            'pix/i/user.png'
        );
    }

    /**
     * Tests that user exists, and that core-t/user maps to it.
     */
    public function test_core_t_user() {
        $this->assert_icon_data(
            'user',
            't/user',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user"></span>',
            'core-t/user',
            'pix/t/user.png'
        );
    }

    /**
     * Tests that star-half-o exists, and that core-i/star-rating maps to it.
     */
    public function test_core_i_star_rating() {
        $this->assert_icon_data(
            'star-half-o',
            'i/star-rating',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-star-half-o"></span>',
            'core-i/star-rating',
            'pix/i/star-rating.png'
        );
    }

    /**
     * Tests that star-o exists, and that totara_plan-i/star_grey maps to it.
     */
    public function test_totara_plan_i_star_grey() {
        $this->assert_icon_data(
            'star-o',
            'i/star_grey',
            'totara_plan',
            '<span aria-hidden="true" class="flex-icon fa fa-star-o"></span>',
            'totara_plan-i/star_grey',
            'totara/plan/pix/i/star_grey.png'
        );
    }

    /**
     * Tests that star exists, and that totara_plan-i/star maps to it.
     */
    public function test_totara_plan_i_star() {
        $this->assert_icon_data(
            'star',
            'i/star',
            'totara_plan',
            '<span aria-hidden="true" class="flex-icon fa fa-star"></span>',
            'totara_plan-i/star',
            'totara/plan/pix/i/star.png'
        );
    }

    /**
     * Tests that star-o exists, and that totara_plan-t/star_grey maps to it.
     */
    public function test_totara_plan_t_star_grey() {
        $this->assert_icon_data(
            'star-o',
            't/star_grey',
            'totara_plan',
            '<span aria-hidden="true" class="flex-icon fa fa-star-o"></span>',
            'totara_plan-t/star_grey',
            'totara/plan/pix/t/star_grey.png'
        );
    }

    /**
     * Tests that star exists, and that totara_plan-t/star maps to it.
     */
    public function test_totara_plan_t_star() {
        $this->assert_icon_data(
            'star',
            't/star',
            'totara_plan',
            '<span aria-hidden="true" class="flex-icon fa fa-star"></span>',
            'totara_plan-t/star',
            'totara/plan/pix/t/star.png'
        );
    }

    /**
     * Tests that line-chart exists, and that core-i/stats maps to it.
     */
    public function test_core_i_stats() {
        $this->assert_icon_data(
            'line-chart',
            'i/stats',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-line-chart"></span>',
            'core-i/stats',
            'pix/i/stats.gif'
        );
    }

    /**
     * Tests that toggle-on exists, and that core-i/switch maps to it.
     */
    public function test_core_i_switch() {
        $this->assert_icon_data(
            'toggle-on',
            'i/switch',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-toggle-on"></span>',
            'core-i/switch',
            'pix/i/switch.gif'
        );
    }

    /**
     * Tests that user-refresh exists, and that core-i/switchrole maps to it.
     */
    public function test_core_i_switchrole() {
        $this->assert_icon_data(
            'user-refresh',
            'i/switchrole',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-user ft-stack-main"></span><span class="fa fa-refresh ft-stack-suffix ft-state-info"></span></span>',
            'core-i/switchrole',
            'pix/i/switchrole.png'
        );
    }

    /**
     * Tests that arrows-h exists, and that core-i/twoway maps to it.
     */
    public function test_core_i_twoway() {
        $this->assert_icon_data(
            'arrows-h',
            'i/twoway',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrows-h"></span>',
            'core-i/twoway',
            'pix/i/twoway.png'
        );
    }

    /**
     * Tests that unlock exists, and that core-i/unlock maps to it.
     */
    public function test_core_i_unlock() {
        $this->assert_icon_data(
            'unlock',
            'i/unlock',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-unlock"></span>',
            'core-i/unlock',
            'pix/i/unlock.gif'
        );
    }

    /**
     * Tests that unlock exists, and that core-t/unlock maps to it.
     */
    public function test_core_t_unlock() {
        $this->assert_icon_data(
            'unlock',
            't/unlock',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-unlock"></span>',
            'core-t/unlock',
            'pix/t/unlock.png'
        );
    }

    /**
     * Tests that arrow-up exists, and that core-i/up maps to it.
     */
    public function test_core_i_up() {
        $this->assert_icon_data(
            'arrow-up',
            'i/up',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrow-up"></span>',
            'core-i/up',
            'pix/i/up.png'
        );
    }

    /**
     * Tests that arrow-up exists, and that core-t/disable_up maps to it.
     */
    public function test_core_t_disable_up() {
        $this->assert_icon_data(
            'arrow-up',
            't/disable_up',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrow-up"></span>',
            'core-t/disable_up',
            'pix/t/disable_up.png'
        );
    }

    /**
     * Tests that arrow-up exists, and that core-t/up maps to it.
     */
    public function test_core_t_up() {
        $this->assert_icon_data(
            'arrow-up',
            't/up',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrow-up"></span>',
            'core-t/up',
            'pix/t/up.png'
        );
    }

    /**
     * Tests that user-times exists, and that core-i/userdel maps to it.
     */
    public function test_core_i_userdel() {
        $this->assert_icon_data(
            'user-times',
            'i/userdel',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user-times"></span>',
            'core-i/userdel',
            'pix/i/userdel.png'
        );
    }

    /**
     * Tests that user-times exists, and that core-t/usernot maps to it.
     */
    public function test_core_t_usernot() {
        $this->assert_icon_data(
            'user-times',
            't/usernot',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user-times"></span>',
            'core-t/usernot',
            'pix/t/usernot.gif'
        );
    }

    /**
     * Tests that user-event exists, and that core-i/userevent maps to it.
     */
    public function test_core_i_userevent() {
        $this->assert_icon_data(
            'user-event',
            'i/userevent',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-user ft-stack-main"></span><span class="fa fa-clock-o ft-stack-suffix ft-state-info"></span></span>',
            'core-i/userevent',
            'pix/i/userevent.png'
        );
    }

    /**
     * Tests that user-event exists, and that block_totara_stats-statlearnerhours maps to it.
     */
    public function test_block_totara_stats_statlearnerhours() {
        $this->assert_icon_data(
            'user-event',
            'statlearnerhours',
            'block_totara_stats',
            '<span class="flex-icon ft-stack"><span class="fa fa-user ft-stack-main"></span><span class="fa fa-clock-o ft-stack-suffix ft-state-info"></span></span>',
            'block_totara_stats-statlearnerhours',
            'blocks/totara_stats/pix/statlearnerhours.gif'
        );
    }

    /**
     * Tests that group-event exists, and that core-i/groupevent maps to it.
     */
    public function test_core_i_groupevent() {
        $this->assert_icon_data(
            'group-event',
            'i/groupevent',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-users ft-stack-main"></span><span class="fa fa-clock-o ft-stack-suffix ft-state-info"></span></span>',
            'core-i/groupevent',
            'pix/i/groupevent.png'
        );
    }

    /**
     * Tests that contact-add exists, and that core-t/addcontact maps to it.
     */
    public function test_core_t_addcontact() {
        $this->assert_icon_data(
            'contact-add',
            't/addcontact',
            'core',
            '<span class="flex-icon ft-stack"><span class="ft ft-address-book ft-stack-main"></span><span class="fa fa-plus ft-stack-suffix ft-state-info"></span></span>',
            'core-t/addcontact',
            'pix/t/addcontact.png'
        );
    }

    /**
     * Tests that contact-remove exists, and that core-t/removecontact maps to it.
     */
    public function test_core_t_removecontact() {
        $this->assert_icon_data(
            'contact-remove',
            't/removecontact',
            'core',
            '<span class="flex-icon ft-stack"><span class="ft ft-address-book ft-stack-main"></span><span class="fa fa-minus ft-stack-suffix ft-state-danger"></span></span>',
            'core-t/removecontact',
            'pix/t/removecontact.png'
        );
    }

    /**
     * Tests that trophy exists, and that core-t/award maps to it.
     */
    public function test_core_t_award() {
        $this->assert_icon_data(
            'trophy',
            't/award',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-trophy"></span>',
            'core-t/award',
            'pix/t/award.png'
        );
    }

    /**
     * Tests that dock_to_block exists, and that core-t/block_to_dock_rtl maps to it.
     */
    public function test_core_t_block_to_dock_rtl() {
        $this->assert_icon_data(
            'dock_to_block',
            't/block_to_dock_rtl',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-square-o-right"></span>',
            'core-t/block_to_dock_rtl',
            'pix/t/block_to_dock_rtl.png'
        );
    }

    /**
     * Tests that dock_to_block exists, and that core-t/dock_to_block maps to it.
     */
    public function test_core_t_dock_to_block() {
        $this->assert_icon_data(
            'dock_to_block',
            't/dock_to_block',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-square-o-right"></span>',
            'core-t/dock_to_block',
            'pix/t/dock_to_block.png'
        );
    }

    /**
     * Tests that block_to_dock exists, and that core-t/block_to_dock maps to it.
     */
    public function test_core_t_block_to_dock() {
        $this->assert_icon_data(
            'block_to_dock',
            't/block_to_dock',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-square-o-left"></span>',
            'core-t/block_to_dock',
            'pix/t/block_to_dock.png'
        );
    }

    /**
     * Tests that block_to_dock exists, and that core-t/dock_to_block_rtl maps to it.
     */
    public function test_core_t_dock_to_block_rtl() {
        $this->assert_icon_data(
            'block_to_dock',
            't/dock_to_block_rtl',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-caret-square-o-left"></span>',
            'core-t/dock_to_block_rtl',
            'pix/t/dock_to_block_rtl.png'
        );
    }

    /**
     * Tests that block exists, and that core-t/block maps to it.
     */
    public function test_core_t_block() {
        $this->assert_icon_data(
            'block',
            't/block',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-ban"></span>',
            'core-t/block',
            'pix/t/block.png'
        );
    }

    /**
     * Tests that cache exists, and that core-t/cache maps to it.
     */
    public function test_core_t_cache() {
        $this->assert_icon_data(
            'cache',
            't/cache',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-bolt"></span>',
            'core-t/cache',
            'pix/t/cache.gif'
        );
    }

    /**
     * Tests that calculator-off exists, and that core-t/calc_off maps to it.
     */
    public function test_core_t_calc_off() {
        $this->assert_icon_data(
            'calculator-off',
            't/calc_off',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-calculator ft-stack-main"></span><span class="ft ft-slash ft-stack-over ft-state-danger"></span></span>',
            'core-t/calc_off',
            'pix/t/calc_off.png'
        );
    }

    /**
     * Tests that calculator exists, and that core-t/calc maps to it.
     */
    public function test_core_t_calc() {
        $this->assert_icon_data(
            'calculator',
            't/calc',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-calculator"></span>',
            'core-t/calc',
            'pix/t/calc.png'
        );
    }

    /**
     * Tests that times-circle-o exists, and that core-t/dockclose maps to it.
     */
    public function test_core_t_dockclose() {
        $this->assert_icon_data(
            'times-circle-o',
            't/dockclose',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-times-circle-o"></span>',
            'core-t/dockclose',
            'pix/t/dockclose.png'
        );
    }

    /**
     * Tests that editmenu exists, and that core-t/edit_menu maps to it.
     */
    public function test_core_t_edit_menu() {
        $this->assert_icon_data(
            'editmenu',
            't/edit_menu',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-cog ft-stack-main"></span><span class="fa fa-caret-down ft-stack-suffix"></span></span>',
            'core-t/edit_menu',
            'pix/t/edit_menu.png'
        );
    }

    /**
     * Tests that no-email exists, and that core-t/emailno maps to it.
     */
    public function test_core_t_emailno() {
        $this->assert_icon_data(
            'no-email',
            't/emailno',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-envelope-o ft-stack-main"></span><span class="ft ft-slash ft-stack-over ft-state-danger"></span></span>',
            'core-t/emailno',
            'pix/t/emailno.png'
        );
    }

    /**
     * Tests that circle-success exists, and that core-t/go maps to it.
     */
    public function test_core_t_go() {
        $this->assert_icon_data(
            'circle-success',
            't/go',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-circle ft-state-success"></span>',
            'core-t/go',
            'pix/t/go.png'
        );
    }

    /**
     * Tests that arrow-left exists, and that core-t/left maps to it.
     */
    public function test_core_t_left() {
        $this->assert_icon_data(
            'arrow-left',
            't/left',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrow-left"></span>',
            'core-t/left',
            'pix/t/left.png'
        );
    }

    /**
     * Tests that arrow-right exists, and that core-t/right maps to it.
     */
    public function test_core_t_right() {
        $this->assert_icon_data(
            'arrow-right',
            't/right',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrow-right"></span>',
            'core-t/right',
            'pix/t/right.png'
        );
    }

    /**
     * Tests that comment exists, and that core-t/message maps to it.
     */
    public function test_core_t_message() {
        $this->assert_icon_data(
            'comment',
            't/message',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-comment"></span>',
            'core-t/message',
            'pix/t/message.png'
        );
    }

    /**
     * Tests that comments exists, and that core-t/messages maps to it.
     */
    public function test_core_t_messages() {
        $this->assert_icon_data(
            'comments',
            't/messages',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-comments"></span>',
            'core-t/messages',
            'pix/t/messages.png'
        );
    }

    /**
     * Tests that arrows-v exists, and that core-t/move maps to it.
     */
    public function test_core_t_move() {
        $this->assert_icon_data(
            'arrows-v',
            't/move',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-arrows-v"></span>',
            'core-t/move',
            'pix/t/move.png'
        );
    }

    /**
     * Tests that sliders exists, and that core-t/preferences maps to it.
     */
    public function test_core_t_preferences() {
        $this->assert_icon_data(
            'sliders',
            't/preferences',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-sliders"></span>',
            'core-t/preferences',
            'pix/t/preferences.png'
        );
    }

    /**
     * Tests that recycle exists, and that core-t/recycle maps to it.
     */
    public function test_core_t_recycle() {
        $this->assert_icon_data(
            'recycle',
            't/recycle',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-recycle"></span>',
            'core-t/recycle',
            'pix/t/recycle.png'
        );
    }

    /**
     * Tests that circle-disabled exists, and that core-t/stop_gray maps to it.
     */
    public function test_core_t_stop_gray() {
        $this->assert_icon_data(
            'circle-disabled',
            't/stop_gray',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-circle ft-state-disabled"></span>',
            'core-t/stop_gray',
            'pix/t/stop_gray.png'
        );
    }

    /**
     * Tests that circle-danger exists, and that core-t/stop maps to it.
     */
    public function test_core_t_stop() {
        $this->assert_icon_data(
            'circle-danger',
            't/stop',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-circle ft-state-danger"></span>',
            'core-t/stop',
            'pix/t/stop.png'
        );
    }

    /**
     * Tests that minus-square exists, and that core-t/switch_minus maps to it.
     */
    public function test_core_t_switch_minus() {
        $this->assert_icon_data(
            'minus-square',
            't/switch_minus',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-minus-square"></span>',
            'core-t/switch_minus',
            'pix/t/switch_minus.png'
        );
    }

    /**
     * Tests that plus-square exists, and that core-t/switch_plus_rtl maps to it.
     */
    public function test_core_t_switch_plus_rtl() {
        $this->assert_icon_data(
            'plus-square',
            't/switch_plus_rtl',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-square"></span>',
            'core-t/switch_plus_rtl',
            'pix/t/switch_plus_rtl.png'
        );
    }

    /**
     * Tests that plus-square exists, and that core-t/switch_plus maps to it.
     */
    public function test_core_t_switch_plus() {
        $this->assert_icon_data(
            'plus-square',
            't/switch_plus',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-square"></span>',
            'core-t/switch_plus',
            'pix/t/switch_plus.png'
        );
    }

    /**
     * Tests that plus-square exists, and that core-t/switch maps to it.
     */
    public function test_core_t_switch() {
        $this->assert_icon_data(
            'plus-square',
            't/switch',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-square"></span>',
            'core-t/switch',
            'pix/t/switch.gif'
        );
    }

    /**
     * Tests that external-link-square exists, and that core-t/switch_whole maps to it.
     */
    public function test_core_t_switch_whole() {
        $this->assert_icon_data(
            'external-link-square',
            't/switch_whole',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-external-link-square"></span>',
            'core-t/switch_whole',
            'pix/t/switch_whole.png'
        );
    }

    /**
     * Tests that unlock-alt exists, and that core-t/unlocked maps to it.
     */
    public function test_core_t_unlocked() {
        $this->assert_icon_data(
            'unlock-alt',
            't/unlocked',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-unlock-alt"></span>',
            'core-t/unlocked',
            'pix/t/unlocked.png'
        );
    }

    /**
     * Tests that user-disabled exists, and that core-u/f1 maps to it.
     */
    public function test_core_u_f1() {
        $this->assert_icon_data(
            'user-disabled',
            'u/f1',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user ft-state-disabled"></span>',
            'core-u/f1',
            'pix/u/f1.png'
        );
    }

    /**
     * Tests that user-disabled exists, and that core-u/f2 maps to it.
     */
    public function test_core_u_f2() {
        $this->assert_icon_data(
            'user-disabled',
            'u/f2',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user ft-state-disabled"></span>',
            'core-u/f2',
            'pix/u/f2.png'
        );
    }

    /**
     * Tests that user-disabled exists, and that core-u/f3 maps to it.
     */
    public function test_core_u_f3() {
        $this->assert_icon_data(
            'user-disabled',
            'u/f3',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user ft-state-disabled"></span>',
            'core-u/f3',
            'pix/u/f3.png'
        );
    }

    /**
     * Tests that user-disabled exists, and that core-u/user100 maps to it.
     */
    public function test_core_u_user100() {
        $this->assert_icon_data(
            'user-disabled',
            'u/user100',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user ft-state-disabled"></span>',
            'core-u/user100',
            'pix/u/user100.png'
        );
    }

    /**
     * Tests that user-disabled exists, and that core-u/user35 maps to it.
     */
    public function test_core_u_user35() {
        $this->assert_icon_data(
            'user-disabled',
            'u/user35',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-user ft-state-disabled"></span>',
            'core-u/user35',
            'pix/u/user35.png'
        );
    }

    /**
     * Tests that dropbox exists, and that repository_dropbox-icon maps to it.
     */
    public function test_repository_dropbox_icon() {
        $this->assert_icon_data(
            'dropbox',
            'icon',
            'repository_dropbox',
            '<span aria-hidden="true" class="flex-icon fa fa-dropbox"></span>',
            'repository_dropbox-icon',
            'repository/dropbox/pix/icon.png'
        );
    }

    /**
     * Tests that flickr exists, and that repository_flickr_public-icon maps to it.
     */
    public function test_repository_flickr_public_icon() {
        $this->assert_icon_data(
            'flickr',
            'icon',
            'repository_flickr_public',
            '<span aria-hidden="true" class="flex-icon fa fa-flickr"></span>',
            'repository_flickr_public-icon',
            'repository/flickr_public/pix/icon.png'
        );
    }

    /**
     * Tests that flickr exists, and that repository_flickr-icon maps to it.
     */
    public function test_repository_flickr_icon() {
        $this->assert_icon_data(
            'flickr',
            'icon',
            'repository_flickr',
            '<span aria-hidden="true" class="flex-icon fa fa-flickr"></span>',
            'repository_flickr-icon',
            'repository/flickr/pix/icon.png'
        );
    }

    /**
     * Tests that skyatlas exists, and that repository_skydrive-icon maps to it.
     */
    public function test_repository_skydrive_icon() {
        $this->assert_icon_data(
            'skyatlas',
            'icon',
            'repository_skydrive',
            '<span aria-hidden="true" class="flex-icon fa fa-skyatlas"></span>',
            'repository_skydrive-icon',
            'repository/skydrive/pix/icon.png'
        );
    }

    /**
     * Tests that youtube-play exists, and that repository_youtube-icon maps to it.
     */
    public function test_repository_youtube_icon() {
        $this->assert_icon_data(
            'youtube-play',
            'icon',
            'repository_youtube',
            '<span aria-hidden="true" class="flex-icon fa fa-youtube-play"></span>',
            'repository_youtube-icon',
            'repository/youtube/pix/icon.png'
        );
    }

    /**
     * Tests that ellipsis-h exists, and that totara_program-progress_or maps to it.
     */
    public function test_totara_program_progress_or() {
        $this->assert_icon_data(
            'ellipsis-h',
            'progress_or',
            'totara_program',
            '<span aria-hidden="true" class="flex-icon fa fa-ellipsis-h"></span>',
            'totara_program-progress_or',
            'totara/program/pix/progress_or.png'
        );
    }

    /**
     * Tests that competency-achieved exists, and that block_totara_stats-statcompachieved maps to it.
     */
    public function test_block_totara_stats_statcompachieved() {
        $this->assert_icon_data(
            'competency-achieved',
            'statcompachieved',
            'block_totara_stats',
            '<span class="flex-icon ft-stack"><span class="ft ft-competency ft-stack-main"></span><span class="fa fa-check ft-stack-suffix ft-state-success"></span></span>',
            'block_totara_stats-statcompachieved',
            'blocks/totara_stats/pix/statcompachieved.gif'
        );
    }

    /**
     * Tests that course-completed exists, and that block_totara_stats-statcoursescompleted maps to it.
     */
    public function test_block_totara_stats_statcoursescompleted() {
        $this->assert_icon_data(
            'course-completed',
            'statcoursescompleted',
            'block_totara_stats',
            '<span class="flex-icon ft-stack"><span class="fa fa-cube ft-stack-main"></span><span class="fa fa-check ft-stack-suffix ft-state-success"></span></span>',
            'block_totara_stats-statcoursescompleted',
            'blocks/totara_stats/pix/statcoursescompleted.gif'
        );
    }

    /**
     * Tests that course-started exists, and that block_totara_stats-statcoursesstarted maps to it.
     */
    public function test_block_totara_stats_statcoursesstarted() {
        $this->assert_icon_data(
            'course-started',
            'statcoursesstarted',
            'block_totara_stats',
            '<span class="flex-icon ft-stack"><span class="fa fa-cube ft-stack-main"></span><span class="fa fa-play ft-stack-suffix"></span></span>',
            'block_totara_stats-statcoursesstarted',
            'blocks/totara_stats/pix/statcoursesstarted.gif'
        );
    }

    /**
     * Tests that objective-achieved exists, and that block_totara_stats-statobjachieved maps to it.
     */
    public function test_block_totara_stats_statobjachieved() {
        $this->assert_icon_data(
            'objective-achieved',
            'statobjachieved',
            'block_totara_stats',
            '<span class="flex-icon ft-stack"><span class="fa fa-bullseye ft-stack-main"></span><span class="fa fa-check ft-stack-suffix ft-state-success"></span></span>',
            'block_totara_stats-statobjachieved',
            'blocks/totara_stats/pix/statobjachieved.gif'
        );
    }

    /**
     * Tests that grades exists, and that gradingform_guide-icon maps to it.
     */
    public function test_gradingform_guide_icon() {
        $this->assert_icon_data(
            'grades',
            'icon',
            'gradingform_guide',
            '<span aria-hidden="true" class="flex-icon ft ft-grades"></span>',
            'gradingform_guide-icon',
            'grade/grading/form/guide/pix/icon.png'
        );
    }

    /**
     * Tests that grades exists, and that gradingform_rubric-icon maps to it.
     */
    public function test_gradingform_rubric_icon() {
        $this->assert_icon_data(
            'grades',
            'icon',
            'gradingform_rubric',
            '<span aria-hidden="true" class="flex-icon ft ft-grades"></span>',
            'gradingform_rubric-icon',
            'grade/grading/form/rubric/pix/icon.png'
        );
    }

    /**
     * Tests that grades exists, and that core-i/grades maps to it.
     */
    public function test_core_i_grades() {
        $this->assert_icon_data(
            'grades',
            'i/grades',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-grades"></span>',
            'core-i/grades',
            'pix/i/grades.png'
        );
    }

    /**
     * Tests that grades exists, and that core-t/grades maps to it.
     */
    public function test_core_t_grades() {
        $this->assert_icon_data(
            'grades',
            't/grades',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-grades"></span>',
            'core-t/grades',
            'pix/t/grades.png'
        );
    }

    /**
     * Tests that highlight exists, and that assignfeedback_editpdf-highlight maps to it.
     */
    public function test_assignfeedback_editpdf_highlight() {
        $this->assert_icon_data(
            'highlight',
            'highlight',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon ft ft-highlight"></span>',
            'assignfeedback_editpdf-highlight',
            'mod/assign/feedback/editpdf/pix/highlight.png'
        );
    }

    /**
     * Tests that stamp exists, and that assignfeedback_editpdf-stamp maps to it.
     */
    public function test_assignfeedback_editpdf_stamp() {
        $this->assert_icon_data(
            'stamp',
            'stamp',
            'assignfeedback_editpdf',
            '<span aria-hidden="true" class="flex-icon ft ft-stamp"></span>',
            'assignfeedback_editpdf-stamp',
            'mod/assign/feedback/editpdf/pix/stamp.png'
        );
    }

    /**
     * Tests that seminar exists, and that mod_facetoface-icon maps to it.
     */
    public function test_mod_facetoface_icon() {
        $this->assert_icon_data(
            'seminar',
            'icon',
            'mod_facetoface',
            '<span aria-hidden="true" class="flex-icon ft ft-seminar"></span>',
            'mod_facetoface-icon',
            'mod/facetoface/pix/icon.png'
        );
    }

    /**
     * Tests that seminar exists, and that mod_workshop-icon maps to it.
     */
    public function test_mod_workshop_icon() {
        $this->assert_icon_data(
            'seminar',
            'icon',
            'mod_workshop',
            '<span aria-hidden="true" class="flex-icon ft ft-seminar"></span>',
            'mod_workshop-icon',
            'mod/workshop/pix/icon.png'
        );
    }

    /**
     * Tests that addressbook exists, and that mod_glossary-icon maps to it.
     */
    public function test_mod_glossary_icon() {
        $this->assert_icon_data(
            'addressbook',
            'icon',
            'mod_glossary',
            '<span aria-hidden="true" class="flex-icon ft ft-addressbook"></span>',
            'mod_glossary-icon',
            'mod/glossary/pix/icon.png'
        );
    }

    /**
     * Tests that checklist exists, and that mod_quiz-icon maps to it.
     */
    public function test_mod_quiz_icon() {
        $this->assert_icon_data(
            'checklist',
            'icon',
            'mod_quiz',
            '<span aria-hidden="true" class="flex-icon ft ft-checklist"></span>',
            'mod_quiz-icon',
            'mod/quiz/pix/icon.png'
        );
    }

    /**
     * Tests that assign exists, and that mod_assign-icon maps to it.
     */
    public function test_mod_assign_icon() {
        $this->assert_icon_data(
            'assign',
            'icon',
            'mod_assign',
            '<span class="flex-icon ft-stack"><span class="fa fa-file-text-o ft-stack-main"></span><span class="fa fa-thumb-tack ft-stack-suffix ft-state-info"></span></span>',
            'mod_assign-icon',
            'mod/assign/pix/icon.png'
        );
    }

    /**
     * Tests that folder-create exists, and that core-a/create_folder maps to it.
     */
    public function test_core_a_create_folder() {
        $this->assert_icon_data(
            'folder-create',
            'a/create_folder',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-folder-o ft-stack-main"></span><span class="fa fa-plus ft-stack-suffix"></span></span>',
            'core-a/create_folder',
            'pix/a/create_folder.png'
        );
    }

    /**
     * Tests that folder-create exists, and that core-t/adddir maps to it.
     */
    public function test_core_t_adddir() {
        $this->assert_icon_data(
            'folder-create',
            't/adddir',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-folder-o ft-stack-main"></span><span class="fa fa-plus ft-stack-suffix"></span></span>',
            'core-t/adddir',
            'pix/t/adddir.png'
        );
    }

    /**
     * Tests that viewtreeactive exists, and that core-a/view_tree_active maps to it.
     */
    public function test_core_a_view_tree_active() {
        $this->assert_icon_data(
            'viewtreeactive',
            'a/view_tree_active',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-viewtreeactive"></span>',
            'core-a/view_tree_active',
            'pix/a/view_tree_active.png'
        );
    }

    /**
     * Tests that viewtreeactive exists, and that core-i/withsubcat maps to it.
     */
    public function test_core_i_withsubcat() {
        $this->assert_icon_data(
            'viewtreeactive',
            'i/withsubcat',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-viewtreeactive"></span>',
            'core-i/withsubcat',
            'pix/i/withsubcat.png'
        );
    }

    /**
     * Tests that no-subcategory exists, and that core-i/nosubcat maps to it.
     */
    public function test_core_i_nosubcat() {
        $this->assert_icon_data(
            'no-subcategory',
            'i/nosubcat',
            'core',
            '<span class="flex-icon ft-stack"><span class="ft ft-viewtreeactive ft-stack-main"></span><span class="ft ft-slash ft-stack-over ft-state-danger"></span></span>',
            'core-i/nosubcat',
            'pix/i/nosubcat.png'
        );
    }

    /**
     * Tests that mean exists, and that core-i/agg_mean maps to it.
     */
    public function test_core_i_agg_mean() {
        $this->assert_icon_data(
            'mean',
            'i/agg_mean',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-mean"></span>',
            'core-i/agg_mean',
            'pix/i/agg_mean.png'
        );
    }

    /**
     * Tests that mean exists, and that core-i/mean maps to it.
     */
    public function test_core_i_mean() {
        $this->assert_icon_data(
            'mean',
            'i/mean',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-mean"></span>',
            'core-i/mean',
            'pix/i/mean.gif'
        );
    }

    /**
     * Tests that mean exists, and that core-t/mean maps to it.
     */
    public function test_core_t_mean() {
        $this->assert_icon_data(
            'mean',
            't/mean',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-mean"></span>',
            'core-t/mean',
            'pix/t/mean.gif'
        );
    }

    /**
     * Tests that sigma exists, and that core-i/agg_sum maps to it.
     */
    public function test_core_i_agg_sum() {
        $this->assert_icon_data(
            'sigma',
            'i/agg_sum',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-sigma"></span>',
            'core-i/agg_sum',
            'pix/i/agg_sum.png'
        );
    }

    /**
     * Tests that sigma exists, and that core-t/sigma maps to it.
     */
    public function test_core_t_sigma() {
        $this->assert_icon_data(
            'sigma',
            't/sigma',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-sigma"></span>',
            'core-t/sigma',
            'pix/t/sigma.gif'
        );
    }

    /**
     * Tests that sigma-plus exists, and that core-t/sigmaplus maps to it.
     */
    public function test_core_t_sigmaplus() {
        $this->assert_icon_data(
            'sigma-plus',
            't/sigmaplus',
            'core',
            '<span class="flex-icon ft-stack"><span class="ft ft-sigma ft-stack-main"></span><span class="fa fa-plus ft-stack-suffix"></span></span>',
            'core-t/sigmaplus',
            'pix/t/sigmaplus.gif'
        );
    }

    /**
     * Tests that cog-lock exists, and that core-i/configlock maps to it.
     */
    public function test_core_i_configlock() {
        $this->assert_icon_data(
            'cog-lock',
            'i/configlock',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-cog ft-stack-main"></span><span class="fa fa-lock ft-stack-suffix ft-state-danger"></span></span>',
            'core-i/configlock',
            'pix/i/configlock.png'
        );
    }

    /**
     * Tests that site-lock exists, and that core-i/hierarchylock maps to it.
     */
    public function test_core_i_hierarchylock() {
        $this->assert_icon_data(
            'site-lock',
            'i/hierarchylock',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-sitemap ft-stack-main"></span><span class="fa fa-lock ft-stack-suffix ft-state-danger"></span></span>',
            'core-i/hierarchylock',
            'pix/i/hierarchylock.png'
        );
    }

    /**
     * Tests that log exists, and that core-i/log maps to it.
     */
    public function test_core_i_log() {
        $this->assert_icon_data(
            'log',
            'i/log',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-hbars"></span>',
            'core-i/log',
            'pix/i/log.gif'
        );
    }

    /**
     * Tests that log exists, and that core-t/log maps to it.
     */
    public function test_core_t_log() {
        $this->assert_icon_data(
            'log',
            't/log',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-hbars"></span>',
            'core-t/log',
            'pix/t/log.gif'
        );
    }

    /**
     * Tests that mahara exists, and that core-i/mahara_host maps to it.
     */
    public function test_core_i_mahara_host() {
        $this->assert_icon_data(
            'mahara',
            'i/mahara_host',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-mahara"></span>',
            'core-i/mahara_host',
            'pix/i/mahara_host.gif'
        );
    }

    /**
     * Tests that mnet-host exists, and that core-i/mnethost maps to it.
     */
    public function test_core_i_mnethost() {
        $this->assert_icon_data(
            'mnet-host',
            'i/mnethost',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-mnethost"></span>',
            'core-i/mnethost',
            'pix/i/mnethost.png'
        );
    }

    /**
     * Tests that moodle exists, and that core-i/moodle_host maps to it.
     */
    public function test_core_i_moodle_host() {
        $this->assert_icon_data(
            'moodle',
            'i/moodle_host',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-moodle"></span>',
            'core-i/moodle_host',
            'pix/i/moodle_host.png'
        );
    }

    /**
     * Tests that new exists, and that core-i/new maps to it.
     */
    public function test_core_i_new() {
        $this->assert_icon_data(
            'new',
            'i/new',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-new"></span>',
            'core-i/new',
            'pix/i/new.gif'
        );
    }

    /**
     * Tests that permission-lock exists, and that core-i/permissionlock maps to it.
     */
    public function test_core_i_permissionlock() {
        $this->assert_icon_data(
            'permission-lock',
            'i/permissionlock',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-user ft-stack-main"></span><span class="fa fa-lock ft-stack-suffix ft-state-danger"></span></span>',
            'core-i/permissionlock',
            'pix/i/permissionlock.png'
        );
    }

    /**
     * Tests that portfolio exists, and that core-i/portfolio maps to it.
     */
    public function test_core_i_portfolio() {
        $this->assert_icon_data(
            'portfolio',
            'i/portfolio',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-profile"></span>',
            'core-i/portfolio',
            'pix/i/portfolio.gif'
        );
    }

    /**
     * Tests that portfolio-add exists, and that core-t/portfolioadd maps to it.
     */
    public function test_core_t_portfolioadd() {
        $this->assert_icon_data(
            'portfolio-add',
            't/portfolioadd',
            'core',
            '<span class="flex-icon ft-stack"><span class="ft ft-profile ft-stack-main"></span><span class="fa fa-plus ft-stack-suffix"></span></span>',
            'core-t/portfolioadd',
            'pix/t/portfolioadd.png'
        );
    }

    /**
     * Tests that scales exists, and that core-i/scales maps to it.
     */
    public function test_core_i_scales() {
        $this->assert_icon_data(
            'scales',
            'i/scales',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-stats-bars"></span>',
            'core-i/scales',
            'pix/i/scales.png'
        );
    }

    /**
     * Tests that scales exists, and that core-t/scales maps to it.
     */
    public function test_core_t_scales() {
        $this->assert_icon_data(
            'scales',
            't/scales',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-stats-bars"></span>',
            'core-t/scales',
            'pix/t/scales.gif'
        );
    }

    /**
     * Tests that backpack exists, and that core-t/backpack maps to it.
     */
    public function test_core_t_backpack() {
        $this->assert_icon_data(
            'backpack',
            't/backpack',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-backpack"></span>',
            'core-t/backpack',
            'pix/t/backpack.png'
        );
    }

    /**
     * Tests that bars exists, and that core-t/contextmenu maps to it.
     */
    public function test_core_t_contextmenu() {
        $this->assert_icon_data(
            'bars',
            't/contextmenu',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-bars"></span>',
            'core-t/contextmenu',
            'pix/t/contextmenu.png'
        );
    }

    /**
     * Tests that ranges exists, and that core-t/ranges maps to it.
     */
    public function test_core_t_ranges() {
        $this->assert_icon_data(
            'ranges',
            't/ranges',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-vbars"></span>',
            'core-t/ranges',
            'pix/t/ranges.gif'
        );
    }

    /**
     * Tests that alfresco exists, and that repository_alfresco-icon maps to it.
     */
    public function test_repository_alfresco_icon() {
        $this->assert_icon_data(
            'alfresco',
            'icon',
            'repository_alfresco',
            '<span aria-hidden="true" class="flex-icon ft ft-alfresco"></span>',
            'repository_alfresco-icon',
            'repository/alfresco/pix/icon.png'
        );
    }

    /**
     * Tests that gdrive exists, and that repository_googledocs-icon maps to it.
     */
    public function test_repository_googledocs_icon() {
        $this->assert_icon_data(
            'gdrive',
            'icon',
            'repository_googledocs',
            '<span aria-hidden="true" class="flex-icon ft ft-gdrive"></span>',
            'repository_googledocs-icon',
            'repository/googledocs/pix/icon.png'
        );
    }

    /**
     * Tests that picasa exists, and that repository_picasa-icon maps to it.
     */
    public function test_repository_picasa_icon() {
        $this->assert_icon_data(
            'picasa',
            'icon',
            'repository_picasa',
            '<span aria-hidden="true" class="flex-icon ft ft-picasa"></span>',
            'repository_picasa-icon',
            'repository/picasa/pix/icon.png'
        );
    }

    /**
     * Tests that plus-circle-info exists, and that totara_core-msgicons/blended-add maps to it.
     */
    public function test_totara_core_msgicons_blended_add() {
        $this->assert_icon_data(
            'plus-circle-info',
            'msgicons/blended-add',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-circle ft-state-info"></span>',
            'totara_core-msgicons/blended-add',
            'totara/core/pix/msgicons/blended-add.png'
        );
    }

    /**
     * Tests that plus-circle-info exists, and that totara_core-msgicons/competency-add maps to it.
     */
    public function test_totara_core_msgicons_competency_add() {
        $this->assert_icon_data(
            'plus-circle-info',
            'msgicons/competency-add',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-circle ft-state-info"></span>',
            'totara_core-msgicons/competency-add',
            'totara/core/pix/msgicons/competency-add.png'
        );
    }

    /**
     * Tests that plus-circle-info exists, and that totara_core-msgicons/course-add maps to it.
     */
    public function test_totara_core_msgicons_course_add() {
        $this->assert_icon_data(
            'plus-circle-info',
            'msgicons/course-add',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-circle ft-state-info"></span>',
            'totara_core-msgicons/course-add',
            'totara/core/pix/msgicons/course-add.png'
        );
    }

    /**
     * Tests that plus-circle-info exists, and that totara_core-msgicons/elearning-add maps to it.
     */
    public function test_totara_core_msgicons_elearning_add() {
        $this->assert_icon_data(
            'plus-circle-info',
            'msgicons/elearning-add',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-circle ft-state-info"></span>',
            'totara_core-msgicons/elearning-add',
            'totara/core/pix/msgicons/elearning-add.png'
        );
    }

    /**
     * Tests that plus-circle-info exists, and that totara_core-msgicons/evidence-add maps to it.
     */
    public function test_totara_core_msgicons_evidence_add() {
        $this->assert_icon_data(
            'plus-circle-info',
            'msgicons/evidence-add',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-circle ft-state-info"></span>',
            'totara_core-msgicons/evidence-add',
            'totara/core/pix/msgicons/evidence-add.png'
        );
    }

    /**
     * Tests that plus-circle-info exists, and that totara_core-msgicons/facetoface-add maps to it.
     */
    public function test_totara_core_msgicons_facetoface_add() {
        $this->assert_icon_data(
            'plus-circle-info',
            'msgicons/facetoface-add',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-circle ft-state-info"></span>',
            'totara_core-msgicons/facetoface-add',
            'totara/core/pix/msgicons/facetoface-add.png'
        );
    }

    /**
     * Tests that plus-circle-info exists, and that totara_core-msgicons/learningplan-add maps to it.
     */
    public function test_totara_core_msgicons_learningplan_add() {
        $this->assert_icon_data(
            'plus-circle-info',
            'msgicons/learningplan-add',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-circle ft-state-info"></span>',
            'totara_core-msgicons/learningplan-add',
            'totara/core/pix/msgicons/learningplan-add.png'
        );
    }

    /**
     * Tests that plus-circle-info exists, and that totara_core-msgicons/objective-add maps to it.
     */
    public function test_totara_core_msgicons_objective_add() {
        $this->assert_icon_data(
            'plus-circle-info',
            'msgicons/objective-add',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-circle ft-state-info"></span>',
            'totara_core-msgicons/objective-add',
            'totara/core/pix/msgicons/objective-add.png'
        );
    }

    /**
     * Tests that plus-circle-info exists, and that totara_core-msgicons/program-add maps to it.
     */
    public function test_totara_core_msgicons_program_add() {
        $this->assert_icon_data(
            'plus-circle-info',
            'msgicons/program-add',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-circle ft-state-info"></span>',
            'totara_core-msgicons/program-add',
            'totara/core/pix/msgicons/program-add.png'
        );
    }

    /**
     * Tests that plus-circle-info exists, and that totara_core-msgicons/resource-add maps to it.
     */
    public function test_totara_core_msgicons_resource_add() {
        $this->assert_icon_data(
            'plus-circle-info',
            'msgicons/resource-add',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-plus-circle ft-state-info"></span>',
            'totara_core-msgicons/resource-add',
            'totara/core/pix/msgicons/resource-add.png'
        );
    }

    /**
     * Tests that thumbs-up-success exists, and that totara_core-msgicons/blended-approve maps to it.
     */
    public function test_totara_core_msgicons_blended_approve() {
        $this->assert_icon_data(
            'thumbs-up-success',
            'msgicons/blended-approve',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-up ft-state-success"></span>',
            'totara_core-msgicons/blended-approve',
            'totara/core/pix/msgicons/blended-approve.png'
        );
    }

    /**
     * Tests that thumbs-up-success exists, and that totara_core-msgicons/competency-approve maps to it.
     */
    public function test_totara_core_msgicons_competency_approve() {
        $this->assert_icon_data(
            'thumbs-up-success',
            'msgicons/competency-approve',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-up ft-state-success"></span>',
            'totara_core-msgicons/competency-approve',
            'totara/core/pix/msgicons/competency-approve.png'
        );
    }

    /**
     * Tests that thumbs-up-success exists, and that totara_core-msgicons/course-approve maps to it.
     */
    public function test_totara_core_msgicons_course_approve() {
        $this->assert_icon_data(
            'thumbs-up-success',
            'msgicons/course-approve',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-up ft-state-success"></span>',
            'totara_core-msgicons/course-approve',
            'totara/core/pix/msgicons/course-approve.png'
        );
    }

    /**
     * Tests that thumbs-up-success exists, and that totara_core-msgicons/elearning-approve maps to it.
     */
    public function test_totara_core_msgicons_elearning_approve() {
        $this->assert_icon_data(
            'thumbs-up-success',
            'msgicons/elearning-approve',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-up ft-state-success"></span>',
            'totara_core-msgicons/elearning-approve',
            'totara/core/pix/msgicons/elearning-approve.png'
        );
    }

    /**
     * Tests that thumbs-up-success exists, and that totara_core-msgicons/evidence-approve maps to it.
     */
    public function test_totara_core_msgicons_evidence_approve() {
        $this->assert_icon_data(
            'thumbs-up-success',
            'msgicons/evidence-approve',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-up ft-state-success"></span>',
            'totara_core-msgicons/evidence-approve',
            'totara/core/pix/msgicons/evidence-approve.png'
        );
    }

    /**
     * Tests that thumbs-up-success exists, and that totara_core-msgicons/facetoface-approve maps to it.
     */
    public function test_totara_core_msgicons_facetoface_approve() {
        $this->assert_icon_data(
            'thumbs-up-success',
            'msgicons/facetoface-approve',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-up ft-state-success"></span>',
            'totara_core-msgicons/facetoface-approve',
            'totara/core/pix/msgicons/facetoface-approve.png'
        );
    }

    /**
     * Tests that thumbs-up-success exists, and that totara_core-msgicons/learningplan-approve maps to it.
     */
    public function test_totara_core_msgicons_learningplan_approve() {
        $this->assert_icon_data(
            'thumbs-up-success',
            'msgicons/learningplan-approve',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-up ft-state-success"></span>',
            'totara_core-msgicons/learningplan-approve',
            'totara/core/pix/msgicons/learningplan-approve.png'
        );
    }

    /**
     * Tests that thumbs-up-success exists, and that totara_core-msgicons/objective-approve maps to it.
     */
    public function test_totara_core_msgicons_objective_approve() {
        $this->assert_icon_data(
            'thumbs-up-success',
            'msgicons/objective-approve',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-up ft-state-success"></span>',
            'totara_core-msgicons/objective-approve',
            'totara/core/pix/msgicons/objective-approve.png'
        );
    }

    /**
     * Tests that thumbs-up-success exists, and that totara_core-msgicons/program-approve maps to it.
     */
    public function test_totara_core_msgicons_program_approve() {
        $this->assert_icon_data(
            'thumbs-up-success',
            'msgicons/program-approve',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-up ft-state-success"></span>',
            'totara_core-msgicons/program-approve',
            'totara/core/pix/msgicons/program-approve.png'
        );
    }

    /**
     * Tests that thumbs-up-success exists, and that totara_core-msgicons/resource-approve maps to it.
     */
    public function test_totara_core_msgicons_resource_approve() {
        $this->assert_icon_data(
            'thumbs-up-success',
            'msgicons/resource-approve',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-up ft-state-success"></span>',
            'totara_core-msgicons/resource-approve',
            'totara/core/pix/msgicons/resource-approve.png'
        );
    }

    /**
     * Tests that check-circle-success exists, and that totara_core-msgicons/blended-complete maps to it.
     */
    public function test_totara_core_msgicons_blended_complete() {
        $this->assert_icon_data(
            'check-circle-success',
            'msgicons/blended-complete',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-circle ft-state-success"></span>',
            'totara_core-msgicons/blended-complete',
            'totara/core/pix/msgicons/blended-complete.png'
        );
    }

    /**
     * Tests that check-circle-success exists, and that totara_core-msgicons/competency-complete maps to it.
     */
    public function test_totara_core_msgicons_competency_complete() {
        $this->assert_icon_data(
            'check-circle-success',
            'msgicons/competency-complete',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-circle ft-state-success"></span>',
            'totara_core-msgicons/competency-complete',
            'totara/core/pix/msgicons/competency-complete.png'
        );
    }

    /**
     * Tests that check-circle-success exists, and that totara_core-msgicons/course-complete maps to it.
     */
    public function test_totara_core_msgicons_course_complete() {
        $this->assert_icon_data(
            'check-circle-success',
            'msgicons/course-complete',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-circle ft-state-success"></span>',
            'totara_core-msgicons/course-complete',
            'totara/core/pix/msgicons/course-complete.png'
        );
    }

    /**
     * Tests that check-circle-success exists, and that totara_core-msgicons/elearning-complete maps to it.
     */
    public function test_totara_core_msgicons_elearning_complete() {
        $this->assert_icon_data(
            'check-circle-success',
            'msgicons/elearning-complete',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-circle ft-state-success"></span>',
            'totara_core-msgicons/elearning-complete',
            'totara/core/pix/msgicons/elearning-complete.png'
        );
    }

    /**
     * Tests that check-circle-success exists, and that totara_core-msgicons/evidence-complete maps to it.
     */
    public function test_totara_core_msgicons_evidence_complete() {
        $this->assert_icon_data(
            'check-circle-success',
            'msgicons/evidence-complete',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-circle ft-state-success"></span>',
            'totara_core-msgicons/evidence-complete',
            'totara/core/pix/msgicons/evidence-complete.png'
        );
    }

    /**
     * Tests that check-circle-success exists, and that totara_core-msgicons/facetoface-complete maps to it.
     */
    public function test_totara_core_msgicons_facetoface_complete() {
        $this->assert_icon_data(
            'check-circle-success',
            'msgicons/facetoface-complete',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-circle ft-state-success"></span>',
            'totara_core-msgicons/facetoface-complete',
            'totara/core/pix/msgicons/facetoface-complete.png'
        );
    }

    /**
     * Tests that check-circle-success exists, and that totara_core-msgicons/learningplan-complete maps to it.
     */
    public function test_totara_core_msgicons_learningplan_complete() {
        $this->assert_icon_data(
            'check-circle-success',
            'msgicons/learningplan-complete',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-circle ft-state-success"></span>',
            'totara_core-msgicons/learningplan-complete',
            'totara/core/pix/msgicons/learningplan-complete.png'
        );
    }

    /**
     * Tests that check-circle-success exists, and that totara_core-msgicons/objective-complete maps to it.
     */
    public function test_totara_core_msgicons_objective_complete() {
        $this->assert_icon_data(
            'check-circle-success',
            'msgicons/objective-complete',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-circle ft-state-success"></span>',
            'totara_core-msgicons/objective-complete',
            'totara/core/pix/msgicons/objective-complete.png'
        );
    }

    /**
     * Tests that check-circle-success exists, and that totara_core-msgicons/program-complete maps to it.
     */
    public function test_totara_core_msgicons_program_complete() {
        $this->assert_icon_data(
            'check-circle-success',
            'msgicons/program-complete',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-circle ft-state-success"></span>',
            'totara_core-msgicons/program-complete',
            'totara/core/pix/msgicons/program-complete.png'
        );
    }

    /**
     * Tests that check-circle-success exists, and that totara_core-msgicons/resource-complete maps to it.
     */
    public function test_totara_core_msgicons_resource_complete() {
        $this->assert_icon_data(
            'check-circle-success',
            'msgicons/resource-complete',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-check-circle ft-state-success"></span>',
            'totara_core-msgicons/resource-complete',
            'totara/core/pix/msgicons/resource-complete.png'
        );
    }

    /**
     * Tests that alarm exists, and that totara_core-msgicons/blended-deadline maps to it.
     */
    public function test_totara_core_msgicons_blended_deadline() {
        $this->assert_icon_data(
            'alarm',
            'msgicons/blended-deadline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-alarm"></span>',
            'totara_core-msgicons/blended-deadline',
            'totara/core/pix/msgicons/blended-deadline.png'
        );
    }

    /**
     * Tests that alarm exists, and that totara_core-msgicons/competency-deadline maps to it.
     */
    public function test_totara_core_msgicons_competency_deadline() {
        $this->assert_icon_data(
            'alarm',
            'msgicons/competency-deadline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-alarm"></span>',
            'totara_core-msgicons/competency-deadline',
            'totara/core/pix/msgicons/competency-deadline.png'
        );
    }

    /**
     * Tests that alarm exists, and that totara_core-msgicons/course-deadline maps to it.
     */
    public function test_totara_core_msgicons_course_deadline() {
        $this->assert_icon_data(
            'alarm',
            'msgicons/course-deadline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-alarm"></span>',
            'totara_core-msgicons/course-deadline',
            'totara/core/pix/msgicons/course-deadline.png'
        );
    }

    /**
     * Tests that alarm exists, and that totara_core-msgicons/elearning-deadline maps to it.
     */
    public function test_totara_core_msgicons_elearning_deadline() {
        $this->assert_icon_data(
            'alarm',
            'msgicons/elearning-deadline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-alarm"></span>',
            'totara_core-msgicons/elearning-deadline',
            'totara/core/pix/msgicons/elearning-deadline.png'
        );
    }

    /**
     * Tests that alarm exists, and that totara_core-msgicons/evidence-deadline maps to it.
     */
    public function test_totara_core_msgicons_evidence_deadline() {
        $this->assert_icon_data(
            'alarm',
            'msgicons/evidence-deadline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-alarm"></span>',
            'totara_core-msgicons/evidence-deadline',
            'totara/core/pix/msgicons/evidence-deadline.png'
        );
    }

    /**
     * Tests that alarm exists, and that totara_core-msgicons/facetoface-deadline maps to it.
     */
    public function test_totara_core_msgicons_facetoface_deadline() {
        $this->assert_icon_data(
            'alarm',
            'msgicons/facetoface-deadline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-alarm"></span>',
            'totara_core-msgicons/facetoface-deadline',
            'totara/core/pix/msgicons/facetoface-deadline.png'
        );
    }

    /**
     * Tests that alarm exists, and that totara_core-msgicons/learningplan-deadline maps to it.
     */
    public function test_totara_core_msgicons_learningplan_deadline() {
        $this->assert_icon_data(
            'alarm',
            'msgicons/learningplan-deadline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-alarm"></span>',
            'totara_core-msgicons/learningplan-deadline',
            'totara/core/pix/msgicons/learningplan-deadline.png'
        );
    }

    /**
     * Tests that alarm exists, and that totara_core-msgicons/objective-deadline maps to it.
     */
    public function test_totara_core_msgicons_objective_deadline() {
        $this->assert_icon_data(
            'alarm',
            'msgicons/objective-deadline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-alarm"></span>',
            'totara_core-msgicons/objective-deadline',
            'totara/core/pix/msgicons/objective-deadline.png'
        );
    }

    /**
     * Tests that alarm exists, and that totara_core-msgicons/program-deadline maps to it.
     */
    public function test_totara_core_msgicons_program_deadline() {
        $this->assert_icon_data(
            'alarm',
            'msgicons/program-deadline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-alarm"></span>',
            'totara_core-msgicons/program-deadline',
            'totara/core/pix/msgicons/program-deadline.png'
        );
    }

    /**
     * Tests that alarm exists, and that totara_core-msgicons/resource-deadline maps to it.
     */
    public function test_totara_core_msgicons_resource_deadline() {
        $this->assert_icon_data(
            'alarm',
            'msgicons/resource-deadline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-alarm"></span>',
            'totara_core-msgicons/resource-deadline',
            'totara/core/pix/msgicons/resource-deadline.png'
        );
    }

    /**
     * Tests that thumbs-down-danger exists, and that totara_core-msgicons/blended-decline maps to it.
     */
    public function test_totara_core_msgicons_blended_decline() {
        $this->assert_icon_data(
            'thumbs-down-danger',
            'msgicons/blended-decline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-down ft-state-danger"></span>',
            'totara_core-msgicons/blended-decline',
            'totara/core/pix/msgicons/blended-decline.png'
        );
    }

    /**
     * Tests that thumbs-down-danger exists, and that totara_core-msgicons/competency-decline maps to it.
     */
    public function test_totara_core_msgicons_competency_decline() {
        $this->assert_icon_data(
            'thumbs-down-danger',
            'msgicons/competency-decline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-down ft-state-danger"></span>',
            'totara_core-msgicons/competency-decline',
            'totara/core/pix/msgicons/competency-decline.png'
        );
    }

    /**
     * Tests that thumbs-down-danger exists, and that totara_core-msgicons/course-decline maps to it.
     */
    public function test_totara_core_msgicons_course_decline() {
        $this->assert_icon_data(
            'thumbs-down-danger',
            'msgicons/course-decline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-down ft-state-danger"></span>',
            'totara_core-msgicons/course-decline',
            'totara/core/pix/msgicons/course-decline.png'
        );
    }

    /**
     * Tests that thumbs-down-danger exists, and that totara_core-msgicons/elearning-decline maps to it.
     */
    public function test_totara_core_msgicons_elearning_decline() {
        $this->assert_icon_data(
            'thumbs-down-danger',
            'msgicons/elearning-decline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-down ft-state-danger"></span>',
            'totara_core-msgicons/elearning-decline',
            'totara/core/pix/msgicons/elearning-decline.png'
        );
    }

    /**
     * Tests that thumbs-down-danger exists, and that totara_core-msgicons/evidence-decline maps to it.
     */
    public function test_totara_core_msgicons_evidence_decline() {
        $this->assert_icon_data(
            'thumbs-down-danger',
            'msgicons/evidence-decline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-down ft-state-danger"></span>',
            'totara_core-msgicons/evidence-decline',
            'totara/core/pix/msgicons/evidence-decline.png'
        );
    }

    /**
     * Tests that thumbs-down-danger exists, and that totara_core-msgicons/facetoface-decline maps to it.
     */
    public function test_totara_core_msgicons_facetoface_decline() {
        $this->assert_icon_data(
            'thumbs-down-danger',
            'msgicons/facetoface-decline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-down ft-state-danger"></span>',
            'totara_core-msgicons/facetoface-decline',
            'totara/core/pix/msgicons/facetoface-decline.png'
        );
    }

    /**
     * Tests that thumbs-down-danger exists, and that totara_core-msgicons/learningplan-decline maps to it.
     */
    public function test_totara_core_msgicons_learningplan_decline() {
        $this->assert_icon_data(
            'thumbs-down-danger',
            'msgicons/learningplan-decline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-down ft-state-danger"></span>',
            'totara_core-msgicons/learningplan-decline',
            'totara/core/pix/msgicons/learningplan-decline.png'
        );
    }

    /**
     * Tests that thumbs-down-danger exists, and that totara_core-msgicons/objective-decline maps to it.
     */
    public function test_totara_core_msgicons_objective_decline() {
        $this->assert_icon_data(
            'thumbs-down-danger',
            'msgicons/objective-decline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-down ft-state-danger"></span>',
            'totara_core-msgicons/objective-decline',
            'totara/core/pix/msgicons/objective-decline.png'
        );
    }

    /**
     * Tests that thumbs-down-danger exists, and that totara_core-msgicons/program-decline maps to it.
     */
    public function test_totara_core_msgicons_program_decline() {
        $this->assert_icon_data(
            'thumbs-down-danger',
            'msgicons/program-decline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-down ft-state-danger"></span>',
            'totara_core-msgicons/program-decline',
            'totara/core/pix/msgicons/program-decline.png'
        );
    }

    /**
     * Tests that thumbs-down-danger exists, and that totara_core-msgicons/resource-decline maps to it.
     */
    public function test_totara_core_msgicons_resource_decline() {
        $this->assert_icon_data(
            'thumbs-down-danger',
            'msgicons/resource-decline',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-thumbs-down ft-state-danger"></span>',
            'totara_core-msgicons/resource-decline',
            'totara/core/pix/msgicons/resource-decline.png'
        );
    }

    /**
     * Tests that alarm-warning exists, and that totara_core-msgicons/blended-due maps to it.
     */
    public function test_totara_core_msgicons_blended_due() {
        $this->assert_icon_data(
            'alarm-warning',
            'msgicons/blended-due',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-warning"></span></span>',
            'totara_core-msgicons/blended-due',
            'totara/core/pix/msgicons/blended-due.png'
        );
    }

    /**
     * Tests that alarm-warning exists, and that totara_core-msgicons/competency-due maps to it.
     */
    public function test_totara_core_msgicons_competency_due() {
        $this->assert_icon_data(
            'alarm-warning',
            'msgicons/competency-due',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-warning"></span></span>',
            'totara_core-msgicons/competency-due',
            'totara/core/pix/msgicons/competency-due.png'
        );
    }

    /**
     * Tests that alarm-warning exists, and that totara_core-msgicons/course-due maps to it.
     */
    public function test_totara_core_msgicons_course_due() {
        $this->assert_icon_data(
            'alarm-warning',
            'msgicons/course-due',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-warning"></span></span>',
            'totara_core-msgicons/course-due',
            'totara/core/pix/msgicons/course-due.png'
        );
    }

    /**
     * Tests that alarm-warning exists, and that totara_core-msgicons/elearning-due maps to it.
     */
    public function test_totara_core_msgicons_elearning_due() {
        $this->assert_icon_data(
            'alarm-warning',
            'msgicons/elearning-due',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-warning"></span></span>',
            'totara_core-msgicons/elearning-due',
            'totara/core/pix/msgicons/elearning-due.png'
        );
    }

    /**
     * Tests that alarm-warning exists, and that totara_core-msgicons/evidence-due maps to it.
     */
    public function test_totara_core_msgicons_evidence_due() {
        $this->assert_icon_data(
            'alarm-warning',
            'msgicons/evidence-due',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-warning"></span></span>',
            'totara_core-msgicons/evidence-due',
            'totara/core/pix/msgicons/evidence-due.png'
        );
    }

    /**
     * Tests that alarm-warning exists, and that totara_core-msgicons/facetoface-due maps to it.
     */
    public function test_totara_core_msgicons_facetoface_due() {
        $this->assert_icon_data(
            'alarm-warning',
            'msgicons/facetoface-due',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-warning"></span></span>',
            'totara_core-msgicons/facetoface-due',
            'totara/core/pix/msgicons/facetoface-due.png'
        );
    }

    /**
     * Tests that alarm-warning exists, and that totara_core-msgicons/feedback360-remind maps to it.
     */
    public function test_totara_core_msgicons_feedback360_remind() {
        $this->assert_icon_data(
            'alarm-warning',
            'msgicons/feedback360-remind',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-warning"></span></span>',
            'totara_core-msgicons/feedback360-remind',
            'totara/core/pix/msgicons/feedback360-remind.png'
        );
    }

    /**
     * Tests that alarm-warning exists, and that totara_core-msgicons/learningplan-due maps to it.
     */
    public function test_totara_core_msgicons_learningplan_due() {
        $this->assert_icon_data(
            'alarm-warning',
            'msgicons/learningplan-due',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-warning"></span></span>',
            'totara_core-msgicons/learningplan-due',
            'totara/core/pix/msgicons/learningplan-due.png'
        );
    }

    /**
     * Tests that alarm-warning exists, and that totara_core-msgicons/objective-due maps to it.
     */
    public function test_totara_core_msgicons_objective_due() {
        $this->assert_icon_data(
            'alarm-warning',
            'msgicons/objective-due',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-warning"></span></span>',
            'totara_core-msgicons/objective-due',
            'totara/core/pix/msgicons/objective-due.png'
        );
    }

    /**
     * Tests that alarm-warning exists, and that totara_core-msgicons/program-due maps to it.
     */
    public function test_totara_core_msgicons_program_due() {
        $this->assert_icon_data(
            'alarm-warning',
            'msgicons/program-due',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-warning"></span></span>',
            'totara_core-msgicons/program-due',
            'totara/core/pix/msgicons/program-due.png'
        );
    }

    /**
     * Tests that alarm-warning exists, and that totara_core-msgicons/resource-due maps to it.
     */
    public function test_totara_core_msgicons_resource_due() {
        $this->assert_icon_data(
            'alarm-warning',
            'msgicons/resource-due',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-warning"></span></span>',
            'totara_core-msgicons/resource-due',
            'totara/core/pix/msgicons/resource-due.png'
        );
    }

    /**
     * Tests that times-circle-danger exists, and that totara_core-msgicons/blended-fail maps to it.
     */
    public function test_totara_core_msgicons_blended_fail() {
        $this->assert_icon_data(
            'times-circle-danger',
            'msgicons/blended-fail',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times-circle ft-state-danger"></span>',
            'totara_core-msgicons/blended-fail',
            'totara/core/pix/msgicons/blended-fail.png'
        );
    }

    /**
     * Tests that times-circle-danger exists, and that totara_core-msgicons/competency-fail maps to it.
     */
    public function test_totara_core_msgicons_competency_fail() {
        $this->assert_icon_data(
            'times-circle-danger',
            'msgicons/competency-fail',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times-circle ft-state-danger"></span>',
            'totara_core-msgicons/competency-fail',
            'totara/core/pix/msgicons/competency-fail.png'
        );
    }

    /**
     * Tests that times-circle-danger exists, and that totara_core-msgicons/course-fail maps to it.
     */
    public function test_totara_core_msgicons_course_fail() {
        $this->assert_icon_data(
            'times-circle-danger',
            'msgicons/course-fail',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times-circle ft-state-danger"></span>',
            'totara_core-msgicons/course-fail',
            'totara/core/pix/msgicons/course-fail.png'
        );
    }

    /**
     * Tests that times-circle-danger exists, and that totara_core-msgicons/elearning-fail maps to it.
     */
    public function test_totara_core_msgicons_elearning_fail() {
        $this->assert_icon_data(
            'times-circle-danger',
            'msgicons/elearning-fail',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times-circle ft-state-danger"></span>',
            'totara_core-msgicons/elearning-fail',
            'totara/core/pix/msgicons/elearning-fail.png'
        );
    }

    /**
     * Tests that times-circle-danger exists, and that totara_core-msgicons/evidence-fail maps to it.
     */
    public function test_totara_core_msgicons_evidence_fail() {
        $this->assert_icon_data(
            'times-circle-danger',
            'msgicons/evidence-fail',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times-circle ft-state-danger"></span>',
            'totara_core-msgicons/evidence-fail',
            'totara/core/pix/msgicons/evidence-fail.png'
        );
    }

    /**
     * Tests that times-circle-danger exists, and that totara_core-msgicons/facetoface-fail maps to it.
     */
    public function test_totara_core_msgicons_facetoface_fail() {
        $this->assert_icon_data(
            'times-circle-danger',
            'msgicons/facetoface-fail',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times-circle ft-state-danger"></span>',
            'totara_core-msgicons/facetoface-fail',
            'totara/core/pix/msgicons/facetoface-fail.png'
        );
    }

    /**
     * Tests that times-circle-danger exists, and that totara_core-msgicons/learningplan-fail maps to it.
     */
    public function test_totara_core_msgicons_learningplan_fail() {
        $this->assert_icon_data(
            'times-circle-danger',
            'msgicons/learningplan-fail',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times-circle ft-state-danger"></span>',
            'totara_core-msgicons/learningplan-fail',
            'totara/core/pix/msgicons/learningplan-fail.png'
        );
    }

    /**
     * Tests that times-circle-danger exists, and that totara_core-msgicons/objective-fail maps to it.
     */
    public function test_totara_core_msgicons_objective_fail() {
        $this->assert_icon_data(
            'times-circle-danger',
            'msgicons/objective-fail',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times-circle ft-state-danger"></span>',
            'totara_core-msgicons/objective-fail',
            'totara/core/pix/msgicons/objective-fail.png'
        );
    }

    /**
     * Tests that times-circle-danger exists, and that totara_core-msgicons/program-fail maps to it.
     */
    public function test_totara_core_msgicons_program_fail() {
        $this->assert_icon_data(
            'times-circle-danger',
            'msgicons/program-fail',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times-circle ft-state-danger"></span>',
            'totara_core-msgicons/program-fail',
            'totara/core/pix/msgicons/program-fail.png'
        );
    }

    /**
     * Tests that times-circle-danger exists, and that totara_core-msgicons/resource-fail maps to it.
     */
    public function test_totara_core_msgicons_resource_fail() {
        $this->assert_icon_data(
            'times-circle-danger',
            'msgicons/resource-fail',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times-circle ft-state-danger"></span>',
            'totara_core-msgicons/resource-fail',
            'totara/core/pix/msgicons/resource-fail.png'
        );
    }

    /**
     * Tests that commenting-info exists, and that totara_core-msgicons/blended-newcomment maps to it.
     */
    public function test_totara_core_msgicons_blended_newcomment() {
        $this->assert_icon_data(
            'commenting-info',
            'msgicons/blended-newcomment',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-commenting ft-state-info"></span>',
            'totara_core-msgicons/blended-newcomment',
            'totara/core/pix/msgicons/blended-newcomment.png'
        );
    }

    /**
     * Tests that commenting-info exists, and that totara_core-msgicons/competency-newcomment maps to it.
     */
    public function test_totara_core_msgicons_competency_newcomment() {
        $this->assert_icon_data(
            'commenting-info',
            'msgicons/competency-newcomment',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-commenting ft-state-info"></span>',
            'totara_core-msgicons/competency-newcomment',
            'totara/core/pix/msgicons/competency-newcomment.png'
        );
    }

    /**
     * Tests that commenting-info exists, and that totara_core-msgicons/course-newcomment maps to it.
     */
    public function test_totara_core_msgicons_course_newcomment() {
        $this->assert_icon_data(
            'commenting-info',
            'msgicons/course-newcomment',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-commenting ft-state-info"></span>',
            'totara_core-msgicons/course-newcomment',
            'totara/core/pix/msgicons/course-newcomment.png'
        );
    }

    /**
     * Tests that commenting-info exists, and that totara_core-msgicons/elearning-newcomment maps to it.
     */
    public function test_totara_core_msgicons_elearning_newcomment() {
        $this->assert_icon_data(
            'commenting-info',
            'msgicons/elearning-newcomment',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-commenting ft-state-info"></span>',
            'totara_core-msgicons/elearning-newcomment',
            'totara/core/pix/msgicons/elearning-newcomment.png'
        );
    }

    /**
     * Tests that commenting-info exists, and that totara_core-msgicons/evidence-newcomment maps to it.
     */
    public function test_totara_core_msgicons_evidence_newcomment() {
        $this->assert_icon_data(
            'commenting-info',
            'msgicons/evidence-newcomment',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-commenting ft-state-info"></span>',
            'totara_core-msgicons/evidence-newcomment',
            'totara/core/pix/msgicons/evidence-newcomment.png'
        );
    }

    /**
     * Tests that commenting-info exists, and that totara_core-msgicons/facetoface-newcomment maps to it.
     */
    public function test_totara_core_msgicons_facetoface_newcomment() {
        $this->assert_icon_data(
            'commenting-info',
            'msgicons/facetoface-newcomment',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-commenting ft-state-info"></span>',
            'totara_core-msgicons/facetoface-newcomment',
            'totara/core/pix/msgicons/facetoface-newcomment.png'
        );
    }

    /**
     * Tests that commenting-info exists, and that totara_core-msgicons/learningplan-newcomment maps to it.
     */
    public function test_totara_core_msgicons_learningplan_newcomment() {
        $this->assert_icon_data(
            'commenting-info',
            'msgicons/learningplan-newcomment',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-commenting ft-state-info"></span>',
            'totara_core-msgicons/learningplan-newcomment',
            'totara/core/pix/msgicons/learningplan-newcomment.png'
        );
    }

    /**
     * Tests that commenting-info exists, and that totara_core-msgicons/objective-newcomment maps to it.
     */
    public function test_totara_core_msgicons_objective_newcomment() {
        $this->assert_icon_data(
            'commenting-info',
            'msgicons/objective-newcomment',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-commenting ft-state-info"></span>',
            'totara_core-msgicons/objective-newcomment',
            'totara/core/pix/msgicons/objective-newcomment.png'
        );
    }

    /**
     * Tests that commenting-info exists, and that totara_core-msgicons/program-newcomment maps to it.
     */
    public function test_totara_core_msgicons_program_newcomment() {
        $this->assert_icon_data(
            'commenting-info',
            'msgicons/program-newcomment',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-commenting ft-state-info"></span>',
            'totara_core-msgicons/program-newcomment',
            'totara/core/pix/msgicons/program-newcomment.png'
        );
    }

    /**
     * Tests that commenting-info exists, and that totara_core-msgicons/resource-newcomment maps to it.
     */
    public function test_totara_core_msgicons_resource_newcomment() {
        $this->assert_icon_data(
            'commenting-info',
            'msgicons/resource-newcomment',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-commenting ft-state-info"></span>',
            'totara_core-msgicons/resource-newcomment',
            'totara/core/pix/msgicons/resource-newcomment.png'
        );
    }

    /**
     * Tests that alarm-danger exists, and that totara_core-msgicons/blended-overdue maps to it.
     */
    public function test_totara_core_msgicons_blended_overdue() {
        $this->assert_icon_data(
            'alarm-danger',
            'msgicons/blended-overdue',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-bolt ft-stack-suffix ft-state-danger"></span></span>',
            'totara_core-msgicons/blended-overdue',
            'totara/core/pix/msgicons/blended-overdue.png'
        );
    }

    /**
     * Tests that alarm-danger exists, and that totara_core-msgicons/competency-overdue maps to it.
     */
    public function test_totara_core_msgicons_competency_overdue() {
        $this->assert_icon_data(
            'alarm-danger',
            'msgicons/competency-overdue',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-bolt ft-stack-suffix ft-state-danger"></span></span>',
            'totara_core-msgicons/competency-overdue',
            'totara/core/pix/msgicons/competency-overdue.png'
        );
    }

    /**
     * Tests that alarm-danger exists, and that totara_core-msgicons/course-overdue maps to it.
     */
    public function test_totara_core_msgicons_course_overdue() {
        $this->assert_icon_data(
            'alarm-danger',
            'msgicons/course-overdue',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-bolt ft-stack-suffix ft-state-danger"></span></span>',
            'totara_core-msgicons/course-overdue',
            'totara/core/pix/msgicons/course-overdue.png'
        );
    }

    /**
     * Tests that alarm-danger exists, and that totara_core-msgicons/elearning-overdue maps to it.
     */
    public function test_totara_core_msgicons_elearning_overdue() {
        $this->assert_icon_data(
            'alarm-danger',
            'msgicons/elearning-overdue',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-bolt ft-stack-suffix ft-state-danger"></span></span>',
            'totara_core-msgicons/elearning-overdue',
            'totara/core/pix/msgicons/elearning-overdue.png'
        );
    }

    /**
     * Tests that alarm-danger exists, and that totara_core-msgicons/evidence-overdue maps to it.
     */
    public function test_totara_core_msgicons_evidence_overdue() {
        $this->assert_icon_data(
            'alarm-danger',
            'msgicons/evidence-overdue',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-bolt ft-stack-suffix ft-state-danger"></span></span>',
            'totara_core-msgicons/evidence-overdue',
            'totara/core/pix/msgicons/evidence-overdue.png'
        );
    }

    /**
     * Tests that alarm-danger exists, and that totara_core-msgicons/facetoface-overdue maps to it.
     */
    public function test_totara_core_msgicons_facetoface_overdue() {
        $this->assert_icon_data(
            'alarm-danger',
            'msgicons/facetoface-overdue',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-bolt ft-stack-suffix ft-state-danger"></span></span>',
            'totara_core-msgicons/facetoface-overdue',
            'totara/core/pix/msgicons/facetoface-overdue.png'
        );
    }

    /**
     * Tests that alarm-danger exists, and that totara_core-msgicons/learningplan-overdue maps to it.
     */
    public function test_totara_core_msgicons_learningplan_overdue() {
        $this->assert_icon_data(
            'alarm-danger',
            'msgicons/learningplan-overdue',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-bolt ft-stack-suffix ft-state-danger"></span></span>',
            'totara_core-msgicons/learningplan-overdue',
            'totara/core/pix/msgicons/learningplan-overdue.png'
        );
    }

    /**
     * Tests that alarm-danger exists, and that totara_core-msgicons/objective-overdue maps to it.
     */
    public function test_totara_core_msgicons_objective_overdue() {
        $this->assert_icon_data(
            'alarm-danger',
            'msgicons/objective-overdue',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-bolt ft-stack-suffix ft-state-danger"></span></span>',
            'totara_core-msgicons/objective-overdue',
            'totara/core/pix/msgicons/objective-overdue.png'
        );
    }

    /**
     * Tests that alarm-danger exists, and that totara_core-msgicons/program-overdue maps to it.
     */
    public function test_totara_core_msgicons_program_overdue() {
        $this->assert_icon_data(
            'alarm-danger',
            'msgicons/program-overdue',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-bolt ft-stack-suffix ft-state-danger"></span></span>',
            'totara_core-msgicons/program-overdue',
            'totara/core/pix/msgicons/program-overdue.png'
        );
    }

    /**
     * Tests that alarm-danger exists, and that totara_core-msgicons/resource-overdue maps to it.
     */
    public function test_totara_core_msgicons_resource_overdue() {
        $this->assert_icon_data(
            'alarm-danger',
            'msgicons/resource-overdue',
            'totara_core',
            '<span class="flex-icon ft-stack"><span class="ft ft-alarm ft-stack-main"></span><span class="fa fa-bolt ft-stack-suffix ft-state-danger"></span></span>',
            'totara_core-msgicons/resource-overdue',
            'totara/core/pix/msgicons/resource-overdue.png'
        );
    }

    /**
     * Tests that times-danger exists, and that totara_core-msgicons/blended-remove maps to it.
     */
    public function test_totara_core_msgicons_blended_remove() {
        $this->assert_icon_data(
            'times-danger',
            'msgicons/blended-remove',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'totara_core-msgicons/blended-remove',
            'totara/core/pix/msgicons/blended-remove.png'
        );
    }

    /**
     * Tests that times-danger exists, and that totara_core-msgicons/competency-remove maps to it.
     */
    public function test_totara_core_msgicons_competency_remove() {
        $this->assert_icon_data(
            'times-danger',
            'msgicons/competency-remove',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'totara_core-msgicons/competency-remove',
            'totara/core/pix/msgicons/competency-remove.png'
        );
    }

    /**
     * Tests that times-danger exists, and that totara_core-msgicons/course-remove maps to it.
     */
    public function test_totara_core_msgicons_course_remove() {
        $this->assert_icon_data(
            'times-danger',
            'msgicons/course-remove',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'totara_core-msgicons/course-remove',
            'totara/core/pix/msgicons/course-remove.png'
        );
    }

    /**
     * Tests that times-danger exists, and that totara_core-msgicons/elearning-remove maps to it.
     */
    public function test_totara_core_msgicons_elearning_remove() {
        $this->assert_icon_data(
            'times-danger',
            'msgicons/elearning-remove',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'totara_core-msgicons/elearning-remove',
            'totara/core/pix/msgicons/elearning-remove.png'
        );
    }

    /**
     * Tests that times-danger exists, and that totara_core-msgicons/evidence-remove maps to it.
     */
    public function test_totara_core_msgicons_evidence_remove() {
        $this->assert_icon_data(
            'times-danger',
            'msgicons/evidence-remove',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'totara_core-msgicons/evidence-remove',
            'totara/core/pix/msgicons/evidence-remove.png'
        );
    }

    /**
     * Tests that times-danger exists, and that totara_core-msgicons/facetoface-remove maps to it.
     */
    public function test_totara_core_msgicons_facetoface_remove() {
        $this->assert_icon_data(
            'times-danger',
            'msgicons/facetoface-remove',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'totara_core-msgicons/facetoface-remove',
            'totara/core/pix/msgicons/facetoface-remove.png'
        );
    }

    /**
     * Tests that times-danger exists, and that totara_core-msgicons/feedback360-cancel maps to it.
     */
    public function test_totara_core_msgicons_feedback360_cancel() {
        $this->assert_icon_data(
            'times-danger',
            'msgicons/feedback360-cancel',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'totara_core-msgicons/feedback360-cancel',
            'totara/core/pix/msgicons/feedback360-cancel.png'
        );
    }

    /**
     * Tests that times-danger exists, and that totara_core-msgicons/learningplan-remove maps to it.
     */
    public function test_totara_core_msgicons_learningplan_remove() {
        $this->assert_icon_data(
            'times-danger',
            'msgicons/learningplan-remove',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'totara_core-msgicons/learningplan-remove',
            'totara/core/pix/msgicons/learningplan-remove.png'
        );
    }

    /**
     * Tests that times-danger exists, and that totara_core-msgicons/objective-remove maps to it.
     */
    public function test_totara_core_msgicons_objective_remove() {
        $this->assert_icon_data(
            'times-danger',
            'msgicons/objective-remove',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'totara_core-msgicons/objective-remove',
            'totara/core/pix/msgicons/objective-remove.png'
        );
    }

    /**
     * Tests that times-danger exists, and that totara_core-msgicons/program-remove maps to it.
     */
    public function test_totara_core_msgicons_program_remove() {
        $this->assert_icon_data(
            'times-danger',
            'msgicons/program-remove',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'totara_core-msgicons/program-remove',
            'totara/core/pix/msgicons/program-remove.png'
        );
    }

    /**
     * Tests that times-danger exists, and that totara_core-msgicons/resource-remove maps to it.
     */
    public function test_totara_core_msgicons_resource_remove() {
        $this->assert_icon_data(
            'times-danger',
            'msgicons/resource-remove',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-times ft-state-danger"></span>',
            'totara_core-msgicons/resource-remove',
            'totara/core/pix/msgicons/resource-remove.png'
        );
    }

    /**
     * Tests that question-circle-warning exists, and that totara_core-msgicons/blended-request maps to it.
     */
    public function test_totara_core_msgicons_blended_request() {
        $this->assert_icon_data(
            'question-circle-warning',
            'msgicons/blended-request',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle ft-state-warning"></span>',
            'totara_core-msgicons/blended-request',
            'totara/core/pix/msgicons/blended-request.png'
        );
    }

    /**
     * Tests that question-circle-warning exists, and that totara_core-msgicons/competency-request maps to it.
     */
    public function test_totara_core_msgicons_competency_request() {
        $this->assert_icon_data(
            'question-circle-warning',
            'msgicons/competency-request',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle ft-state-warning"></span>',
            'totara_core-msgicons/competency-request',
            'totara/core/pix/msgicons/competency-request.png'
        );
    }

    /**
     * Tests that question-circle-warning exists, and that totara_core-msgicons/course-request maps to it.
     */
    public function test_totara_core_msgicons_course_request() {
        $this->assert_icon_data(
            'question-circle-warning',
            'msgicons/course-request',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle ft-state-warning"></span>',
            'totara_core-msgicons/course-request',
            'totara/core/pix/msgicons/course-request.png'
        );
    }

    /**
     * Tests that question-circle-warning exists, and that totara_core-msgicons/elearning-request maps to it.
     */
    public function test_totara_core_msgicons_elearning_request() {
        $this->assert_icon_data(
            'question-circle-warning',
            'msgicons/elearning-request',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle ft-state-warning"></span>',
            'totara_core-msgicons/elearning-request',
            'totara/core/pix/msgicons/elearning-request.png'
        );
    }

    /**
     * Tests that question-circle-warning exists, and that totara_core-msgicons/evidence-request maps to it.
     */
    public function test_totara_core_msgicons_evidence_request() {
        $this->assert_icon_data(
            'question-circle-warning',
            'msgicons/evidence-request',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle ft-state-warning"></span>',
            'totara_core-msgicons/evidence-request',
            'totara/core/pix/msgicons/evidence-request.png'
        );
    }

    /**
     * Tests that question-circle-warning exists, and that totara_core-msgicons/facetoface-request maps to it.
     */
    public function test_totara_core_msgicons_facetoface_request() {
        $this->assert_icon_data(
            'question-circle-warning',
            'msgicons/facetoface-request',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle ft-state-warning"></span>',
            'totara_core-msgicons/facetoface-request',
            'totara/core/pix/msgicons/facetoface-request.png'
        );
    }

    /**
     * Tests that question-circle-warning exists, and that totara_core-msgicons/feedback360-request maps to it.
     */
    public function test_totara_core_msgicons_feedback360_request() {
        $this->assert_icon_data(
            'question-circle-warning',
            'msgicons/feedback360-request',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle ft-state-warning"></span>',
            'totara_core-msgicons/feedback360-request',
            'totara/core/pix/msgicons/feedback360-request.png'
        );
    }

    /**
     * Tests that question-circle-warning exists, and that totara_core-msgicons/learningplan-request maps to it.
     */
    public function test_totara_core_msgicons_learningplan_request() {
        $this->assert_icon_data(
            'question-circle-warning',
            'msgicons/learningplan-request',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle ft-state-warning"></span>',
            'totara_core-msgicons/learningplan-request',
            'totara/core/pix/msgicons/learningplan-request.png'
        );
    }

    /**
     * Tests that question-circle-warning exists, and that totara_core-msgicons/objective-request maps to it.
     */
    public function test_totara_core_msgicons_objective_request() {
        $this->assert_icon_data(
            'question-circle-warning',
            'msgicons/objective-request',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle ft-state-warning"></span>',
            'totara_core-msgicons/objective-request',
            'totara/core/pix/msgicons/objective-request.png'
        );
    }

    /**
     * Tests that question-circle-warning exists, and that totara_core-msgicons/program-request maps to it.
     */
    public function test_totara_core_msgicons_program_request() {
        $this->assert_icon_data(
            'question-circle-warning',
            'msgicons/program-request',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle ft-state-warning"></span>',
            'totara_core-msgicons/program-request',
            'totara/core/pix/msgicons/program-request.png'
        );
    }

    /**
     * Tests that question-circle-warning exists, and that totara_core-msgicons/resource-request maps to it.
     */
    public function test_totara_core_msgicons_resource_request() {
        $this->assert_icon_data(
            'question-circle-warning',
            'msgicons/resource-request',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-question-circle ft-state-warning"></span>',
            'totara_core-msgicons/resource-request',
            'totara/core/pix/msgicons/resource-request.png'
        );
    }

    /**
     * Tests that pencil-square-info exists, and that totara_core-msgicons/blended-update maps to it.
     */
    public function test_totara_core_msgicons_blended_update() {
        $this->assert_icon_data(
            'pencil-square-info',
            'msgicons/blended-update',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-pencil-square ft-state-info"></span>',
            'totara_core-msgicons/blended-update',
            'totara/core/pix/msgicons/blended-update.png'
        );
    }

    /**
     * Tests that pencil-square-info exists, and that totara_core-msgicons/competency-update maps to it.
     */
    public function test_totara_core_msgicons_competency_update() {
        $this->assert_icon_data(
            'pencil-square-info',
            'msgicons/competency-update',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-pencil-square ft-state-info"></span>',
            'totara_core-msgicons/competency-update',
            'totara/core/pix/msgicons/competency-update.png'
        );
    }

    /**
     * Tests that pencil-square-info exists, and that totara_core-msgicons/course-update maps to it.
     */
    public function test_totara_core_msgicons_course_update() {
        $this->assert_icon_data(
            'pencil-square-info',
            'msgicons/course-update',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-pencil-square ft-state-info"></span>',
            'totara_core-msgicons/course-update',
            'totara/core/pix/msgicons/course-update.png'
        );
    }

    /**
     * Tests that pencil-square-info exists, and that totara_core-msgicons/elearning-update maps to it.
     */
    public function test_totara_core_msgicons_elearning_update() {
        $this->assert_icon_data(
            'pencil-square-info',
            'msgicons/elearning-update',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-pencil-square ft-state-info"></span>',
            'totara_core-msgicons/elearning-update',
            'totara/core/pix/msgicons/elearning-update.png'
        );
    }

    /**
     * Tests that pencil-square-info exists, and that totara_core-msgicons/evidence-update maps to it.
     */
    public function test_totara_core_msgicons_evidence_update() {
        $this->assert_icon_data(
            'pencil-square-info',
            'msgicons/evidence-update',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-pencil-square ft-state-info"></span>',
            'totara_core-msgicons/evidence-update',
            'totara/core/pix/msgicons/evidence-update.png'
        );
    }

    /**
     * Tests that pencil-square-info exists, and that totara_core-msgicons/facetoface-update maps to it.
     */
    public function test_totara_core_msgicons_facetoface_update() {
        $this->assert_icon_data(
            'pencil-square-info',
            'msgicons/facetoface-update',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-pencil-square ft-state-info"></span>',
            'totara_core-msgicons/facetoface-update',
            'totara/core/pix/msgicons/facetoface-update.png'
        );
    }

    /**
     * Tests that pencil-square-info exists, and that totara_core-msgicons/feedback360-update maps to it.
     */
    public function test_totara_core_msgicons_feedback360_update() {
        $this->assert_icon_data(
            'pencil-square-info',
            'msgicons/feedback360-update',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-pencil-square ft-state-info"></span>',
            'totara_core-msgicons/feedback360-update',
            'totara/core/pix/msgicons/feedback360-update.png'
        );
    }

    /**
     * Tests that pencil-square-info exists, and that totara_core-msgicons/learningplan-update maps to it.
     */
    public function test_totara_core_msgicons_learningplan_update() {
        $this->assert_icon_data(
            'pencil-square-info',
            'msgicons/learningplan-update',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-pencil-square ft-state-info"></span>',
            'totara_core-msgicons/learningplan-update',
            'totara/core/pix/msgicons/learningplan-update.png'
        );
    }

    /**
     * Tests that pencil-square-info exists, and that totara_core-msgicons/objective-update maps to it.
     */
    public function test_totara_core_msgicons_objective_update() {
        $this->assert_icon_data(
            'pencil-square-info',
            'msgicons/objective-update',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-pencil-square ft-state-info"></span>',
            'totara_core-msgicons/objective-update',
            'totara/core/pix/msgicons/objective-update.png'
        );
    }

    /**
     * Tests that pencil-square-info exists, and that totara_core-msgicons/resource-update maps to it.
     */
    public function test_totara_core_msgicons_resource_update() {
        $this->assert_icon_data(
            'pencil-square-info',
            'msgicons/resource-update',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-pencil-square ft-state-info"></span>',
            'totara_core-msgicons/resource-update',
            'totara/core/pix/msgicons/resource-update.png'
        );
    }

    /**
     * Tests that blended exists, and that totara_core-msgicons/blended-regular maps to it.
     */
    public function test_totara_core_msgicons_blended_regular() {
        $this->assert_icon_data(
            'blended',
            'msgicons/blended-regular',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-blended"></span>',
            'totara_core-msgicons/blended-regular',
            'totara/core/pix/msgicons/blended-regular.png'
        );
    }

    /**
     * Tests that competency exists, and that totara_core-msgicons/competency-regular maps to it.
     */
    public function test_totara_core_msgicons_competency_regular() {
        $this->assert_icon_data(
            'competency',
            'msgicons/competency-regular',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-competency"></span>',
            'totara_core-msgicons/competency-regular',
            'totara/core/pix/msgicons/competency-regular.png'
        );
    }

    /**
     * Tests that cube exists, and that totara_core-msgicons/course-regular maps to it.
     */
    public function test_totara_core_msgicons_course_regular() {
        $this->assert_icon_data(
            'cube',
            'msgicons/course-regular',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-cube"></span>',
            'totara_core-msgicons/course-regular',
            'totara/core/pix/msgicons/course-regular.png'
        );
    }

    /**
     * Tests that cube exists, and that core-i/course maps to it.
     */
    public function test_core_i_course() {
        $this->assert_icon_data(
            'cube',
            'i/course',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-cube"></span>',
            'core-i/course',
            'pix/i/course.png'
        );
    }

    /**
     * Tests that course-event exists, and that core-i/courseevent maps to it.
     */
    public function test_core_i_courseevent() {
        $this->assert_icon_data(
            'course-event',
            'i/courseevent',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-cube ft-stack-main"></span><span class="fa fa-clock-o ft-stack-suffix"></span></span>',
            'core-i/courseevent',
            'pix/i/courseevent.png'
        );
    }

    /**
     * Tests that laptop exists, and that totara_core-msgicons/default maps to it.
     */
    public function test_totara_core_msgicons_default() {
        $this->assert_icon_data(
            'laptop',
            'msgicons/default',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-laptop"></span>',
            'totara_core-msgicons/default',
            'totara/core/pix/msgicons/default.png'
        );
    }

    /**
     * Tests that laptop exists, and that totara_core-msgicons/elearning-regular maps to it.
     */
    public function test_totara_core_msgicons_elearning_regular() {
        $this->assert_icon_data(
            'laptop',
            'msgicons/elearning-regular',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-laptop"></span>',
            'totara_core-msgicons/elearning-regular',
            'totara/core/pix/msgicons/elearning-regular.png'
        );
    }

    /**
     * Tests that paperclip exists, and that totara_core-msgicons/evidence-regular maps to it.
     */
    public function test_totara_core_msgicons_evidence_regular() {
        $this->assert_icon_data(
            'paperclip',
            'msgicons/evidence-regular',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-paperclip"></span>',
            'totara_core-msgicons/evidence-regular',
            'totara/core/pix/msgicons/evidence-regular.png'
        );
    }

    /**
     * Tests that seminar exists, and that totara_core-msgicons/facetoface-regular maps to it.
     */
    public function test_totara_core_msgicons_facetoface_regular() {
        $this->assert_icon_data(
            'seminar',
            'msgicons/facetoface-regular',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-seminar"></span>',
            'totara_core-msgicons/facetoface-regular',
            'totara/core/pix/msgicons/facetoface-regular.png'
        );
    }

    /**
     * Tests that briefcase exists, and that totara_core-msgicons/learningplan-regular maps to it.
     */
    public function test_totara_core_msgicons_learningplan_regular() {
        $this->assert_icon_data(
            'briefcase',
            'msgicons/learningplan-regular',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-briefcase"></span>',
            'totara_core-msgicons/learningplan-regular',
            'totara/core/pix/msgicons/learningplan-regular.png'
        );
    }

    /**
     * Tests that briefcase exists, and that totara_core-plan maps to it.
     */
    public function test_totara_core_plan() {
        $this->assert_icon_data(
            'briefcase',
            'plan',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-briefcase"></span>',
            'totara_core-plan',
            'totara/core/pix/plan.gif'
        );
    }

    /**
     * Tests that objective exists, and that totara_core-msgicons/objective-regular maps to it.
     */
    public function test_totara_core_msgicons_objective_regular() {
        $this->assert_icon_data(
            'objective',
            'msgicons/objective-regular',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-bullseye"></span>',
            'totara_core-msgicons/objective-regular',
            'totara/core/pix/msgicons/objective-regular.png'
        );
    }

    /**
     * Tests that cubes exists, and that totara_core-msgicons/program-regular maps to it.
     */
    public function test_totara_core_msgicons_program_regular() {
        $this->assert_icon_data(
            'cubes',
            'msgicons/program-regular',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon fa fa-cubes"></span>',
            'totara_core-msgicons/program-regular',
            'totara/core/pix/msgicons/program-regular.png'
        );
    }

    /**
     * Tests that archives-alt exists, and that totara_core-msgicons/resource-regular maps to it.
     */
    public function test_totara_core_msgicons_resource_regular() {
        $this->assert_icon_data(
            'archives-alt',
            'msgicons/resource-regular',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-archives-alt"></span>',
            'totara_core-msgicons/resource-regular',
            'totara/core/pix/msgicons/resource-regular.png'
        );
    }

    /**
     * Tests that archive-alt exists, and that totara_core-record maps to it.
     */
    public function test_totara_core_record() {
        $this->assert_icon_data(
            'archive-alt',
            'record',
            'totara_core',
            '<span aria-hidden="true" class="flex-icon ft ft-archive-alt"></span>',
            'totara_core-record',
            'totara/core/pix/record.gif'
        );
    }

    /**
     * Tests that certificate exists, and that mod_certificate-icon maps to it.
     */
    public function test_mod_certificate_icon() {
        $this->assert_icon_data(
            'certificate',
            'icon',
            'mod_certificate',
            '<span aria-hidden="true" class="flex-icon ft ft-certificate"></span>',
            'mod_certificate-icon',
            'mod/certificate/pix/icon.gif'
        );
    }

    /**
     * Tests that chart-bar exists, and that mod_survey-icon maps to it.
     */
    public function test_mod_survey_icon() {
        $this->assert_icon_data(
            'chart-bar',
            'icon',
            'mod_survey',
            '<span aria-hidden="true" class="flex-icon ft ft-chart-bar"></span>',
            'mod_survey-icon',
            'mod/survey/pix/icon.png'
        );
    }

    /**
     * Tests that box-net exists, and that repository_boxnet-icon maps to it.
     */
    public function test_repository_boxnet_icon() {
        $this->assert_icon_data(
            'box-net',
            'icon',
            'repository_boxnet',
            '<span aria-hidden="true" class="flex-icon ft ft-box-net"></span>',
            'repository_boxnet-icon',
            'repository/boxnet/pix/icon.png'
        );
    }

    /**
     * Tests that search-comments exists, and that assignfeedback_editpdf-comment_search maps to it.
     */
    public function test_assignfeedback_editpdf_comment_search() {
        $this->assert_icon_data(
            'search-comments',
            'comment_search',
            'assignfeedback_editpdf',
            '<span class="flex-icon ft-stack"><span class="fa fa-comment ft-stack-main"></span><span class="fa fa-search ft-stack-suffix"></span></span>',
            'assignfeedback_editpdf-comment_search',
            'mod/assign/feedback/editpdf/pix/comment_search.png'
        );
    }

    /**
     * Tests that clock-locked exists, and that core-t/locktime maps to it.
     */
    public function test_core_t_locktime() {
        $this->assert_icon_data(
            'clock-locked',
            't/locktime',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-clock-o ft-stack-main"></span><span class="fa fa-lock ft-stack-suffix ft-state-danger"></span></span>',
            'core-t/locktime',
            'pix/t/locktime.png'
        );
    }

    /**
     * Tests that areafiles exists, and that repository_areafiles-icon maps to it.
     */
    public function test_repository_areafiles_icon() {
        $this->assert_icon_data(
            'areafiles',
            'icon',
            'repository_areafiles',
            '<span class="flex-icon ft-stack"><span class="fa fa-file-text-o ft-stack-main"></span><span class="fa fa-paperclip ft-stack-suffix"></span></span>',
            'repository_areafiles-icon',
            'repository/areafiles/pix/icon.gif'
        );
    }

    /**
     * Tests that cogs-risk exists, and that core-i/risk_config maps to it.
     */
    public function test_core_i_risk_config() {
        $this->assert_icon_data(
            'cogs-risk',
            'i/risk_config',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-cogs ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-danger"></span></span>',
            'core-i/risk_config',
            'pix/i/risk_config.png'
        );
    }

    /**
     * Tests that database-risk exists, and that core-i/risk_dataloss maps to it.
     */
    public function test_core_i_risk_dataloss() {
        $this->assert_icon_data(
            'database-risk',
            'i/risk_dataloss',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-database ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-danger"></span></span>',
            'core-i/risk_dataloss',
            'pix/i/risk_dataloss.png'
        );
    }

    /**
     * Tests that shield-risk exists, and that core-i/risk_managetrust maps to it.
     */
    public function test_core_i_risk_managetrust() {
        $this->assert_icon_data(
            'shield-risk',
            'i/risk_managetrust',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-shield ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-danger"></span></span>',
            'core-i/risk_managetrust',
            'pix/i/risk_managetrust.png'
        );
    }

    /**
     * Tests that user-risk exists, and that core-i/risk_personal maps to it.
     */
    public function test_core_i_risk_personal() {
        $this->assert_icon_data(
            'user-risk',
            'i/risk_personal',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-user ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-danger"></span></span>',
            'core-i/risk_personal',
            'pix/i/risk_personal.png'
        );
    }

    /**
     * Tests that envelope-risk exists, and that core-i/risk_spam maps to it.
     */
    public function test_core_i_risk_spam() {
        $this->assert_icon_data(
            'envelope-risk',
            'i/risk_spam',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-envelope ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-danger"></span></span>',
            'core-i/risk_spam',
            'pix/i/risk_spam.png'
        );
    }

    /**
     * Tests that code-risk exists, and that core-i/risk_xss maps to it.
     */
    public function test_core_i_risk_xss() {
        $this->assert_icon_data(
            'code-risk',
            'i/risk_xss',
            'core',
            '<span class="flex-icon ft-stack"><span class="fa fa-code ft-stack-main"></span><span class="fa fa-warning ft-stack-suffix ft-state-danger"></span></span>',
            'core-i/risk_xss',
            'pix/i/risk_xss.png'
        );
    }

    /**
     * Tests that asterisk exists, and that core-req maps to it.
     */
    public function test_core_req() {
        $this->assert_icon_data(
            'asterisk',
            'req',
            'core',
            '<span aria-hidden="true" class="flex-icon fa fa-asterisk"></span>',
            'core-req',
            'pix/req.gif'
        );
    }

    /**
     * Tests that spacer exists, and that core-spacer maps to it.
     */
    public function test_core_spacer() {
        $this->assert_icon_data(
            'spacer',
            'spacer',
            'core',
            '<span aria-hidden="true" class="flex-icon ft ft-spacer"></span>',
            'core-spacer',
            'pix/spacer.gif'
        );
    }

}