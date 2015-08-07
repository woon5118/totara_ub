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
 * @author Mark Webster <mark.webster@catalyst-eu.net>
 * @author Brian Barnes <brian.barnes@totaralms.com>
 * @package totara
 * @subpackage totara_question
 */

define(['jquery', 'core/str', 'core/config'], function($, mdlstrings, mdlcfg) {
    var myappraisal = {

        config: {},
        /**
        * module initialisation method called by php js_init_call()
        *
        * @param object    YUI instance
        * @param string    args supplied in JSON format
        */
        init : function(args) {

            if (args) {
                myappraisal.config = $.parseJSON(args);
            }

            var $mainForm = $('input#id_submitbutton').closest('form');
            var $saveProgress = $("<input>").attr({"type" : "hidden", "name" : "submitaction"}).val('saveprogress');
            var $completeStage = $("<input>").attr({"type" : "hidden", "name" : "submitaction"}).val('completestage');

            $('#saveprogress').on('submit', function(e){
              window.onbeforeunload = null; // Prevent leaving page warning.
              e.preventDefault();
              $mainForm.append($saveProgress);
              $mainForm.submit();
            });

            $('#completestage').on('submit', function(e){
              window.onbeforeunload = null; // Prevent leaving page warning.
              e.preventDefault();
              $mainForm.append($completeStage);
              $mainForm.submit();
            });

            // Get moodle strings - yes their implementation isn't great.
            var requiredstrings = [];
            requiredstrings.push({key: 'printyourappraisal', component: 'totara_appraisal'});
            requiredstrings.push({key: 'snapshotdialogtitle', component: 'totara_appraisal'});

            mdlstrings.get_strings(requiredstrings).done(function (strings) {
                var tstr = [];
                for (var i = 0; i < requiredstrings.length; i++) {
                    tstr[requiredstrings[i].key] = strings[i];
                }

                // Print and PDF dialog boxes.
                var snapshoturl = mdlcfg.wwwroot + '/totara/appraisal/snapshot.php';
                var urlparamselect = {
                    appraisalid: myappraisal.config.appraisalid,
                    role: myappraisal.config.role,
                    subjectid: myappraisal.config.subjectid,
                    action: 'stages'
                };
                var urlparamstrselect = $.param(urlparamselect);

                myappraisal.stagesSelectDialog(
                    'print',
                    tstr.printyourappraisal,
                    snapshoturl + '?' + urlparamstrselect,
                    snapshoturl
                );

                var urlparampdf = {
                    appraisalid: myappraisal.config.appraisalid,
                    role: myappraisal.config.role,
                    subjectid: myappraisal.config.subjectid,
                    action: 'snapshot',
                    sesskey: mdlcfg.sesskey
                };
                var urlparamstrpdf = $.param(urlparampdf);

                myappraisal.savePdfDialog(
                    'savepdf',
                    tstr.snapshotdialogtitle,
                    snapshoturl + '?' + urlparamstrpdf
                );
            });

            setInterval(myappraisal.keepAlive, 1000 * myappraisal.config.keepalivetime);
        },

        stagesSelectDialog: function(name, title, findurl, printurl) {
            var handler = new totaraDialog_handler();

            handler._print = function(e, printurl) {
                var urlparam = $('#printform').serialize();

                M.util.help_popups.setup(Y);
                var popupdata = {
                    name: 'printpopup',
                    url: printurl+'?'+urlparam,
                    options: "height=500,width=600,top=100,left=100,menubar=0,location=0,scrollbars,resizable,toolbar,status,directories=0,dependent"
                };
                openpopup(e, popupdata);

                handler._cancel();
            };

            var requiredstrings = [];
            requiredstrings.push({key: 'printnow', component: 'totara_appraisal'});
            requiredstrings.push({key: 'cancel', component: 'moodle'});
            mdlstrings.get_strings(requiredstrings).done(function(strings) {
                var tstr = [];
                for (var i = 0; i < requiredstrings.length; i++) {
                    tstr[requiredstrings[i].key] = strings[i];
                }

                var buttonObj = {};
                buttonObj[tstr.printnow] = function(e) { handler._print(e, printurl); };
                buttonObj[tstr.cancel] = function() { handler._cancel(); };

                totaraDialogs[name] = new totaraDialog(
                    name,
                    'show-'+name+'-dialog',
                    {
                        buttons: buttonObj,
                        title: '<h2>'+title+'</h2>'
                    },
                    findurl,
                    handler
                );
            });
        },

        savePdfDialog: function(name, title, findurl) {
            var handler = new totaraDialog_handler();

            handler._download = function() {
                var url = $('#downloadurl').val();
                if (url) {
                    window.location.href = url;
                    handler._cancel();
                }
            };

            handler._open = function() {
                handler._dialog.dialog.html(mdlstrings.get_string('snapshotgeneration', 'totara_appraisal'));
            };

            var requiredstrings = [];
            requiredstrings.push({key: 'downloadnow', component: 'totara_appraisal'});
            requiredstrings.push({key: 'cancel', component: 'moodle'});
            mdlstrings.get_strings(requiredstrings).done(function(strings) {
                var tstr = [];
                for (var i = 0; i < requiredstrings.length; i++) {
                    tstr[requiredstrings[i].key] = strings[i];
                }
                var buttonObj = {};
                buttonObj[tstr.downloadnow] = function() { handler._download(); };
                buttonObj[tstr.cancel] = function() { handler._cancel(); };

                totaraDialogs[name] = new totaraDialog(
                    name,
                    'show-'+name+'-dialog',
                    {
                        buttons: buttonObj,
                        title: '<h2>'+title+'</h2>',
                        height: '200'
                    },
                    findurl,
                    handler
                );
            });
        },

        keepAlive: function() {
            $.get(mdlcfg.wwwroot + '/totara/appraisal/keepalive.php');
        }
    };
    return myappraisal;
});
