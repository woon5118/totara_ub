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

/* global $, totaraSingleSelectDialog */

M.totara_seminar_facilitator = M.totara_seminar_facilitator || {

    Y: null,
    config: {},

    /**
     * module initialisation method called by php js_init_call()
     * @param object YUI instance
     * @param array args
     */
    init: function(Y, args) {
        this.Y = Y;
        this.config = args;
        // Check jQuery dependency is available
        if (typeof $ === 'undefined') {
            throw new Error('M.totara_seminar_facilitator.init()-> jQuery dependency required for this module to function.');
        }

        this.checkuseraddvisibility();
        $('select[name="facilitatortype"]').change(M.totara_seminar_facilitator.checkuseraddvisibility);

        var show_warning =  function() {
            if (M.totara_seminar_facilitator.config.userid != $('input[name="userid"]').val()) {
                var notificationHolder = $('#user-notifications');
                if (!notificationHolder) {
                    return;
                }
                notificationHolder.append(M.totara_seminar_facilitator.config.warningblock);
            }
        };

        (function() {
            var url = M.cfg.wwwroot+'/mod/facetoface/facilitator/ajax/users.php?userid=';
            totaraSingleSelectDialog(
                'facilitator',
                M.util.get_string('choosefacilitator', 'mod_facetoface') + M.totara_seminar_facilitator.config.dialog_display_facilitator,
                url + M.totara_seminar_facilitator.config.userid,
                'userid',
                'facilitatortitle',
                show_warning,
                M.totara_seminar_facilitator.config.can_edit
            );
        })();
    },

    checkuseraddvisibility: function() {
        var option = $('select[name="facilitatortype"] option:selected');
        if (option.val() == '0') {
            $('#show-facilitator-dialog').attr("disabled", false);
        } else {
            $('#show-facilitator-dialog').attr("disabled", true);
            $('a.dialog-singleselect-deletable').click();
        }
    },
};
