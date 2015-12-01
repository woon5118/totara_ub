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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */

define(['jquery'], function($) {
    var addRemove = {

        /**
         * module initialisation method called by php js_call_amd()
         */
        init: function() {

            // Click on "add" button will move selected users from potential to existing.
            $('#add').click(function(evt) {
                evt.preventDefault();
                $selected = $('#addselect').find("option:selected");
                // Move to existing.
                $selected.each(function(_i, elem) {
                    var insidx = 0;
                    if ($('#removeselect option').size()) {
                        // Find best position (closest option will smaller index to insert after it).
                        $('#removeselect option').each(function(extidx, extopt) {
                            if ($(extopt).data('idx') < $(elem).data('idx')) {
                                insidx = $(extopt).data('idx');
                            }
                        });
                    }

                    if (insidx > 0) {
                        $(elem).insertAfter($('#removeselect option[data-idx=' + insidx + ']'));
                    } else {
                        $('#removeselect').append(elem);
                    }
                });
            });

            // Click on "remove" button will move selected users from potential to existing.
            $('#remove').click(function(evt) {
                evt.preventDefault();
                $selected = $('#removeselect').find("option:selected");
                // Move to potential.
                $selected.each(function(_i, elem) {
                    var insidx = 0;
                    if ($('#addselect option').size()) {
                        // Find best position (closest option will smaller index to insert after it).
                        $('#addselect option').each(function(extidx, extopt) {
                            if ($(extopt).data('idx') < $(elem).data('idx')) {
                                insidx = $(extopt).data('idx');
                            }
                        });
                    }

                    if (insidx > 0) {
                        $(elem).insertAfter($('#addselect option[data-idx=' + insidx + ']'));
                    } else {
                        $('#addselect').append(elem);
                    }
                });
            });

            $('#interested').click(function() {
               $('form#assignform').submit();
            });

            $('#searchtext, #searchtoremovetext').focus(function() {
                $('#add').attr('disabled', 'disabled');
                $('#remove').attr('disabled', 'disabled');
            });

            $('#removeselect').focus(function() {
                $('#add').attr('disabled', 'disabled');
                $('#remove').removeAttr('disabled');
            });

            $('#addselect').focus(function() {
                $('#add').removeAttr('disabled');
                $('#remove').attr('disabled', 'disabled');
            });

            $('#assignform').submit(function() {
               // Select all users to send them with form.
                $('#removeselect option').prop('selected', true);
            });

            $('#searchtoremovetext').on('keyup keypress blur change', function() {
                var value = $('#searchtoremovetext').val();
                if (value.length > 0) {
                    $('#searchtoremovereset').show();
                    // Filter.
                    $('#removeselect option').each(function(ind, elem) {
                        var $elem = $(elem);
                        if($elem.text().search(value) !== -1) {
                            $elem.show();
                        } else {
                            $elem.hide();
                        }
                    });
                } else {
                    $('#searchtoremovereset').hide();
                    $('#removeselect option').show();
                }
            });

            $('#searchtoremovereset').click(function(evt) {
                // Reset search.
                evt.preventDefault();
                $('#searchtoremovetext').val('');
                $('#searchtoremovetext').trigger('change');
            });
        }
    };

    return addRemove;
});