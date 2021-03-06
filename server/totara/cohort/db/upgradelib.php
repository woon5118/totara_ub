<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2014 onwards Totara Learning Solutions LTD
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
 * @author David Curry <david.curry@toraralearning.com>
 * @package totara_cohort
 */

/**
 * This function moves all instances of a specified rule to a new rule, note that
 * there is no validation on the type/name combination.
 *
 * @param string $oldtype   The cohort_rules.ruletype value for existing rules
 * @param string $oldname   The cohort_rules.name value for existing rules
 * @param string $newtype   The new ruletype value for the rules
 * @param string $newname   The new name value for the rules
 *
 * @return boolean
 */
function totara_cohort_migrate_rules($oldtype, $oldname, $newtype, $newname) {
    global $DB;

    if ($oldtype == $newtype && $oldname == $newname) {
        // Well that was easy.
        return true;
    }

    // Migrate the rules to the new type.
    $sqlruletype = "UPDATE {cohort_rules}
                       SET ruletype = :newtype,
                           name = :newname
                     WHERE ruletype = :oldtype
                       AND name = :oldname";

    $paramstype = array(
        'newtype' => $newtype,
        'newname' => $newname,
        'oldtype' => $oldtype,
        'oldname' => $oldname
    );

    $DB->execute($sqlruletype, $paramstype);

    return true;
}

/**
 * This function update the "cohort_rule_params" records where the rule is "Has direct reports" and
 * param is equal to "Does not have direct reports" to reflect with new conditions
 */
function totara_cohort_update_has_direct_reports_rule() {
    global $DB;

    $rules  = $DB->get_records('cohort_rules', ['name' => 'hasdirectreports'], '', 'id');
    $ids = [];
    foreach ($rules as $rule) {
        $ids[] = $rule->id;
    }
    if (!empty($ids)) {
        list($sqlin, $params) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'rsid');
        $sql = "SELECT chp.ruleid
              FROM {cohort_rule_params} chp
             WHERE chp.name = 'listofvalues'
               AND chp.value = '0'
               AND chp.ruleid {$sqlin}";
        $ruleparams = $DB->get_records_sql($sql, $params);
        $ids = [];
        foreach ($ruleparams as $data) {
            $ids[] = $data->ruleid;
        }
        if (!empty($ids)) {
            list($sqlin, $params) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'rps');
            $sql = "UPDATE {cohort_rule_params} SET value = '0' WHERE name = 'equal' AND ruleid {$sqlin}";
            $DB->execute($sql, $params);
        }
    }
    return true;
}