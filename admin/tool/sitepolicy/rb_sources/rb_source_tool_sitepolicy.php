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

defined('MOODLE_INTERNAL') || die();

class rb_source_tool_sitepolicy extends rb_base_source {
    use \core_user\rb\source\report_trait;

    public $base, $joinlist, $columnoptions, $filteroptions;
    public $contentoptions, $paramoptions, $requiredcolumns;

    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        $this->usedcomponents[] = 'tool_sitepolicy';

        if ($groupid instanceof rb_global_restriction_set) {
            throw new coding_exception('Wrong parameter orders detected during report source instantiation.');
        }
        // Remember the active global restriction set.
        $this->globalrestrictionset = $globalrestrictionset;

        // Apply global user restrictions.
        $this->add_global_report_restriction_join('base', 'userid');

        $this->base = '{tool_sitepolicy_user_consent}';
        $this->joinlist = $this->define_joinlist();
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();
        $this->contentoptions = $this->define_contentoptions();
        $this->paramoptions = $this->define_paramoptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->defaultfilters = $this->define_defaultfilters();
        $this->sourcetitle = get_string('sourcetitle', 'rb_source_tool_sitepolicy');
        parent::__construct();
    }

    protected function define_joinlist() {
        $joinlist = array(
            new rb_join(
                'consentoption',
                'INNER',
                '{tool_sitepolicy_consent_options}',
                'base.consentoptionid = consentoption.id'),

            new rb_join(
                'policyversion',
                'INNER',
                '{tool_sitepolicy_policy_version}',
                'policyversion.id = consentoption.policyversionid',
                null,
                'consentoption'),

            new rb_join(
                'localisedpolicy',
                'INNER',
                '{tool_sitepolicy_localised_policy}',
                'localisedpolicy.policyversionid = policyversion.id
                 AND localisedpolicy.language = base.language',
                null,
                'policyversion'),

            new rb_join(
                'localisedconsent',
                'INNER',
                '{tool_sitepolicy_localised_consent}',
                'localisedconsent.localisedpolicyid = localisedpolicy.id
                 AND localisedconsent.consentoptionid = base.consentoptionid',
                null,
                'localisedpolicy'),

            new rb_join(
                'primarylocalisedpolicy',
                'INNER',
                '{tool_sitepolicy_localised_policy}',
                'primarylocalisedpolicy.policyversionid = policyversion.id
                 AND primarylocalisedpolicy.isprimary = 1',
                null,
                'policyversion'),

            new rb_join(
                'primarylocalisedconsent',
                'INNER',
                '{tool_sitepolicy_localised_consent}',
                'primarylocalisedconsent.localisedpolicyid = primarylocalisedpolicy.id
                 AND primarylocalisedconsent.consentoptionid = base.consentoptionid',
                null,
                'primarylocalisedpolicy'),

            new rb_join(
                'author',
                'INNER',
                '{user}',
                'primarylocalisedpolicy.authorid = author.id',
                null,
                'primarylocalisedpolicy'),

            new rb_join(
                'publisher',
                'INNER',
                '{user}',
                'policyversion.publisherid = publisher.id',
                null,
                'policyversion')

        );
        // optionally include some standard joins
        $this->add_core_user_tables($joinlist, 'base', 'userid');
        return $joinlist;
    }

    protected function define_columnoptions() {
        $statusdraft = \tool_sitepolicy\policyversion::STATUS_DRAFT;
        $statuspublished = \tool_sitepolicy\policyversion::STATUS_PUBLISHED;
        $statusarchived = \tool_sitepolicy\policyversion::STATUS_ARCHIVED;

        $columnoptions = array(
            new rb_column_option(
                'primarypolicy',
                'primarytitle',
                get_string('policytitle', 'rb_source_tool_sitepolicy'),
                'primarylocalisedpolicy.title',
                array('joins' => 'primarylocalisedpolicy')),

            new rb_column_option(
                'primarypolicy',
                'primarydatecreated',
                get_string('policydatecreated', 'rb_source_tool_sitepolicy'),
                'policyversion.timecreated',
                array('joins' => 'policyversion',
                      'displayfunc' => 'nice_datetime')),

            new rb_column_option(
                'primarypolicy',
                'primarycreatedby',
                get_string('policycreatedby', 'rb_source_tool_sitepolicy'),
                'author.username',
                array('joins' => 'author')),

            new rb_column_option(
                'primarypolicy',
                'versionnumber',
                get_string('policyversion', 'rb_source_tool_sitepolicy'),
                'policyversion.versionnumber',
                array('joins' => 'policyversion')),

            new rb_column_option(
                'primarypolicy',
                'status',
                get_string('policystatus', 'rb_source_tool_sitepolicy'),
                "(CASE
                    WHEN policyversion.timepublished IS NULL THEN '{$statusdraft}'
                    WHEN policyversion.timearchived IS NOT NULL THEN '{$statusarchived}'
                    ELSE '{$statuspublished}'
                  END)",
                array('joins' => 'policyversion',
                      'displayfunc' => 'sitepolicy_versionstatus')),

            new rb_column_option(
                'primarypolicy',
                'datepublished',
                get_string('policydatepublished', 'rb_source_tool_sitepolicy'),
                'policyversion.timepublished',
                array('joins' => 'policyversion',
                      'displayfunc' => 'nice_datetime')),

            new rb_column_option(
                'primarypolicy',
                'publishedby',
                get_string('policypublishedby', 'rb_source_tool_sitepolicy'),
                'publisher.username',
                array('joins' => 'publisher')),

            new rb_column_option(
                'primarypolicy',
                'primarystatement',
                get_string('policystatement', 'rb_source_tool_sitepolicy'),
                'primarylocalisedconsent.statement',
                array('joins' => 'primarylocalisedconsent')),

            new rb_column_option(
                'primarypolicy',
                'primaryresponse',
                get_string('policyresponse', 'rb_source_tool_sitepolicy'),
                'base.hasconsented',
                array('joins' => 'primarylocalisedconsent',
                      'displayfunc' => 'sitepolicy_userresponse',
                      'extrafields' => array(
                            'primarylocalisedconsent.nonconsentoption',
                            'primarylocalisedconsent.consentoption'
                       ))),

            new rb_column_option(
                'userpolicy',
                'consented',
                get_string('userreponseconsented', 'rb_source_tool_sitepolicy'),
                'base.hasconsented',
                array(
                    'displayfunc' => 'yes_or_no')),

            new rb_column_option(
                'userpolicy',
                'language',
                get_string('userreponselanguage', 'rb_source_tool_sitepolicy'),
                'base.language',
                array(
                    'displayfunc' => 'language_code')),

            new rb_column_option(
                'userpolicy',
                'statement',
                get_string('userreponsestatement', 'rb_source_tool_sitepolicy'),
                'localisedconsent.statement',
                array('joins' => 'localisedconsent')),

            new rb_column_option(
                'userpolicy',
                'response',
                get_string('userresponseoption', 'rb_source_tool_sitepolicy'),
                'base.hasconsented',
                array('joins' => 'localisedconsent',
                      'displayfunc' => 'sitepolicy_userresponse',
                      'extrafields' => array(
                            'localisedconsent.nonconsentoption',
                            'localisedconsent.consentoption'
                       ))),

            new rb_column_option(
                'userpolicy',
                'timeconsented',
                get_string('usertimeconsented', 'rb_source_tool_sitepolicy'),
                'base.timeconsented',
                array('displayfunc' => 'nice_datetime')),
            );
        $this->add_core_user_columns($columnoptions);
        return $columnoptions;
    }

    protected function define_filteroptions() {
        $filteroptions = array(
            new rb_filter_option(
                'primarypolicy',
                'versionnumber',
                get_string('policyversion', 'rb_source_tool_sitepolicy'),
                'number'),

            new rb_filter_option(
                'userpolicy',
                'consented',
                get_string('userconsentoptions','rb_source_tool_sitepolicy'),
                'multicheck',
                array('selectfunc' => 'user_consent')),

            new rb_filter_option(
                'primarypolicy',
                'currentversion',
                get_string('policycurrentversion','rb_source_tool_sitepolicy'),
                'multicheck',
                array(
                    'addtypetoheading' => true,
                    'selectfunc' => 'current_version',
                    'attributes' => rb_filter_option::select_width_limiter(),
                ),
                'policyversion.id',
                'policyversion'
            ),

            new rb_filter_option(
                'primarypolicy',
                'status',
                get_string('policystatus','rb_source_tool_sitepolicy'),
                'multicheck',
                array('selectfunc' => 'versionstatus')
            ),

            new rb_filter_option(
                'userpolicy',
                'language',
                get_string('userreponselanguage','rb_source_tool_sitepolicy'),
                'select',
                array('selectfunc' => 'userlanguage')),

        );
        $this->add_core_user_filters($filteroptions);
        return $filteroptions;
    }

    protected function define_paramoptions() {
        $paramoptions = array(
            new rb_param_option(
                'userid',
                'base.userid'
            )
        );
        return $paramoptions;
    }

    protected function define_defaultcolumns() {
        $defaultcolumns = array(
            array(
                'type' => 'primarypolicy',
                'value' => 'primarytitle'
            ),
            array(
                'type' => 'primarypolicy',
                'value' => 'versionnumber'
            ),
            array(
                'type' => 'userpolicy',
                'value' => 'statement'
            ),
            array(
                'type' => 'userpolicy',
                'value' => 'response'
            ),
            array(
                'type' => 'userpolicy',
                'value' => 'consented'
            ),
            array(
                'type' => 'userpolicy',
                'value' => 'language'
            ),
            array(
                'type' => 'userpolicy',
                'value' => 'timeconsented'
            )
        );

        return $defaultcolumns;
    }

    protected function define_defaultfilters() {
        $defaultfilters = array(
            array(
                'type' => 'primarypolicy',
                'value' => 'currentversion'
            )
        );

        return $defaultfilters;
    }


    public function rb_filter_user_consent() {
        $consentoptions = array();
        $consentoptions['1'] = get_string('yes','rb_source_tool_sitepolicy');
        $consentoptions['0'] =  get_string('no','rb_source_tool_sitepolicy');
        return $consentoptions;
    }

    public function rb_filter_current_version() {
        global $DB;
        $sql = "SELECT policyversion.id, localisedpolicy.title, policyversion.versionnumber
                FROM {tool_sitepolicy_localised_policy} localisedpolicy
                JOIN {tool_sitepolicy_policy_version} policyversion
                ON localisedpolicy.policyversionid = policyversion.id
                WHERE policyversion.timepublished IS NOT NULL AND policyversion.timearchived IS NULL
                AND localisedpolicy.isprimary = 1";
        $versions = $DB->get_records_sql($sql);
        $currentversion = array();
        foreach ($versions as $version) {
            $currentversion[$version->id] = get_string('currentversionstring','rb_source_tool_sitepolicy',
                ['title'=>$version->title]);
        }
        return $currentversion;
    }

    public function rb_filter_versionstatus() {
        $consentoptions = array();

        $statusarr = [
            \tool_sitepolicy\policyversion::STATUS_PUBLISHED,
            \tool_sitepolicy\policyversion::STATUS_ARCHIVED];

        foreach ($statusarr as $status) {
            $consentoptions[$status] = get_string("versionstatus{$status}", 'tool_sitepolicy');
        }

        return $consentoptions;
    }

    public function rb_filter_userlanguage() {
        $userlanguage = array();
        global $DB;
        $sql = "SELECT distinct language FROM {tool_sitepolicy_localised_policy}";
        $languages = $DB->get_records_sql($sql);
        foreach ($languages as $language) {
            $userlanguage[$language->language] = get_string_manager()->get_list_of_translations()[$language->language];
        }
        return $userlanguage;
    }

    /**
     * Inject column_test data into database.
     * @param totara_reportbuilder_column_testcase $testcase
     */
    public function phpunit_column_test_add_data(totara_reportbuilder_column_testcase $testcase) {
        global $DB;

        if (!PHPUNIT_TEST) {
            throw new coding_exception('phpunit_column_test_add_data() cannot be used outside of unit tests');
        }

        $totara_report_builder_data = array('id' => 1, 'fullname' => 'Site Policy Report', 'shortname' => 'tool_sitepolicy', 'source' => 'tool_sitepolicy',
            'hidden' => 0, 'cache' => 0, 'accessmode' => 0, 'contentmode' => 0, 'description' => 'Report description', 'recordsperpage' => 10,
            'defaultsortcolumn' => null, 'defaultsortorder' => 0, 'embedded' => 0, 'initialdisplay' => 0, 'toolbarsearch' => 1,
            'globalrestriction' => 0, 'timemodified' => 0, 'showtotalcount' => 0, 'useclonedb' => 0);

        $DB->insert_record('report_builder', $totara_report_builder_data);


        $sitepolicy_data= ['timecreated'=> 1515529244];
        $sitepolicy_data['id'] = $DB->insert_record('tool_sitepolicy_site_policy', $sitepolicy_data);

        $policyversion_data = ['versionnumber'=>1515529244, 'timecreated'=>1515461888, 'timepublished'=>1515462800, 'sitepolicyid'=>$sitepolicy_data['id'], 'publisherid'=>2];
        $policyversion_data['id'] = $DB->insert_record('tool_sitepolicy_policy_version', $policyversion_data);

        $localisedpolicy_data = ['language'=>'en','title'=>'Terms and Conditions', 'policytext'=>'statment','timecreated'=>1515461888, 'isprimary'=>1, 'authorid'=>2, 'policyversionid'=>$policyversion_data['id']];
        $localisedpolicy_data['id'] = $DB->insert_record('tool_sitepolicy_localised_policy', $localisedpolicy_data);

        $consentoption_data = ['mandatory'=>1, 'policyversionid'=>$policyversion_data['id']];
        $consentoption_data['id'] = $DB->insert_record('tool_sitepolicy_consent_options', $consentoption_data);

        $localisedconsent_data = ['statement'=>'Consent','consentoption'=>'yes','nonconsentoption'=>'no','localisedpolicyid'=>$localisedpolicy_data['id'],'consentoptionid'=>$consentoption_data['id']];
        $localisedconsent_data['id'] = $DB->insert_record('tool_sitepolicy_localised_consent', $localisedconsent_data);

        $userconsent_data = ['userid'=>2,'timeconsented'=>1515617791, 'hasconsented'=>1, 'consentoptionid'=>$consentoption_data['id'], 'language' => 'en'];
        $userconsent_data['id'] = $DB->insert_record('tool_sitepolicy_user_consent', $userconsent_data);
    }

    public function global_restrictions_supported() {
        return true;
    }
}
