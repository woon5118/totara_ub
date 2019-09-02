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
 * @package pathway_criteria_group
 */

define(['core/str', 'core/notification', 'core/templates', 'core/ajax'],
function(str, notification, templates, ajax) {

    /**
     * Class constructor for the PwCriteriaGroup.
     *
     * @class
     * @constructor
     */
    function PwCriteriaGroup() {
        if (!(this instanceof PwCriteriaGroup)) {
            return new PwCriteriaGroup();
        }

        this.widget = '';

        /**
         * Pathway data.
         * This object should only contain the data to be sent on the save api endpoint.
         */
        this.pathway = {
            id: 0,
            criteria: [],
        };

        // Key to use in achievementPath events
        this.pwKey = '';

        // Unique criteria key
        this.lastCritKey = 0;

        // Criteria is indexed by the criteria key.
        // The values received via the bubbled events are added in a 'detail' attribute
        // which is packed into the pathway.criteria array as that is the only information
        // we must send when saving the criteria
        this.criteria = {};
        this.markedForDeletionCriteria = {};

        this.typeModal = null;

        this.endpoints = {
            criteria: 'pathway_criteria_group_get_criteria',
            criteriatemplate: 'totara_criteria_get_definition_template',
            create: 'pathway_criteria_group_create',
            update: 'pathway_criteria_group_update',
            delete: 'pathway_criteria_group_delete',
            // basketdelete: 'totara_core_basket_delete',
        };

        this.filename = 'criteria_group.js';
    }

    PwCriteriaGroup.prototype = {

        /**
         * Add event listeners for PwCriteriaGroups
         *
         */
        events: function() {
            var that = this;

            this.widget.addEventListener('click', function(e) {
                if (!e.target) {
                    return false;
                }

                if (e.target.closest('[data-cg-action]')) {
                    var action = e.target.closest('[data-cg-action]').getAttribute('data-cg-action');

                    if (action === 'addcrit') {
                        that.showCriteriaTypeOptions();
                    }

                } else if (e.target.closest('[data-criteria_group-crit-type-option]')) {
                    var selectedType = e.target.closest('[data-criteria_group-crit-type-option]').getAttribute('data-criteria_group-crit-type-option');

                    that.hideCritTypeSelectors();
                    that.addCriterion(that.pwKey, selectedType);

                } else if (e.target.closest('[data-crit-action]')) {
                    var wgt = e.target.closest('[data-crit-action]'),
                        action = wgt.getAttribute('data-crit-action'),
                        keyWgt = wgt.closest('[data-tw-criterion-key]'),
                        critKey;

                    if (!keyWgt) {
                        // Something went wrong - we can't determine which criterion to perform the action on
                        notification.exception({
                            fileName: that.filename,
                            message: "Can't determine target of the " + action + " criterion action",
                            name: 'No criterion action target'
                        });
                        return;
                    }

                    that.hideCritTypeSelectors();
                    critKey = keyWgt.getAttribute('data-tw-criterion-key');

                    if (action === 'toggle-detail') {
                        that.toggleCriterionDetail(critKey);
                    } else if (action === 'remove') {
                        that.removeCriterion(critKey);
                    } else if (action === 'undo') {
                        that.undoCriterionRemoval(critKey);
                    }
                }

                return false;
            });
        },

        // Listen for propagated events
        bubbledEventsListener: function() {
            var that = this,
                criteriaEvents = 'totara_criteria/criterion:';

            this.widget.addEventListener(criteriaEvents + 'update', function(e) {
                var critKey = e.detail.key,
                    criterion = e.detail.criterion;

                if (criterion) {
                    // Using an associative array with the string key.
                    // We store this associative array in a separate variable and ensure that the
                    // pathway contains data that will serialize as expected

                    if (!that.criteria[critKey]) {
                        that.criteria[critKey] = {};

                        // If previous marked for deletion, remove from that list
                        if (that.markedForDeletionCriteria[critKey]) {
                            delete that.markedForDeletionCriteria[critKey];
                        }
                    }

                    that.criteria[critKey].detail = criterion;
                }

                // Propagate up to achievementPaths
                that.packCriteria();
                that.triggerEvent('update', {pathway: that.pathway});

                that.showHideNoCriteria();
            });

            this.widget.addEventListener(criteriaEvents + 'dirty', function(e) {
                var critKey = e.detail.key;

                if (that.criteria[critKey]) {
                    that.criteria[critKey].dirty = true;

                    // Also propagate up
                    that.triggerEvent('dirty', {});
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
         * Initialise the data and display it
         */
        initData: function() {
            var that = this,
                pwWgt = this.widget.closest('[data-pw-key]'),
                pwKey = 0,
                pwId = 0,
                idWgt = this.widget.closest('[data-pw-id]'),
                apiArgs;

            if (pwWgt) {
                pwKey = pwWgt.getAttribute('data-pw-key') ? pwWgt.getAttribute('data-pw-key') : 0;
            }

            if (!idWgt) {
                // New pw - we also need the comp_id and scalevalue
                var compIdWgt = document.querySelector('[data-comp-id]'),
                    compId = 1,
                    svWgt = this.widget.closest('[data-pw-scalevalue]'),
                    initialCritType = this.widget.hasAttribute('data-pw-init-criterion') ? this.widget.getAttribute('data-pw-init-criterion') : '',
                    scalevalue = 1;

                if (compIdWgt) {
                    compId = compIdWgt.getAttribute('data-comp-id') ? compIdWgt.getAttribute('data-comp-id') : 1;
                }

                if (svWgt) {
                    scalevalue = svWgt.getAttribute('data-pw-scalevalue') ? svWgt.getAttribute('data-pw-scalevalue') : 1;
                }

                that.pathway = that.createEmptyPw(compId, scalevalue);
                that.pwKey = pwKey;

                that.widget.setAttribute('data-pw-save-endpoint', this.endpoints.create);

                // There should be an initial criterion type
                if (initialCritType) {
                    return that.addCriterion(pwKey, initialCritType);
                } else {
                    // We need to show 'no criteria', set the save-endpoint and bubble the pathway up
                    that.showHideNoCriteria();
                    that.triggerEvent('update', {pathway: that.pathway});

                    return;
                }
            }

            // Existing pw
            pwId = idWgt.getAttribute('data-pw-id') ? idWgt.getAttribute('data-pw-id') : 0;
            apiArgs = {
                args: {id: pwId},
                methodname: this.endpoints.criteria
            };

            ajax.getData(apiArgs).then(function (responses) {
                var criteria = responses.results,
                    target = that.widget.querySelector('.critgrp_criteria'),
                    promiseData = [];

                that.criteria = [];
                that.pathway.criteria = [];
                that.pwKey = pwKey;
                that.pathway.id = pwId;

                for (var a = 0; a < criteria.length; a++) {
                    var crit = criteria[a];

                    // Collapsed by default
                    crit.key = that.getNextCritKey();
                    crit.expanded = false;

                    if (!crit.singleuse) {
                        crit.expandable = true;
                        crit.showand = (a > 0);
                    } else {
                        // Hide the add button
                        crit.expandable = false;
                        crit.showand = false;
                        that.hideBottomActions();
                    }
                    that.criteria[crit.key] = crit;

                    promiseData.push(crit);
                }

                templates.renderAppend('pathway_criteria_group/partial_group_criteria', {criteria: promiseData}, target).then(function (responses) {
                    templates.runTemplateJS('');
                    that.showHideNoCriteria();
                }).catch(function(e) {
                    e.fileName = that.filename;
                    e.name = 'Error showing criteria detail';
                    notification.exception(e);
                });
            }).catch(function(e) {
                e.fileName = that.filename;
                e.name = 'Error getting criteria';
                notification.exception(e);
            });

            return false;
        },

        /**
         * Return the next unique key for indexing in the criteria structure
         *
         * @return {string} Next key
         */
        getNextCritKey: function() {
            this.lastCritKey++;
            return this.pwKey + '_crit_' + this.lastCritKey;
        },


        /**
         * Pack the pathway criteria data to ensure that it will be serialize as expected
         * when sent to the save api endpoint
         */
        packCriteria: function() {
            this.pathway.criteria = [];

            for (var critKey in this.criteria) {
                if (this.criteria[critKey].detail) {
                    this.pathway.criteria.push(this.criteria[critKey].detail);
                }
            }
        },

        /**
         * Show or hide the 'No Criteria' message
         */
        showHideNoCriteria: function() {
            var nCrit = this.pathway.criteria.length,
                nDeletedCrit = 0;

            for (var key in this.markedForDeletionCriteria) {
                nDeletedCrit += 1;
            }

            if ((nCrit + nDeletedCrit) == 0) {
                this.widget.querySelector('.critgrp_criteria_none').classList.remove('cc_hidden');
            } else {
                this.widget.querySelector('.critgrp_criteria_none').classList.add('cc_hidden');
            }

            return false;
        },

        /**
         * Hide the bottom actions
         */
        hideBottomActions: function() {
            var wgt = this.widget.closest('[data-pw-key]').querySelector('.critgrp_bottom_action');
            wgt.classList.add('cc_hidden');

            return false;
        },

        /**
         * Show the bottom actions
         */
        showBottomActions: function() {
            var wgt = this.widget.closest('[data-pw-key]').querySelector('.critgrp_bottom_action');
            wgt.classList.remove('cc_hidden');

            return false;
        },

        /**
         * Return an empty pathway for the key
         *
         * @param {int} compId
         * @param {int} scalevalue
         * @return {Object}
         */
        createEmptyPw: function(compId, scalevalue) {
            return {
                comp_id: compId,
                scalevalue: scalevalue,
                criteria: [],
            };
        },

        /**
         * Hide all criteria type selectors
         */
        hideCritTypeSelectors: function() {
            // We also want to close the options lists in parent templates
            var critTypeNodes = document.querySelectorAll('[data-crit-type-toggle]');

            for (var a = 0; a < critTypeNodes.length; a++) {
                critTypeNodes[a].classList.add('cc_hidden');
            }

            return false;
        },

        /**
         * Show the criteria type dropdown for the specific scalevalue
         */
        showCriteriaTypeOptions: function() {
            var toOpen = this.widget.querySelector('[data-crit-type-toggle="criteria_group"]'),
                expanded = toOpen ? !toOpen.classList.contains('cc_hidden') : false;

            this.hideCritTypeSelectors();

            // Now show the correct list
            if (toOpen && !expanded) {
                toOpen.classList.remove('cc_hidden');
            }

            return false;
        },

        /**
         * Add a new criterion
         *
         * @param {string} pwKey Key of pathway to add the new criterion to
         * @param {string} critType Type of criterion to add
         */
        addCriterion: function(pwKey, critType) {
            var that = this,
                apiArgs = {
                    'args': {type: critType},
                    'methodname': this.endpoints.criteriatemplate},
                target = this.widget.querySelector('.critgrp_criteria');

            ajax.getData(apiArgs).then(function(responses) {
                var templatename = 'pathway_criteria_group/partial_criterion',
                    crit = responses.results;

                crit.key = that.getNextCritKey();
                if (!crit.singleuse) {
                    crit.expandable = true;
                    crit.showand = that.pathway.criteria.length > 0;
                } else {
                    // Hide the add button
                    crit.expandable = false;
                    crit.showand = false;
                    that.hideBottomActions();

                    // Bubble event up to indicate that we are using a single-use criterion
                    that.triggerEvent('singleuse', {used: true});
                }

                // Can never add single use criterion if we have another criterion
                that.toggleSingleUse(false);

                that.criteria[crit.key] = crit;

                // Display the criterion
                templates.renderAppend(templatename, crit, target).then(
                    function(responses) {
                        templates.runTemplateJS('');
                        that.triggerEvent('dirty', {});
                    },
                    function(error) {
                        alert(error);
                    }
                );
            }).catch(function(e) {
                e.fileName = that.filename;
                e.name = 'Error getting criteria template';
                notification.exception(e);
            });

            return false;
        },

        /**
         * Toggle disabling / enabling of singleUse criterion types in this group
         *
         * @param {bool} allowSingleUse
         */
        toggleSingleUse: function(allowSingleUse) {
            var singleUseActiveNodes = this.widget.querySelectorAll('[data-crit-type-singleuse-active]'),
                singleUseDisabledNodes = this.widget.querySelectorAll('[data-crit-type-singleuse-disabled]');

            // Only need to test 1
            if (singleUseActiveNodes.length > 0) {
                if (allowSingleUse) {
                    for (var k = 0; k < singleUseActiveNodes.length; k++) {
                        singleUseActiveNodes[k].classList.remove('cc_hidden');
                    }
                    for (var k = 0; k < singleUseDisabledNodes.length; k++) {
                        singleUseDisabledNodes[k].classList.add('cc_hidden');
                    }
                } else {
                    for (var k = 0; k < singleUseActiveNodes.length; k++) {
                        singleUseActiveNodes[k].classList.add('cc_hidden');
                    }
                    for (var k = 0; k < singleUseDisabledNodes.length; k++) {
                        singleUseDisabledNodes[k].classList.remove('cc_hidden');
                    }
                }
            }

            return false;
        },

        /**
         * Toggle disabling / enabling of singleUse criterion types in ALL groups
         *
         * @param {bool} allowSingleUse
         */
        toggleAllSingleUse: function(allowSingleUse) {
            var critTypeNodes = document.querySelectorAll('[data-crit-type-toggle="criteria_group"]'),
                singleUseActiveNodes,
                singleUseDisabledNodes,
                pwWgt,
                criteria,
                pwAllow;

            for (var a = 0; a < critTypeNodes.length; a++) {
                pwAllow = allowSingleUse;
                singleUseActiveNodes = critTypeNodes[a].querySelectorAll('[data-crit-type-singleuse-active]');
                singleUseDisabledNodes = critTypeNodes[a].querySelectorAll('[data-crit-type-singleuse-disabled]');

                // Only need to test 1
                if (singleUseActiveNodes.length == 0) {
                    continue;
                }

                if (allowSingleUse) {
                    // Before we allow single use in a group,
                    // ensure there are no active criteria in that group (using title)
                    pwWgt = critTypeNodes[a].closest('[data-pw-key]');
                    if (pwWgt) {
                        criteria = pwWgt.querySelectorAll('[data-criterion-active]');
                        if (criteria.length > 0) {
                            pwAllow = false;
                        }
                    }
                }

                if (pwAllow) {
                    for (var k = 0; k < singleUseActiveNodes.length; k++) {
                        singleUseActiveNodes[k].classList.remove('cc_hidden');
                    }
                    for (var k = 0; k < singleUseDisabledNodes.length; k++) {
                        singleUseDisabledNodes[k].classList.add('cc_hidden');
                    }
                } else {
                    for (var k = 0; k < singleUseActiveNodes.length; k++) {
                        singleUseActiveNodes[k].classList.add('cc_hidden');
                    }
                    for (var k = 0; k < singleUseDisabledNodes.length; k++) {
                        singleUseDisabledNodes[k].classList.remove('cc_hidden');
                    }
                }
            }

            return false;
        },

        /**
         * Toggle the display of the criterion detail
         *
         * @param  {String} critKey Key of criterion whose detail display should be toggled
         */
        toggleCriterionDetail: function(critKey) {
            var critTarget = this.widget.querySelector('[data-tw-criterion-key="' + critKey + '"]'),
                expandTarget = this.widget.querySelector('[data-crit-detail="' + critKey + '"]'),
                isExpanded = critTarget.hasAttribute('data-crit-detail-expanded') ? critTarget.getAttribute('data-crit-detail-expanded') : 0,
                expandedIcon = critTarget.querySelector('[data-crit-detail-icon="expanded"]'),
                collapsedIcon = critTarget.querySelector('[data-crit-detail-icon="collapsed"]');

            if (isExpanded == 1) {
                expandTarget.classList.add('cc_hidden');
                expandedIcon.classList.add('cc_hidden');
                collapsedIcon.classList.remove('cc_hidden');
                critTarget.removeAttribute('data-crit-detail-expanded');
            } else {
                expandTarget.classList.remove('cc_hidden');
                expandedIcon.classList.remove('cc_hidden');
                collapsedIcon.classList.add('cc_hidden');
                critTarget.setAttribute('data-crit-detail-expanded', "1");
            }

            return false;
        },

        /**
         * Remove the criterion.
         * If it has an id (exists on the database), its title will still be shown
         * to indicate that final removal will only happen when changes are applied
         *
         * @param  {String} critKey Key of criterion to remove
         */
        removeCriterion: function(critKey) {
            var that = this,
                critTarget = this.widget.querySelector('[data-tw-criterion-key="' + critKey + '"]'),
                critAndTarget = this.widget.querySelector('[data-pw-and="' + critKey + '"]'),
                templateData;

            // If it is a single use criterion, bubble event up to indicate that we
            // are no longer using a single-use criterion
            if (this.criteria[critKey].singleuse) {
                this.toggleAllSingleUse(true);
                this.triggerEvent('singleuse', {used: false});
                this.showBottomActions();
            }

            // If it has an id,
            //      move the criteria to the 'markedForDeletion' array
            //      indicate pending deletion through css
            // else
            //      simply delete the criterion

            if (this.criteria[critKey]) {
                if (this.criteria[critKey].id && this.criteria[critKey].id != 0) {
                    // Existing
                    // Replace content of the criterion div to display deleted name only

                    // Target the detail only
                    var detailTarget = critTarget.querySelector('[data-criterion-detail]'),
                        copyObj = {};

                    templateData = this.criteria[critKey];
                    templateData.deleted = true;

                    copyObj[critKey] = this.criteria[critKey];
                    Object.assign(this.markedForDeletionCriteria, copyObj);
                    delete this.criteria[critKey];
                    this.packCriteria();

                    templates.renderReplace('pathway_criteria_group/partial_criterion_detail', templateData, detailTarget).then(function() {
                        // Show undo action
                        var removeWgt = critTarget.querySelector('[data-crit-action="remove"]'),
                            undoWgt = critTarget.querySelector('[data-crit-action="undo"]');
                        removeWgt.classList.add('cc_hidden');
                        undoWgt.classList.remove('cc_hidden');

                        that.triggerEvent('update', {pathway: that.pathway});
                        that.triggerEvent('dirty', {});
                    });
                } else {
                    // Remove the whole criterion and AND divider
                    if (critTarget) {
                        critTarget.remove();
                    }

                    if (critAndTarget) {
                        critAndTarget.remove();
                    } else {
                        // If we removed the top criterion and there are more criteria,
                        // we need now to remove the new top criterion's AND
                        if (this.pathway.criteria.length > 1) {
                            critAndTarget = this.widget.querySelector('[data-pw-and]');
                            if (critAndTarget) {
                                critAndTarget.remove();
                            }
                        }
                    }

                    delete this.criteria[critKey];
                    this.packCriteria();

                    if (this.pathway.criteria.length == 0) {
                        this.triggerEvent('remove', {});
                    } else {
                        this.triggerEvent('update', {pathway: this.pathway});
                    }

                    this.triggerEvent('dirty', {});
                }
            }

            return false;
        },

        /**
         * Undo the removal of the criterion.
         *
         * @param  {String} critKey Key of criterion to remove
         */
        undoCriterionRemoval: function(critKey) {
            if (!this.markedForDeletionCriteria[critKey]) {
                return;
            }

            var that = this,
                critTarget = this.widget.querySelector('[data-tw-criterion-key="' + critKey + '"]'),
                detailTarget = critTarget.querySelector('[data-criterion-detail]'),
                templateData,
                copyObj = {};

            // Handle the case where an existing single-use has been removed, another one added
            // and then the user tries to undo removal of the original criterion
            if (this.markedForDeletionCriteria[critKey].singleuse) {
                var singleUseWgt = document.querySelector('[data-singleuse]'),
                    hasSingleUse = '0';

                if (singleUseWgt) {
                    hasSingleUse = singleUseWgt.getAttribute('data-singleuse');
                }

                if (hasSingleUse == '1') {
                    notification.clearNotifications();

                    str.get_string('error:cant_undo_singleuse', 'pathway_criteria_group').done(function(message) {
                        notification.addNotification({
                            message: message,
                            type: 'error'
                        });

                        // Scroll to top to make sure that the notification is visible
                        window.scrollTo(0, 0);
                    }).fail(notification.exception);

                    return;
                }
            }

            copyObj[critKey] = this.markedForDeletionCriteria[critKey];
            Object.assign(this.criteria, copyObj);
            delete this.markedForDeletionCriteria[critKey];
            this.packCriteria();

            // If it is a single use criterion, bubble event up to indicate that we
            // are using a single-use criterion
            if (this.criteria[critKey].singleuse) {
                this.triggerEvent('singleuse', {used: true});
                this.toggleAllSingleUse(false);
            } else {
                // Just this pw is affected
                this.toggleSingleUse(false);
            }

            templateData = this.criteria[critKey];
            templateData.deleted = false;

            templates.renderReplace('pathway_criteria_group/partial_criterion_detail', templateData, detailTarget).then(function() {
                // Show delete action
                var removeWgt = critTarget.querySelector('[data-crit-action="remove"]'),
                    undoWgt = critTarget.querySelector('[data-crit-action="undo"]');
                undoWgt.classList.add('cc_hidden');
                removeWgt.classList.remove('cc_hidden');

                that.triggerEvent('update', {pathway: that.pathway});
                that.triggerEvent('dirty', {});
            });

            return false;
        },

        /**
         * Trigger event
         *
         * @param {string} eventName
         * @param {object} data
         */
        triggerEvent: function(eventName, data) {
            data.key = this.pwKey;

            var propagateEvent = new CustomEvent('totara_competency/pathway:' + eventName, {
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
            var wgt = new PwCriteriaGroup();
            wgt.setParent(parent);
            wgt.events();
            wgt.bubbledEventsListener();
            wgt.initData();
            resolve(wgt);
        });
    };

    return {
        init: init
    };
 });