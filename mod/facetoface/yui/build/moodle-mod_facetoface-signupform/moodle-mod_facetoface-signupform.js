YUI.add('moodle-mod_facetoface-signupform', function (Y, NAME) {

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
 * @author Andrew Hancox <andrewdchancox@googlemail.com>
 * @package totara
 * @subpackage facetoface
 */

M.mod_facetoface = M.mod_facetoface || {};
M.mod_facetoface.signupform = {
    /** Selectors. */
    SELECTORS: {
        TSANDCS:             '.tsandcs'
    },

    opts: {},

    init: function() {
        var body = Y.one('body');
        M.mod_facetoface.signupform.opts = arguments;
        // Can't attach handler to body as we need to stop default action.
        Y.all(M.mod_facetoface.signupform.SELECTORS.TSANDCS).each(function(node) {
            node.on("click", M.mod_facetoface.signupform.showTsandCs);
        });
    },

    showTsandCs : function(e) {
        e.preventDefault();
        var content;

        if (typeof(M.mod_facetoface.signupform.opts[0]) == 'string') {
            content = M.mod_facetoface.signupform.opts[0];
        } else {
            facetofaceid = e.target.getAttribute('facetofaceid');
            content = M.mod_facetoface.signupform.opts[0]['selfapprovaltandc_' + facetofaceid];
        }

        // Set Default options
        var params = {
            bodyContent : content,
            headerContent : M.util.get_string('selfapprovaltandc', 'mod_facetoface'),
            visible : true,
            lightbox : true // This dialogue should be modal
        };

        // Create the panel
        tsandcsdlg = new M.core.dialogue(params);

        tsandcsdlg.addButton({
            label: M.util.get_string('close', 'mod_facetoface'),
            action: function(e) {
                e.preventDefault();
                tsandcsdlg.destroy();
            },
            section: Y.WidgetStdMod.FOOTER
        });
    }
};

}, '@VERSION@', {"requires": ["base", "node", "moodle-core-notification-dialogue", "moodle-core-notification-alert"]});
