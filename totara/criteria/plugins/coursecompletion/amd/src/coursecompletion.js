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
 * @package criteria_coursecompletion
 */

define(['core/templates', 'core/notification', 'core/ajax', 'totara_core/modal_list', 'totara_core/loader_manager'],
function(templates, notification, ajax, ModalList, Loader) {

    /**
     * Class constructor for CriterionCourseCompletion.
     *
     * @class
     * @constructor
     */
    function CriterionCourseCompletion() {
        if (!(this instanceof CriterionCourseCompletion)) {
            return new CriterionCourseCompletion();
        }

        this.widget = ''; // Parent widget
        this.loader = null; // Loading overlay manager

        /**
         * Criterion data.
         * This object should only contain the data to be sent on the save api endpoints.
         * The variable names MUST correlate to the save endpoint parameters
         */
        this.criterion = {
            type: 'coursecompletion',
            itemids: [],
            aggregation: {},
        };

        // Saving items from the basket - therefore not stored in criterion
        this.courses = []; // Courses to complete
        this.criterionKey = ''; // Unique key to use in bubbled events
        this.courseAdder = null; // Initialized adder for courses

        this.endpoints = {
            detail: 'criteria_coursecompletion_get_detail',
            courses: 'totara_competency_get_courses',
            courseCategories: 'totara_competency_get_categories',
        };

        this.domClasses = {
            hidden: 'crit_hidden',
        };

        this.filename = 'coursecompletion.js';
    }

    CriterionCourseCompletion.prototype = {

        /**
         * Add event listeners for CriterionCourseCompletions
         */
        events: function() {
            var that = this;

            this.widget.addEventListener('click', function(e) {
                if (!e.target) {
                    return;
                }

                // Add courses link clicked
                if (e.target.closest('[data-tw-criterionCourseCompletion-addCourses]')) {
                    e.preventDefault();
                    that.addCourses();

                // Item remove link clicked
                } else if (e.target.closest('[data-tw-criterionCourseCompletion-item-remove]')) {
                    e.preventDefault();

                    var courseNode = e.target.closest('[data-tw-criterionCourseCompletion-item-value]'),
                        courseId;

                    if (!courseNode) {
                        return;
                    }

                    courseId = courseNode.getAttribute('data-tw-criterionCourseCompletion-item-value');
                    that.removeCourse(courseId);
                }
            });

            this.widget.addEventListener('change', function(e) {
                if (!e.target) {
                    return;
                }

                // Aggregation method changed
                if (e.target.closest('[data-tw-criterionCourseCompletion-aggregationMethod-changed]')) {
                    e.preventDefault();

                    var newMethod = e.target.closest('[data-tw-criterionCourseCompletion-aggregationMethod-changed]').value;

                    if (that.criterion.aggregation.method != newMethod) {
                        that.setAggregationMethod(newMethod);

                        that.triggerEvent('update', {criterion: that.criterion});
                        that.triggerEvent('dirty', {});
                    }

                // Aggregation count changed
                } else if (e.target.closest('[data-tw-criterionCourseCompletion-aggregationCount-changed]')) {
                    e.preventDefault();

                    var newCount = e.target.closest('[data-tw-criterionCourseCompletion-aggregationCount-changed]').value;

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
                        resolve(that.createEmptyCriterion(key));
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

                    Promise.all([that.setCourses(instance.items), that.initCourseAdder()]).then(function() {
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
            var methodNode = this.widget.querySelector('[data-tw-criterionCourseCompletion-aggregationMethod="' + method + '"]'),
                methodInput = methodNode.querySelector('[data-tw-criterionCourseCompletion-aggregationMethod-changed]'),
                countInput = this.widget.querySelector('[data-tw-criterionCourseCompletion-aggregationCount-changed]'),
                testCountInput = methodNode.querySelector('[data-tw-criterionCourseCompletion-aggregationCount-changed]');

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
            var countInput = this.widget.querySelector('[data-tw-criterionCourseCompletion-aggregationCount-changed]');

            this.criterion.aggregation.reqitems = reqItems;

            if (countInput) {
                countInput.value = reqItems;
            }
        },

        /**
         * Set and display the courses to be completed
         *
         * @param {Object} courses Array of courses
         * @return {Promise}
         */
        setCourses: function(courses) {
            var that = this,
                coursesTarget = this.widget.querySelector('[data-tw-criterionCourseCompletion-courses]'),
                coursesPromiseArr = [],
                templateData = {};

            return new Promise(function(resolve) {
                // We want to index the items with the ids for easy adder results processing
                that.courses = [];
                that.criterion.itemids = [];

                if (courses.length == 0 || !coursesTarget) {
                    that.showHideNoCourses();
                    resolve();

                } else {
                    for (var a = 0; a < courses.length; a++) {
                        that.courses[courses[a].id] = courses[a];
                        that.criterion.itemids.push(courses[a].id);
                        templateData = {item_parent: 'criterionCourseCompletion', value: courses[a].id, text: courses[a].name};
                        coursesPromiseArr.push(templates.renderAppend('totara_criteria/partial_item', templateData, coursesTarget));
                    }

                    Promise.all(coursesPromiseArr).then(function() {
                        that.showHideNoCourses();
                        resolve();
                    }).catch(function(e) {
                        e.fileName = that.filename;
                        e.name = 'Error showing courses';
                        notification.exception(e);
                    });
                }
            });
        },

        /**
         * Create an empty coursecompletion criterion instance with the key
         *
         * @param {int} key
         * @return {Promise}
         */
        createEmptyCriterion: function(key) {
            var that = this;

            // Ensure the basket is empty
            return new Promise(function(resolve) {
                resolve({
                    results: {
                        id: 0,
                        // itemsbasketkey: basketkey,
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
         * Initialise the course adder
         *
         * @return {Promise}
         */
        initCourseAdder: function() {
            var that = this;

            return new Promise(function(resolve) {

                var adderData = {
                    key: 'courseAdder_' + that.criterionKey,
                    title: [{
                        key: 'selectcourses',
                        component: 'criteria_coursecompletion'
                    }],
                    list: {
                        map: {
                            cols: [{
                                dataPath: 'fullname',
                                headerString: {
                                    key: 'selectcourses',
                                    component: 'totara_competency',
                                },
                            }],
                        },
                        service: that.endpoints.courses,
                    },
                    primaryDropDown: {
                        filterKey: 'category',
                        serviceLabelKey: 'fullname',
                        placeholderString: [{
                            component: 'totara_competency',
                            key: 'allcategories',
                        }],
                        service: that.endpoints.courseCategories,
                        serviceArgs: {}
                    },
                    primarySearch: {
                        filterKey: 'name',
                        placeholderString: [{
                            component:  'totara_competency',
                            key: 'searchcourses'
                        }]
                    },
                    onSaved: function(adder, courseIds, courseData) {
                        that.updateCourses(courseData);
                    },
                };

                ModalList.adder(adderData).then(function(modal) {
                    that.courseAdder = modal;
                    resolve(modal);
                }).catch(function(e) {
                    notification.exception({
                        fileName: that.filename,
                        message: e[0] + ' modal: ' + e[1],
                        name: 'Error loading modal list adder'
                    });
                });
            });
        },


        /**
         * Open the adder to add courses
         */
        addCourses: function() {
            var that = this;

            if (!this.courseAdder) {
                this.initCourseAdder().then(function(modal) {
                    that.courseAdder.show(that.criterion.itemids);
                }).catch(function(e) {
                    e.fileName = that.filename;
                    e.name = 'Error initialsing the course adder';
                    notification.exception(e);
                });
            } else {
                this.courseAdder.show(this.criterion.itemids);
            }
        },

        /**
         * Update the displayed courses
         *
         * @param  {[Object]} items Selected courses
         */
        updateCourses: function(courses) {
            var that = this,
                coursesTarget = that.widget.querySelector('[data-tw-criterionCourseCompletion-courses]'),
                id,
                fullname,
                coursesPromiseArr = [],
                templateData = {};

            for (var a = 0; a < courses.length; a++) {
                id = courses[a].id;
                fullname = courses[a].fullname;

                if (!this.courses[id]) {
                    this.courses[id] = {
                        id: id,
                        name: fullname};
                    this.criterion.itemids.push(id);

                    templateData = {item_parent: 'criterionCourseCompletion', value: id, text: fullname};
                    coursesPromiseArr.push(templates.renderAppend('totara_criteria/partial_item', templateData, coursesTarget));
                }
            }

            if (coursesPromiseArr.length > 0) {
                Promise.all(coursesPromiseArr).then(function() {
                    that.showHideNoCourses();

                    that.triggerEvent('update', {criterion: that.criterion});
                    that.triggerEvent('dirty', {});
                }).catch(function(e) {
                    e.fileName = that.filename;
                    e.name = 'Showing courses';
                    notification.exception(e);
                });
            }
        },

        removeCourse: function(id) {
            id = parseInt(id);

            var that = this,
                coursesTarget = that.widget.querySelector('[data-tw-criterionCourseCompletion-item-value="' + id + '"]'),
                idIndex = this.criterion.itemids.indexOf(id);

            if (this.courses[id]) {
                delete this.courses[id];
                if (idIndex >= 0) {
                    this.criterion.itemids.splice(idIndex, 1);
                }

                if (coursesTarget) {
                    coursesTarget.remove();
                }
            }

            // Show nocourses warning
            that.showHideNoCourses();

            that.triggerEvent('update', {criterion: that.criterion});
            that.triggerEvent('dirty', {});
        },

        /**
         * Show or hide the No Criteria warning depending on the number of items
         */
        showHideNoCourses: function() {
            var target = this.widget.querySelector('[data-tw-criterionCourseCompletion-error="nocourses"]');
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
            var wgt = new CriterionCourseCompletion();
            wgt.setParent(parent);
            wgt.events();
            wgt.loader = Loader.init(parent);
            wgt.loader.show();
            resolve(wgt);

            M.util.js_pending('criterionCourseCompletion');
            wgt.getDetail().then(function() {
                wgt.loader.hide();
                M.util.js_complete('criterionCourseCompletion');
            });
        });
    };

    return {
        init: init
    };
 });