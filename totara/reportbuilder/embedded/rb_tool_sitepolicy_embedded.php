<?php
/**
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
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
 * @package tool_sitepolicy
 */

class rb_tool_sitepolicy_embedded extends rb_base_embedded
{
    public $url, $source, $fullname, $columns, $filters;
    public $contentmode, $embeddedparams;

    public function __construct()
    {
        $this->url = '/admin/tool/sitepolicy/sitepolicyreport.php';
        $this->source = 'tool_sitepolicy';
        $this->shortname = 'tool_sitepolicy';
        $this->fullname = get_string('sourcetitle', 'rb_source_tool_sitepolicy');

        $this->columns = $this->define_columns();
        $this->filters = $this->define_filters();

        $this->contentmode = REPORT_BUILDER_CONTENT_MODE_NONE;

        parent::__construct();
    }

    public function embedded_global_restrictions_supported()
    {
        return false;
    }


    protected function define_columns()
    {
        $columns = array(
            array(
                'type' => 'user',
                'value' => 'namelink',
                'heading' => get_string('userfullname', 'totara_reportbuilder'),
            ),
            array(

                'type' => 'primarypolicy',
                'value' => 'primarytitle',
                'heading' => get_string('embeddedprimarytitle', 'rb_source_tool_sitepolicy')
            ),
            array(
                'type' => 'primarypolicy',
                'value' => 'versionnumber',
                'heading' => get_string('embeddedversionnumber', 'rb_source_tool_sitepolicy')
            ),
            array(
                'type' => 'userpolicy',
                'value' => 'statement',
                'heading' => get_string('embeddeduserstatement', 'rb_source_tool_sitepolicy')
            ),
            array(
                'type' => 'userpolicy',
                'value' => 'response',
                'heading' => get_string('embeddeduserresponse', 'rb_source_tool_sitepolicy')
            ),
            array(
                'type' => 'userpolicy',
                'value' => 'consented',
                'heading' => get_string('embeddeduserconsented', 'rb_source_tool_sitepolicy')
            ),
            array(
                'type' => 'userpolicy',
                'value' => 'language',
                'heading' => get_string('embeddeduserlanguage', 'rb_source_tool_sitepolicy')
            ),
	    array(
                'type' => 'userpolicy',
                'value' => 'timeconsented',
                'heading' => get_string('embeddedusertimeconsented', 'rb_source_tool_sitepolicy')
            )
        );

        return $columns;
    }

    protected function define_filters()
    {
        $filters = array(
            array(
                'type' => 'primarypolicy',
                'value' => 'currentversion',
                'advanced' => 0
            )
        );

        return $filters;
    }

    public function is_capable($reportfor, $report)
    {
        return true;

    }
}
