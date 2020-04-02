/**
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
 * @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
 * @package totara_core
 */

 /**
  * Handles the select region panel visibility toggle event for select region panel test page
  */
define([], function() {

    /**
     * Class constructor for the TestSelectRegionToggle
     *
     * @class
     * @constructor
     */
    function TestSelectRegionToggle() {
        if (!(this instanceof TestSelectRegionToggle)) {
            return new TestSelectRegionToggle();
        }
    }

    // Listen for propagated events
    TestSelectRegionToggle.prototype.registerEventListeners = function() {
        document.addEventListener('totara_core/select_region_panel_toggle:changed', function(e) {
            var target = document.querySelector(e.detail.target);
            target.classList.toggle(e.detail.toggleClass);
        });
    };

    /**
    * widget initialisation method
    *
    * @returns {Promise}
    */
    var init = function() {
        return new Promise(function(resolve) {
            // Create an instance of widget
            var wgt = new TestSelectRegionToggle();
            wgt.registerEventListeners();
            resolve(wgt);
        });
    };

    return {
        init: init
    };
});