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
 * @author Marco Song <marco.song@totaralearning.com>
 * @package criteria_othercompetency
 */

define(['core/templates', 'core/notification', 'core/ajax', 'totara_core/modal_list', 'totara_core/loader_manager'],
    function(templates, notification, ajax, ModalList, Loader) {

        /**
         * Class constructor for CriterionOtherCompetency.
         *
         * @class
         * @constructor
         */
        function CriterionOtherCompetency() {
            if (!(this instanceof CriterionOtherCompetency)) {
                return new CriterionOtherCompetency();
            }

            this.widget = ''; // Parent widget
            this.loader = null; // Loading overlay manager

            /**
             * Criterion data.
             * This object should only contain the data to be sent on the save api endpoints.
             * The variable names MUST correlate to the save endpoint parameters
             */
            this.criterion = {
                type: 'othercompetency',
                aggregation: {},
                itemids: [],
            };

            // Saving items from the basket - therefore not stored in criterion
            this.criterionKey = ''; // Unique key to use in bubbled events

            this.endpoints = {
                detail: 'criteria_othercompetency_get_detail',
            };

            this.domClasses = {
                hidden: 'crit_hidden',
            };

            this.filename = 'othercompetency.js';
        }

        CriterionOtherCompetency.prototype = {
            /**
             * Set parent
             * @param {node} parent
             */
            setParent: function(parent) {
                this.widget = parent;
            },

            /**
             * Retrieve the criterion detail
             * @return {Promise}
             */
            getDetail: function() {
                var that = this,
                    criterionNode = this.widget.closest('[data-tw-criterion-key]'),
                    key = 0,
                    id = 0,
                    apiArgs,
                    detailPromise = null;

                return new Promise(function(resolve) {
                    if (criterionNode) {
                        key = criterionNode.hasAttribute('data-tw-criterion-key') ? criterionNode.getAttribute('data-tw-criterion-key') : 0;
                        id = criterionNode.hasAttribute('data-tw-criterion-id') ? criterionNode.getAttribute('data-tw-criterion-id') : 0;
                    }

                    if (id == 0) {
                        // New criterion - no detail yet
                        detailPromise = new Promise(function(resolve) {
                            resolve(that.createEmptyCriterion());
                        });

                    } else {
                        apiArgs = {
                            args: {id: id},
                            methodname: that.endpoints.detail
                        };

                        detailPromise = ajax.getData(apiArgs);
                    }

                    detailPromise.then(function(responses) {
                        var instance = responses.results;

                        // We want only the data required for saving in that.criterion
                        // Not doing this earlier to prevent setting criterion attributes if
                        // something went wrong (e.g. invalid id, etc.)
                        that.criterion.id = id;
                        that.criterionKey = key;

                        // Aggregation
                        that.setAggregationMethod(instance.aggregation.method);
                        that.setAggregationCount(instance.aggregation.reqitems);

                        Promise.all([]).then(function() {
                            that.triggerEvent('update', {criterion: that.criterion});
                            resolve();
                        });
                    }).catch(function(e) {
                        e.fileName = that.filename;
                        e.name = 'Error retrieving detail';
                        notification.exception(e);
                    });
                });
            },

            /**
             * Set the aggregation method
             * @param {int} method New aggregation method (method constant as defined in totara/criteria/classes/criterion.php)
             */
            setAggregationMethod: function(method) {
                var methodNode = this.widget.querySelector('[data-tw-criterionOtherCompetency-aggregationMethod="' + method + '"]'),
                    methodInput = methodNode.querySelector('[data-tw-criterionOtherCompetency-aggregationMethod-changed]'),
                    countInput = this.widget.querySelector('[data-tw-criterionOtherCompetency-aggregationCount-changed]'),
                    testCountInput = methodNode.querySelector('[data-tw-criterionOtherCompetency-aggregationCount-changed]');

                this.criterion.aggregation.method = method;

                if (methodInput) {
                    methodInput.checked = true;
                }

                if (countInput) {
                    // To avoid hardcoding that method 1 == all, etc, we use the fact that the reqItems input
                    // is in the same div as the 'any' radio button to determine whether to disable or enable
                    // the count input
                    countInput.disabled = testCountInput ? false : true;
                }
            },

            /**
             * Set the aggregation required item
             * @param {int} reqItems Required item count
             */
            setAggregationCount: function(reqItems) {
                var countInput = this.widget.querySelector('[data-tw-criterionOtherCompetency-aggregationCount-changed]');

                this.criterion.aggregation.reqitems = reqItems;

                if (countInput) {
                    countInput.value = reqItems;
                }
            },

            /**
             * Create an empty othercompetency criterion instance with the key
             *
             * @param {int} key
             * @return {Promise}
             */
            createEmptyCriterion: function() {
                // Ensure the basket is empty
                return new Promise(function(resolve) {
                    resolve({
                        results: {
                            id: 0,
                            itemids: [],
                            aggregation: {
                                method: 1,
                                reqitems: 1
                            }
                        }
                    });
                });
            },

            /**
             * Trigger event
             *
             * @param {string} eventName
             * @param {object} data
             */
            triggerEvent: function(eventName, data) {
                data.key = this.criterionKey;

                var propagateEvent = new CustomEvent('totara_criteria/criterion:' + eventName, {
                    bubbles: true,
                    detail: data
                });

                this.widget.dispatchEvent(propagateEvent);
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
                var wgt = new CriterionOtherCompetency();
                wgt.setParent(parent);
                wgt.loader = Loader.init(parent);
                wgt.loader.show();
                resolve(wgt);

                M.util.js_pending('criterionOtherCompetency');
                wgt.getDetail().then(function() {
                    wgt.loader.hide();
                    M.util.js_complete('criterionOtherCompetency');
                });
            });
        };

        return {
            init: init
        };
    });
