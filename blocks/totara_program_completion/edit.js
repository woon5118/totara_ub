/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Eugene Venter <eugene@catalyst.net.nz>
 * @package block
 * @subpackage totara_program_completion
 */
M.block_totara_program_completion_edit = M.block_totara_program_completion_edit || {

    Y: null,

    // Optional php params and defaults defined here, args passed to init method
    // below will override these values.
    config: {},

    /**
     * Module initialisation method called by php js_init_call().
     *
     * @param object    YUI instance
     * @param string    args supplied in JSON format
     */
    init: function(Y, args) {
        // Save a reference to the Y instance (all of its dependencies included).
        this.Y = Y;

        // If defined, parse args into this module's config object.
        if (args) {
            var jargs = Y.JSON.parse(args);
            for (var a in jargs) {
                if (Y.Object.owns(jargs, a)) {
                    this.config[a] = jargs[a];
                }
            }
        }

        // Check jQuery dependency is available.
        if (typeof $ === 'undefined') {
            throw new Error('M.block_totara_program_completion.init()-> jQuery dependency required for this module.');
        }

        var url = M.cfg.wwwroot + '/blocks/totara_program_completion/';

        // Init programs dialog.
        var phandler = new totaraDialog_handler_blockprograms();
        phandler.baseurl = url;
        var pbuttons = {};
        pbuttons[M.util.get_string('save','totara_core')] = function() { phandler._update() };
        pbuttons[M.util.get_string('cancel','moodle')] = function() { phandler._cancel() };

        totaraDialogs['addblockprograms'] = new totaraDialog(
            'addblockprograms',
            'add-block-programs-dialog',
            {
                buttons: pbuttons,
                title: '<h2>' + M.util.get_string('addprograms', 'block_totara_program_completion') + '</h2>'
            },
            url+'findprograms.php?selected=' + this.config.programsselected
                    + '&blockid=' + this.config.blockid
                    + '&sesskey=' + M.cfg.sesskey,
            phandler
        );

    }
};


// Create handler for the dialog.
totaraDialog_handler_blockprograms = function() {
    // Base url
    this.baseurl = '';
    this.program_items = $('input:hidden[name="config_programids"]').val();
    this.program_items = (this.program_items && this.program_items.length > 0) ? this.program_items.split(',') : [];
    this.program_table = $('#block-programs-table');

    this.add_program_delete_event_handlers();

    this.check_table_hidden_status();

};

totaraDialog_handler_blockprograms.prototype = new totaraDialog_handler_treeview_multiselect();

/**
 * Add a row to a table on the calling page.
 * Also hides the dialog and any no item notice.
 *
 * @param string    HTML response
 * @return void
 */
totaraDialog_handler_blockprograms.prototype._update = function(response) {

    var self = this;
    var elements = $('.selected > div > span', this._container);
    var selected = this._get_ids(elements);
    var selected_str = selected.join(',');
    var url = this._dialog.default_url.split("selected=");
    var params = url[1].slice(url[1].indexOf('&'));
    this._dialog.default_url = url[0] + 'selected=' + selected_str + params;

    var newids = new Array();

    // Loop through the selected elements.
    $(selected).each(function(_, itemid) {
        if (!self.program_item_exists(itemid)) {
            newids.push(itemid);
            self.add_program_item(itemid);
        }
    });

    if (newids.length > 0) {
        this._dialog.showLoading();

        var ajax_url = M.cfg.wwwroot + '/blocks/totara_program_completion/program_item.php?itemid=' + newids.join(',') + params;
        $.getJSON(ajax_url, function(data) {
            if (data.error) {
                self._dialog.hide();
                alert(data.error);
                return;
            }
            $.each(data['items'], function(index, html) {
                self.create_item(html);
            });

            self._dialog.hide();
        });
    } else {
        this._dialog.hide();
    }
};

/**
 * Checks if the item id exists.
 */
totaraDialog_handler_blockprograms.prototype.program_item_exists = function(itemid) {
    for (var x in this.program_items) {
        if (this.program_items[x] == itemid) {
            return true;
        }
    }
    return false;
};

totaraDialog_handler_blockprograms.prototype.check_table_hidden_status = function() {
    if (this.program_items.length == 0) {
        $(this.program_table).hide();
    } else {
        $(this.program_table).show();
    }
};

totaraDialog_handler_blockprograms.prototype.add_program_delete_event_handlers = function() {
    // Remove previous click event handlers.
    $('.blockprogramdeletelink', this.program_table).unbind('click');

    // Add fresh event handlers.
    var self = this;
    this.program_table.on('click', '.blockprogramdeletelink', function(event) {
        event.preventDefault();
        self.remove_program_item(this);
    });
};

/**
 * Adds an item.
 */
totaraDialog_handler_blockprograms.prototype.add_program_item = function(itemid) {
    this.program_items.push(itemid);

    $('input:hidden[name="config_programids"]').val(this.program_items.join(','));

    this.check_table_hidden_status();
};

/**
 * Creates an element and then adds it.
 */
totaraDialog_handler_blockprograms.prototype.create_item = function(html) {
    var element = $(html);

    // Add the item element to the table.
    this.program_table.append(element);
};

totaraDialog_handler_blockprograms.prototype.remove_program_item = function(item) {
    var row = $(item).closest('li');
    var itemid = row.data('progid');

    // Remove the item from the array of items.
    this.program_items = $.grep(this.program_items, function (element, x) {
        return (element == itemid);
    }, true);

    // Remove item from interface.
    row.remove();

    this.check_table_hidden_status();

    $('input:hidden[name="config_programids"]').val(this.program_items.join(','));

    var url = this._dialog.default_url.split("selected=");
    var params = url[1].slice(url[1].indexOf('&'));
    this._dialog.default_url = url[0] + 'selected=' + this.program_items.join(',') + params;
};

