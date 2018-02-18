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
 * @author  Sam Hemelryk <sam.hemelryk@totaralms.com>
 * @package tool_sitepolicy
 */

/**
 * @module  tool_sitepolicy/form_element_consentconsent_statement
 * @class   ConsentStatement
 * @author  Sam Hemelryk <sam.hemelryk@totaralms.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'totara_form/form', 'core/templates'], function($, Form) {

    /**
     * ConsentStatement element
     *
     * @class
     * @constructor
     * @augments Form.Element
     *
     * @param {(Form|Group)} parent
     * @param {string} type
     * @param {string} id
     * @param {HTMLElement} node
     */
    function ConsentStatement(parent, type, id, node) {

        if (!(this instanceof ConsentStatement)) {
            return new ConsentStatement(parent, type, id, node);
        }

        Form.Element.apply(this, arguments);

        this.input = null;

    }

    ConsentStatement.prototype = Object.create(Form.Element.prototype);
    ConsentStatement.constructor = ConsentStatement;

    /**
     * Returns a string describing this object.
     * @returns {string}
     */
    ConsentStatement.prototype.toString = function() {
        return '[object ConsentStatement]';
    };

    /**
     * Initialises a new instance of this element.
     * @param {Function} done
     */
    ConsentStatement.prototype.init = function(done) {
        var form = this.getForm(),
            node = $(this.getNode()),
            addbutton = node.find('input[data-rel="addstatement"]'),
            removebutton = node.find('input[data-rel="removestatement"]'),
            name = $('#' + this.id).attr('data-element-name'),
            plusone = $(node.find('input[name="' + name + '__addstatement"]')),
            self = this;
        addbutton.on('click', function() {
            plusone.val(1);
            form.reload();
        });
        removebutton.on('click', function(e) {
            e.preventDefault();
            var me = $(this),
                statement = $(me.closest('div[data-rel="statement-block"]')),
                input = $(statement.find('input[name="' + name + '__removedstatement[' + me.data('index') + ']"]'));

            self.delete_confirm(e, function() {
                input.val(1);
                statement.addClass('hidden');
                statement.find('input[required]').removeAttr('required');
            });
        });
        done();
    };

    /**
     * Confirm deletion of consent statement
     * @param e - Event
     * @param yescallback - Callback if user agreed
     */
    ConsentStatement.prototype.delete_confirm = function(e, yescallback) {
        require(['core/modal_factory', 'core/modal_events', 'core/str'], function(modals, modalEvents, str) {
            var requiredstrings = [
                {key: 'deleteconsentconfirmtitle', component: 'tool_sitepolicy'},
                {key: 'deleteconsentconfirmbody', component: 'tool_sitepolicy'}
            ];
            var stringsPromise = str.get_strings(requiredstrings);
            stringsPromise.done(function(strings) {
                var modalConfig = {
                    type: modals.types.CONFIRM,
                    title: strings[0],
                    body: strings[1]
                };
                var confirm = modals.create(modalConfig, $(e));
                confirm.done(function(modal) {
                    var root = modal.getRoot();
                    root.on(modalEvents.yes, yescallback);
                    modal.show();
                });
            });
        });
    };

    return ConsentStatement;
});