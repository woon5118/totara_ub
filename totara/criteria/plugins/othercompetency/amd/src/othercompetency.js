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

define(['core/templates', 'core/notification', 'core/ajax', 'totara_competency/modal_list', 'totara_competency/loader_manager',
'totara_competency/list_framework_hierarchy_events'],
    function (templates, notification,ajax, ModalList, Loader,HierarchyEvents) {

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
            this.competencies = []; // other competencies to complete
            this.competencyAdder = null; // Initialized Adder for other competencies

            this.endpoints = {
                detail: 'criteria_othercompetency_get_detail',
                competencies: 'totara_competency_competency_index',
            };

            this.domClasses = {
                hidden: 'tw-criterion-hidden',
            };

            this.filename = 'othercompetency.js';
        }

        CriterionOtherCompetency.prototype = {
            /**
             * Add event listeners for CriterionOtherCompetency
             */
            events: function () {
                var that = this;

                this.widget.addEventListener('click', function (e) {
                    if (!e.target) {
                        return;
                    }

                    // Add competencies link clicked
                    if (e.target.closest('[data-tw-criterionOtherCompetency-addCompetencies]')) {
                        e.preventDefault();
                        that.addCompetencies();

                        // Item remove link clicked
                    } else if (e.target.closest('[data-tw-criterionOtherCompetency-item-remove]')) {
                        e.preventDefault();

                        var competencyNode = e.target.closest('[data-tw-criterionOtherCompetency-item-value]'),
                            competencyId;

                        if (!competencyNode) {
                            return;
                        }

                        competencyId = competencyNode.getAttribute('data-tw-criterionOtherCompetency-item-value');
                        that.removeCompetency(competencyId);
                    }
                });

                this.widget.addEventListener('change', function (e) {
                    if (!e.target) {
                        return;
                    }

                    // Aggregation method changed
                    if (e.target.closest('[data-tw-criterionOtherCompetency-aggregationMethod-changed]')) {
                        e.preventDefault();

                        var newMethod = e.target.closest('[data-tw-criterionOtherCompetency-aggregationMethod-changed]').value;

                        if (that.criterion.aggregation.method != newMethod) {
                            that.setAggregationMethod(newMethod);

                            that.triggerEvent('update', {criterion: that.criterion});
                            that.triggerEvent('dirty', {});
                        }

                        // Aggregation count changed
                    } else if (e.target.closest('[data-tw-criterionOtherCompetency-aggregationCount-changed]')) {
                        e.preventDefault();

                        var newCount = e.target.closest('[data-tw-criterionOtherCompetency-aggregationCount-changed]').value;

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
             * @param {node} parent
             */
            setParent: function (parent) {
                this.widget = parent;
            },

            /**
             * Retrieve the criterion detail
             * @return {Promise}
             */
            getDetail: function () {
                var that = this,
                    criterionNode = this.widget.closest('[data-tw-criterion-key]'),
                    key = 0,
                    id = 0,
                    apiArgs,
                    detailPromise = null;

                return new Promise(function (resolve) {
                    if (criterionNode) {
                        key = criterionNode.hasAttribute('data-tw-criterion-key') ? criterionNode.getAttribute('data-tw-criterion-key') : 0;
                        id = criterionNode.hasAttribute('data-tw-criterion-id') ? criterionNode.getAttribute('data-tw-criterion-id') : 0;
                    }

                    if (id == 0) {
                        // New criterion - no detail yet
                        detailPromise = new Promise(function (resolve) {
                            resolve(that.createEmptyCriterion());
                        });

                    } else {
                        apiArgs = {
                            args: {id: id},
                            methodname: that.endpoints.detail
                        };

                        detailPromise = ajax.getData(apiArgs);
                    }

                    detailPromise.then(function (responses) {
                        var instance = responses.results;

                        // We want only the data required for saving in that.criterion
                        // Not doing this earlier to prevent setting criterion attributes if
                        // something went wrong (e.g. invalid id, etc.)
                        that.criterion.id = id;
                        that.criterionKey = key;

                        // Aggregation
                        that.setAggregationMethod(instance.aggregation.method);
                        that.setAggregationCount(instance.aggregation.reqitems);

                        Promise.all([that.setCompetencies(instance.items), that.initCompetencyAdder()]).then(function () {
                            that.triggerEvent('update', {criterion: that.criterion});
                            resolve();
                        });
                    }).catch(function (e) {
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
            setAggregationMethod: function (method) {
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
            setAggregationCount: function (reqItems) {
                var countInput = this.widget.querySelector('[data-tw-criterionOtherCompetency-aggregationCount-changed]');

                this.criterion.aggregation.reqitems = reqItems;

                if (countInput) {
                    countInput.value = reqItems;
                }
            },

            /**
             * Set and display the competencies to be completed
             *
             * @param {Object} competencies Array of competencies
             * @return {Promise}
             */
            setCompetencies: function (competencies) {
                var that = this,
                    competenciesTarget = this.widget.querySelector('[data-tw-criterionOtherCompetency-competencies]'),
                    competenciesPromiseArr = [],
                    templateData = {};

                return new Promise(function (resolve) {
                    // We want to index the items with the ids for easy adder results processing
                    that.competencies = [];
                    that.criterion.itemids = [];

                    if (competencies.length == 0 || !competenciesTarget) {
                        that.showHideNotEnoughCompetency();
                        resolve();
                    } else {
                        for (var a = 0; a < competencies.length; a++) {
                            that.competencies[competencies[a].id] = competencies[a];
                            that.criterion.itemids.push(competencies[a].id);
                            templateData = {item_parent: 'criterionOtherCompetency', value: competencies[a].id, text: competencies[a].name};
                            if (competencies[a].error) {
                                templateData.error = competencies[a].error;
                            }
                            competenciesPromiseArr.push(templates.renderAppend('totara_criteria/partial_item', templateData, competenciesTarget));
                        }

                        Promise.all(competenciesPromiseArr).then(function () {
                            that.showHideNotEnoughCompetency();
                            resolve();
                        }).catch(function (e) {
                            e.fileName = that.filename;
                            e.name = 'Error showing competencies';
                            notification.exception(e);
                        });
                    }
                });
            },

            /**
             * Create an empty othercompetency criterion instance with the key
             *
             * @param {int} key
             * @return {Promise}
             */
            createEmptyCriterion: function () {
                // Ensure the basket is empty
                return new Promise(function (resolve) {
                    resolve({
                        results: {
                            id: 0,
                            items: [],
                            aggregation: {
                                method: 1,
                                reqitems: 1
                            }
                        }
                    });
                });
            },

            /**
             * Initialise the competency adder
             *
             * @return {Promise}
             */
            initCompetencyAdder: function () {
                var that = this;

                return new Promise(function (resolve) {
                    HierarchyEvents.init().then(function (eventData) {
                        var adderData = {
                            // externalBasket: that.baskets.positions,
                            key: 'competencies',
                            crumbtrail: {
                                service: 'totara_competency_competency_show',
                                stringList: [
                                    {
                                        component: 'criteria_othercompetency',
                                        key: 'hierarchy_list:competency:all',
                                    },
                                    {
                                        component: 'criteria_othercompetency',
                                        key: 'hierarchy_list:competency:all_in_framework',
                                    }
                                ]
                            },
                            events: eventData,
                            expandable: {
                                args: {include: {crumbs: 1}},
                                service: 'totara_competency_competency_show',
                                template: 'totara_competency/hierarchy_expanded',
                            },
                            levelToggle: true,
                            list: {
                                map: {
                                    cols: [{
                                        dataPath: 'fullname',
                                        expandedViewTrigger: true,
                                        headerString: {
                                            component: 'criteria_othercompetency',
                                            key: 'selectcompetencies',
                                        },
                                    }],
                                    extraRowData: [{
                                        key: 'framework',
                                        dataPath: 'frameworkid'
                                    }],
                                    hasExpandedView: true,
                                    hasHierarchy: true,
                                },
                                defaultFilters: {
                                    'excluded_competency_id': document.querySelector('[data-comp-id]').getAttribute('data-comp-id')
                                },
                                service: 'totara_competency_competency_index',
                            },
                            onSaved: function (modal, items, selectionData) {
                                that.updateCompetencies(selectionData);
                            },
                            primaryDropDown: {
                                filterKey: 'framework',
                                placeholderString: [{
                                    component: 'criteria_othercompetency',
                                    key: 'allframeworks'
                                }],
                                service: 'totara_competency_get_frameworks',
                                serviceArgs: {},
                                serviceLabelKey: 'fullname'
                            },
                            primarySearch: {
                                filterKey: 'text',
                                placeholderString: [{
                                    component: 'totara_core',
                                    key: 'search'
                                }],
                            },
                            title: [{
                                component: 'criteria_othercompetency',
                                key: 'hierarchy_list:competency:select',
                            }],
                        };

                        ModalList.adder(adderData).then(function (modal) {
                            that.competencyAdder = modal;
                            resolve(modal);
                        });
                    });
                });
            },

            /**
             * Open the adder to add competencies
             */
            addCompetencies: function () {
                var that = this;

                if (!this.competencyAdder) {
                    this.initCompetencyAdder().then(function () {
                        that.competencyAdder.show(that.criterion.itemids);
                    }).catch(function (e) {
                        e.fileName = that.filename;
                        e.name = 'Error initialsing the competency adder';
                        notification.exception(e);
                    });
                } else {
                    this.competencyAdder.show(this.criterion.itemids);
                }
            },

            /**
             * Update the displayed competencies
             *
             * @param  {[Object]} items Selected competencies
             */
            updateCompetencies: function (competencies) {
                var that = this,
                    competenciesTarget = that.widget.querySelector('[data-tw-criterionOtherCompetency-competencies]'),
                    id,
                    fullname,
                    competenciesPromiseArr = [],
                    templateData = {};

                for (var a = 0; a < competencies.length; a++) {
                    id = competencies[a].id;
                    fullname = competencies[a].fullname;

                    if (!this.competencies[id]) {
                        this.competencies[id] = {
                            id: id,
                            name: fullname};
                        this.criterion.itemids.push(id);

                        templateData = {item_parent: 'criterionOtherCompetency', value: id, text: fullname};
                        if (competencies[a].error) {
                            templateData.error = competencies[a].error;
                        }
                        competenciesPromiseArr.push(templates.renderAppend('totara_criteria/partial_item', templateData, competenciesTarget));
                    }
                }

                if (competenciesPromiseArr.length > 0) {
                    Promise.all(competenciesPromiseArr).then(function () {
                        that.showHideNotEnoughCompetency();

                        that.triggerEvent('update', {criterion: that.criterion});
                        that.triggerEvent('dirty', {});
                    }).catch(function (e) {
                        e.fileName = that.filename;
                        e.name = 'Showing competencies';
                        notification.exception(e);
                    });
                }
            },


            removeCompetency: function (id) {
                id = parseInt(id);

                var that = this,
                    competencyTarget = that.widget.querySelector('[data-tw-criterionOtherCompetency-item-value="' + id + '"]'),
                    idIndex = this.criterion.itemids.indexOf(id);

                if (this.competencies[id]) {
                    delete this.competencies[id];
                    if (idIndex >= 0) {
                        this.criterion.itemids.splice(idIndex, 1);
                    }

                    if (competencyTarget) {
                        competencyTarget.remove();
                    }
                }

                // Show nocompetencies warning
                that.showHideNotEnoughCompetency();

                that.triggerEvent('update', {criterion: that.criterion});
                that.triggerEvent('dirty', {});
            },

            /**
             * Show or hide the No Criteria warning depending on the number of items
             */
            showHideNotEnoughCompetency: function () {
                var target = this.widget.querySelector('[data-tw-criterionOtherCompetency-error="notenoughothercompetency"]');
                if (!target) {
                    return;
                }

                if (this.criterion.itemids.length > 0) {
                    // Hide the warning
                    target.classList.add(this.domClasses.hidden);
                } else {
                    target.classList.remove(this.domClasses.hidden);
                }
            },

            /**
             * Trigger event
             *
             * @param {string} eventName
             * @param {object} data
             */
            triggerEvent: function (eventName, data) {
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
        var init = function (parent) {
            return new Promise(function (resolve) {
                var wgt = new CriterionOtherCompetency();
                wgt.setParent(parent);
                wgt.events();
                wgt.loader = Loader.init(parent);
                wgt.loader.show();
                resolve(wgt);

                M.util.js_pending('criterionOtherCompetency');
                wgt.getDetail().then(function () {
                    wgt.loader.hide();
                    M.util.js_complete('criterionOtherCompetency');
                });
            });
        };

        return {
            init: init
        };
    });
