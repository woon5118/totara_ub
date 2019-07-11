<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_facetoface
 */

defined('TOTARA_DIALOG_SEARCH') || die();

require_once($CFG->dirroot . '/totara/core/searchlib.php');
require_once($CFG->dirroot . '/totara/core/dialogs/search_form.php');
require_once($CFG->dirroot . '/mod/facetoface/dialogs/seminar_dialog_content.php');

global $OUTPUT;

// Get parameter values
$query      = optional_param('query', null, PARAM_TEXT); // search query
$page       = optional_param('page', 0, PARAM_INT); // results page number
$searchtype = $this->searchtype;

// Trim whitespace off search query
$query = trim($query);
// Extra form data
$formdata = array(
    'hidden'        => $this->customdata,
    'query'         => $query,
    'searchtype'    => $searchtype
);
$mform = new dialog_search_form(null, $formdata);
// Display form
$mform->display();

// Generate results
if (strlen($query)) {

    if (!$this->items) {
        $params['query'] = $query;
        $message = get_string('noresultsfor', 'totara_core', (object)$params);
        echo html_writer::tag('p', $message, array('class' => 'message'));
        die();
    }

    $founditems = [];
    // Generate some treeview data
    foreach ($this->items as $result) {

        if (stripos($result->fullname, $query) === false) {
             continue;
        }
        // Add datakey attributes to item.
        $item = new stdClass();
        foreach ($this->datakeys as $key) {
            $item->$key = $result->$key;
        }
        $item->id = $result->id;
        $item->fullname = format_string($result->fullname);

        $founditems[$item->id] = $item;
    }

    if (count($founditems) > 0) {
        $total = count($founditems);
        // Support pagination.
        $data = [
            'search'        => true,
            'query'         => $query,
            'searchtype'    => $searchtype,
            'page'          => $page,
            'sesskey'       => sesskey()
        ];
        $baseurl = new moodle_url($this->baseurl, array_merge($data, $this->urlparams));
        $start = $page * DIALOG_SEARCH_NUM_PER_PAGE;
        $items = array_slice($founditems, $start, DIALOG_SEARCH_NUM_PER_PAGE);
        $output = $OUTPUT->render(new \paging_bar($total, $page, DIALOG_SEARCH_NUM_PER_PAGE, $baseurl));
        echo html_writer::tag('div', $output, array('class' => "search-paging"));

        $dialog = new \seminar_dialog_content();
        $dialog->items = array();
        $dialog->parent_items = array();
        $dialog->set_datakeys($this->datakeys);

        $dialog->items = $items;
        $dialog->disabled_items = $this->disabled_items;
        echo $dialog->generate_treeview();
    } else {
        $params['query'] = $query;
        $message = get_string('noresultsfor', 'totara_core', (object)$params);
        echo html_writer::tag('p', $message, array('class' => 'message'));
    }
}
