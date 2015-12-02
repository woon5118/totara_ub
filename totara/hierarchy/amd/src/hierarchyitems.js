/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @author Brian Barnes <brian.barnes@totaralms.com>
 * @package totara_hierarchy
 */

/**
 * This file defines the functionality of the mygoals page
 */
define(['jquery', 'core/config'], function($, mdlconfig) {
    var hierarchy = {
        /**
         * Setup for linking competencies and courses
         *
         * @param prefix string the hierarchy type to work with
         */
        init: function (prefix) {
            $(document).on('change', '.list-assignedcompetencies .menulinktype, #list-evidence .menulinktype, #list-coursecompetency .menulinktype', function () {
                hierarchy._update_linktype(prefix, $(this).data('id'), $(this).val());
            });
        },

        /**
         * Updates the link type
         *
         * @param prefix string the type of hierarchy to work with
         * @param id int The id of the hierarchy item to work with
         * @param val int the value to change it to
         */
        _update_linktype: function (prefix, id, val) {
            var params = {
                type: prefix,
                c: id,
                sesskey: mdlconfig.sesskey,
                t: val
            };
            $.get(mdlconfig.wwwroot + '/totara/plan/update-linktype.php', params);
        }
    };

    return hierarchy;
});