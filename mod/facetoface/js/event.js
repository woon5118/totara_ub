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
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */

 /* global $, totaraDialog, totaraDialogs */

M.totara_f2f_room = M.totara_f2f_room || {

    Y: null,
    // Optional php params and defaults defined here, args passed to init method
    // below will override these values
    config: {},
    // Public handler reference for the dialog
    totaraDialog_handler_preRequisite: null,

    /**
     * Per-date fields that should be copied to clone event.
     */
    clonefields: ['roomids', 'assetids', 'timestart', 'timefinish', 'sessiontimezone'],

    /**
     * Base url
     * @var string
     */
    url: M.cfg.wwwroot+'/mod/facetoface/',

    /**
     * module initialisation method called by php js_init_call()
     *
     * @param object    YUI instance
     * @param object    configuration from PHP script
     */
    init: function(Y, config){
        // Save a reference to the Y instance (all of its dependencies included)
        this.Y = Y;
        this.config = config;

        var module = this;

        // Check jQuery dependency is available
        if (typeof $ === 'undefined') {
            throw new Error('M.totara_f2f_room.init()-> jQuery dependency required for this module to function.');
        }

        this.init_dates();
        this.init_rooms();
        this.init_assets();

        // Count of all dates (active and removed).
        var cntdates = Number($('input[name="cntdates"]').val());

        // Use room capacity button.
        var $capacitybtn = $('<input name="defaultcapacity" type="button" id="id_defaultcapacity">');
        $capacitybtn.val(M.util.get_string('useroomcapacity', 'facetoface'));
        $capacitybtn.click(function(e) {
            e.preventDefault();
            var min = 0;
            for (var offset = 0; offset < cntdates; offset++) {
                if ($('input[name="datedelete[' + offset + ']"]').val() > 0) {
                    continue;
                }

                var current = Number($('input[name="roomcapacity[' + offset + ']"]').val());
                if (min === 0 || (min > current && current > 0)) {
                    min = current;
                }
            }
            if (min > 0) {
                $('#id_capacity').val(min);
            }
        });
        $capacitybtn.insertAfter($('#id_capacity'));

        /**
         * Check if all dates are removed/there no dates and show notifications, disable certain elements
         */
        var dates_count_changed = function() {
            var cnt = 0;
            var roomcnt = 0;
            for (var offset = 0; offset < cntdates; offset++) {
                if ($('input[name="datedelete[' + offset + ']"]').val() > 0) {
                    continue;
                }
                var ids = [];
                var $input = $('input[name="roomids[' + offset + ']"]');
                if ($input.val().length > 0) {
                    ids = $input.val().split(',');
                    ids.forEach(function() {
                        roomcnt++;
                    });
                }
                cnt++;
            }
            if (roomcnt) {
                $capacitybtn.attr('disabled', false);
            } else {
                $capacitybtn.attr('disabled', true);
            }

            if (cnt === 0) {
                var $sesstable = $('.sessiondates table.f2fmanagedates');
                $sesstable.hide();
                $sesstable.parent().append($('<div class="nodates_notification">' + M.util.get_string('nodatesyet', 'facetoface') + '</div>'));
            }
        };
        dates_count_changed();

        // Remove date.
        $('a.dateremove').each(function() {
            var offset = $(this).data('offset');
            // Delete date set field "datedelete[offset]" to 1 and hide row (do not remove row,
            // as it needs to be submitted in order to process form).
            if ($('input[name="datedelete[' + offset + ']"]').val() > 0) {
                $(this).closest('tr').hide();
            }
            $(this).click(function(e) {
                e.preventDefault();
                $('input[name="datedelete[' + offset + ']"]').val(1);
                $(this).closest('tr').hide();
                dates_count_changed();
            });
        });

        // Kamino.
        $('a.dateclone').click(function() {
            var $form = $(this).closest('form');
            var srcoffset = $(this).data('offset');
            // Offset starts with 0, so no increment is needed here.
            var newoffset = cntdates;

            //Generate new fields and copy their values.
            module.clonefields.forEach(function(name){
                var $newelem = $('<input type="hidden" name="' + name + '[' + newoffset + ']"/>');
                var srcval = $('input[name="' + name + '[' + srcoffset + ']"]').val();
                $newelem.val(srcval);
                $form.append($newelem);
            });

            // Submit via date_add_fields.
            $('input[name="date_add_fields"]').click();
        });

        // Add new date.
        $('input[name="date_add_fields"]').click(function(){
            skipClientValidation = true;
            $('input[name="cntdates"]').val(cntdates+1);
        });

        // Show sesion dates.
        $('.sessiondates').removeClass('hidden');
    },

    init_dates: function() {
        var url = this.url;
            // Select date dialog.
        $('.mod_facetoface-show-selectdate-dialog').each(function() {
            var offset = $(this).data('offset');
            var $dateitem = $('#timeframe-text' + offset);

            if ($('input[name="datedelete[' + offset + ']"]').val() > 0) {
                return;
            }

            // Init date display.
            $dateitem.empty();
            $dateitem.text(M.util.get_string('loadinghelp', 'moodle'));
            $.post(
                url + 'events/ajax/date_item.php',
                {
                    timestart: $('input[name="timestart[' + offset + ']"]').val(),
                    timefinish: $('input[name="timefinish[' + offset + ']"]').val(),
                    sesiontimezone: $('input[name="sessiontimezone[' + offset + ']"]').val(),
                    sesskey: M.cfg.sesskey
                },
                function(elem) {
                    $dateitem.empty();
                    $dateitem.html(elem);
                    $dateitem.addClass('nonempty');
                },
                'json'
            );

            // Date dialog & handler.
            var handler = new totaraDialog_handler_form();

            var buttonsObj = {};
            buttonsObj[M.util.get_string('ok','moodle')] = function() { handler.submit(); };
            buttonsObj[M.util.get_string('cancel','moodle')] = function() { handler._cancel(); };

            // Change behaviour of update function.
            handler._updatePage = function(response) {
                try {
                    // We expect json if dates processed without errors.
                    var dates = $.parseJSON(response);
                    $('input[name="timestart[' + offset + ']"]').val(dates.timestart);
                    $('input[name="timefinish[' + offset + ']"]').val(dates.timefinish);
                    $('input[name="sessiontimezone[' + offset + ']"]').val(dates.sessiontimezone);
                    $('#timeframe-text' + offset).html(dates.html);

                    handler._dialog.hide();
                } catch(e) {
                    this._dialog.render(response);
                }
            };

            totaraDialogs['selectdate'+offset] = new totaraDialog(
                'selectdate'+offset+'-dialog',
                $(this).attr('id'),
                {
                    buttons: buttonsObj,
                    title: '<h2>' + M.util.get_string('dateselect', 'facetoface') + '</h2>'
                },
                function() {
                    return url + 'events/ajax/sessiondates.php?sessiondateid=' + $('input[name="sessiondateid[' + offset + ']"]').val() +
                        '&facetofaceid=' + M.totara_f2f_room.config.facetofaceid +
                        '&roomids=' + $('input[name="roomids[' + offset + ']"]').val() +
                        '&assetids=' + $('input[name="assetids[' + offset + ']"]').val() +
                        '&timezone=' + encodeURIComponent($('input[name="sessiontimezone[' + offset + ']"]').val()) +
                        '&start=' + $('input[name="timestart[' + offset + ']"]').val() +
                        '&finish=' + $('input[name="timefinish[' + offset + ']"]').val() +
                        '&sesskey=' + M.cfg.sesskey;
                },
                handler
            );
        });
    },

    /**
     * Prepare rooms dialogs and ajax updates.
     */
    init_rooms: function() {
        var url = this.url;

        /**
         * Create DOM for room with attached action buttons and handlers.
         * @param data room data (name, custom, etc)
         * @param $input associated hidden input that stored ids
         * @return
         */
        var render_room_item = function(data, $input, offset) {
            var $elem = $('<li class="roomname" id="roomname' + offset + '_' + data.id + '" data-roomid="' + data.id + '" data-custom="' + data.custom + '" data-capacity="' + data.capacity + '">' + data.name + '</li>');
            require(['core/templates'], function(templates) {
                if (Number(data.custom) > 0) {
                    var $editbutton = $('<a href="#"></a>');
                    $editbutton.click(function(e) {
                        e.preventDefault();
                        M.totara_f2f_room.config.editroom = data.id;
                        totaraDialogs['editcustomroom' + offset].config.title = '<h2>' + M.util.get_string('editroom', 'facetoface') + '</h2>';
                        totaraDialogs['editcustomroom' + offset].open();
                    });
                    $elem.append($editbutton);
                    templates.renderIcon('edit', M.util.get_string('editroom', 'facetoface')).done(function(html) {
                        $editbutton.html(html);
                    });
                }

                var $deletebutton = $('<a href="#"></a>');
                $deletebutton.click(function(e) {
                    e.preventDefault();
                    var $li = $deletebutton.closest('li');
                    var delid = $li.data('roomid') + "";
                    var ids = $input.val().split(',');
                    var index = ids.indexOf(delid);
                    if (index > -1) {
                        ids.splice(index, 1);
                        $input.val(ids.join());
                    }
                    $li.remove();
                    var min = 0;
                    ids.forEach(function(id) {
                        var li = $('#roomname' + offset + '_' + id);
                        var current = Number(li.data().capacity);
                        if (min === 0 || (min > current && current > 0)) {
                            min = current;
                        }
                        $('input[name="roomcapacity[' + offset + ']"]').val(min);
                    });
                });
                $elem.append($deletebutton);
                templates.renderIcon('delete', M.util.get_string('delete', 'totara_core')).done(function(html) {
                    $deletebutton.html(html);
                });
            });
            return $elem;
        };

        // Select room dialog.
        $('.show-selectrooms-dialog').each(function() {
            var offset = $(this).data('offset');
            var $roomlist = $('#roomlist' + offset);
            var $input = $('input[name="roomids[' + offset + ']"]');

            if ($('input[name="datedelete[' + offset + ']"]').val() > 0) {
                return;
            }

            // Init rooms.
            function load_rooms() {
                var inititems = $input.val();
                if (inititems.length) {
                    $roomlist.append($('<li>' + M.util.get_string('loadinghelp', 'moodle') + '</li>'));
                    $.post(
                        url + 'room/ajax/room_item.php',
                        {
                            facetofaceid:  M.totara_f2f_room.config.facetofaceid,
                            itemids: inititems,
                            sesskey: M.cfg.sesskey
                        },
                        function(data) {
                            $roomlist.empty();
                            var min = 0;
                            data.forEach(function(elem) {
                                var $elem = render_room_item(elem, $input, offset);
                                var current = Number(elem.capacity);
                                if (min === 0 || (min > current && current > 0)) {
                                    min = current;
                                }
                                $('input[name="roomcapacity[' + offset + ']"]').val(min);
                                $roomlist.append($elem);
                            });
                        },
                        'json'
                    );
                }
            }
            load_rooms();

            // Create new room dialog handler.
            var editcustomroomhandler = new totaraDialog_handler_form();
            editcustomroomhandler.every_load = function() {
                totaraDialog_handler_form.prototype.every_load.call(this);
                handler._dialog.hide();
            };
            // Change behaviour of update function.
            editcustomroomhandler._updatePage = function(response) {
                try {
                    // We expect json if dates processed without errors.
                    var elem = $.parseJSON(response);
                    var ids = [];
                    if ($input.val().length > 0) {
                        ids = $input.val().split(',');
                    }
                    if (ids.indexOf(elem.id.toString()) === -1) {
                        ids.push(elem.id);
                    }
                    $input.val(ids.toString());
                    load_rooms();
                    editcustomroomhandler._dialog.hide();
                } catch(e) {
                    this._dialog.render(response);
                }
            };

            // Create new room dialog.
            var buttonsObj = {};
            buttonsObj[M.util.get_string('ok', 'moodle')] = function() { editcustomroomhandler.submit(); };
            buttonsObj[M.util.get_string('cancel', 'moodle')] = function() { editcustomroomhandler._cancel(); };

            totaraDialogs['editcustomroom' + offset] = new totaraDialog(
                'editcustomroom' + offset + '-dialog',
                'show-editcustomroom' + offset + '-dialog',
                {
                    buttons: buttonsObj,
                    title: '<h2>' + M.util.get_string('createnewroom', 'facetoface') + '</h2>'
                },
                function() {
                    var id = 0;
                    // Store id in M.totara_f2f_room.config for now to allow edit custom rooms.
                    if (typeof M.totara_f2f_room.config.editroom !== "undefined") {
                        id = Number(M.totara_f2f_room.config.editroom);
                        M.totara_f2f_room.config.editroom = 0;
                    }
                    return url + 'room/ajax/room_edit.php?id=' + id + '&f=' + M.totara_f2f_room.config.facetofaceid +
                        '&s=' + M.totara_f2f_room.config.sessionid + '&sesskey=' + M.cfg.sesskey;
                },
                editcustomroomhandler
            );

            // Select room dialog handler.
            var handler = new totaraDialog_handler_treeview_multiselect();
            handler._update = function() {
                var elements = $('.selected > div > span', this._container);
                var ids = this._get_ids(elements);
                $roomlist.empty();
                // Display elements.
                var min = 0;
                ids.forEach(function(id) {
                    var $item = $('#item_' + id, this._container).clone();
                    // Get name and render room.
                    $('span', $item).remove();
                    var $elem = render_room_item($item.data(), $input, offset);
                    var current = Number($item.data().capacity);
                    if (min === 0 || (min > current && current > 0)) {
                        min = current;
                    }
                    $('input[name="roomcapacity[' + offset + ']"]').val(min);
                    if (min > 0) {
                        $('input[name="defaultcapacity"]').attr('disabled', false);
                    }
                    $roomlist.append($elem);
                });
                $input.val(ids.join());
                this._dialog.hide();
            };

            // Select room dialog.
            var buttonsObj = {};
            buttonsObj[M.util.get_string('ok','moodle')] = function() { handler._update(); };
            buttonsObj[M.util.get_string('cancel','moodle')] = function() { handler._cancel(); };

            handler.oldLoad = handler.load;

            handler.load = function(response) {
                handler.oldLoad(response);
                var context = $(".ui-dialog [id^='selectrooms'][id$='-dialog']"),
                    height = context.height() - $('.dialog-footer', context).outerHeight();

                $('.select', context).outerHeight(height);
            };

            totaraDialogs['selectrooms' + offset] = new totaraDialog(
                'selectrooms' + offset + '-dialog',
                $(this).attr('id'),
                {
                    buttons: buttonsObj,
                    title: '<h2>' + M.util.get_string('chooserooms', 'facetoface') + '</h2>'
                },
                function() {
                    var sessionid = M.totara_f2f_room.config.sessionid;
                    if (Number(M.totara_f2f_room.config.clone) == 1) {
                        sessionid = 0;
                    }
                    return url + 'room/ajax/sessionrooms.php?sessionid=' + sessionid +
                        '&facetofaceid=' + M.totara_f2f_room.config.facetofaceid +
                        '&timestart=' + $('input[name="timestart[' + offset + ']"]').val() +
                        '&timefinish=' + $('input[name="timefinish[' + offset + ']"]').val() +
                        '&selected=' + $('input[name="roomids[' + offset + ']"]').val() +
                        '&offset=' + offset +
                        '&sesskey=' + M.cfg.sesskey;
                },
                handler
            );
        });
    },

    /**
     * Prepare assets dialogs and ajax updates.
     */
    init_assets: function() {
        var url = this.url;

        /**
         * Create DOM for asset with attached action buttons and handlers.
         * @param data asset data (name, custom, etc)
         * @param $input associated hidden input that stored ids
         * @return
         */
        var render_asset_item = function(data, $input, offset) {
            var $elem = $('<li class="assetname" id="assetname' + offset + '_' + data.id + '" data-assetid="' + data.id + '" data-custom="' + data.custom + '">' + data.name + '</li>');
            require(['core/templates'], function (templates) {
                if (Number(data.custom) > 0) {
                    var $editbutton = $('<a href="#"></a>');
                    $editbutton.click(function(e) {
                        e.preventDefault();
                        M.totara_f2f_room.config.editasset = data.id;
                        totaraDialogs['editcustomasset' + offset].config.title = '<h2>' + M.util.get_string('editasset', 'facetoface') + '</h2>';
                        totaraDialogs['editcustomasset' + offset].open();
                    });
                    $elem.append($editbutton);
                    templates.renderIcon('edit', M.util.get_string('editasset', 'facetoface')).done(function (html) {
                        $editbutton.html(html);
                    });
                }

                var $deletebutton = $('<a href="#"></a>');
                $deletebutton.click(function(e) {
                    e.preventDefault();
                    var $li = $deletebutton.closest('li');
                    var delid = $li.data('assetid') + "";
                    var ids = $input.val().split(',');
                    var index = ids.indexOf(delid);
                    if (index > -1) {
                        ids.splice(index, 1);
                        $input.val(ids.join());
                    }
                    $li.remove();
                });
                $elem.append($deletebutton);
                templates.renderIcon('delete', M.util.get_string('delete', 'totara_core')).done(function (html) {
                    $deletebutton.html(html);
                });
            });
            return $elem;
        };

        // Select assets dialog.
        $('.show-selectassets-dialog').each(function() {
            var offset = $(this).data('offset');
            var $assetlist = $('#assetlist' + offset);
            var $input = $('input[name="assetids[' + offset + ']"]');

            if ($('input[name="datedelete[' + offset + ']"]').val() > 0) {
                return;
            }

            // Init assets.
            function load_assets() {
                var inititems = $input.val();
                if (inititems.length) {
                    $assetlist.append($('<li>' + M.util.get_string('loadinghelp', 'moodle') + '</li>'));
                    $.post(
                        url + 'asset/ajax/asset_item.php',
                        {
                            facetofaceid:  M.totara_f2f_room.config.facetofaceid,
                            itemids: inititems,
                            sesskey: M.cfg.sesskey
                        },
                        function(data) {
                            $assetlist.empty();
                            data.forEach(function(elem){
                                var $elem = render_asset_item(elem, $input, offset);
                                $assetlist.append($elem);
                            });
                        },
                        'json'
                    );
                }
            }
            load_assets();

            // Create new asset dialog handler.
            var editcustomassethandler = new totaraDialog_handler_form();
            editcustomassethandler.every_load = function() {
                totaraDialog_handler_form.prototype.every_load.call(this);
                handler._dialog.hide();
            };
            // Change behaviour of update function.
            editcustomassethandler._updatePage = function(response) {
                try {
                    // We expect json if dates processed without errors.
                    var elem = $.parseJSON(response);
                    var ids = [];
                    if ($input.val().length > 0) {
                        ids = $input.val().split(',');
                    }
                    if (ids.indexOf(elem.id.toString()) === -1) {
                        ids.push(elem.id);
                    }
                    $input.val(ids.toString());
                    load_assets();
                    editcustomassethandler._dialog.hide();
                } catch(e) {
                    this._dialog.render(response);
                }
            };

            // Create new room dialog.
            var buttonsObj = {};
            buttonsObj[M.util.get_string('ok','moodle')] = function() { editcustomassethandler.submit(); };
            buttonsObj[M.util.get_string('cancel','moodle')] = function() { editcustomassethandler._cancel(); };

            totaraDialogs['editcustomasset' + offset] = new totaraDialog(
                'editcustomasset' + offset + '-dialog',
                'show-editcustomasset' + offset + '-dialog',
                {
                    buttons: buttonsObj,
                    title: '<h2>' + M.util.get_string('createnewasset', 'facetoface') + '</h2>'
                },
                function() {
                    var id = 0;
                    // Store id in M.totara_f2f_room.config for now to allow edit custom assets.
                    if (typeof M.totara_f2f_room.config.editasset !== "undefined") {
                        id = Number(M.totara_f2f_room.config.editasset);
                        M.totara_f2f_room.config.editasset = 0;
                    }
                    return url + 'asset/ajax/asset_edit.php?id=' + id + '&f=' + M.totara_f2f_room.config.facetofaceid +
                        '&s=' + M.totara_f2f_room.config.sessionid + '&sesskey=' + M.cfg.sesskey;
                },
                editcustomassethandler
            );

            // Select asset dialog handler.
            var handler = new totaraDialog_handler_treeview_multiselect();
            handler._update = function() {
                var elements = $('.selected > div > span', this._container);
                var ids = this._get_ids(elements);
                $assetlist.empty();

                // Display elements.
                ids.forEach(function(id){
                    var $item = $('#item_' + id, this._container).clone();
                    // Get name and render asset.
                    $('span', $item).remove();
                    $item.data('name', $item.text());
                    var $elem = render_asset_item($item.data(), $input, offset);
                    $assetlist.append($elem);
                });
                $input.val(ids.join());
                this._dialog.hide();
            };

            // Select asset dialog.
            var buttonsObj = {};
            buttonsObj[M.util.get_string('ok','moodle')] = function() { handler._update(); };
            buttonsObj[M.util.get_string('cancel','moodle')] = function() { handler._cancel(); };

            handler.oldLoad = handler.load;

            handler.load = function(response) {
                handler.oldLoad(response);
                var context = $(".ui-dialog [id^='selectassets'][id$='-dialog']"),
                    height = context.height() - $('.dialog-footer', context).outerHeight();

                $('.select', context).outerHeight(height);
            };

            totaraDialogs['selectassets'+offset] = new totaraDialog(
                'selectassets'+offset+'-dialog',
                $(this).attr('id'),
                {
                    buttons: buttonsObj,
                    title: '<h2>' + M.util.get_string('chooseassets', 'facetoface') + '</h2>'
                },
                function() {
                    var sessionid = M.totara_f2f_room.config.sessionid;
                    if (Number(M.totara_f2f_room.config.clone) == 1) {
                        sessionid = 0;
                    }
                    return url + 'asset/ajax/sessionassets.php?sessionid=' + sessionid +
                        '&facetofaceid=' + M.totara_f2f_room.config.facetofaceid +
                        '&timestart=' + $('input[name="timestart[' + offset + ']"]').val() +
                        '&timefinish=' + $('input[name="timefinish[' + offset + ']"]').val() +
                        '&selected=' + $('input[name="assetids[' + offset + ']"]').val() +
                        '&offset=' + offset +
                        '&sesskey=' + M.cfg.sesskey;
                },
                handler
            );
        });
    }
};
