<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Courteney Brownie <courteney.brownie@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package tool_sitepolicy
 */

require_once(__DIR__ . '/../../../config.php');

use \tool_sitepolicy\policyversion;

defined('MOODLE_INTERNAL') || die();

class tool_sitepolicy_renderer extends plugin_renderer_base {
    /**
     * Generates Site Policies table
     * @return string
     */
    public function manage_site_policy_table() {
        global $CFG;
        $out = $this->single_button(new moodle_url("/{$CFG->admin}/tool/sitepolicy/sitepoliciesform.php"),
            get_string('policycreatenew', 'tool_sitepolicy'));

        $sitepolicylist = \tool_sitepolicy\sitepolicy::get_sitepolicylist();

        $table = new html_table();
        $row = [];

        $numpolicies = count($sitepolicylist);
        if ($numpolicies < 1) {
            $table->head[] = (get_string('policiesempty', 'tool_sitepolicy'));
        } else {
            $table->head = [
                get_string('policieslabelname', 'tool_sitepolicy'),
                get_string('policieslabelrevisions', 'tool_sitepolicy'),
                get_string('policieslabelstatus', 'tool_sitepolicy'),
            ];
            foreach ($sitepolicylist as $entry) {
                $versionlisturl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionlist.php", ['sitepolicyid' => $entry->id]);
                $versionformurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionform.php", ['localisedpolicy' => $entry->localisedpolicyid, 'ret' => 'policies']);
                $rowitems = [];

                // Title
                $rowitems[] = new html_table_cell(html_writer::link($versionlisturl, $title = $entry->title));

                // Status
                $status = get_string('policystatus'.$entry->status, 'tool_sitepolicy');
                $draft = (int)($entry->status == policyversion::STATUS_DRAFT);
                $draftlink = '';
                if ($draft) {
                    $draftlink = $this->help_icon('policystatusdraft', 'tool_sitepolicy') . ' ' .
                        html_writer::link($versionformurl, get_string('policiesrevisionnewdraft', 'tool_sitepolicy'));
                }

                // Revisions
                $rowitems[] = new html_table_cell($entry->numpublished . ' ' . $draftlink);
                $rowitems[] = new html_table_cell($status);
                $row[] = new html_table_row($rowitems);
            }
        }
        $table->data = $row;
        $out .= $this->output->render($table);
        return $out;
    }

    /**
     * Generates localised policy table
     * @param int $sitepolicyid
     * @return string
     */
    public function manage_version_policy_table(int $sitepolicyid) {
        global $CFG;
        $table = new html_table();
        $table->head = [
            get_string('versionslabelversion', 'tool_sitepolicy'),
            get_string('versionslabelstatus', 'tool_sitepolicy'),
            get_string('versionslabelnumtrans', 'tool_sitepolicy'),
            get_string('versionslabeldatepublish', 'tool_sitepolicy'),
            get_string('versionslabeldatearchive', 'tool_sitepolicy'),
            get_string('versionslabelactions', 'tool_sitepolicy')
        ];
        $row = [];
        $versionlist = policyversion::get_versionlist($sitepolicyid);
        $out = '';

        foreach ($versionlist as $entry) {
            $versionformurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionform.php", ['localisedpolicy' => $entry->primarylocalisedid, 'ret' => 'versions']);
            $rowitems = [];

            // Version number.
            $rowitems[''] = new html_table_cell($entry->versionnumber);

            // Status.
            $status = $entry->status;
            $statusstr = get_string('versionstatus'. $entry->status, 'tool_sitepolicy');
            $rowitems[] = new html_table_cell($statusstr);

            // Number of translations.
            $translationlisturl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/translationlist.php", ['policyversionid' => $entry->id]);
            $incomplete = "";
            if ($status == policyversion::STATUS_DRAFT && $entry->cnt_translatedoptions != $entry->cnt_options) {
                $incomplete = $this->output->flex_icon('warning',
                    ['alt' => get_string('versionstatusincomplete', 'tool_sitepolicy')]
                );
            }

            $viewstr = get_string('versionstranslationsview', 'tool_sitepolicy');

            $a = new stdClass();
            $a->cnt = $entry->cnt_translations;
            $a->link = html_writer::link($translationlisturl, $viewstr);
            $a->incomplete = $incomplete;
            $cellentry = get_string('versionpolicycellentry', 'tool_sitepolicy', $a);

            $rowitems[] = new html_table_cell($cellentry);

            // Options
            $options = [];
            switch ($status) {
                case policyversion::STATUS_DRAFT:
                    $rowitems[] = new html_table_cell('-');
                    $rowitems[] = new html_table_cell('-');

                    $publishurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionpublish.php", ['policyversionid' => $entry->id]);
                    $deleteurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versiondelete.php", ['policyversionid' => $entry->id]);

                    if (!empty($incomplete)) {
                        $options[] = $this->output->action_icon('#',
                            new pix_icon('a/logout', get_string('versionpublish', 'tool_sitepolicy'), 'moodle'), null, ['disabled' => 'disabled']);
                    } else {
                        $options[] = $this->output->action_icon($publishurl,
                            new pix_icon('a/logout', get_string('versionpublish', 'tool_sitepolicy'), 'moodle'));
                    }
                    $options[] = $this->output->action_icon($versionformurl, new pix_icon('i/manual_item', get_string('versionedit', 'tool_sitepolicy'), 'moodle'));

                    $options[] = $this->output->action_icon($deleteurl,
                        new pix_icon('i/delete', get_string('versiondelete', 'tool_sitepolicy'), 'moodle'));
                    $out = $this->single_button($versionformurl, get_string('versionscontinueedit', 'tool_sitepolicy'));
                    break;

                case policyversion::STATUS_PUBLISHED:
                    $rowitems[] = new html_table_cell(userdate($entry->timepublished));
                    $rowitems[] = new html_table_cell('-');

                    $archiveurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionarchive.php", ['policyversionid' => $entry->id]);
                    $options[] = $this->output->action_icon($archiveurl,
                        new pix_icon('archive', get_string('versionarchive', 'tool_sitepolicy'),
                            'tool_sitepolicy'));
                    break;

                case policyversion::STATUS_ARCHIVED:
                    $rowitems[] = new html_table_cell(userdate($entry->timepublished));
                    $rowitems[] = new html_table_cell(userdate($entry->timearchived));
            }

            $rowitems[] = new html_table_cell(implode('', $options));
            $row[] = new html_table_row($rowitems);
        }

        $table->data = $row;
        $out .= $this->output->render($table);

        return $out;
    }

    /**
     * Generates Add translation single select
     *
     * @param policyversion $version
     * @return string
     */
    public function add_translation_single_select(policyversion $version) {
        global $CFG;
        $syslanguages = get_string_manager()->get_list_of_translations();
        $verlanguages = $version->get_languages();
        $options = array_diff_key($syslanguages, $verlanguages);

        $translationformurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/translationform.php", ['policyversionid' => $version->get_id()]);

        $select = new \single_select($translationformurl, 'language', $options, '', ['' => get_string('translationsadd', 'tool_sitepolicy')], 'addtranslationform');
        $select->class = 'singleselect pull-right';

        return $this->output->render($select);
    }

    /**
     * Generates Translations table
     *
     * @param policyversion $version
     * @return string
     */
    public function manage_translation_table(policyversion $version) {
        global $CFG;
        $table = new html_table();

        $table->head = [
            get_string('translationslabellanguage', 'tool_sitepolicy'),
            get_string('translationslabelstatus', 'tool_sitepolicy'),
            get_string('translationslabeloptions', 'tool_sitepolicy')
        ];

        $row = [];

        $versionsummary = $version->get_summary();
        $out = '';

        foreach ($versionsummary as $entries => $entry) {
            $versionformurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionform.php", ['localisedpolicy' => $entry->id, 'ret' => 'translations']);
            $translationformurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/translationform.php", ['localisedpolicy' => $entry->id]);
            $deleteurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/translationdelete.php", ['localisedpolicy' => $entry->id]);
            $viewpolicyurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/viewpolicy.php");
            $language = get_string_manager()->get_list_of_languages($entry->primarylanguage)[$entry->language];
            $rowitems = [];

            // Language.
            $languagestr = $language;
            if ($entry->isprimary == 1) {
                $languagestr = get_string('translationprimary', 'tool_sitepolicy', $language);

            }
            $rowitems[] = new html_table_cell(html_writer::link(new moodle_url($viewpolicyurl, ['language' => $entry->language, 'policyversionid' => $version->get_id(), 'versionnumber' => $version->get_versionnumber()]), $languagestr));

            // Status.
            if (!empty($entry->timepublished)) {
                // Active or archived.
                $rowitems[] = new html_table_cell(get_string('translationstatuscomplete', 'tool_sitepolicy'));
                $rowitems[] = new html_table_cell('-');
            } else {
                // Draft.
                $status = get_string('translationstatuscomplete', 'tool_sitepolicy');
                if ($entry->incomplete) {
                    $status = get_string('translationstatusincomplete', 'tool_sitepolicy');
                }
                $rowitems[] = new html_table_cell($status);

                $option = [];
                if ($entry->isprimary == 1) {
                    $option[] = $this->output->action_icon($versionformurl, new pix_icon('i/manual_item', get_string('translationedit', 'tool_sitepolicy'), 'moodle'));
                } else {
                    $option[] = $this->output->action_icon($translationformurl, new pix_icon('i/manual_item', get_string('translationedit', 'tool_sitepolicy'), 'moodle'));
                    $option[] = $this->output->action_icon($deleteurl, new pix_icon('i/delete', get_string('translationdelete', 'tool_sitepolicy'), 'moodle'));
                }
                $rowitems[] = new html_table_cell(implode('', $option));
            }

            $row[] = new html_table_row($rowitems);
        }

        $table->data = $row;
        $out .= $this->output->render($table);

        return $out;
    }

    /**
     * Confirmation page for version actions
     * @param string $heading
     * @param string $message
     * @param single_button $continue
     * @param single_button $cancel
     * @return string
     */
    public function action_confirm(string $heading, string $message, single_button $continue, single_button $cancel): string {
        $output = $this->box_start('generalbox modal modal-dialog modal-in-page show', 'notice');
        $output .= $this->box_start('modal-content', 'modal-content');
        $output .= $this->box_start('modal-header', 'modal-header');
        $output .= html_writer::tag('h4', $heading);
        $output .= $this->box_end();
        $output .= $this->box_start('modal-body', 'modal-body');
        $output .= html_writer::tag('p', $message);
        $output .= $this->box_end();
        $output .= $this->box_start('modal-footer', 'modal-footer');
        $output .= html_writer::tag('div', $this->render($continue) . $this->render($cancel), ['class' => 'buttons']);
        $output .= $this->box_end();
        $output .= $this->box_end();
        $output .= $this->box_end();
        return $output;
    }

    /**
     * Generates User Consents table
     * @param int $userid
     * @return string
     */
    public function manage_userconsents_table(int $userid) {
        global $CFG;
        $consentresponse = \tool_sitepolicy\userconsent::get_userconsenttable($userid);
        $viewpolicyurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/userpolicy.php");

        $table = new html_table();
        if (empty($consentresponse)) {
            $table->head[] = (get_string('userconsentlistempty', 'tool_sitepolicy'));
        } else {
            $table->head = [
                get_string('userconsentlistlabelpolicy', 'tool_sitepolicy'),
                get_string('userconsentlistlabelversion', 'tool_sitepolicy'),
                get_string('userconsentlistlabellanguage', 'tool_sitepolicy'),
                get_string('userconsentlistlabelstatement', 'tool_sitepolicy'),
                get_string('userconsentlistlabelresponse', 'tool_sitepolicy'),
                get_string('userconsentlistlabeldateconsented', 'tool_sitepolicy')

            ];
            $row = [];

            $previousid = 0;
            foreach ($consentresponse as $response) {
                $rowitems = [];

                //Policy Title and Version Number - if needed
                $rowitems[] = new html_table_cell('');
                $rowitems[] = new html_table_cell('');
                if ($response->policyversionid != $previousid) {
                    $rowitems[0] = new html_table_cell(html_writer::link(new moodle_url($viewpolicyurl,
                        ['policyversionid' => $response->policyversionid,
                         'versionnumber' => $response->versionnumber,
                         'language' => $response->language]),
                        $response->title));
                    $rowitems[1] = new html_table_cell($response->versionnumber);
                }

                //Language
                $rowitems[] = new html_table_cell(get_string_manager()->get_list_of_languages($response->language)[$response->language]);

                //Consent Statement
                $rowitems[] = new html_table_cell($response->statement);

                //Consent Response
                $rowitems[] = new html_table_cell($response->response);

                //Date Consented
                $rowitems[] = new html_table_cell(userdate($response->timeconsented));
                $row[] = new html_table_row($rowitems);
                $previousid = $response->policyversionid;

            }
            $table->data = $row;
        }
        $out = '';
        $out .= $this->output->render($table);
        return $out;
    }
}
