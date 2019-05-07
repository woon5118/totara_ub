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
 * @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
 * @package totara_core
 * @subpackage basket_manager
 */

 /* The basket manager, handles common actions for basket storage.
  * It also manages events propagated from the selection_basket AMD including clearing the basket
  * and showing/hiding the basket view
  */
define([], function() {

    /**
     * Class constructor for the BasketManager.
     *
     * @class
     * @constructor
     */
    function BasketManager() {
        if (!(this instanceof BasketManager)) {
            return new BasketManager();
        }
        this.basketRequestArgs = {
            action: '',
            basket: 0,
            ids: []
        };
        this.classBasketWide = 'tw-selectionBasket--wide';
        this.eventListener = 'totara_core/selection_basket';
        this.eventNode = null;
        this.selectedItems = [];
        this.webservices = {
            del: 'totara_core_basket_delete',
            show: 'totara_core_basket_show',
            upd: 'totara_core_basket_update',
        };
        this.widget = null;
    }

    // Listen for propagated events
    BasketManager.prototype.bubbledEventsListener = function() {
        var that = this;

        // Events from Selection basket
        this.eventNode.addEventListener(this.eventListener + ':transition', function(e) {
            // Basket is now small
            if (e.detail.state === 'small') {
                that.onBasketHidden();
            }
            M.util.js_complete('basketTransition');
        });

        // Events from Selection basket
        this.eventNode.addEventListener(this.eventListener + ':update', function(e) {
            var action = e.detail.action;

            if (action === 'show') {
                M.util.js_pending('basketTransition');
                that.onBasketShow();
            } else if (action === 'hide') {
                that.onBasketHide();
            } else if (action === 'clear') {
                that.onBasketClear();
            }
        });
    };

    /**
     * Clears basket request values
     */
    BasketManager.prototype.clearBasketArgs = function() {
        this.basketRequestArgs.action = '';
        this.basketRequestArgs.ids = [];
    };

    /**
     * Get the propagated event name
     *
     * @return {string} event listener
     */
    BasketManager.prototype.getAllArgs = function() {
        return this.basketRequestArgs;
    };

    /**
     * Get the propagated event name
     *
     * @return {string} event listener
     */
    BasketManager.prototype.getBasketKey = function() {
        return this.basketRequestArgs.basket;
    };

    /**
     * Get the propagated event name
     *
     * @return {string} event listener
     */
    BasketManager.prototype.getEventListener = function() {
        return this.eventListener;
    };

    /**
     * Return array of selected items
     *
     * @return {array} event listener
     */
    BasketManager.prototype.getSelectedItems = function() {
        return this.selectedItems;
    };

    /**
     * Return webservice string
     *
     * @param {string} type, 'del', 'show', 'upd'
     * @return {array} event listener
     */
    BasketManager.prototype.getWebservice = function(type) {
        return this.webservices[type];
    };

    /**
     * Return wide class variation name
     *
     * @return {string} class name
     */
    BasketManager.prototype.getWideClass = function() {
        return this.classBasketWide;
    };

    /* Clear basket selection */
    BasketManager.prototype.onBasketClear = function() { /* Null */ };

    /* Hidden basket selection */
    BasketManager.prototype.onBasketHidden = function() { /* Null */ };

    /* Hide basket selection */
    BasketManager.prototype.onBasketHide = function() { /* Null */ };

    /* Show basket selection */
    BasketManager.prototype.onBasketShow = function() { /* Null */ };

    /* on basket update */
    BasketManager.prototype.onBasketUpdate = function() { /* Null */ };

    /**
     * Set basket request args
     *
     * @param {string} action, 'add' or 'remove'
     * @param {int} id
     */
    BasketManager.prototype.setBasketArgs = function(action, id) {
        // If current basket action does not match new one, clear it
        if (this.basketRequestArgs.action !== action) {
            this.basketRequestArgs.ids = [];
            this.basketRequestArgs.action = action;
        }

        // ID already included
        if (this.basketRequestArgs.ids.indexOf(id) > -1) {
            return;
        }

        this.basketRequestArgs.ids.push(id);
    };

    /**
     * Set unique basket request key for storing selection
     *
     * @param {string} key
     */
    BasketManager.prototype.setBasketKey = function(key) {
        this.basketRequestArgs.basket = key;
    };

    /**
     * Set selected items returned from basket
     *
     * @param {array} items
     */
    BasketManager.prototype.setSelectedItems = function(items) {
        if (!items) {
            items = [];
        }
        this.selectedItems = items;
    };

    /**
     * Toggle basket show/hide btn
     */
    BasketManager.prototype.toggleExpandedView = function() {
        this.widget.classList.toggle(this.classBasketWide);
    };

    /**
     * Toggle basket show/hide btn
     */
    BasketManager.prototype.toggleShowBasketBtn = function() {
        this.widget.classList.toggle('tw-selectionBasket__displayed');
    };

    /**
     * Render the basket with the provided data
     *
     * @param {Object} data
     * @return {Promise}
     */
    BasketManager.prototype.renderBasket = function(data) {
        var count,
            selectionBasket = this.widget,
            that = this;

        this.setSelectedItems(data.ids);
        this.clearBasketArgs();

        return new Promise(function(resolve) {
            // Update count
            if (selectionBasket) {
                count = 0;
                if (that.selectedItems) {
                    count = that.selectedItems.length;
                }
                selectionBasket.setAttribute('data-tw-selectionbasket-countchange', count);
            }
            // event basket rendered
            that.onBasketUpdate();
            resolve();
        });
    };

    /**
     * Set page number for request
     *
     * @param {node} parent
     */
    BasketManager.prototype.setParent = function(parent) {
        this.widget = parent;
    };

    /**
     * Set page number for request
     *
     * @param {node} parent
     */
    BasketManager.prototype.setParentEventNode = function(parent) {
        this.eventNode = parent;
    };

    /**
     * Return the basket show args for webservice
     *
     * @return {Object}
     */
    BasketManager.prototype.updateBasketView = function() {
        return {
            args: {basket: this.getBasketKey()},
            callback: [this.renderBasket.bind(this)],
            methodname: this.getWebservice('show')
        };
    };

    /**
     * Return the basket update args for webservice
     *
     * @return {Object}
     */
    BasketManager.prototype.updateBasketValAndView = function() {
        return {
            args: this.getAllArgs(),
            callback: [this.renderBasket.bind(this)],
            methodname: this.getWebservice('upd')
        };
    };

    /**
     * Return the args for delete basket webservice
     *
     * @return {Object}
     */
    BasketManager.prototype.updateDeleteBasket = function() {
        return {
            args: {basket: this.getBasketKey()},
            callback: [this.renderBasket.bind(this)],
            methodname: this.getWebservice('del')
        };
    };

    /**
     * initialisation method
     *
     * @param {node} parent
     * @param {string} key
     * @returns {Object} promise
     */
    var init = function(parent, key) {

        var basketNode = parent.querySelector('[data-tw-selectionBasket]');

        var wgt = new BasketManager();
        wgt.setParent(basketNode);
        wgt.setParentEventNode(parent);
        wgt.setBasketKey(key);
        wgt.bubbledEventsListener();
        return wgt;
    };

    return {
        init: init
    };
 });