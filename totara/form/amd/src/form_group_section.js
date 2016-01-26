/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @package totara_form
 */

/**
 * Totara form section.
 *
 * @module  totara_form/form_group_section
 * @class   SectionGroup
 * @author  Sam Hemelryk <sam.hemelryk@totaralms.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'totara_form/form', 'core/str'], function($, Form, Str) {

    /**
     * SectionGroup class
     *
     * @class
     * @constructor
     * @augments Form.Element
     *
     * @param {(Form|Group)} parent
     * @param {string} id
     * @param {HTMLElement} node
     */
    function SectionGroup(parent, id, node) {

        if (!(this instanceof SectionGroup)) {
            return new SectionGroup(parent, id, node);
        }

        Form.Group.call(this, parent, id, node);

        this.fieldset = null;
        this.legend = null;
        this.input = null;
        this.link = null;

    }

    SectionGroup.prototype = Object.create(Form.Group.prototype);
    SectionGroup.prototype.constructor = SectionGroup;

    /**
     * Returns a string describing this object.
     * @returns {string}
     */
    SectionGroup.prototype.toString = function() {
        return '[object SectionGroup]';
    };

    /**
     * Initialises a new instance of this group.
     * @param {Function} done
     */
    SectionGroup.prototype.init = function(done) {

        this.fieldset = $('#' + this.id);
        this.legend = $('#' + this.id + ' legend.tf_section_legend');
        this.input = $('#' + this.id + ' input.tf_section_collapsible');

        if (!this.fieldset.hasClass('collapsed') && !this.fieldset.hasClass('collapsible')) {
            done();
            return;
        }
        if (this.fieldset.hasClass('collapsed')) {
            this.legend.html('<a href="#" role="button" aria-expanded="false">' + this.legend.html() + '</a>');
        } else {
            this.legend.html('<a href="#" role="button" aria-expanded="true">' + this.legend.html() + '</a>');
        }

        this.link = $('#' + this.id + ' legend.tf_section_legend a');
        this.link.click($.proxy(this.toggleCollapsed, this));

        SectionGroup.instances.push(this);
        SectionGroup.instancecount++;

        if (SectionGroup.instancecount === 2) {
            SectionGroup.addExpandAll(this.getForm());
        }

        done();

    };

    SectionGroup.prototype.toggleCollapsed = function(event) {
        event.preventDefault();

        if (this.fieldset.hasClass('collapsed')) {
            this.expand();
        } else {
            this.collapse();
        }
    };

    SectionGroup.prototype.expand = function() {
        Form.debug('Expanding section ' + this.legend.text(), this, Form.LOGLEVEL.debug);
        this.fieldset.removeClass('collapsed');
        this.fieldset.addClass('collapsible');
        this.link.attr('aria-expanded', 'true');
        this.input.val(1);
    };

    SectionGroup.prototype.collapse = function() {
        Form.debug('Collapsing section ' + this.legend.text(), this, Form.LOGLEVEL.debug);
        this.fieldset.addClass('collapsed');
        this.fieldset.removeClass('collapsible');
        this.link.attr('aria-expanded', 'false');
        this.input.val(0);
    };

    SectionGroup.getValue = function() {
        // Null should always be returned if we do not know the value.
        // This is a generic element, as such as can't be sure what the value of the thing is.
        // Rather than guess (we should never guess) we will return null.
        // The form will have to check with the server to see what the value is.
        return null;
    };

    /**
     * Static instances count.
     * When 2 or more sections are present we want to print an "Expand all" link.
     */
    SectionGroup.instancecount = 0;
    SectionGroup.instances = [];
    SectionGroup.expandallcontrol = null;
    SectionGroup.addExpandAll = function(form) {
        Form.debug('Adding expand/collapse all for sections', this, Form.LOGLEVEL.info);

        var actionhtml = '<div class="collapsible-actions form-section-expandall-control">';
        actionhtml += '<a href="#" class="collapseexpand" role="button" aria-controls="">&nbsp;-&nbsp;</a>';
        actionhtml += '</div>';

        form.form.prepend(actionhtml);
        SectionGroup.expandallcontrol = $('.form-section-expandall-control');
        Str.get_string('expandall').done(function(expandall) {
            SectionGroup.expandallcontrol.find('a').html(expandall);
        });
        SectionGroup.expandallcontrol.click($.proxy(this.toggleAll, this));
        SectionGroup.expandallcontrol.data('state', 'expand');

    };

    SectionGroup.toggleAll = function(e) {
        e.preventDefault();
        var control = SectionGroup.expandallcontrol,
            state = control.data('state'),
            link = control.find('a'),
            expand = (state === 'expand'),
            i, instance;

        for (i = 0; i < SectionGroup.instances.length; i++) {
            instance = SectionGroup.instances[i];
            if (expand) {
                instance.expand();
            } else {
                instance.collapse();
            }
        }

        if (expand) {
            control.data('state', 'collapse');
            link.addClass('collapse-all');
            Str.get_string('collapseall', 'core').done(function(collapseall) {
                link.html(collapseall);
            });
        } else {
            control.data('state', 'expand');
            link.removeClass('collapse-all');
            Str.get_string('expandall', 'core').done(function(expandall) {
                link.html(expandall);
            });
        }
    };

    return SectionGroup;

});
