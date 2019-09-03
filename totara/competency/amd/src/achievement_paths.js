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

define(['core/templates', 'core/ajax',
        'core/modal_factory', 'core/modal_events', 'core/notification', 'core/str'],
function(templates, ajax, modalFactory, modalEvents, notification, str) {

    /**
     * Class constructor for the AchievementPaths.
     *
     * @class
     * @constructor
     */
    function AchievementPaths() {
        if (!(this instanceof AchievementPaths)) {
            return new AchievementPaths();
        }
        this.widget = '';

        this.comp_id = '';
        this.scale_id = '';
        this.pathways = [];
        this.scalevalues = [];
        this.markedForDeletionPathways = [];

        this.nPaths = 0;
        this.nDeletedPaths = 0;
        this.singlevalShown = false;
        this.critTypes = [];

        this.lastKey = 0;

        this.aggType = '';
        this.aggFunction = '';

        this.dirty = false;

        this.endpoints = {
            competencyScale: 'totara_competency_get_scale',
            scalevalues: 'totara_competency_get_scale_values',
            criteriaTypes: 'pathway_criteria_group_get_criteria_types',
            pathways: 'totara_competency_get_pathways',
            pathwaySummary: 'totara_competency_get_summary_template',
            definitionTemplate: 'totara_competency_get_definition_template',
            defaultpreset: 'totara_competency_link_default_preset',
            deletePathways: 'totara_competency_delete_pathways',
            overallAggregation: 'totara_competency_set_overall_aggregation',
        };

        this.filename = 'achievement_paths.js';
    }

    AchievementPaths.prototype = {

        /**
         * Add event listeners for AchievementPathss
         *
         */
        events: function() {
            var that = this;

            this.widget.addEventListener('click', function(e) {
                if (!e.target) {
                    return;
                }

                if (e.target.closest('[data-cc-action]')) {
                    var wgt = e.target.closest('[data-cc-action]'),
                        action = wgt.getAttribute('data-cc-action');

                    if (action === 'linkdefaultpreset') {
                        that.linkDefaultPreset();
                    } else if (action === 'applychanges') {
                        that.applyChanges();
                    } else if (action === 'cancelchanges') {
                        that.cancelChanges();
                    }

                } else if (e.target.closest('[data-scalevalue-action')) {
                    var action = e.target.closest('[data-scalevalue-action]').getAttribute('data-scalevalue-action'),
                        svWgt = e.target.closest('[data-scalevalue-id]');

                    if (action === 'add-pw') {
                        that.showCriteriaTypeOptions(svWgt);
                    }

                } else if (e.target.closest('[data-scalevalue-crit-type-option]')) {
                    var critType = e.target.closest('[data-scalevalue-crit-type-option]').getAttribute('data-scalevalue-crit-type-option');
                        svWgt = e.target.closest('[data-scalevalue-id]');

                    // In v1 we assume the only singlevalue pathway is criteria_group.
                    // If there is ever a need for additional singlevalue pathways, the
                    // implementation must be enhanced to cater for that

                    that.addSinglevaluePath(svWgt, 'criteria_group', critType);

                } else if (e.target.closest('[data-pw-action')) {
                    var wgt = e.target.closest('[data-pw-action]'),
                        action = wgt.getAttribute('data-pw-action'),
                        pwKey = e.target.closest('[data-pw-key]').getAttribute('data-pw-key');

                    if (action === 'remove') {
                        that.removePathway(pwKey);
                    } else if (action === 'undo') {
                        that.undoRemovePathway(pwKey);
                    }

                } else if (e.target.closest('[data-cc-agg-action')) {
                    var action = e.target.closest('[data-cc-agg-action]').getAttribute('data-cc-agg-action');

                    if (action === 'edit') {
                        that.disableOrdering();
                    } else if (action === 'move') {
                        that.enableOrdering();
                    }
                }
            });

            this.widget.addEventListener('change', function(e) {
                if (!e.target) {
                    return;
                }

                if (e.target.closest('[data-cc-add-pathway]')) {
                    var pathType = e.target.closest('[data-cc-add-pathway]').querySelector('option:checked').value;
                        that.addPath(pathType);
                } else if (e.target.closest('[data-cc-pw-agg-changed]')) {
                    var wgt = e.target.closest('[data-cc-pw-agg-changed]'),
                        selectedOption = wgt.querySelector('option:checked'),
                        aggActions = that.widget.querySelector('[data-cc-pw-agg-actions]');

                    that.setOverallAggregation();
                    that.dirty = true;
                }
            });

            this.widget.addEventListener('dragstart', function(e) {
                if (e.target.closest('[data-cc-orderable]')) {
                    var wgt = e.target.closest('[data-cc-orderable]'),
                        moveKey = wgt.getAttribute('data-cc-orderable');
                    e.dataTransfer.setData('orderKey', moveKey);
                }
            });

            this.widget.addEventListener('dragover', function(e) {
                e.preventDefault();
            });

            this.widget.addEventListener('drop', function(e) {
                e.preventDefault();

                if (e.target.closest('[data-cc-orderable]')) {
                    var moveKey = e.dataTransfer.getData('orderKey'),
                        moveSource = that.widget.querySelector('[data-cc-orderable="' + moveKey + '"]'),
                        dropTarget = e.target.closest('[data-cc-orderable]');

                    dropTarget.parentNode.insertBefore(moveSource, dropTarget.nextSibling);
                    // dropTarget.parentNode.insertBefore(moveSource, dropTarget);
                }
            });

            window.addEventListener('beforeunload', function(e) {
                if (that.dirty) {
                    e.preventDefault();
                    // TODO: Test in IE
                    e.returnValue = '';
                    return '';
                }
            });
        },

        // Listen for propagated events
        bubbledEventsListener: function() {
            var that = this,
                pwEvents = 'totara_competency/pathway:';

            this.widget.addEventListener(pwEvents + 'dirty', function(e) {
                var key = e.detail.key;

                if (that.pathways[key]) {
                    that.pathways[key].dirty = true;
                }

                that.dirty = true;
            });

            this.widget.addEventListener(pwEvents + 'update', function(e) {
                var key = e.detail.key,
                    pw = e.detail.pathway;

                if (!that.pathways[key]) {
                    that.pathways[key] = {};
                    that.nPaths += 1;
                    that.showHideNoPaths();
                }

                that.pathways[key].detail = pw;
            });

            this.widget.addEventListener(pwEvents + 'remove', function(e) {
                var pwKey = e.detail.key;

                that.removePathway(pwKey);
            });

            this.widget.addEventListener(pwEvents + 'singleuse', function(e) {
                var isUsed = e.detail.used;

                that.widget.setAttribute('data-singleuse', isUsed ? '1' : '0');
                that.toggleSingleUseCritTypes(!isUsed);
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
         * Show notification
         *
         * @param {String} type success, warning, error, etc/
         * @param {String} messageStringKey used for get_string
         * @param {String} messageComponent used for get_string
         * @param {Object} messageParams used for get_string, optional
         */
        showNotification : function(type, messageStringKey, messageComponent, messageParams) {
            if (typeof messageParams === 'undefined') {
                messageParams = {};
            }
            // Clear old notifications.
            notification.clearNotifications();

            str.get_string(messageStringKey, messageComponent, messageParams).done(function(message) {
                notification.addNotification({
                    message: message,
                    type: type
                });

                // Scroll to top to make sure that the notification is visible
                window.scrollTo(0, 0);
            }).fail(notification.exception);
        },


        /**
         * Retrieve the pathways, store the data in the
         */
        initData: function() {
            var that = this;

            if (this.widget.hasAttribute('data-comp-id')) {
                this.comp_id = this.widget.getAttribute('data-comp-id');
            }

            this.setOverallAggregation();
            this.setScale().then(function() {
                that.updatePage();
            });
        },

        /**
         * Return the next unique key for indexing in the pathways structure
         *
         * @return {string} Next key
         */
        getNextKey: function() {
            this.lastKey++;
            return 'pw_' + this.lastKey;
        },

        /**
         * Update the detail on this page
         */
        updatePage: function() {
            if (!this.comp_id || this.scalevalues.length == 0) {
                return;
            }

            var that = this,
                templatePromises = [],
                apiArgs;

            this.getCriteriaTypes().then(function() {
                apiArgs = {
                    'args': {comp_id: that.comp_id},
                    'methodname': that.endpoints.pathways};

                // Get all the pathways and its detail
                ajax.getData(apiArgs).then(function(responses) {
                    var pwData = responses.results;

                    // Clean out all previous pathway data
                    that.clearPathways();

                    that.nPaths = pwData.length;
                    that.singlevalShown = false;

                    for (var a = 0; a < pwData.length; a++) {
                        var pw = pwData[a];

                        // pathway must provide templatename
                        if (!pw.pathway_templatename) {
                            notification.exception({
                                fileName: that.filename,
                                message: 'Templatename for pathway ' + pw.title + ' (' + pw.id + ') is missing',
                                name: 'Pathway without templatename'
                            });

                            return;
                        }

                        // We add a unique key for all paths as new paths don't have ids
                        pw.key = that.getNextKey();
                        that.pathways[pw.key] = pw;

                        if (pw.scalevalue) {
                            that.singlevalShown = true;
                            that.scalevalues[pw.scalevalue].pathways.push(pw.key);
                        } else {
                            var target;

                            if (that.singlevalShown) {
                                target = that.widget.querySelector('[data-pw-multivalues="high-sortorder"]');
                            } else {
                                target = that.widget.querySelector('[data-pw-multivalues="low-sortorder"]');
                            }

                            var templatename = 'totara_competency/partial_pathway';

                            // Display the pathway in the correct div
                            pw.outerborder = true;
                            pw.actions = true;
                            pw.orderable = true;
                            templatePromises.push(templates.renderAppend(templatename, pw, target));
                        }
                    }

                    // Add the scalevalues template
                    templatePromises.push(that.showSinglevaluePaths());

                    Promise.all(templatePromises).then(function(data) {
                        that.calculateSortorderFromDisplay();
                        // We've just read all from the database.
                        // The sortorder calculation may have changed the
                        // pathways' sortorders, but that was not due to
                        // any action from the user.
                        // So - resetting the dirty flag in this case
                        that.dirty = false;

                        // Manually call init on all templates
                        // Run global scan
                        templates.runTemplateJS('');

                        that.showHideNoPaths();
                    }).catch(function(e) {
                        e.fileName = that.filename;
                        e.name = 'Error displaying pathways';
                        notification.exception(e);
                    });
                }).catch(function(e) {
                    e.fileName = that.filename;
                    e.name = 'Error retrieving pathways';
                    notification.exception(e);
                });
            }).catch(function(e) {
                e.fileName = that.filename;
                e.name = 'Error retrieving criteria types';
                notification.exception(e);
            });
        },


        /**
         * Set the id of the scale to use as well as the scale values
         *
         * @return {Promise}
         */
        setScale : function() {
            var that = this;

            return new Promise(function(resolve, reject) {
                // Get the scale id first
                if (!that.scale_id && that.widget.hasAttribute('data-scale-id')) {
                    that.scale_id = that.widget.getAttribute('data-scale-id');
                }

                if (that.scale_id) {
                    that.setScaleValues().then(function(responses) {
                        resolve();
                    });
                } else {
                    // Get from competency
                    if (!that.comp_id) {
                        reject('Competency or scale id is required');
                    }

                    var apiArgs = {
                        'args': {comp_id: that.comp_id},
                        'methodname': that.endpoints.competencyScale
                    };

                    ajax.getData(apiArgs).then(function(responses) {
                        that.scale_id = responses.results;
                        that.setScaleValues().then(function(responses) {
                            resolve();
                        }).catch(function(e) {
                            e.fileName = that.filename;
                            e.name = 'Error setting scalevalues';
                            notification.exception(e);
                        });
                    }).catch(function(e) {
                        e.fileName = that.filename;
                        e.name = 'Error retrieving scale';
                        notification.exception(e);
                    });
                }
            });
        },


        /**
         * Set the scale values of the scale
         *
         * @return {Promise } [description]
         */
        setScaleValues: function() {
            var that = this;

            return new Promise(function(resolve, reject) {
                if (!that.scale_id) {
                    reject('Scale id not set');
                }

                var apiArgs = {
                    'args': {scale_id: that.scale_id},
                    'methodname': that.endpoints.scalevalues};

                ajax.getData(apiArgs).then(function (responses) {
                    that.scalevalues = [];

                    for (var a = 0; a < responses.results.length; a++) {
                        var svalue = responses.results[a];
                        svalue.pathways = [];

                        that.scalevalues[svalue.id] = svalue;
                    }

                    resolve();
                }).catch(function(e) {
                    e.fileName = that.filename;
                    e.name = 'Error retrieving scalevalues';
                    notification.exception(e);
                });
            });
        },

        /**
         * Clear all pathway data
         */
        clearPathways: function() {
            this.pathways = [];
            this.nPaths = 0;

            for (var idx in this.scalevalues) {
                this.scalevalues[idx].pathways = [];
            }

            var divs = this.widget.querySelectorAll('[data-pw-multivalues]');
            for (var a = 0; a < divs.length; a++) {
                divs[a].innerHTML = '';
            }

            divs = this.widget.querySelectorAll('[data-pw-singlevalues]');
            for (var a = 0; a < divs.length; a++) {
                divs[a].innerHTML = '';
            }

            this.markedForDeletionPathways = [];
            this.nDeletedPaths = 0;
        },

        /**
         * Show the scalevalues and all the singlevalue pathways
         *
         * @return {Promise}
         */
        showSinglevaluePaths: function() {
            var that = this,
                target = this.widget.querySelector('[data-pw-singlevalues]'),
                templatename = 'totara_competency/scalevalue_pathways_edit',
                templatedata = {scalevalues: []};


            // Mustache doesn't like non-sequencial array indexes
            for (var id in this.scalevalues) {
                var svalue = this.scalevalues[id],
                    toadd = {
                        id: svalue.id,
                        name: svalue.name,
                        proficient: svalue.proficient,
                        criteriaTypes: this.critTypes,
                        critTypeLevel: 'scalevalue',
                        pathways: [],
                    };

                for (var a = 0; a < svalue.pathways.length; a++) {
                    this.pathways[svalue.pathways[a]].showor = a > 0;
                    this.pathways[svalue.pathways[a]].orderable = false;
                    this.pathways[svalue.pathways[a]].critTypeLevel = this.pathways[svalue.pathways[a]].type;
                    toadd.pathways.push(this.pathways[svalue.pathways[a]]);

                }

                templatedata.scalevalues.push(toadd);
            }

            // Display the pathway in the correct div
            return templates.renderReplace(templatename, templatedata, target);
        },

        /**
         * Add a new pathway
         * If the singlevalue block is shown, new pathways are added below it
         *
         * @param {string} Type of path to add
         */
        addPath: function(pathType) {
            if (pathType == '0') {
                return;
            }

            if (pathType === 'singlevalue') {
                // Just open the scalevalue block.
                // User has to add the actual path the the appropriate scalevalue
                // TODO: Confirm with Jen that this is acceptable behaviour
                this.singlevalShown = true;
                this.showHideNoPaths();
                return;
            }

            var that = this,
                apiArgs = {
                    'args': {type: pathType},
                    'methodname': this.endpoints.definitionTemplate},
                target;

            ajax.getData(apiArgs).then(function(responses) {
                var key = that.getNextKey(),
                    templatename = 'totara_competency/partial_pathway',
                    pw = responses.results;

                pw.key = key;
                pw.outerborder = true;
                pw.actions = true;
                pw.orderable = true;

                that.pathways[key] = pw;
                that.nPaths += 1;

                if (that.singlevalShown) {
                    target = that.widget.querySelector('[data-pw-multivalues="high-sortorder"]');
                } else {
                    target = that.widget.querySelector('[data-pw-multivalues="low-sortorder"]');
                }

                // Display the pathway in the correct div
                templates.renderAppend(templatename, that.pathways[key], target).then(function() {
                    that.calculateSortorderFromDisplay();
                    that.dirty = true;
                    that.showHideNoPaths();
                    templates.runTemplateJS('');
                }).catch(function(e) {
                    e.fileName = that.filename;
                    e.name = 'Error displayin ' + pathType;
                    notification.exception(e);
                });
            }).catch(function(e) {
                e.fileName = that.filename;
                e.name = 'Error retrieving definition template for ' + pathType;
                notification.exception(e);
            });
        },

        /**
         * Hide all criteria type selectors
         */
        hideCritTypeSelectors: function() {
            var critTypeNodes = this.widget.querySelectorAll('[data-crit-type-toggle]');

            for (var a = 0; a < critTypeNodes.length; a++) {
                critTypeNodes[a].classList.add('cc_hidden');
            }
        },

        /**
         * Show the criteria type dropdown for the specific scalevalue
         *
         * @param {node} svWgt Scalevalue widget
         */
        showCriteriaTypeOptions: function(svWgt) {
            var toOpen = svWgt.querySelector('[data-crit-type-toggle="scalevalue"]'),
                expanded = toOpen ? !toOpen.classList.contains('cc_hidden') : false;

            this.hideCritTypeSelectors();

            // Now show the correct list
            if (toOpen && !expanded) {
                toOpen.classList.remove('cc_hidden');
            }
        },

        /**
         * Add a new singlevalue pathway for the specific scalevalue
         *
         * @param {node} svWgt Scalevalue widget to add the path to
         * @param {string} Type of path to add
         * @param {string} Type of criterion to add
         */
        addSinglevaluePath: function(svWgt, pathType, critType) {
            var that = this,
                scalevalue = svWgt.getAttribute('data-scalevalue-id'),
                apiArgs = {
                    'args': {type: pathType},
                    'methodname': this.endpoints.definitionTemplate},
                target = svWgt.querySelector('[data-cc-scalevalue-pw-list]');

            ajax.getData(apiArgs).then(function(responses) {
                var key = that.getNextKey(),
                    templatename = 'totara_competency/partial_pathway',
                    pw = responses.results;

                pw.key = key;
                pw.scalevalue = scalevalue;
                pw.outerborder = false;
                pw.showor = that.scalevalues[pw.scalevalue].pathways.length > 0;
                pw.critType = critType;
                pw.criteriaTypes = that.critTypes;
                pw.critTypeLevel = pathType;
                that.pathways[key] = pw;
                that.scalevalues[scalevalue].pathways.push(key);
                that.nPaths += 1;

                // Display the pathway in the correct div
                templates.renderAppend(templatename, that.pathways[key], target).then(function() {
                    that.hideCritTypeSelectors();
                    that.calculateSortorderFromDisplay();
                    that.dirty = true;
                    templates.runTemplateJS('');
                }).catch(function(e) {
                    e.fileName = that.filename;
                    e.name = 'Error displaying template for ' + pathType;
                    notification.exception(e);
                });
            }).catch(function(e) {
                e.fileName = that.filename;
                e.name = 'Error retrieving definition template for ' + pathType;
                notification.exception(e);
            });
        },

        /**
         * Apply all changes
         * - Find all elements with data-pw-save-endpoint
         * - Foreach
         *     - find the pathway type and key from the closest data-pw-type and data-pw-key
         *     - Call the save api endpoint
         *
         */
        applyChanges: function() {
            var that = this,
                pwList = this.widget.querySelectorAll('[data-pw-save-endpoint]'),
                apiEndpoint,
                apiReqSortorder,
                pwKey,
                apiArgs,
                promiseArr = [];

            if (!this.dirty) {
                return;
            }

            // Generate a key to group all changes together for logging
            // Must convert to seconds to be compatible with PHP timestamps
            var actionTime = Math.round(Date.now() / 1000);

            for (var a = 0; a < pwList.length; a++) {
                apiEndpoint = pwList[a].getAttribute('data-pw-save-endpoint');
                apiReqSortorder = pwList[a].getAttribute('data-pw-save-req-sortorder');
                pwKey = pwList[a].closest('[data-pw-key]').getAttribute('data-pw-key');

                if (this.pathways[pwKey] && this.pathways[pwKey].dirty && this.pathways[pwKey].detail) {
                    apiArgs = {
                        'args': this.pathways[pwKey].detail,
                        'methodname': apiEndpoint};

                    // Add applyKey
                    apiArgs.args.actiontime = actionTime;

                    // If the pathway require the sortorder on the save endpoint, add it to args
                    if (apiReqSortorder) {
                        apiArgs.args.sortorder = this.pathways[pwKey].sortorder;
                    }

                    promiseArr.push(ajax.getData(apiArgs));
                }
            }

            // Deleted pathways
            if (this.nDeletedPaths > 0) {
                var toDelete = [];
                for (pwKey in this.markedForDeletionPathways) {
                    toDelete.push({type: this.markedForDeletionPathways[pwKey].type, id: this.markedForDeletionPathways[pwKey].id});
                }

                apiArgs = {
                    'args': {comp_id: this.comp_id, pathways: toDelete, actiontime: actionTime},
                    'methodname': this.endpoints.deletePathways};
                promiseArr.push(ajax.getData(apiArgs));
            }

            // Overall aggregation
            apiArgs = {
                'args': {comp_id: this.comp_id, type: this.aggType, actiontime: actionTime},
                'methodname': this.endpoints.overallAggregation};
            promiseArr.push(ajax.getData(apiArgs));

            if (promiseArr.length > 0) {
                Promise.all(promiseArr).then(function(data) {
                    that.showNotification('success', 'applysuccess', 'totara_competency', {});
                    that.dirty = false;

                    // TODO: This will reread everything! Can we somehow only re-read the changed ones?
                    //       Main thing to look for - save endpoint of new pws
                    that.updatePage();
                }).catch(function(e) {
                    e.fileName = that.filename;
                    e.name = 'Error applying changes';
                    notification.exception(e);
                });
            }
        },

        /**
         * Show / hide sections depending on the presence of paths
         */
        showHideNoPaths: function() {
            var aggDiv = this.widget.querySelector('[data-cc-pw-aggregation]'),
                noPathsDiv = this.widget.querySelector('.pw_none'),
                singlevalDiv = this.widget.querySelector('[data-pw-singlevalues]'),
                singlevalOption = this.widget.querySelector('[name="cc_add_pathway_select"] option[value="singlevalue"]'),
                presetBtn = this.widget.querySelector('[data-cc-action="linkdefaultpreset"]');

            if (this.nPaths == 0 && !this.singlevalShown) {
                if (aggDiv) {
                    aggDiv.classList.add('cc_hidden');
                }
                if (noPathsDiv) {
                    noPathsDiv.classList.remove('cc_hidden');
                }
                if (presetBtn) {
                    presetBtn.classList.remove('cc_hidden');
                }
            } else {
                if (aggDiv) {
                    aggDiv.classList.remove('cc_hidden');
                }
                if (noPathsDiv) {
                    noPathsDiv.classList.add('cc_hidden');
                }
                if (presetBtn) {
                    presetBtn.classList.add('cc_hidden');
                }
            }

            if (this.singlevalShown) {
                if (singlevalDiv) {
                    singlevalDiv.classList.remove('cc_hidden');
                }
                if (singlevalOption) {
                    singlevalOption.disabled = true;
                }
            } else {
                if (singlevalDiv) {
                    singlevalDiv.classList.add('cc_hidden');
                }
                if (singlevalOption) {
                    singlevalOption.disabled = false;
                }
            }
        },

        /**
         * Calculate and set the sortorders from the order the pathways appear on the screen
         */
        calculateSortorderFromDisplay: function() {
            var pwNodes = this.widget.querySelectorAll('[data-pw-key]'),
                lastOrder = 0,
                pwKey,
                pw;

            for (var a = 0; a < pwNodes.length; a++) {
                pwKey = pwNodes[a].getAttribute('data-pw-key');
                if (this.pathways[pwKey]) {
                    pw = this.pathways[pwKey];
                } else if (this.markedForDeletionPathways[pwKey]) {
                    pw = this.markedForDeletionPathways[pwKey];
                }

                if (pw.sortorder != lastOrder + 1) {
                    this.pathways[pwKey].dirty = true;
                    this.dirty = true;
                }

                pw.sortorder = ++lastOrder;

                pwNodes[a].setAttribute('data-pw-sortorder', pw.sortorder);
            }
        },

        /**
         * Link the pathways in the default preset to this competency
         */
        linkDefaultPreset: function() {
            var that = this,
                apiArgs = {
                    'args': {comp_id: this.comp_id},
                    'methodname': this.endpoints.defaultpreset};

            // Get all the pathways and its detail
            ajax.getData(apiArgs).then(function(responses) {
                that.updatePage();
            }).catch(function(e) {
                e.fileName = that.filename;
                e.name = 'Error linking default preset pathways';
                notification.exception(e);
            });
        },

        /**
         * Discard changes and navigate to the applicable url
         * Confirmation is handled through the 'beforeunload' event handler
         */
        cancelChanges: function() {
            if (this.widget.querySelector('[data-cc-cancel-href]')) {
                var backUrl = this.widget.querySelector('[data-cc-cancel-href]').getAttribute('data-cc-cancel-href');
                window.location.href = backUrl;
            }
        },

        /**
         * Remove the specific pathway
         * If it has an id (exists on the database), show summary detail
         * to indicate that final removal will only happen when changes are applied
         */
        removePathway: function(pwKey) {
            var that = this,
                pwTarget = this.widget.querySelector('[data-pw-key="' + pwKey + '"]'),
                pwOrTarget = this.widget.querySelector('[data-pw-or="' + pwKey + '"]');

            if (this.pathways[pwKey]) {
                this.dirty = true;

                if (this.pathways[pwKey].id && this.pathways[pwKey].id != 0) {
                    // Exists on db - show indication that path will be deleted when changes are applied
                    // Show the pw summary detail which have no actions

                    var detailTarget = pwTarget.querySelector('[data-pathway-detail]'),
                        copyObj = {};

                    copyObj[pwKey] = this.pathways[pwKey];
                    Object.assign(this.markedForDeletionPathways, copyObj);
                    this.nDeletedPaths += 1;
                    delete this.pathways[pwKey];

                    var deleteNodes = pwTarget.querySelectorAll('[data-pw-on-delete]'),
                        deleteAction;
                    for (var a = 0; a < deleteNodes.length; a++) {
                        deleteAction = deleteNodes[a].getAttribute('data-pw-on-delete');
                        if (deleteAction == 'mark') {
                            deleteNodes[a].classList.add('cc_deleted');
                        } else if (deleteAction == 'hide') {
                            deleteNodes[a].classList.add('cc_hidden');
                        }
                    }

                    // Show undo action
                    var removeWgt = pwTarget.querySelector('[data-pw-action="remove"]'),
                        undoWgt = pwTarget.querySelector('[data-pw-action="undo"]');
                    removeWgt.classList.add('cc_hidden');
                    undoWgt.classList.remove('cc_hidden');

                } else {
                    // Remove it totally
                    if (this.pathways[pwKey].scalevalue) {
                        var scalevalue = this.pathways[pwKey].scalevalue,
                            idx = this.scalevalues[scalevalue].pathways.indexOf(pwKey);

                        if (idx >= 0) {
                            this.scalevalues[scalevalue].pathways.splice(idx, 1);
                        }

                        if (pwOrTarget) {
                            pwOrTarget.remove();
                        } else if (this.scalevalues[scalevalue].pathways.length > 0) {
                            // If we removed the top pathway for the scalevalue and there are more pathways,
                            // we need now to remove the new top OR
                            var svWgt = this.widget.querySelector('[data-scalevalue-id="' + scalevalue + '"]');

                            if (svWgt) {
                                pwOrTarget = svWgt.querySelector('[data-pw-or]');
                                if (pwOrTarget) {
                                    pwOrTarget.remove();
                                }
                            }
                        }
                    }

                    delete this.pathways[pwKey];
                    this.nPaths -= 1;
                    this.showHideNoPaths();

                    if (pwTarget) {
                        pwTarget.remove();
                    }

                    this.dirty = true;
                }
            }
        },

        /**
         * Undo the removal of a specific pathway
         */
        undoRemovePathway: function(pwKey) {
            var that = this,
                pwTarget = this.widget.querySelector('[data-pw-key="' + pwKey + '"]'),
                detailTarget = pwTarget.querySelector('[data-pathway-detail]'),
                templateData,
                copyObj = {};

            if (!this.markedForDeletionPathways[pwKey]) {
                return;
            }

            copyObj[pwKey] = this.markedForDeletionPathways[pwKey];
            Object.assign(this.pathways, copyObj);
            delete this.markedForDeletionPathways[pwKey];
            this.nDeletedPaths -= 1;

            var deleteNodes = pwTarget.querySelectorAll('[data-pw-on-delete]'),
                deleteAction;
            for (var a = 0; a < deleteNodes.length; a++) {
                deleteAction = deleteNodes[a].getAttribute('data-pw-on-delete');
                if (deleteAction == 'mark') {
                    deleteNodes[a].classList.remove('cc_deleted');
                } else if (deleteAction == 'hide') {
                    deleteNodes[a].classList.remove('cc_hidden');
                }
            }

            // Show undo action
            var removeWgt = pwTarget.querySelector('[data-pw-action="remove"]'),
                undoWgt = pwTarget.querySelector('[data-pw-action="undo"]');
            removeWgt.classList.remove('cc_hidden');
            undoWgt.classList.add('cc_hidden');
        },

        /**
         * Retrieve the criteria types if not yet done
         *
         * @return {Promise}
         */
        getCriteriaTypes: function() {
            var that = this,
                hasSingleUse = this.widget.hasAttribute('data-singleuse') ? this.widget.getAttribute('data-singleuse') : '0';

            return new Promise(function(resolve, reject) {
                if (that.critTypes && that.critTypes.length > 0) {
                    resolve();
                } else {
                    var apiArgs = {
                            args: {},
                            methodname: that.endpoints.criteriaTypes
                        };

                    ajax.getData(apiArgs).then(function(responses) {
                        var critTypes = responses.results;

                        that.critTypes = [];
                        for (var a = 0; a < critTypes.length; a++) {
                            critTypes[a].disabled = critTypes[a].singleuse && hasSingleUse == '1';
                            that.critTypes.push(critTypes[a]);
                        }

                        resolve();
                    }).catch(function(e) {
                        e.fileName = that.filename;
                        e.name = 'Error getting criteria types';
                        notification.exception(e);
                    });
                }
            });
        },

        /**
         * Toggle availability of single use criteria types on the scalevalue level
         *
         * @param {bool} allowSingleUse
         */
        toggleSingleUseCritTypes: function(allowSingleUse) {
            var critTypeNodes = this.widget.querySelectorAll('[data-crit-type-toggle="scalevalue"]'),
                singleUseActiveNodes,
                singleUseDisabledNodes,
                svWgt,
                scalevalue,
                svAllow,
                pwKeys;

            // Update all criteria type dropdowns on the top level only
            for (var a = 0; a < critTypeNodes.length; a++) {
                svWgt = critTypeNodes[a].closest('[data-scalevalue-id]');
                scalevalue = null;

                if (svWgt) {
                    scalevalue = svWgt.getAttribute('data-scalevalue-id');
                }

                if (!scalevalue) {
                    continue;
                }

                singleUseActiveNodes = critTypeNodes[a].querySelectorAll('[data-crit-type-singleuse-active]');
                singleUseDisabledNodes = critTypeNodes[a].querySelectorAll('[data-crit-type-singleuse-disabled]');

                // Only need to test 1
                if (singleUseActiveNodes.length == 0) {
                    continue;
                }

                // You can't add single use criteria if there is already a pathway resulting
                // in thee scalevalue
                svAllow = allowSingleUse;
                if (svAllow) {
                    pwKeys = this.scalevalues[scalevalue].pathways;
                    for (k = 0; k < pwKeys.length; k++) {
                        if (this.pathways[pwKeys[k]]) {
                            // Not deleted
                            svAllow = false;
                            break;
                        }
                    }
                }

                if (svAllow) {
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
        },

        setOverallAggregation: function() {
            var aggWgt = this.widget.querySelector('[data-cc-pw-agg-changed]'),
                selectedType = aggWgt.querySelector('option:checked'),
                aggActions = this.widget.querySelector('[data-cc-pw-agg-actions]');

            this.aggType = selectedType.value;
            if (selectedType.hasAttribute('data-cc-pw-agg-function')) {
                this.aggFunction = selectedType.getAttribute('data-cc-pw-agg-function');

                if (aggActions) {
                    aggActions.classList.remove('cc_hidden');
                }
            } else {
                this.aggFunction = '';
                if (aggActions) {
                    aggActions.classList.add('cc_hidden');
                }
            }
        },

        /**
         * Enable patwhay ordering through drag and drop
         *
         * Action items that should not be available during ordering should have
         * the data attribute data-cc-on-ordering with either a value 'disable', 'hide' or 'show'
         *
         * Draggable items should have the data attribute data-cc-orderable
         */
        enableOrdering: function() {
            var editWgt = this.widget.querySelector('[data-cc-agg-action="edit"]'),
                moveWgt = this.widget.querySelector('[data-cc-agg-action="move"]'),
                orderableItems = this.widget.querySelectorAll('[data-cc-orderable]'),
                onOrderingItems = this.widget.querySelectorAll('[data-cc-on-ordering]'),
                action;

            for (var a = 0; a < orderableItems.length; a++) {
                orderableItems[a].classList.add('cc_order_border');
                orderableItems[a].draggable = true;
            }

            for (var a = 0; a < onOrderingItems.length; a++) {
                action = onOrderingItems[a].getAttribute('data-cc-on-ordering');

                if (action == 'hide') {
                    onOrderingItems[a].classList.add('cc_hidden');
                } else if (action == 'show') {
                    onOrderingItems[a].classList.remove('cc_hidden');
                } else if (action == 'disable') {
                    onOrderingItems[a].disabled = true;
                }
            }

            if (editWgt) {
                editWgt.disabled = false;
            }
            if (moveWgt) {
                moveWgt.disabled = true;
            }
        },

        /**
         * Disable ordering of the patways
         *   - Reverse the 'show/hide/disable' actions
         *   - Revert draggable
         *   - Remove cc_order_border
         *
         * Calculate new pathway sortorders
         *
         * @return {[type]} [description]
         */
        disableOrdering: function() {
            var editWgt = this.widget.querySelector('[data-cc-agg-action="edit"]'),
                moveWgt = this.widget.querySelector('[data-cc-agg-action="move"]'),
                orderableItems = this.widget.querySelectorAll('[data-cc-orderable]'),
                onOrderingItems = this.widget.querySelectorAll('[data-cc-on-ordering]'),
                action;

            for (var a = 0; a < orderableItems.length; a++) {
                orderableItems[a].classList.remove('cc_order_border');
                orderableItems[a].draggable = false;
            }

            for (var a = 0; a < onOrderingItems.length; a++) {
                action = onOrderingItems[a].getAttribute('data-cc-on-ordering');

                if (action == 'hide') {
                    onOrderingItems[a].classList.remove('cc_hidden');
                } else if (action == 'show') {
                    onOrderingItems[a].classList.add('cc_hidden');
                } else if (action == 'disable') {
                    onOrderingItems[a].disabled = false;
                }
            }

            if (editWgt) {
                editWgt.disabled = true;
            }
            if (moveWgt) {
                moveWgt.disabled = false;
            }

            if (this.aggFunction) {
                if (typeof this[this.aggFunction] === 'function') {
                    this[this.aggFunction]();
                }
            }
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
            var wgt = new AchievementPaths();
            wgt.setParent(parent);
            wgt.events();
            wgt.bubbledEventsListener();
            wgt.initData();
            resolve(wgt);
        });
    };

    return {
        init: init,
    };
 });