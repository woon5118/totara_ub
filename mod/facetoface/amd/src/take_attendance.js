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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package mod_facetoface
 */
define([], function () {
    var listeners = {
        export_to_file: function (args) {
            var element = document.getElementById('menuf2f-export-actions');
            element.addEventListener('change', function () {
                var value = this.options[this.selectedIndex].value;
                if (value == "") {
                    // As this is a default option, and it should not be changing the destination
                    // of the site.
                    return;
                }

                window.location.href =
                    args.url + "?s=" + args.sessionid
                    + "&sd=" + args.sessiondateid
                    + "&onlycontent=1&download=" + value.substr(6);

                // After exporting, we should move back the selection to the default value here.
                this.selectedIndex = 0;
            });
        }
    };

    function init(element) {
        var arguments = {
            sessionid: element.dataset.sessionid,
            sessiondateid: element.dataset.sessiondateid,
            url: element.dataset.url
        };

        listeners.export_to_file(arguments);
    }


    return {
        init: init
    };
});