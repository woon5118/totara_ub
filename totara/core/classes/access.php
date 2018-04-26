<?php
/*
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author  Simon Coggins <simon.coggins@totaralearning.com>
 */

namespace totara_core;

defined('MOODLE_INTERNAL') || die();

/**
 * SQL implementation of access control methods.
 *
 * NOTE: this is not public API, use get_has_capability_sql() function instead.
 */
class access {

    /**
     * This function allows you to restrict rows in an existing SQL statement by including the return value as
     * a WHERE clause. You must provide the capability and user you want to check, and a sql field referencing
     * context id. This allows you to check multiple contexts in one SQL query
     * instead of having to call {@link has_capability()} inside a loop.
     *
     * NOTE: role switching is not implemented here
     *
     * @param string        $capability     The name of the capability to check. For example mod/forum:view
     * @param string        $contextidfield An SQL snippet which represents the link to context id in the parent SQL statement.
     * @param int|\stdClass $user           A user id or user object, null means current user
     * @param boolean       $doanything     If false, only real roles of administrators are considered
     *
     * @return array Array of the form array($sql, $params) which can be included in the WHERE clause of an SQL statement.
     */
    public static function get_has_capability_sql($capability, $contextidfield, $user = null, $doanything = true) {
        global $USER, $CFG;

        if (!preg_match('/^(\{?[a-z][a-z0-9_]*\}?)\.[a-z][a-z0-9_]*$/', $contextidfield, $matches)) {
            throw new \coding_exception('Invalid context field specified');
        }
        if ($matches[1] === 'maincontext') {
            throw new \coding_exception('Invalid context field specified, maincontext alias is used internally');
        }

        // Make sure there is a user id specified.
        if ($user === null) {
            $userid = $USER->id;
        } else {
            $userid = is_object($user) ? $user->id : intval($user);
        }

        // Capability must exist.
        if (!$capinfo = get_capability_info($capability)) {
            debugging('Capability "'.$capability.'" was not found! This has to be fixed in code.');
            return array("1=0", array());
        }

        if (isguestuser($userid) or $userid == 0) {
            // Make sure the guest account and not-logged-in users never get any risky caps no matter what the actual settings are.
            if (($capinfo->captype === 'write') or ($capinfo->riskbitmask & (RISK_XSS | RISK_CONFIG | RISK_DATALOSS))) {
                return array("1=0", array());
            }
            // Make sure forcelogin cuts off not-logged-in users if enabled.
            if (!empty($CFG->forcelogin) and $userid == 0) {
                return array("1=0", array());
            }

        } else {
            // Make sure that the user exists and is not deleted.
            $usercontext = \context_user::instance($userid, IGNORE_MISSING);
            if (!$usercontext) {
                return array("1=0", array());
            }
        }

        // Site admin can do anything, unless otherwise specified.
        if (is_siteadmin($userid) && $doanything) {
            return array("1=1", array());
        }

        list($prohibitsql, $prohibitparams) = self::get_prohibit_check_sql($capability, $userid, 'maincontext.id');
        list($allowpreventsql, $allowpreventparams) = self::get_allow_prevent_check_sql($capability, $userid, 'maincontext.id');

        // They must have ALLOW in at least one role, and no prohibits in any role.
        $hascapsql = "
EXISTS (
    SELECT 'x'
      FROM {context} maincontext
     WHERE maincontext.id = {$contextidfield}

       AND EXISTS (
{$allowpreventsql}
                  )

       AND NOT EXISTS (
{$prohibitsql}
                      )
       )
";
        $hascapparams = array_merge($allowpreventparams, $prohibitparams);

        return array($hascapsql, $hascapparams);
    }

    /**
     * Use get_has_capability_sql() to emulate has_capability(),
     * this is intended mainly for testing purposes.
     *
     *
     * Note this still doesn't do all the other checks that the existing has_capability() function does,
     * for example role switching is completely ignored.
     *
     * @param string        $capability
     * @param \context      $context
     * @param int|\stdClass $user
     * @param bool          $doanything
     *
     * @return bool
     */
    public static function has_capability($capability, \context $context, $user = null, $doanything = true) {
        global $DB;

        list($hascapsql, $hascapparams) = self::get_has_capability_sql($capability, 'c.id', $user, $doanything);
        if ($hascapsql === "1=1") {
            return true;
        }
        if ($hascapsql === "1=0") {
            return false;
        }

        $sql = "SELECT 'x' FROM {context} c WHERE c.id = {$context->id} AND {$hascapsql}";
        $params = array_merge(array(), $hascapparams);

        return $DB->record_exists_sql($sql, $params);
    }

    /**
     * Returns the SQL for a subquery to obtain role assignments for a specific user.
     *
     * Most of these will come from the role_assignments table but we also need to take
     * into account automatically assigned roles e.g.:
     * - $CFG->defaultuserroleid
     * - $CFG->notloggedinroleid
     * - $CFG->guestroleid
     * - $CFG->defaultfrontpageroleid
     *
     * @param int $userid ID of the user to check permissions for.
     *
     * @return string sql fragment with embedded parameters
     */
    private static function get_role_assignments_subquery($userid) {
        global $CFG;

        $systemcontext = \context_system::instance();
        $userid = intval($userid);

        $queries = array();

        if ($userid == 0) {
            // Zero means a non-logged in user.
            if (!empty($CFG->notloggedinroleid)) {
                // Append the "not logged in role" in the system context.
                $notloggedinroleid = intval($CFG->notloggedinroleid);
                $queries[] = "                             SELECT {$notloggedinroleid} as roleid, {$systemcontext->id} AS contextid";
            }
        } else if (isguestuser($userid)) {
            // Guest account is login as guest allowed.
            if (!empty($CFG->guestroleid)) {
                // Append the "guest role" in the system context.
                $guestroleid = intval($CFG->guestroleid);
                $queries[] = "                             SELECT {$guestroleid} AS roleid, {$systemcontext->id} AS contextid";
            }
        } else {
            // Normal user.
            // Start with authenticated user role.
            if (!empty($CFG->defaultuserroleid)) {
                $defaultuserroleid = intval($CFG->defaultuserroleid);
                $queries[] = "                             SELECT {$defaultuserroleid} AS roleid, {$systemcontext->id} AS contextid";
            }

            // Authenticated user on front page role.
            if (!empty($CFG->defaultfrontpageroleid)) {
                $frontpagecontext = \context_course::instance(get_site()->id);
                $frontpageroleid = intval($CFG->defaultfrontpageroleid);
                $queries[] = "                             SELECT {$frontpageroleid} AS roleid, {$frontpagecontext->id} AS contextid";
            }

            // Add all real role assignments.
            $queries[] = "                             SELECT roleid, contextid FROM {role_assignments} ra WHERE ra.userid = {$userid}";
        }

        if ($queries) {
            // Join the SQL together.
            $sql = implode("\n                        UNION\n", $queries);
            return $sql;
        }

        // Return select with no results.
        return "SELECT NULL AS roleid, NULL AS contextid WHERE 1=0";
    }

    /**
     * Given an SQL field containing a context id, return an SQL snippet that returns
     * non-zero number of rows if the specified user is assigned any roles in that context which
     * grant them ALLOW permission on the specified capability. This takes into account
     * overrides by considering the most specific ALLOW or PREVENT permission.
     *
     * @param string $capability     A capability to check for.
     * @param int    $userid         ID of the user to check permissions for.
     * @param string $contextidfield Field linking to the context id in the original query.
     *
     * @return array Array of SQL and parameters that generate the query.
     */
    private static function get_allow_prevent_check_sql($capability, $userid, $contextidfield) {
        global $DB;

        $capallow = CAP_ALLOW;
        $capprevent = CAP_PREVENT;

        // Build role assignment subquery.
        $roleassignmentssql = self::get_role_assignments_subquery($userid);

        $paramcapability = $DB->get_unique_param('cap');
        if ($DB->get_dbfamily() === 'mysql') {
            // MySQL seems to be unable to do the aggregation with outside references.
            $mysqlhack = "AND maxdepth.childid = lineage.childid";
            $maxdepthsql = "
                  SELECT dlineage.parentid, dra.roleid, MAX(dctx.depth) AS depth, dlineage.childid
                    FROM {context_map} dlineage
                    JOIN (
{$roleassignmentssql}
                         ) dra ON dra.contextid = dlineage.parentid
                    JOIN {context_map} dctxmap ON dctxmap.childid = dlineage.childid
                    JOIN {role_capabilities} drc ON dra.roleid = drc.roleid AND drc.contextid = dctxmap.parentid
                         AND drc.capability = :{$paramcapability} AND (drc.permission = {$capallow} OR drc.permission = {$capprevent})
                    JOIN {context} dctx ON drc.contextid = dctx.id
                GROUP BY dlineage.parentid, dra.roleid, dlineage.childid
";
        } else {
            // This is probably the heaviest subquery, it might be worth exploring optimisation options later.
            $mysqlhack = "";
            $maxdepthsql = "
                  SELECT dlineage.parentid, dra.roleid, MAX(dctx.depth) AS depth
                    FROM {context_map} dlineage
                    JOIN (
{$roleassignmentssql}
                         ) dra ON dra.contextid = dlineage.parentid
                    JOIN {context_map} dctxmap ON dctxmap.childid = dlineage.childid
                    JOIN {role_capabilities} drc ON dra.roleid = drc.roleid AND drc.contextid = dctxmap.parentid
                         AND drc.capability = :{$paramcapability} AND (drc.permission = {$capallow} OR drc.permission = {$capprevent})
                    JOIN {context} dctx ON drc.contextid = dctx.id
                   WHERE dlineage.childid = {$contextidfield}
                GROUP BY dlineage.parentid, dra.roleid
";
        }
        $params = array($paramcapability => $capability);

        // Now wrap it all up in one query:
        // - expand lineage
        // - filter out less specific permissions
        // - remove prevents, leaving only most specific allows
        // - filter out permissions assigned below the level we are checking

        $paramcapability = $DB->get_unique_param('cap');
        $allowpreventsql = "
          SELECT 'x'
            FROM {context_map} lineage
            JOIN (
{$roleassignmentssql}
                 ) ra ON ra.contextid = lineage.parentid
            JOIN {context_map} ctxmap ON ctxmap.childid = lineage.childid
            JOIN {role_capabilities} rc ON ra.roleid = rc.roleid AND rc.contextid = ctxmap.parentid
                 AND rc.capability = :$paramcapability AND rc.permission = {$capallow}
            JOIN {context} ctx ON rc.contextid = ctx.id
            JOIN (
{$maxdepthsql}
                 ) maxdepth ON maxdepth.roleid = ra.roleid AND ctx.depth = maxdepth.depth AND maxdepth.parentid = lineage.parentid $mysqlhack
           WHERE lineage.childid = {$contextidfield}
";
        $params = array_merge($params, array($paramcapability => $capability));

        return array($allowpreventsql, $params);
    }

    /**
     * Given an SQL field containing a context id, return an SQL snippet that returns
     * non-zero number of rows if the specified user is assigned any roles in that context which
     * specifies the PROHIBIT permission on the specified capability.
     *
     * @param string $capability     A capability to check for.
     * @param int    $userid         ID of the user to check permissions for.
     * @param string $contextidfield Field linking to the context id in the original query.
     *
     * @return array Array of SQL and parameters that generate the query.
     */
    private static function get_prohibit_check_sql($capability, $userid, $contextidfield) {
        global $DB;

        // Build role assignment subquery.
        $roleassignmentssql = self::get_role_assignments_subquery($userid);

        $prohibit = CAP_PROHIBIT;

        $paramcapability = $DB->get_unique_param('cap');
        $prohibitsql = "
            SELECT 'x'
              FROM {context_map} lineage
              JOIN (
{$roleassignmentssql}
                   ) ra ON ra.contextid = lineage.parentid
              JOIN {context_map} ctxmap ON ctxmap.childid = lineage.childid
              JOIN {role_capabilities} rc ON ra.roleid = rc.roleid AND rc.contextid = ctxmap.parentid
                   AND rc.capability = :$paramcapability AND rc.permission = {$prohibit}
             WHERE lineage.childid = {$contextidfield}
";
        $params = array($paramcapability => $capability);
        return array($prohibitsql, $params);
    }

    /**
     * Populate the context map table with the latest data from context table.
     *
     * Note this function includes a direct mapping between the item and itself in addition
     * to each parent child relation. If you want parents only you can exclude this but in
     * most cases you want the full context path.
     *
     * NOTE: this may be extremely slow on large installations
     */
    public static function build_context_map() {
        global $DB;

        // Clear out any contexts that no longer apply.
        $deletesql = "DELETE FROM {context_map}
            WHERE NOT EXISTS (
                SELECT 1
                    FROM {context} child
                    JOIN {context} parent
                         ON parent.id = child.id OR child.path LIKE " . $DB->sql_concat('parent.path', "'/%'") . "
                   WHERE {context_map}.parentid = parent.id AND {context_map}.childid = child.id
                )";
        $DB->execute($deletesql);

        // Add missing map entries.
        self::add_missing_map_entries();
    }

    /**
     * Add missing map entries.
     */
    public static function add_missing_map_entries() {
        global $DB;

        // Add mappings that don't already exist.
        $sql = "SELECT parent.id AS parentid, child.id AS childid
                  FROM {context} child
                  JOIN {context} parent
                       ON parent.id = child.id OR child.path LIKE " . $DB->sql_concat('parent.path', "'/%'") . "
             LEFT JOIN {context_map} map
                       ON map.parentid = parent.id AND map.childid = child.id
                 WHERE map.id IS NULL";

        $DB->execute("INSERT INTO {context_map} (parentid, childid) {$sql}");
    }

    /**
     * To be called from \context::update_moved() only.
     *
     * @internal
     * @param \stdClass $record
     */
    public static function context_moved(\stdClass $record) {
        global $DB;

        // Delete own context entries.
        $DB->delete_records('context_map', array('childid' => $record->id));

        if (!trim($record->path, '/')) {
            // This should not happen, admin will have to do a full rebuild from CLI later.
            return;
        }

        // Delete entries for all children.
        $sql = "DELETE
                  FROM {context_map}
                 WHERE {context_map}.childid IN (
                      SELECT id
                        FROM {context}
                       WHERE path LIKE :path
                 )";
        $params = array('path' => $record->path . '/%');
        $DB->execute($sql, $params);

        // Add all entries back.
        self::add_missing_map_entries();
    }

    /**
     * To be called only from \context::insert_context_record() only.
     *
     * @internal
     * @param \stdClass $record
     */
    public static function context_created(\stdClass $record) {
        global $DB;

        // There should not be any map entries, but make sure we do not create duplicates by deleting first.
        $DB->delete_records('context_map', array('childid' => $record->id));

        $parents = trim($record->path, '/');
        if (!$parents) {
            // This should not happen, admin will have to do a full rebuild from CLI later.
            return;
        }

        $parents = explode('/', $parents);

        $records = array();
        foreach ($parents as $parent) {
            $records[] = array('parentid' => $parent, 'childid' => $record->id);
        }
        $DB->insert_records('context_map', $records);
    }

    /**
     * To be called only from \context::delete() only.
     *
     * @internal
     * @param int $contextid
     */
    public static function context_deleted($contextid) {
        global $DB;

        $DB->delete_records('context_map', array('childid' => $contextid));
    }
}
