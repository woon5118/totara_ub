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


define([], function() {
    const CLASS_TOGGLE = '.mod_facetoface__eventinfo__sidebars__toggle';

    return {
        /**
         * Initialisation method
         *
         * @param {HTMLElement} root
         * @returns {Promise}
         */
        init: function(root) {
            /**
             * Update ARIA attributes.
             * @param {HTMLInputElement} el
             */
            function update(el) {
                var hiddenOn = el.checked.toString();
                var hiddenOff = (!el.checked).toString();
                document.getElementById(el.getAttribute('data-id-on')).setAttribute('aria-hidden', hiddenOn);
                document.getElementById(el.getAttribute('data-id-off')).setAttribute('aria-hidden', hiddenOff);
            }
            return new Promise(function(resolve) {
                root.addEventListener('change', function(event) {
                    var element = event.target.closest(CLASS_TOGGLE);
                    if (element !== null) {
                        update(element);
                    }
                });
                Array.prototype.forEach.call(root.querySelectorAll(CLASS_TOGGLE), update);
                resolve(true); // Nothing interesting to return.
            });
        }
    };
});
