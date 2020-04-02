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
 * @package criteria_linkedcourses
 */

define(['core/ajax', 'core/notification', 'totara_competency/loader_manager'],
function(ajax, notification, Loader) {

    /**
     * Class constructor for CriterionLinkedCourses.
     *
     * @class
     * @constructor
     */
    function CriterionLinkedCourses() {
        if (!(this instanceof CriterionLinkedCourses)) {
            return new CriterionLinkedCourses();
        }

        this.widget = ''; // Parent widget
        this.loader = null; // Loading overlay manager

        /**
         * Criterion data.
         * This object should only contain the data to be sent on the save api endpoint.
         * The variable names MUST correlate to the save endpoint parameters
         */
        this.criterion = {
            type: 'linkedcourses',
            metadata: [],
            aggregation: {},
        };

        this.criterionKey = '';  // Unique key to use in bubbled event
        this.competencyKey = 'competency_id'; // Metadata key for competency id

        this.endpoints = {
            detail: 'criteria_linkedcourses_get_detail',
        };

        this.domClasses = {
            hidden: 'tw-criterion-hidden',
        };

        this.fileName = 'linkedcourses.js';
    }

    CriterionLinkedCourses.prototype = {

        /**
         * Add event listeners for CriterionLinkedCoursess
         */
        events: function() {
            var that = this;

            this.widget.addEventListener('change', function(e) {
                if (!e.target) {
                    return;
                }

                if (e.target.closest('[data-tw-criterionLinkedCourses-aggregationMethod-changed]')) {
                    e.preventDefault();

                    var newMethod = e.target.closest('[data-tw-criterionLinkedCourses-aggregationMethod-changed]').value;

                    if (that.criterion.aggregation.method !== newMethod) {
                        that.setAggregationMethod(newMethod);

                        that.triggerEvent('update', {criterion: that.criterion});
                        that.triggerEvent('dirty', {});
                    }

                // Aggregation count changed
                } else if (e.target.closest('[data-tw-criterionLinkedCourses-aggregationCount-changed]')) {
                    e.preventDefault();

                    var newCount = e.target.closest('[data-tw-criterionLinkedCourses-aggregationCount-changed]').value;

                    if (that.criterion.aggregation.reqitems != newCount) {
                        that.setAggregationCount(newCount);

                        that.triggerEvent('update', {criterion: that.criterion});
                        that.triggerEvent('dirty', {});
                    }
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
         * Retrieve the criterion detail
         * @return {Promise}
         */
        getDetail: function() {
            var that = this,
                criterionNode = this.widget.closest('[data-tw-criterion-key]'),
                key = 0,
                id = 0,
                detailPromise,
                apiArgs;

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
                    that.criterion.metadata = instance.metadata;
                    that.criterionKey = key;

                    that.showHideConfigurationError(instance.error);

                    // Aggregation
                    that.setAggregationMethod(instance.aggregation.method);
                    that.setAggregationCount(instance.aggregation.reqitems);

                    that.triggerEvent('update', {criterion: that.criterion});
                    resolve();
                }).catch(function(e) {
                    e.fileName = that.filename;
                    e.name = 'Error retrieving detail';
                    notification.exception(e);
                });
            });
        },

        /**
         * Create an empty linkedcourses criterion
         * @return {Promise}
         */
        createEmptyCriterion: function() {
            var that = this,
                compIdWgt = document.querySelector('[data-comp-id]'),
                compId = 1;

            if (compIdWgt) {
                compId = compIdWgt.getAttribute('data-comp-id') ? compIdWgt.getAttribute('data-comp-id') : 1;
            }

            return new Promise(function(resolve) {
                resolve({
                    results: {
                        id: 0,
                        metadata: [
                            {
                                metakey: that.competencyKey,
                                metavalue: compId
                            }
                        ],
                        aggregation:{
                            method: 1,
                            reqitems: 1
                        }
                    }
                });
            });
        },

        /**
         * Show or hide the configuration error warning
         */
        showHideConfigurationError: function(theError) {
            var target = this.widget.querySelector('[data-tw-criterionLinkedCourses-error]');
            if (!target) {
                return;
            }

            if (theError) {
                // Show the warning
                target.classList.remove(this.domClasses.hidden);
            } else {
                target.classList.add(this.domClasses.hidden);
            }
        },

        /**
         * Set the aggregation method
         *
         * @param {int} method New aggregation method
         */
        setAggregationMethod: function(method) {
            var methodNode = this.widget.querySelector('[data-tw-criterionLinkedCourses-aggregationMethod="' + method + '"]'),
                methodInput = methodNode.querySelector('[data-tw-criterionLinkedCourses-aggregationMethod-changed]'),
                countInput = this.widget.querySelector('[data-tw-criterionLinkedCourses-aggregationCount-changed]'),
                testCountInput = methodNode.querySelector('[data-tw-criterionLinkedCourses-aggregationCount-changed]');

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
         * Set the aggregation reqItems
         *
         * @param {int} reqItems Required item count
         */
        setAggregationCount: function(reqItems) {
            var countInput = this.widget.querySelector('[data-tw-criterionLinkedCourses-aggregationCount-changed]');

            this.criterion.aggregation.reqitems = reqItems;

            if (countInput) {
                countInput.value = reqItems;
            }
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
            var wgt = new CriterionLinkedCourses();
            wgt.setParent(parent);
            wgt.events();
            wgt.loader = Loader.init(parent);
            wgt.loader.show();
            resolve(wgt);

            M.util.js_pending('criterionLinkedCourses');
            wgt.getDetail().then(function() {
                wgt.loader.hide();
                M.util.js_complete('criterionLinkedCourses');
            });
        });
    };

    return {
        init: init
    };
 });
