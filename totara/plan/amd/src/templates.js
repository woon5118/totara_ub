/**
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
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @author Brian Barnes <brian.barnes@totaralms.com>
 * @package totara
 * @subpackage plan
 */

define(['jquery'], function ($) {

    var templatecontrol = {
        // Optional php params and defaults defined here, args passed to init method
        // below will override these values.
        config: {},
        // Public handler reference for the dialog.
        totaraDialog_handler_preRequisite: null,

        /**
         * module initialisation method called by php js_call_amd()
         *
         * @param string    args supplied in JSON format
         */
        init: function(args) {
            // If defined, parse args into this module's config object.
            if (args) {
                var jargs = $.parseJSON(args);
                this.config = jargs;
            }

            var templates = templatecontrol.config.templates;

            // Attach event to drop down.
            $('select#id_templateid').change(function() {
                var select = $(this);

                // Get current value.
                var current = select.val();

                // Overwrite form data.
                $('input#id_name').val(templates[current].fullname);

                var date = new Date(templates[current].enddate * 1000);

                $('#id_enddate_day').val(date.getDate());
                $('#id_enddate_month').val(date.getMonth() + 1);
                $('#id_enddate_year').val(date.getFullYear());
            });

        }
    };
    return templatecontrol;
});
