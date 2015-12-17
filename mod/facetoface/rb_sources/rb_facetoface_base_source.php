<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */
defined('MOODLE_INTERNAL') || die();

abstract class rb_facetoface_base_source extends rb_base_source {
    /*
     * Adds any facetoface session roles to the $joinlist array
     *
     * @param array &$joinlist Array of current join options
     *                         Passed by reference and updated if
     *                         any session roles exist
     */
    function add_facetoface_session_roles_to_joinlist(&$joinlist, $sessionidfield = 'base.sessionid') {
        global $DB;
        // add joins for the following roles as "session_role_X" and
        // "session_role_user_X"
        $sessionroles = self::get_session_roles();
        if (empty($sessionroles)) {
            return;
        }

        // Fields.
        $usernamefields = totara_get_all_user_name_fields_join('role_user');
        $userlistcolumn = $this->rb_group_comma_list($DB->sql_concat_join("' '", $usernamefields));
        // Add id to fields.
        $usernamefieldsid = array_merge(array('role_user.id' => 'userid'), $usernamefields);
        // Length of resulted concatenated fields.
        $lengthfield = array('lengths' => $DB->sql_length($DB->sql_concat_join("' '", $usernamefieldsid)));
        // Final column: concat(strlen(concat(fields)),concat(fields)) so we know length of each username with id.
        $usernamefieldslink = array_merge($lengthfield, $usernamefieldsid);
        $userlistcolumnlink = $this->rb_group_comma_list($DB->sql_concat_join("' '", $usernamefieldslink));

        foreach ($sessionroles as $role) {
            $field = $role->shortname;
            $roleid = $role->id;

            $sql = "(SELECT session_role.sessionid AS sessionid, session_role.roleid AS roleid, %s AS userlist
                    FROM {user} role_user
                      INNER JOIN {facetoface_session_roles} session_role ON (role_user.id = session_role.userid)
                    GROUP BY session_role.sessionid, session_role.roleid)";

            $userkey = "session_role_user_$field";
            $joinlist[] = new rb_join(
                $userkey,
                'LEFT',
                sprintf($sql, $userlistcolumn),
                "($userkey.sessionid = $sessionidfield AND $userkey.roleid = $roleid)",
                REPORT_BUILDER_RELATION_ONE_TO_MANY
            );

            $userkeylink = $userkey . 'link';
            $joinlist[] = new rb_join(
                $userkeylink,
                'LEFT',
                sprintf($sql, $userlistcolumnlink),
                "($userkeylink.sessionid = $sessionidfield AND $userkeylink.roleid = $roleid)",
                REPORT_BUILDER_RELATION_ONE_TO_MANY
            );
        }
    }

    /*
     * Adds any session role fields to the $columnoptions array
     *
     * @param array &$columnoptions Array of current column options
     *                              Passed by reference and updated if
     *                              any session roles exist
     * @return boolean True if session roles exist
     */
    function add_facetoface_session_roles_to_columns(&$columnoptions) {
        $sessionroles = self::get_session_roles();
        if (empty($sessionroles)) {
            return;
        }

        foreach ($sessionroles as $sessionrole) {
            $field = $sessionrole->shortname;
            $name = $sessionrole->name;
            if (empty($name)) {
                $name = role_get_name($sessionrole);
            }

            $userkey = "session_role_user_$field";

            // User name.
            $columnoptions[] = new rb_column_option(
                'role',
                $field . '_name',
                get_string('sessionrole', 'rb_source_facetoface_sessions', $name),
                "$userkey.userlist",
                array(
                    'joins' => $userkey,
                    'dbdatatype' => 'char',
                    'outputformat' => 'text'
                )
            );

            // User name with link to profile.
            $userkeylink = $userkey . 'link';
            $columnoptions[] = new rb_column_option(
                'role',
                $field . '_namelink',
                get_string('sessionrolelink', 'rb_source_facetoface_sessions', $name),
                "$userkeylink.userlist",
                array(
                    'joins' => $userkeylink,
                    'dbdatatype' => 'char',
                    'outputformat' => 'text',
                    'defaultheading' => get_string('sessionrole', 'rb_source_facetoface_sessions', $name),
                    'displayfunc' => 'coded_link_user',
                )
            );
        }
        return true;
    }

    /**
     * Return list of user names linked to their profiles from string of concatenated user names, their ids,
     * and length of every name with id
     * @param string $name Concatenated list of names, ids, and lengths
     * @param stdClass $row
     * @param bool $isexport
     * @return string
     */
    public function rb_display_coded_link_user($name, $row, $isexport = false) {
        // Concatenated names are provided as (kind of) pascal string beginning with id in the following format:
        // length_of_following_string.' '.id.' '.name.', '
        $leftname = $name;
        $result = array();
        while(true) {
            $len = (int)$leftname; // Take string length.
            if (!$len) {
                break;
            }
            $idname = core_text::substr($leftname, core_text::strlen((string)$len)+1, $len, 'UTF-8');
            if (empty($idname)) {
                break;
            }
            $idendpos = core_text::strpos($idname, ' ');
            $id = (int)core_text::substr($idname, 0, $idendpos);
            if (!$id) {
                break;
            }
            $name = trim(core_text::substr($idname, $idendpos));
            $result[] = ($isexport) ? $name : html_writer::link(new moodle_url('/user/view.php', array('id' => $id)), $name);

            // length(length(idname)) + length(' ') + length(idname) + length(', ').
            $leftname = core_text::substr($leftname, core_text::strlen((string)$len)+1+$len+2);
        }
        return implode(', ', $result);
    }

    /*
     * Adds some common user field to the $filteroptions array
     *
     * @param array &$filteroptions Array of current filter options
     *                              Passed by reference and updated by
     *                              this method
     */
    protected function add_facetoface_session_role_fields_to_filters(&$filteroptions) {
        // auto-generate filters for session roles fields
        $sessionroles = self::get_session_roles();
        if (empty($sessionroles)) {
            return;
        }

        foreach ($sessionroles as $sessionrole) {
            $field = $sessionrole->shortname;
            $name = $sessionrole->name;
            if (empty($name)) {
                $name = role_get_name($sessionrole);
            }

            $filteroptions[] = new rb_filter_option(
                'role',
                $field . '_name',
                get_string('sessionrole', 'rb_source_facetoface_sessions', $name),
                'text'
            );
        }
    }

    /**
     * Get session roles from list of allowed roles
     * @return array
     */
    protected static function get_session_roles() {
        global $DB;

        $allowedroles = get_config(null, 'facetoface_session_roles');
        if (!isset($allowedroles) || $allowedroles == '') {
            return array();
        }
        $allowedroles = explode(',', $allowedroles);

        list($allowedrolessql, $params) = $DB->get_in_or_equal($allowedroles);

        $sessionroles = $DB->get_records_sql("SELECT id, name, shortname FROM {role} WHERE id $allowedrolessql", $params);
        if (!$sessionroles) {
            return array();
        }
        return $sessionroles;
    }
}