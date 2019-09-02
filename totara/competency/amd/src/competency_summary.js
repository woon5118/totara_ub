/**
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

define([], function() {

    /**
     * Class constructor for the CompetencySummary.
     *
     * @class
     * @constructor
     */
    function CompetencySummary() {
        if (!(this instanceof CompetencySummary)) {
            return new CompetencySummary();
        }

        this.expandedClass = 'tw-compSummary__section_expanded';
        this.hiddenClass = 'tw-compSummary__hidden';
        this.widget = '';
    }

    CompetencySummary.prototype = {

        /**
         * Add event listeners
         *
         */
        events: function() {
            var that = this;

            this.widget.addEventListener('click', function(e) {
                if (!e.target) {
                    return;
                }

                if (e.target.closest('[data-tw-compSummary-expand]')) {
                    e.preventDefault();

                    var expandNode = e.target.closest('[data-tw-compSummary-expand]');
                    that.toggleExpandView(expandNode);
                }
            });
        },

        /**
         * Set parent
         *
         * @param {node} parent
         */
        setParent: function(parent) {
            this.widget = parent;
        },

        /**
         * Toggle expanded view
         *
         * @param {node} node
         */
        toggleExpandView: function(node) {
            var expanded,
                icons = node.querySelectorAll('[data-tw-compSummary-expand-icon]'),
                section = node.closest('[data-tw-compSummary-section]'),
                sectionBody = section.querySelector('[data-tw-compSummary-section-body]');

            // Toggle section expanded class
            section.classList.toggle(this.expandedClass);
            expanded = section.classList.contains(this.expandedClass);

            // Toggle icons
            for (var i = 0; i < icons.length; i++) {
                icons[i].classList.toggle(this.hiddenClass);
            }

            // Toggle aria tag
            node.setAttribute('aria-expanded', expanded);
            sectionBody.setAttribute('aria-hidden', !expanded);
        },
    };

    /**
     * Initialisation method
     *
     * @param {node} parent
     * @returns {Object} promise
     */
    var init = function(parent) {
        return new Promise(function(resolve) {
            var wgt = new CompetencySummary();
            wgt.setParent(parent);
            wgt.events();
            resolve(wgt);
        });
    };

    return {
        init: init
    };
 });