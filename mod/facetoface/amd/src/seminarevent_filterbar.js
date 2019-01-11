/*
 * This file is part of Totara LMS
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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_facetoface
 */


define([], function () {
    var ATTR_INIT_VALUE = 'data-webkit-init-value';
    var filter = {
        init: function (root) {
            /**
             * Reset a <select>.value if it is not.
             * @param {HTMLSelectElement} el <select> element
             */
            function initialSelection (el) {
                if (el.hasAttribute(ATTR_INIT_VALUE)) {
                    var val = el.getAttribute(ATTR_INIT_VALUE);
                    if (val !== el.value) {
                        el.value = val;
                    }
                }
            }
            // restore the initial selection for Safari
            window.addEventListener(
                'pageshow',
                function () {
                    Array.prototype.forEach.call(
                        root.getElementsByTagName('select'),
                        initialSelection
                    );
                }
            );
            Array.prototype.forEach.call(
                root.getElementsByTagName('select'),
                function (el) {
                    // restore the initial selection for Chrome
                    initialSelection(el);

                    el.addEventListener('change', function (e) {
                        e.target.closest('form').submit();
                    });
                }
            );
        }
    };
    return filter;
});
