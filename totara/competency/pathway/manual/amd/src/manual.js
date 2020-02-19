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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @package pathway_manual
 */

define(['core/templates', 'core/notification', 'core/ajax', 'totara_core/modal_list'],
    function(templates, notification, ajax, ModalList) {

    /**
     * Class constructor for PwManual.
     *
     * @class
     * @constructor
     */
    function PwManual() {
        if (!(this instanceof PwManual)) {
            return new PwManual();
        }

        this.widget = '';

        /**
         * Pathway data.
         * This object should only contain the data to be sent on the save api endpoint.
         *
         * @type {Object}
         */
        this.pathway = {
            id: 0,
            sortorder: 0,
            roles: []
        };

        // Key to use in achievementPath events
        this.pwKey = '';

        this.rolesPicker = null;
        this.fullRoles = []; // Contains the full role data indexed by the id to assist with deletion of pathway.roles
        this.roleIds = []; // Contains the ids of the selected roles

        this.endpoints = {
            create: 'pathway_manual_create',
            update: 'pathway_manual_update',
            detail: 'pathway_manual_get_detail',
            allroles: 'pathway_manual_get_roles',
        };

        this.filename = 'learning_plan.js';
    }

    PwManual.prototype = {

        /**
         * Add event listeners for PwManual
         *
         */
        events: function() {
            var that = this;

            this.widget.addEventListener('click', function(e) {
                if (!e.target) {
                    return;
                }

                if (e.target.closest('[data-pw-manual-action]')) {
                    var action = e.target.closest('[data-pw-manual-action]').getAttribute('data-pw-manual-action');

                    if (action === 'addraters') {
                        that.pickRaters();
                    }
                }

                if (e.target.closest('[data-pw-item-remove]')) {
                    if (e.target.closest('[data-pw-item-value]')) {
                        var id = e.target.closest('[data-pw-item-value]').getAttribute('data-pw-item-value');

                        that.removeRole(id);
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
         * Initialise the data and display it
         *
         * @param {node}
         */
        initData: function(wgt) {
            var that = this,
                pwWgt = this.widget.closest('[data-pw-key]'),
                pwKey = 0,
                pwId = 0,
                idWgt = this.widget.closest('[data-pw-id]'),
                apiArgs,
                detailPromise;

            if (pwWgt) {
                pwKey = pwWgt.getAttribute('data-pw-key') ? pwWgt.getAttribute('data-pw-key') : 0;
            }

            if (idWgt) {
                pwId = idWgt.getAttribute('data-pw-id') ? idWgt.getAttribute('data-pw-id') : 0;
            }

            if (pwId !== 0) {
                apiArgs = {
                    args: {id: pwId},
                    methodname: this.endpoints.detail
                };

                detailPromise = ajax.getData(apiArgs);
            } else {
                // For new paths we only have a key not an id
                detailPromise = that.createEmptyPw();
            }

            detailPromise.then(function (responses) {
                var pw = responses.results,
                    target;

                that.pwKey = pwKey;

                // Set the save-endpoint data attribute
                target = wgt;
                if (pwId === 0) {
                    // New pw - we need the competency_id
                    var compIdWgt = document.querySelector('[data-comp-id]'),
                        compId = 1;

                    if (compIdWgt) {
                        compId = compIdWgt.getAttribute('data-comp-id') ? compIdWgt.getAttribute('data-comp-id') : 1;
                    }

                    that.pathway.competency_id = compId;
                    delete that.pathway.id;

                    target.setAttribute('data-pw-save-endpoint', that.endpoints.create);
                } else {
                    that.pathway.id = pwId;
                    target.setAttribute('data-pw-save-endpoint', that.endpoints.update);
                }

                // We index the roles with the ids to make it easier when adding / removing roles
                var roles = pw.roles,
                    rolesPromises = [];

                target = wgt.querySelector('.pw_roles');

                // Set the patway detail
                that.pathway.roles = [];
                that.roleIds = [];
                for (var a = 0; a < pw.roles.length; a++) {
                    that.pathway.roles.push(pw.roles[a].role);
                    that.roleIds.push(pw.roles[a].id);
                    that.fullRoles[pw.roles[a].id] = pw.roles[a];

                    // Display the role
                    rolesPromises.push(templates.renderAppend('totara_competency/partial_item', {value: roles[a].id, text: roles[a].name}, target));
                }

                that.showHideNoRaters();

                // Init the picker
                rolesPromises.push(that.initRolesPicker());

                Promise.all(rolesPromises).then(function() {
                    that.triggerEvent('update', {pathway: that.pathway});
                }).catch(function(e) {
                    notification.exception({
                        fileName: that.filename,
                        message: e[0] + ' modal: ' + e[1],
                        name: 'Error initialising manual data'
                    });
                });
            }).catch(function(e) {
                e.fileName = that.filename;
                e.name = 'Error retrieving manual detail';
                notification.exception(e);
            });
        },

        /**
         * Initialise the roles picker
         *
         * @return {Promise}
         */
        initRolesPicker: function() {
            var that = this;

            return new Promise(function(resolve) {
                var pickerData = {
                    key: 'pwManualRolesPicker_' + that.pwKey,
                    title: [{
                        key: 'selectraters',
                        component: 'pathway_manual'
                    }],
                    list: {
                        map: {
                            cols: [{
                                dataPath: 'name',
                                headerString: {
                                    key: 'selectraters',
                                    component: 'pathway_manual',
                                },
                            }],
                        },
                        service: that.endpoints.allroles,
                    },
                    onSaved: function(picker, items, itemData) {
                        that.updateRoles(itemData);
                    },
                };

                ModalList.adder(pickerData).then(function(modal) {
                    that.rolesPicker = modal;
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
         * Open the picker to add raters
         *
         */
        pickRaters: function () {
            var that = this;

            if (!this.rolesPicker) {
                this.initRolesPicker().then(function(modal) {
                    modal.show(that.roleIds);
                }).catch(function(e) {
                    notification.exception({
                        fileName: that.filename,
                        message: e[0] + ' modal: ' + e[1],
                        name: 'Error loading modal list adder'
                    });
                });
            } else {
                that.rolesPicker.show(that.roleIds);
            }
        },

        updateRoles: function(roles) {
            var that = this,
                target = this.widget.querySelector('.pw_roles'),
                promiseArr = [],
                templateData = {};

            for (var a = 0; a < roles.length; a++) {
                var role = roles[a];

                if (this.roleIds.indexOf(role.id) < 0) {
                    this.pathway.roles.push(role.role);
                    this.roleIds.push(role.id);
                    this.fullRoles[role.id] = role;

                    templateData = {value: role.id, text: role.name};
                    promiseArr.push(templates.renderAppend('totara_competency/partial_item', templateData, target));
                }
            }

            // Hide the noraters warning
            that.showHideNoRaters();

            if (promiseArr.length > 0) {
                Promise.all(promiseArr).then(function() {
                    that.triggerEvent('update', {pathway: that.pathway});
                    that.triggerEvent('dirty', {});
                }).catch(function(e) {
                    e.fileName = that.filename;
                    e.name = 'Error showing updated roles';
                    notification.exception(e);
                });
            }
        },

        removeRole: function(id) {
            id = parseInt(id);

            var idIndex = this.roleIds.indexOf(id),
                roleIndex = -1,
                target;

            if (idIndex >= 0) {
                if (this.fullRoles[id]) {
                    roleIndex = this.pathway.roles.indexOf(this.fullRoles[id].role);
                    delete this.fullRoles[id];
                }

                if (roleIndex >= 0) {
                    this.pathway.roles.splice(roleIndex, 1);
                }

                this.roleIds.splice(idIndex, 1);

                target = this.widget.querySelector('.pw_item[data-pw-item-value="' + id + '"');
                if (target) {
                    target.remove();

                    this.triggerEvent('update', {pathway: this.pathway});
                    this.triggerEvent('dirty', {});
                }
            }

            // Show noraters warning
            this.showHideNoRaters();
        },

        /**
         * Create an empty pathway with the key
         *
         * @param {int} key
         * @return {Promise}
         */
        createEmptyPw: function() {
            return new Promise(function(resolve) {
                resolve({
                    results: {
                        roles: [],
                    }
                });
            });
        },

        /**
         * Show or hide the No Raters warning depending on the number of raters
         */
        showHideNoRaters: function() {
            var target = this.widget.querySelector('.pw_manual_error_noraters');
            if (this.roleIds.length == 0) {
                target.classList.remove('cc_hidden');
            } else {
                target.classList.add('cc_hidden');
            }
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
            var wgt = new PwManual();
            wgt.setParent(parent);
            wgt.events();
            wgt.initData(parent);
            resolve(wgt);
        });
    };

    return {
        init: init
    };
});
