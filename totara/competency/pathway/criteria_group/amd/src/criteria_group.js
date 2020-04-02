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

define(['core/str', 'core/notification', 'core/templates'],
function (str, notification, templates) {

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
            type: 'criteria_group',
            scalevalue: 0,
            sortorder: 0,
            criteria: [],
        };

        // Key to use in achievementPath events
        this.pwKey = '';

        // Unique criteria key
        this.lastCriterionKey = 0;

        // Criteria is indexed by the criteria key.
        // The values received via the bubbled events are added in a 'detail' attribute
        // which is packed into the pathway.criteria array as that is the only information
        // we must send when saving the criteria
        this.criteria = {};
        this.criteriaLength = 0;
        this.markedForDeletionCriteria = {};

        this.typeModal = null;

        this.endpoints = {
            create: 'pathway_criteria_group_create',
            update: 'pathway_criteria_group_update',
        };

        this.filename = 'criteria_group.js';
    }

    PwCriteriaGroup.prototype = {

        /**
         * Add event listeners for PwCriteriaGroups
         *
         */
        events: function () {
            var that = this,
                action;

            this.widget.addEventListener('click', function (e) {
                if (!e.target) {
                    return false;
                }

                if (e.target.closest('[data-cg-action]')) {
                    action = e.target.closest('[data-cg-action]').getAttribute('data-cg-action');

                    if (action === 'addcriterion') {
                        that.showCriteriaTypeOptions();
                    }
                } else if (e.target.closest('[data-criteria_group-criterion-type]')) {
                    var selectedOption = e.target.closest('[data-criteria_group-criterion-type]');

                    that.hideCriteriaTypeSelectors();
                    that.addCriterion(that.pwKey, selectedOption);
                } else if (e.target.closest('[data-criterion-action]')) {
                    var wgt = e.target.closest('[data-criterion-action]'),
                        keyWgt = wgt.closest('[data-tw-criterion-key]'),
                        criterionKey;

                    action = wgt.getAttribute('data-criterion-action');
                    if (!keyWgt) {
                        // Something went wrong - we can't determine which criterion to perform the action on
                        notification.exception({
                            fileName: that.filename,
                            message: "Can't determine target of the " + action + " criterion action",
                            name: 'No criterion action target'
                        });
                        return;
                    }

                    that.hideCriteriaTypeSelectors();
                    criterionKey = keyWgt.getAttribute('data-tw-criterion-key');

                    if (action === 'toggle-detail') {
                        that.toggleCriterionDetail(criterionKey);
                    } else if (action === 'remove') {
                        that.removeCriterion(criterionKey);
                    } else if (action === 'undo') {
                        that.undoCriterionRemoval(criterionKey);
                    }
                }

                return false;
            });
        },

        // Listen for propagated events
        bubbledEventsListener: function () {
            var that = this,
                criteriaEvents = 'totara_criteria/criterion:',
                criterionKey,
                criterion;

            this.widget.addEventListener(criteriaEvents + 'update', function (e) {
                criterionKey = e.detail.key;
                criterion = e.detail.criterion;

                if (criterion) {
                    // Using an associative array with the string key.
                    // We store this associative array in a separate variable and ensure that the
                    // pathway contains data that will serialize as expected

                    if (!that.criteria[criterionKey]) {
                        that.criteria[criterionKey] = {
                            'id': criterion.id ? criterion.id : 0,
                            'type': criterion.type ? criterion.type : '',
                            'title': criterion.title ? criterion.title : '',
                            'singleuse': criterion.singleuse ? criterion.singleuse : false,
                            'expandable': criterion.expandable ? criterion.expandable : false
                        };

                        // If previous marked for deletion, remove from that list
                        if (that.markedForDeletionCriteria[criterionKey]) {
                            delete that.markedForDeletionCriteria[criterionKey];
                        }

                        that.criteriaLength += 1;
                    }

                    if (criterion.singleuse) {
                        that.triggerEvent('singleuse', {used: true});
                    }
                    // Remove attributes not needed for APIs
                    delete criterion.singleuse;
                    delete criterion.expandable;
                    that.criteria[criterionKey].detail = criterion;
                }

                // Propagate up to achievementPaths
                that.packCriteria();
                that.triggerEvent('update', {pathway: that.pathway});

                that.showHideNoCriteria();
            });

            this.widget.addEventListener(criteriaEvents + 'dirty', function (e) {
                criterionKey = e.detail.key;

                if (that.criteria[criterionKey]) {
                    that.criteria[criterionKey].dirty = true;

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
        setParent: function (parent) {
            this.widget = parent;
        },

        /**
         * Initialise the data and display it
         */
        initData: function () {
            var pwWgt = this.widget.closest('[data-pw-key]'),
                svWgt = this.widget.closest('[data-pw-scalevalue]'),
                pwKey = 0,
                pwId = 0;

            if (pwWgt) {
                pwKey = pwWgt.getAttribute('data-pw-key') ? pwWgt.getAttribute('data-pw-key') : 0;
                pwId = pwWgt.getAttribute('data-pw-id') ? pwWgt.getAttribute('data-pw-id') : 0;
            }

            // Obtain the pathway detail from the dom

            this.pwKey = pwKey;
            if (svWgt) {
                this.pathway.scalevalue = svWgt.getAttribute('data-pw-scalevalue') ? svWgt.getAttribute('data-pw-scalevalue') : 1;
            }

            if (pwId === 0) {
                // New pw - we also need the competency_id and scalevalue
                delete this.pathway.id;

                var compIdNode = document.querySelector('[data-comp-id]');

                if (compIdNode) {
                    this.pathway.competency_id = compIdNode.getAttribute('data-comp-id')
                        ? compIdNode.getAttribute('data-comp-id')
                        : 1;
                }

                this.widget.setAttribute('data-pw-save-endpoint', this.endpoints.create);
            } else {
                this.widget.setAttribute('data-pw-save-endpoint', this.endpoints.update);
                this.pathway.id = pwId;
            }

            this.triggerEvent('update', {pathway: this.pathway});
        },

        /**
         * Return the next unique key for indexing in the criteria structure
         *
         * @return {string} Next key
         */
        getNextCriterionKey: function () {
            this.lastCriterionKey++;
            return this.pwKey + '_new_criterion_' + this.lastCriterionKey;
        },


        /**
         * Pack the pathway criteria data to ensure that it will be serialize as expected
         * when sent to the save api endpoint
         */
        packCriteria: function () {
            this.pathway.criteria = [];

            for (var criterionKey in this.criteria) {
                if (this.criteria[criterionKey].detail) {
                    this.pathway.criteria.push(this.criteria[criterionKey].detail);
                }
            }
        },

        /**
         * Show or hide the 'No Criteria' message
         */
        showHideNoCriteria: function () {
            var nCriteria = this.pathway.criteria.length,
                nDeletedCriteria = 0;

            if (Object.keys(this.markedForDeletionCriteria).length) {
                nDeletedCriteria = 1;
            }

            if ((nCriteria + nDeletedCriteria) == 0) {
                this.widget.querySelector('.critgrp_criteria_none').classList.remove('cc_hidden');
            } else {
                this.widget.querySelector('.critgrp_criteria_none').classList.add('cc_hidden');
            }

            return false;
        },

        /**
         * Hide the bottom actions
         */
        hideBottomActions: function () {
            var wgt = this.widget.closest('[data-pw-key]').querySelector('.critgrp_bottom_action');
            wgt.classList.add('cc_hidden');

            return false;
        },

        /**
         * Show the bottom actions
         */
        showBottomActions: function () {
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
        createEmptyPw: function (compId, scalevalue) {
            return {
                competency_id: compId,
                scalevalue: scalevalue,
                criteria: [],
            };
        },

        /**
         * Hide all criteria type selectors
         */
        hideCriteriaTypeSelectors: function () {
            // We also want to close the options lists in parent templates
            var criteriaTypeNodes = document.querySelectorAll('[data-criteria-type-toggle]');

            for (var a = 0; a < criteriaTypeNodes.length; a++) {
                criteriaTypeNodes[a].classList.add('cc_hidden');
            }

            return false;
        },

        /**
         * Show the criteria type dropdown for the specific scalevalue
         */
        showCriteriaTypeOptions: function () {
            var toOpen = this.widget.querySelector('[data-criteria-type-toggle="criteria_group"]'),
                expanded = toOpen ? !toOpen.classList.contains('cc_hidden') : false;

            this.hideCriteriaTypeSelectors();

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
         * @param {string} criterionType Type of criterion to add
         * @param {string} criterionTemplate Template to display the new criterion
         */
        addCriterion: function (pwKey, criterionOptionNode) {
            var that = this,
                target = this.widget.querySelector('.critgrp_criteria'),
                templatename = 'pathway_criteria_group/partial_group_criteria',
                criterionType = criterionOptionNode.getAttribute('data-criteria_group-criterion-type'),
                criterionTitle = criterionOptionNode.getAttribute('data-criteria_group-criterion-title'),
                criterionTemplatename = criterionOptionNode.getAttribute('data-criteria_group-criterion-templatename'),
                criterionSingleuse = criterionOptionNode.getAttribute('data-criteria_group-criterion-singleuse'),
                criterionKey;

            criterionKey = that.getNextCriterionKey();

            this.criteria[criterionKey] = {
                'key': criterionKey,
                'type': criterionType,
                'title': criterionTitle,
                'criterion_templatename': criterionTemplatename,
                'singleuse': !!+criterionSingleuse,
            };

            // TODO: For now singleuse is used to determine whether there are detail - may need to expand later
            this.criteria[criterionKey].expandable = !this.criteria[criterionKey].singleuse;

            if (this.criteriaLength > 0) {
                this.criteria[criterionKey].showand = true;
            }

            this.criteriaLength += 1;

            // Display the criterion
            templates.renderAppend(templatename,  {criteria: this.criteria[criterionKey]}, target).then(
                function () {
                    templates.runTemplateJS('');
                    that.triggerEvent('dirty', {});
                },
                function (e) {
                    e.fileName = that.filename;
                    e.name = 'Error displaying ' + criterionType;
                    notification.exception(e);
                }
            );

            return false;
        },

        /**
         * Toggle disabling / enabling of singleUse criterion types in this group
         *
         * @param {bool} allowSingleUse
         */
        toggleSingleUse: function (allowSingleUse) {
            var singleUseActiveNodes = this.widget.querySelectorAll('[data-criteria-type-singleuse-active]'),
                singleUseDisabledNodes = this.widget.querySelectorAll('[data-criteria-type-singleuse-disabled]');

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
        toggleAllSingleUse: function (allowSingleUse) {
            var criteriaTypeNodes = document.querySelectorAll('[data-criteria-type-toggle="criteria_group"]'),
                singleUseActiveNodes,
                singleUseDisabledNodes,
                pwWgt,
                criteria,
                pwAllow;

            for (var a = 0; a < criteriaTypeNodes.length; a++) {
                pwAllow = allowSingleUse;
                singleUseActiveNodes = criteriaTypeNodes[a].querySelectorAll('[data-criteria-type-singleuse-active]');
                singleUseDisabledNodes = criteriaTypeNodes[a].querySelectorAll('[data-criteria-type-singleuse-disabled]');

                // Only need to test 1
                if (singleUseActiveNodes.length == 0) {
                    continue;
                }

                if (allowSingleUse) {
                    // Before we allow single use in a group,
                    // ensure there are no active criteria in that group (using title)
                    pwWgt = criteriaTypeNodes[a].closest('[data-pw-key]');
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
         * @param  {String} criterionKey Key of criterion whose detail display should be toggled
         */
        toggleCriterionDetail: function (criterionKey) {
            var criterionTarget = this.widget.querySelector('[data-tw-criterion-key="' + criterionKey + '"]'),
                expandTarget = this.widget.querySelector('[data-criterion-detail="' + criterionKey + '"]'),
                isExpanded = criterionTarget.hasAttribute('data-criterion-detail-expanded')
                    ? criterionTarget.getAttribute('data-criterion-detail-expanded')
                    : 0,
                expandedIcon = criterionTarget.querySelector('[data-criterion-detail-icon="expanded"]'),
                collapsedIcon = criterionTarget.querySelector('[data-criterion-detail-icon="collapsed"]');

            if (isExpanded == 1) {
                expandTarget.classList.add('cc_hidden');
                expandedIcon.classList.add('cc_hidden');
                collapsedIcon.classList.remove('cc_hidden');
                criterionTarget.removeAttribute('data-criterion-detail-expanded');
            } else {
                expandTarget.classList.remove('cc_hidden');
                expandedIcon.classList.remove('cc_hidden');
                collapsedIcon.classList.add('cc_hidden');
                criterionTarget.setAttribute('data-criterion-detail-expanded', "1");
            }

            return false;
        },

        /**
         * Remove the criterion.
         * If it has an id (exists on the database), its title will still be shown
         * to indicate that final removal will only happen when changes are applied
         *
         * @param  {String} criterionKey Key of criterion to remove
         */
        removeCriterion: function (criterionKey) {
            var criterionTarget = this.widget.querySelector('[data-tw-criterion-key="' + criterionKey + '"]'),
                criterionAndTarget = this.widget.querySelector('[data-pw-and="' + criterionKey + '"]'),
                activeNode = criterionTarget.querySelector('[data-criterion-active]'),
                deletedNode = criterionTarget.querySelector('[data-criterion-deleted]'),
                removeIconWgt = criterionTarget.querySelector('[data-criterion-action="remove"]'),
                undoIconWgt = criterionTarget.querySelector('[data-criterion-action="undo"]'),
                copyObj = {};

            if (this.criteria[criterionKey]) {
                // If it is a single use criterion, bubble event up to indicate that we
                // are no longer using a single-use criterion
                if (this.criteria[criterionKey].singleuse) {
                    this.toggleAllSingleUse(true);
                    this.triggerEvent('singleuse', {used: false});
                    this.showBottomActions();
                }

                // If it has an id,
                //      move the criteria to the 'markedForDeletion' array
                //      indicate pending deletion through css
                // else
                //      simply delete the criterion

                if (this.criteria[criterionKey].id && this.criteria[criterionKey].id != 0) {
                    // Existing
                    // Replace content of the criterion div to display deleted name only

                    copyObj[criterionKey] = this.criteria[criterionKey];
                    Object.assign(this.markedForDeletionCriteria, copyObj);
                    delete this.criteria[criterionKey];
                    this.packCriteria();

                    if (activeNode) {
                        activeNode.classList.add('cc_hidden');
                    }

                    if (deletedNode) {
                        deletedNode.classList.remove('cc_hidden');
                    }

                    // Show undo action
                    if (removeIconWgt) {
                        removeIconWgt.classList.add('cc_hidden');
                    }
                    if (undoIconWgt) {
                        undoIconWgt.classList.remove('cc_hidden');
                    }

                    this.triggerEvent('update', {pathway: this.pathway});
                    this.triggerEvent('dirty', {});
                } else {
                    // Remove the whole criterion and AND divider
                    if (criterionTarget) {
                        criterionTarget.remove();
                    }

                    if (criterionAndTarget) {
                        criterionAndTarget.remove();
                    } else {
                        // If we removed the top criterion and there are more criteria,
                        // we need now to remove the new top criterion's AND
                        if (this.pathway.criteria.length > 1) {
                            criterionAndTarget = this.widget.querySelector('[data-pw-and]');
                            if (criterionAndTarget) {
                                criterionAndTarget.remove();
                            }
                        }
                    }

                    delete this.criteria[criterionKey];
                    this.packCriteria();

                    if (this.pathway.criteria.length == 0) {
                        this.triggerEvent('remove', {});
                    } else {
                        this.triggerEvent('update', {pathway: this.pathway});
                    }

                    this.triggerEvent('dirty', {});
                }

                this.criteriaLength -= 1;
            }

            return false;
        },

        /**
         * Undo the removal of the criterion.
         *
         * @param  {String} criterionKey Key of criterion to remove
         */
        undoCriterionRemoval: function (criterionKey) {
            if (!this.markedForDeletionCriteria[criterionKey]) {
                return;
            }

            var criterionTarget = this.widget.querySelector('[data-tw-criterion-key="' + criterionKey + '"]'),
                criterionAndTarget = this.widget.querySelector('[data-pw-and="' + criterionKey + '"]'),
                activeNode = criterionTarget.querySelector('[data-criterion-active]'),
                deletedNode = criterionTarget.querySelector('[data-criterion-deleted]'),
                removeIconWgt = criterionTarget.querySelector('[data-criterion-action="remove"]'),
                undoIconWgt = criterionTarget.querySelector('[data-criterion-action="undo"]'),
                copyObj = {};

            // Handle the case where an existing single-use has been removed, another one added
            // and then the user tries to undo removal of the original criterion
            if (this.markedForDeletionCriteria[criterionKey].singleuse) {
                var singleUseWgt = document.querySelector('[data-singleuse]'),
                    hasSingleUse = '0';

                if (singleUseWgt) {
                    hasSingleUse = singleUseWgt.getAttribute('data-singleuse');
                }

                if (hasSingleUse == '1') {
                    notification.clearNotifications();

                    str.get_string('error:cant_undo_singleuse', 'pathway_criteria_group').done(function (message) {
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

            copyObj[criterionKey] = this.markedForDeletionCriteria[criterionKey];
            Object.assign(this.criteria, copyObj);
            delete this.markedForDeletionCriteria[criterionKey];
            this.packCriteria();

            // If it is a single use criterion, bubble event up to indicate that we
            // are using a single-use criterion
            if (this.criteria[criterionKey].singleuse) {
                this.triggerEvent('singleuse', {used: true});
                this.toggleAllSingleUse(false);
            } else {
                // Just this pw is affected
                this.toggleSingleUse(false);
            }

            if (activeNode) {
                activeNode.classList.remove('cc_hidden');
            }

            if (deletedNode) {
                deletedNode.classList.add('cc_hidden');
            }

            // Hide undo action
            if (removeIconWgt) {
                removeIconWgt.classList.remove('cc_hidden');
            }
            if (undoIconWgt) {
                undoIconWgt.classList.add('cc_hidden');
            }

            this.triggerEvent('update', {pathway: this.pathway});
            this.triggerEvent('dirty', {});

            this.criteriaLength += 1;

            return false;
        },

        /**
         * Trigger event
         *
         * @param {string} eventName
         * @param {object} data
         */
        triggerEvent: function (eventName, data) {
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
    var init = function (parent) {
        return new Promise(function (resolve) {
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
