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
    const ATTR_INIT_VALUE = 'data-webkit-init-value';
    const ATTR_SHOW_TOOLTIPS = 'data-show-tooltips';
    var filter = {
        init: function (root) {
            root.classList.add('mod_facetoface__filter--active');
            /**
             * Execute Array.forEach for <select> elements in the root element.
             * @param {Function} callback A callback function that is passed to the forEach method.
             */
            function forEachSelectElement (callback) {
                Array.prototype.forEach.call(
                    root.getElementsByTagName('select'),
                    callback
                );
            }
            /**
             * Restore the correct <select>.value if it is not.
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
            /**
             * Update a tooltip text i.e. the title attribute.
             * @param {HTMLSelectElement} el <select> element
             */
            function updateTooltip(el) {
                if (el.getAttribute(ATTR_SHOW_TOOLTIPS) == 'true') {
                    var selectedText = '';
                    var index = el.selectedIndex;
                    if (0 < index && index < el.options.length) {
                        selectedText = el.options[index].text;
                    }
                    el.setAttribute('title', selectedText);
                }
            }
            window.addEventListener(
                'pageshow',
                function () {
                    forEachSelectElement(
                        function (el) {
                            // restore the initial selection for Safari
                            initialSelection(el);
                            // restore the tooltip text for Safari
                            updateTooltip(el);
                            // enable filter when the page is displayed
                            el.disabled = false;
                        }
                    );
                }
            );
            forEachSelectElement(
                function (el) {
                    // restore the initial selection for Chrome
                    initialSelection(el);
                    // restore the tooltip text for all web browsers
                    updateTooltip(el);
                    el.addEventListener('change', function (e) {
                        e.target.closest('form').submit();
                        forEachSelectElement(
                            function (el) {
                                el.disabled = true;
                            }
                        );
                    });
                    // enable filter when the page is displayed
                    el.disabled = false;
                }
            );
        }
    };
    return filter;
});
